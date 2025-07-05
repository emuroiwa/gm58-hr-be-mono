<?php

namespace App\Events;

use App\Models\Employee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Employee $employee
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('company.' . $this->employee->company_id),
            new PrivateChannel('hr.' . $this->employee->company_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => "New employee {$this->employee->first_name} {$this->employee->last_name} has been added",
            'employee' => [
                'id' => $this->employee->id,
                'name' => $this->employee->first_name . ' ' . $this->employee->last_name,
                'department' => $this->employee->department?->name,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}
