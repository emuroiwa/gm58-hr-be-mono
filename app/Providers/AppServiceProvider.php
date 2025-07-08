<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repository Interfaces
use App\Contracts\PayrollRepositoryInterface;
use App\Contracts\AttendanceRepositoryInterface;
use App\Contracts\BenefitRepositoryInterface;
use App\Contracts\DepartmentRepositoryInterface;
use App\Contracts\PositionRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\CompanyUserRepositoryInterface;
use App\Contracts\DocumentRepositoryInterface;
use App\Contracts\LeaveRepositoryInterface;
use App\Contracts\NotificationRepositoryInterface;
use App\Contracts\CompanyRepositoryInterface;
use App\Contracts\EmployeeRepositoryInterface;

// Repository Implementations
use App\Repositories\PayrollRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\BenefitRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\PositionRepository;
use App\Repositories\UserRepository;
use App\Repositories\CompanyUserRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\LeaveRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\EmployeeRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository Bindings
        $this->registerRepositories();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register repository bindings for dependency injection
     */
    private function registerRepositories(): void
    {
        // Company Repository
        $this->app->bind(
            CompanyRepositoryInterface::class,
            CompanyRepository::class
        );

        // Employee Repository
        $this->app->bind(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class
        );

        // User Repository
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        // Payroll Repository
        $this->app->bind(
            PayrollRepositoryInterface::class,
            PayrollRepository::class
        );

        // Attendance Repository
        $this->app->bind(
            AttendanceRepositoryInterface::class,
            AttendanceRepository::class
        );

        // Benefit Repository
        $this->app->bind(
            BenefitRepositoryInterface::class,
            BenefitRepository::class
        );

        // Department Repository
        $this->app->bind(
            DepartmentRepositoryInterface::class,
            DepartmentRepository::class
        );

        // Position Repository
        $this->app->bind(
            PositionRepositoryInterface::class,
            PositionRepository::class
        );

        // Company User Repository
        $this->app->bind(
            CompanyUserRepositoryInterface::class,
            CompanyUserRepository::class
        );

        // Document Repository
        $this->app->bind(
            DocumentRepositoryInterface::class,
            DocumentRepository::class
        );

        // Leave Repository
        $this->app->bind(
            LeaveRepositoryInterface::class,
            LeaveRepository::class
        );

        // Notification Repository
        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );
    }
}