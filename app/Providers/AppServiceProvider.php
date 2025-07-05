<?php

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

        // User Repository
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
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

    /**
     * Register singleton repository bindings (optional)
     * Use this method if you want repositories as singletons
     */
    private function registerSingletonRepositories(): void
    {
        // Example: If you want PayrollRepository as singleton
        // $this->app->singleton(
        //     PayrollRepositoryInterface::class,
        //     PayrollRepository::class
        // );
    }
}