<?php

namespace App\Events;

use App\Models\Position;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PositionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Position $position
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('company.' . $this->position->company_id),
            new PrivateChannel('hr.' . $this->position->company_id),
            new PrivateChannel('department.' . $this->position->department_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => "New position '{$this->position->title}' has been created in {$this->position->department->name}",
            'position' => [
                'id' => $this->position->id,
                'title' => $this->position->title,
                'department' => $this->position->department->name,
            ],
            'type' => 'position_created',
            'timestamp' => now()->toISOString(),
        ];
    }
}
