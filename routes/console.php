<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ProcessMonthlyPayroll;
use App\Console\Commands\SendAttendanceReminders;
use App\Console\Commands\GenerateReports;
use App\Console\Commands\BackupDatabase;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// HR System Console Commands
Artisan::command('hr:process-payroll {company_id?}', function ($companyId = null) {
    $this->info('Processing payroll...');
    
    if ($companyId) {
        $this->info("Processing payroll for company: {$companyId}");
        // Process specific company payroll
    } else {
        $this->info('Processing payroll for all companies');
        // Process all company payrolls
    }
    
    $this->info('Payroll processing completed!');
})->purpose('Process monthly payroll for companies');

Artisan::command('hr:attendance-reminders', function () {
    $this->info('Sending attendance reminders...');
    
    // Send attendance reminders to employees who haven't marked attendance
    \App\Jobs\SendAttendanceReminders::dispatch();
    
    $this->info('Attendance reminders sent!');
})->purpose('Send attendance reminders to employees');

Artisan::command('hr:generate-reports {type} {company_id?}', function ($type, $companyId = null) {
    $this->info("Generating {$type} reports...");
    
    $validTypes = ['employee', 'attendance', 'payroll', 'leave', 'performance'];
    
    if (!in_array($type, $validTypes)) {
        $this->error("Invalid report type. Valid types: " . implode(', ', $validTypes));
        return;
    }
    
    if ($companyId) {
        $this->info("Generating {$type} report for company: {$companyId}");
    } else {
        $this->info("Generating {$type} reports for all companies");
    }
    
    $this->info('Report generation completed!');
})->purpose('Generate various HR reports');

Artisan::command('hr:backup {company_id?}', function ($companyId = null) {
    $this->info('Starting database backup...');
    
    if ($companyId) {
        $this->info("Backing up data for company: {$companyId}");
        \App\Jobs\BackupData::dispatch($companyId);
    } else {
        $this->info('Backing up all company data');
        // Backup all companies
        $companies = \App\Models\Company::where('is_active', true)->get();
        foreach ($companies as $company) {
            \App\Jobs\BackupData::dispatch($company->id);
        }
    }
    
    $this->info('Backup process initiated!');
})->purpose('Backup company data');

Artisan::command('hr:sync-employees {company_id?}', function ($companyId = null) {
    $this->info('Syncing employee data...');
    
    if ($companyId) {
        $this->info("Syncing employees for company: {$companyId}");
        \App\Jobs\SyncEmployeeData::dispatch($companyId);
    } else {
        $this->info('Syncing employees for all companies');
        $companies = \App\Models\Company::where('is_active', true)->get();
        foreach ($companies as $company) {
            \App\Jobs\SyncEmployeeData::dispatch($company->id);
        }
    }
    
    $this->info('Employee sync initiated!');
})->purpose('Sync employee data across systems');

Artisan::command('hr:calculate-attendance {company_id} {start_date} {end_date}', function ($companyId, $startDate, $endDate) {
    $this->info("Calculating attendance for company {$companyId} from {$startDate} to {$endDate}");
    
    \App\Jobs\CalculateAttendance::dispatch($companyId, $startDate, $endDate);
    
    $this->info('Attendance calculation initiated!');
})->purpose('Calculate attendance statistics for a date range');
