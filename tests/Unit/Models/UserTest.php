<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_default_attributes()
    {
        // Act
        $user = new User();

        // Assert
        $this->assertEquals('employee', $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_user_fillable_attributes()
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'company_id' => 'company-123',
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'last_login_at' => now(),
        ];

        // Act
        $user = new User($userData);

        // Assert
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('password123', $user->password);
        $this->assertEquals('company-123', $user->company_id);
        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_password_is_hidden()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $userArray = $user->toArray();

        // Assert
        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    public function test_user_has_employee_relationship()
    {
        // Arrange
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $employee = Employee::factory()->create(['user_id' => $user->id, 'company_id' => $company->id]);

        // Act
        $userEmployee = $user->employee;

        // Assert
        $this->assertInstanceOf(Employee::class, $userEmployee);
        $this->assertEquals($employee->id, $userEmployee->id);
    }

    public function test_is_admin_method()
    {
        // Arrange
        $adminUser = User::factory()->create(['role' => 'admin']);
        $employeeUser = User::factory()->create(['role' => 'employee']);

        // Assert
        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($employeeUser->isAdmin());
    }

    public function test_is_manager_method()
    {
        // Arrange
        $adminUser = User::factory()->create(['role' => 'admin']);
        $managerUser = User::factory()->create(['role' => 'manager']);
        $employeeUser = User::factory()->create(['role' => 'employee']);

        // Assert
        $this->assertTrue($adminUser->isManager());
        $this->assertTrue($managerUser->isManager());
        $this->assertFalse($employeeUser->isManager());
    }

    public function test_is_employee_method()
    {
        // Arrange
        $adminUser = User::factory()->create(['role' => 'admin']);
        $employeeUser = User::factory()->create(['role' => 'employee']);

        // Assert
        $this->assertFalse($adminUser->isEmployee());
        $this->assertTrue($employeeUser->isEmployee());
    }

    public function test_user_casts()
    {
        // Arrange
        $user = User::factory()->create([
            'email_verified_at' => '2023-01-01 10:00:00',
            'last_login_at' => '2023-01-02 15:30:00',
            'is_active' => 1
        ]);

        // Assert
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->last_login_at);
        $this->assertIsBool($user->is_active);
    }

    public function test_user_role_defaults_when_not_set()
    {
        // Arrange & Act
        $user = User::factory()->create(['role' => null]);

        // Force reload to get default value
        $user = $user->fresh();

        // Assert - should fall back to default value
        $this->assertNotNull($user->role);
    }

    public function test_user_uuid_trait()
    {
        // Arrange & Act
        $user = User::factory()->create();

        // Assert
        $this->assertIsString($user->id);
        $this->assertEquals(36, strlen($user->id)); // UUID length
        $this->assertMatchesRegularExpression('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $user->id);
    }

    public function test_user_belongs_to_company_trait()
    {
        // Arrange
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        // Act
        $userCompany = $user->company;

        // Assert
        $this->assertInstanceOf(Company::class, $userCompany);
        $this->assertEquals($company->id, $userCompany->id);
    }

    public function test_user_validation_rules()
    {
        // Test that certain fields are required by trying to create invalid users
        
        // Test email uniqueness
        $existingUser = User::factory()->create(['email' => 'test@example.com']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => 'test@example.com']);
    }
}