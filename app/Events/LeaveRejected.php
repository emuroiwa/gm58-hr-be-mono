<?php

namespace App\Events;

use App\Models\Leave;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Leave $leave
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->leave->employee->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => "Your leave request from {$this->leave->start_date->format('M d')} to {$this->leave->end_date->format('M d')} has been rejected",
            'leave_id' => $this->leave->id,
            'type' => 'leave_rejected',
            'timestamp' => now()->toISOString(),
        ];
    }
}
