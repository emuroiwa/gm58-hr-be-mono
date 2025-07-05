<?php

namespace App\Services;

use App\Contracts\LeaveRepositoryInterface;
use App\Contracts\EmployeeRepositoryInterface;
use Carbon\Carbon;
use App\Events\LeaveApproved;
use App\Events\LeaveRejected;

class LeaveService
{
    public function __construct(
        private LeaveRepositoryInterface $leaveRepository,
        private EmployeeRepositoryInterface $employeeRepository
    ) {}

    public function applyLeave($employeeId, array $data)
    {
        $employee = $this->employeeRepository->find($employeeId);
        
        if (!$employee) {
            throw new \Exception('Employee not found');
        }

        // Calculate leave days
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $leaveDays = $startDate->diffInDays($endDate) + 1;

        // Check leave balance
        $this->checkLeaveBalance($employee, $data['leave_type_id'], $leaveDays);

        $leave = $this->leaveRepository->create([
            'employee_id' => $employeeId,
            'leave_type_id' => $data['leave_type_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'days' => $leaveDays,
            'reason' => $data['reason'],
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        return $this->leaveRepository->findWithRelations($leave->id, ['employee', 'leaveType']);
    }

    public function approveLeave($leaveId, $companyId, $approverId, $comments = null)
    {
        $leave = $this->leaveRepository->findByIdAndCompany($leaveId, $companyId);
        
        if (!$leave) {
            throw new \Exception('Leave request not found');
        }

        $updatedLeave = $this->leaveRepository->update($leaveId, [
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'comments' => $comments,
        ]);

        event(new LeaveApproved($updatedLeave));

        return $updatedLeave;
    }

    public function rejectLeave($leaveId, $companyId, $rejectedBy, $comments)
    {
        $leave = $this->leaveRepository->findByIdAndCompany($leaveId, $companyId);
        
        if (!$leave) {
            throw new \Exception('Leave request not found');
        }

        $updatedLeave = $this->leaveRepository->update($leaveId, [
            'status' => 'rejected',
            'rejected_by' => $rejectedBy,
            'rejected_at' => now(),
            'comments' => $comments,
        ]);

        event(new LeaveRejected($updatedLeave));

        return $updatedLeave;
    }

    public function getEmployeeLeaves($employeeId, array $filters = [])
    {
        return $this->leaveRepository->getEmployeeLeaves($employeeId, $filters);
    }

    public function getCompanyLeaves($companyId, array $filters = [])
    {
        return $this->leaveRepository->getCompanyLeaves($companyId, $filters);
    }

    public function getLeaveBalance($employeeId, $leaveTypeId = null)
    {
        $employee = $this->employeeRepository->find($employeeId);
        
        if (!$employee) {
            throw new \Exception('Employee not found');
        }

        if ($leaveTypeId) {
            return $this->calculateLeaveBalance($employee, $leaveTypeId);
        }

        return $this->leaveRepository->getEmployeeLeaveBalances($employeeId);
    }

    private function checkLeaveBalance($employee, $leaveTypeId, $requestedDays)
    {
        $balance = $this->calculateLeaveBalance($employee, $leaveTypeId);
        
        if ($balance < $requestedDays) {
            throw new \Exception("Insufficient leave balance. Available: {$balance} days, Requested: {$requestedDays} days");
        }
    }

    private function calculateLeaveBalance($employee, $leaveTypeId)
    {
        return $this->leaveRepository->calculateLeaveBalance($employee->id, $leaveTypeId);
    }
}
