<?php

namespace App\Events;

use App\Models\Employee;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Employee $employee
    ) {}
}
