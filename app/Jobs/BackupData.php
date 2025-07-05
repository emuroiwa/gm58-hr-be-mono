<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BackupData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour
    public $tries = 2;

    public function __construct(
        public int $companyId,
        public ?int $requestedBy = null,
        public array $tables = [],
        public string $backupType = 'full'
    ) {
        // Default tables to backup if none specified
        if (empty($this->tables)) {
            $this->tables = [
                'companies', 'departments', 'positions', 'employees',
                'users', 'payroll_periods', 'payrolls', 'attendances',
                'leaves', 'leave_types', 'time_sheets', 'performances'
            ];
        }
    }

    public function handle(NotificationService $notificationService)
    {
        try {
            Log::info("Starting data backup", [
                'company_id' => $this->companyId,
                'backup_type' => $this->backupType,
                'tables' => $this->tables
            ]);

            $backupPath = $this->createBackup();

            // Notify user if requested
            if ($this->requestedBy) {
                $user = User::find($this->requestedBy);
                if ($user) {
                    $notificationService->sendNotification(
                        $user->id,
                        'Data Backup Completed',
                        "Data backup completed successfully. File: " . basename($backupPath),
                        'success',
                        [
                            'backup_path' => $backupPath,
                            'backup_type' => $this->backupType,
                            'file_size' => Storage::size($backupPath)
                        ]
                    );
                }
            }

            Log::info("Data backup completed", [
                'backup_path' => $backupPath,
                'file_size' => Storage::size($backupPath)
            ]);

        } catch (Exception $e) {
            Log::error("Data backup failed", [
                'company_id' => $this->companyId,
                'error' => $e->getMessage()
            ]);

            // Notify user of failure
            if ($this->requestedBy) {
                $user = User::find($this->requestedBy);
                if ($user) {
                    $notificationService = app(NotificationService::class);
                    $notificationService->sendNotification(
                        $user->id,
                        'Data Backup Failed',
                        "Data backup failed: " . $e->getMessage(),
                        'error'
                    );
                }
            }

            throw $e;
        }
    }

    private function createBackup(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_company_{$this->companyId}_{$this->backupType}_{$timestamp}.sql";
        $backupPath = "backups/{$filename}";

        $sql = $this->generateBackupSql();
        Storage::put($backupPath, $sql);

        return $backupPath;
    }

    private function generateBackupSql(): string
    {
        $sql = "-- Company Data Backup\n";
        $sql .= "-- Company ID: {$this->companyId}\n";
        $sql .= "-- Generated: " . now()->toDateTimeString() . "\n\n";

        foreach ($this->tables as $table) {
            $sql .= $this->backupTable($table);
        }

        return $sql;
    }

    private function backupTable(string $table): string
    {
        $sql = "\n-- Table: {$table}\n";
        
        // Get table structure
        $createTable = DB::select("SHOW CREATE TABLE {$table}")[0];
        $sql .= $createTable->{'Create Table'} . ";\n\n";

        // Get table data (filtered by company_id if applicable)
        $query = DB::table($table);
        
        // Apply company filter if table has company_id column
        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        if (in_array('company_id', $columns)) {
            $query->where('company_id', $this->companyId);
        }

        $records = $query->get();

        if ($records->count() > 0) {
            $sql .= "INSERT INTO `{$table}` VALUES\n";
            $values = [];

            foreach ($records as $record) {
                $recordArray = (array) $record;
                $escapedValues = array_map(function ($value) {
                    if (is_null($value)) {
                        return 'NULL';
                    }
                    return "'" . addslashes($value) . "'";
                }, $recordArray);
                
                $values[] = '(' . implode(', ', $escapedValues) . ')';
            }

            $sql .= implode(",\n", $values) . ";\n\n";
        }

        return $sql;
    }

    public function failed(Exception $exception)
    {
        Log::error("Data backup job permanently failed", [
            'company_id' => $this->companyId,
            'backup_type' => $this->backupType,
            'error' => $exception->getMessage()
        ]);
    }
}
