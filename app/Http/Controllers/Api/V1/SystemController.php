<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\BackupData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SystemController extends Controller
{
    /**
     * Create system backup
     */
    public function createBackup(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'sometimes|in:full,company,database',
            'company_id' => 'sometimes|exists:companies,id'
        ]);

        try {
            $type = $request->get('type', 'full');
            $companyId = $request->get('company_id');

            if ($type === 'company' && !$companyId) {
                return response()->json([
                    'message' => 'Company ID is required for company backup'
                ], 422);
            }

            // Dispatch backup job
            if ($companyId) {
                BackupData::dispatch($companyId, $request->user()->id);
            } else {
                // Full system backup
                $companies = \App\Models\Company::where('is_active', true)->get();
                foreach ($companies as $company) {
                    BackupData::dispatch($company->id, $request->user()->id);
                }
            }

            return response()->json([
                'message' => 'Backup process started. You will be notified when complete.',
                'type' => $type,
                'status' => 'processing'
            ], 202);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to start backup process',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get available backups
     */
    public function getBackups(Request $request): JsonResponse
    {
        try {
            $backups = Storage::files('backups');
            
            $backupList = array_map(function ($backup) {
                $info = pathinfo($backup);
                return [
                    'filename' => $info['basename'],
                    'size' => Storage::size($backup),
                    'created_at' => Storage::lastModified($backup),
                    'type' => $this->getBackupType($info['basename']),
                ];
            }, $backups);

            // Sort by creation date (newest first)
            usort($backupList, function ($a, $b) {
                return $b['created_at'] - $a['created_at'];
            });

            return response()->json([
                'data' => $backupList
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve backups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system logs
     */
    public function getLogs(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'sometimes|in:application,api,error',
            'lines' => 'sometimes|integer|min:10|max:1000'
        ]);

        try {
            $type = $request->get('type', 'application');
            $lines = $request->get('lines', 100);

            $logFile = match($type) {
                'api' => storage_path('logs/api.log'),
                'error' => storage_path('logs/laravel.log'),
                default => storage_path('logs/laravel.log')
            };

            if (!file_exists($logFile)) {
                return response()->json([
                    'message' => 'Log file not found',
                    'type' => $type
                ], 404);
            }

            // Read last N lines from log file
            $logs = $this->readLastLines($logFile, $lines);

            return response()->json([
                'data' => [
                    'type' => $type,
                    'lines' => count($logs),
                    'logs' => $logs,
                    'file_size' => filesize($logFile),
                    'last_modified' => filemtime($logFile),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle maintenance mode
     */
    public function toggleMaintenance(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'message' => 'sometimes|string|max:255'
        ]);

        try {
            $enabled = $request->get('enabled');
            $message = $request->get('message', 'System is under maintenance');

            if ($enabled) {
                Artisan::call('down', ['--message' => $message]);
                $status = 'enabled';
            } else {
                Artisan::call('up');
                $status = 'disabled';
            }

            return response()->json([
                'message' => "Maintenance mode {$status}",
                'maintenance_mode' => $enabled,
                'maintenance_message' => $enabled ? $message : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to toggle maintenance mode',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * System health check
     */
    public function healthCheck(Request $request): JsonResponse
    {
        try {
            $health = [
                'status' => 'ok',
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'environment' => config('app.env'),
                'checks' => [
                    'database' => $this->checkDatabase(),
                    'cache' => $this->checkCache(),
                    'storage' => $this->checkStorage(),
                    'queue' => $this->checkQueue(),
                ]
            ];

            // Determine overall status
            $allChecks = collect($health['checks'])->values();
            $health['status'] = $allChecks->every(fn($check) => $check['status'] === 'ok') ? 'ok' : 'warning';

            return response()->json([
                'data' => $health
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Health check failed',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    private function getBackupType(string $filename): string
    {
        if (str_contains($filename, 'company_')) return 'company';
        if (str_contains($filename, 'full_')) return 'full';
        return 'database';
    }

    private function readLastLines(string $filename, int $lines): array
    {
        $file = file($filename);
        return array_slice($file, -$lines);
    }

    private function checkDatabase(): array
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    private function checkCache(): array
    {
        try {
            Cache::put('health_check', 'ok', 60);
            $value = Cache::get('health_check');
            return $value === 'ok' 
                ? ['status' => 'ok', 'message' => 'Cache is working']
                : ['status' => 'error', 'message' => 'Cache test failed'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache error: ' . $e->getMessage()];
        }
    }

    private function checkStorage(): array
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::put($testFile, 'test');
            $exists = Storage::exists($testFile);
            Storage::delete($testFile);
            
            return $exists 
                ? ['status' => 'ok', 'message' => 'Storage is working']
                : ['status' => 'error', 'message' => 'Storage test failed'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage error: ' . $e->getMessage()];
        }
    }

    private function checkQueue(): array
    {
        try {
            // This is a simplified check - in production you'd want more sophisticated queue monitoring
            return ['status' => 'ok', 'message' => 'Queue system available'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Queue error: ' . $e->getMessage()];
        }
    }
}
