<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\EmployeeService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class ImportEmployees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutes
    public $tries = 2;

    public function __construct(
        public int $companyId,
        public int $importedBy,
        public string $filePath,
        public array $options = []
    ) {}

    public function handle(EmployeeService $employeeService, NotificationService $notificationService)
    {
        try {
            Log::info("Starting employee import", [
                'company_id' => $this->companyId,
                'file_path' => $this->filePath
            ]);

            $importResults = $this->processImport($employeeService);

            // Notify user of completion
            $user = User::find($this->importedBy);
            if ($user) {
                $notificationService->sendNotification(
                    $user->id,
                    'Employee Import Completed',
                    "Employee import completed. Successful: {$importResults['successful']}, Failed: {$importResults['failed']}",
                    'success',
                    $importResults
                );
            }

            // Clean up file
            Storage::delete($this->filePath);

            Log::info("Employee import completed", $importResults);

        } catch (Exception $e) {
            Log::error("Employee import failed", [
                'company_id' => $this->companyId,
                'file_path' => $this->filePath,
                'error' => $e->getMessage()
            ]);

            // Notify user of failure
            $user = User::find($this->importedBy);
            if ($user) {
                $notificationService = app(NotificationService::class);
                $notificationService->sendNotification(
                    $user->id,
                    'Employee Import Failed',
                    "Employee import failed: " . $e->getMessage(),
                    'error'
                );
            }

            throw $e;
        }
    }

    private function processImport(EmployeeService $employeeService): array
    {
        $fileContent = Storage::get($this->filePath);
        $employees = $this->parseFile($fileContent);

        $successful = 0;
        $failed = 0;
        $errors = [];

        foreach ($employees as $index => $employeeData) {
            try {
                // Validate employee data
                $validator = $this->validateEmployeeData($employeeData);
                
                if ($validator->fails()) {
                    $errors[] = [
                        'row' => $index + 1,
                        'errors' => $validator->errors()->toArray()
                    ];
                    $failed++;
                    continue;
                }

                // Create employee
                $employeeService->createEmployee($this->companyId, $employeeData);
                $successful++;

            } catch (Exception $e) {
                $errors[] = [
                    'row' => $index + 1,
                    'error' => $e->getMessage()
                ];
                $failed++;
            }
        }

        return [
            'total_processed' => count($employees),
            'successful' => $successful,
            'failed' => $failed,
            'errors' => $errors
        ];
    }

    private function parseFile(string $content): array
    {
        $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);
        
        switch (strtolower($extension)) {
            case 'csv':
                return $this->parseCsv($content);
            case 'json':
                return $this->parseJson($content);
            default:
                throw new Exception("Unsupported file format: {$extension}");
        }
    }

    private function parseCsv(string $content): array
    {
        $lines = str_getcsv($content, "\n");
        $headers = str_getcsv(array_shift($lines));
        $employees = [];

        foreach ($lines as $line) {
            $values = str_getcsv($line);
            if (count($values) === count($headers)) {
                $employees[] = array_combine($headers, $values);
            }
        }

        return $employees;
    }

    private function parseJson(string $content): array
    {
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON format");
        }

        return $data;
    }

    private function validateEmployeeData(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'required|date',
            'job_title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'salary' => 'nullable|numeric|min:0',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern',
            'status' => 'nullable|in:active,inactive,terminated',
        ]);
    }

    public function failed(Exception $exception)
    {
        Log::error("Employee import job permanently failed", [
            'company_id' => $this->companyId,
            'file_path' => $this->filePath,
            'error' => $exception->getMessage()
        ]);

        // Clean up file
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }
    }
}
