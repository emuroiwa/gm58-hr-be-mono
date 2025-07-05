<?php

namespace App\Events;

use App\Models\Department;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepartmentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Department $department
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('company.' . $this->department->company_id),
            new PrivateChannel('hr.' . $this->department->company_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => "New department '{$this->department->name}' has been created",
            'department' => [
                'id' => $this->department->id,
                'name' => $this->department->name,
                'manager' => $this->department->manager?->first_name . ' ' . $this->department->manager?->last_name,
            ],
            'type' => 'department_created',
            'timestamp' => now()->toISOString(),
        ];
    }
}
