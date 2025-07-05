<?php

namespace App\Services;

use App\Contracts\TimeSheetRepositoryInterface;
use Carbon\Carbon;

class TimeSheetService
{
    public function __construct(
        private TimeSheetRepositoryInterface $timeSheetRepository
    ) {}

    public function createTimeEntry($employeeId, array $data)
    {
        return $this->timeSheetRepository->create([
            'employee_id' => $employeeId,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'] ?? null,
            'break_duration' => $data['break_duration'] ?? 0,
            'description' => $data['description'] ?? null,
            'project' => $data['project'] ?? null,
            'task' => $data['task'] ?? null,
            'status' => 'draft',
        ]);
    }

    public function updateTimeEntry($timeSheetId, $employeeId, array $data)
    {
        $timeSheet = $this->timeSheetRepository->findByIdAndEmployee($timeSheetId, $employeeId);
        
        if (!$timeSheet) {
            throw new \Exception('Time entry not found');
        }

        $updatedTimeSheet = $this->timeSheetRepository->update($timeSheetId, $data);
        
        if ($updatedTimeSheet->start_time && $updatedTimeSheet->end_time) {
            $this->calculateDuration($updatedTimeSheet);
        }

        return $updatedTimeSheet;
    }

    public function submitTimeSheet($employeeId, $weekStartDate)
    {
        $weekEndDate = Carbon::parse($weekStartDate)->endOfWeek();
        
        $this->timeSheetRepository->updateByEmployeeAndDateRange(
            $employeeId, 
            $weekStartDate, 
            $weekEndDate, 
            ['status' => 'submitted']
        );

        return ['message' => 'Timesheet submitted successfully'];
    }

    public function getEmployeeTimeSheets($employeeId, array $filters = [])
    {
        return $this->timeSheetRepository->getEmployeeTimeSheets($employeeId, $filters);
    }

    public function getCompanyTimeSheets($companyId, array $filters = [])
    {
        return $this->timeSheetRepository->getCompanyTimeSheets($companyId, $filters);
    }

    public function approveTimeSheet($timeSheetId, $companyId, $approverId)
    {
        $timeSheet = $this->timeSheetRepository->findByIdAndCompany($timeSheetId, $companyId);
        
        if (!$timeSheet) {
            throw new \Exception('Time sheet not found');
        }

        return $this->timeSheetRepository->update($timeSheetId, [
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
    }

    private function calculateDuration($timeSheet)
    {
        if ($timeSheet->start_time && $timeSheet->end_time) {
            $start = Carbon::parse($timeSheet->start_time);
            $end = Carbon::parse($timeSheet->end_time);
            $duration = $end->diffInMinutes($start) - $timeSheet->break_duration;
            
            $this->timeSheetRepository->update($timeSheet->id, ['duration' => $duration]);
        }
    }
}
