<?php

namespace App\Events;

use App\Models\PayrollPeriod;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayrollProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public PayrollPeriod $payrollPeriod
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('company.' . $this->payrollPeriod->company_id),
            new PrivateChannel('payroll.' . $this->payrollPeriod->company_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => "Payroll for {$this->payrollPeriod->name} has been processed",
            'payroll_period' => [
                'id' => $this->payrollPeriod->id,
                'name' => $this->payrollPeriod->name,
                'total_employees' => $this->payrollPeriod->payrolls()->count(),
                'total_amount' => $this->payrollPeriod->total_net,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}
