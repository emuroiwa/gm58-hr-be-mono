<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a default currency for testing
        Currency::factory()->create([
            'id' => 1,
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$'
        ]);
    }

    public function test_company_registration_success()
    {
        // Arrange
        $registrationData = [
            'company_name' => 'Test Company Inc.',
            'company_email' => 'info@testcompany.com',
            'company_phone' => '+1-555-123-4567',
            'company_address' => '123 Business St',
            'company_city' => 'Business City',
            'company_state' => 'CA',
            'company_country' => 'US',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@testcompany.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'phone' => '+1-555-987-6543',
            'currency_id' => 1,
            'timezone' => 'America/New_York'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/register-company', $registrationData);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'company' => [
                    'id',
                    'name',
                    'email',
                    'is_active'
                ],
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'is_active'
                ],
                'message'
            ]
        ]);

        // Verify database records
        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company Inc.',
            'email' => 'info@testcompany.com',
            'is_active' => true
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@testcompany.com',
            'role' => 'admin',
            'is_active' => true
        ]);

        $this->assertDatabaseHas('employees', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@testcompany.com',
            'status' => 'active'
        ]);

        // Verify relationships
        $user = User::where('email', 'john.doe@testcompany.com')->first();
        $this->assertNotNull($user->company);
        $this->assertNotNull($user->employee);
        $this->assertEquals('Test Company Inc.', $user->company->name);
    }

    public function test_company_registration_with_minimal_data()
    {
        // Arrange
        $registrationData = [
            'company_name' => 'Minimal Company',
            'company_email' => 'info@minimal.com',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@minimal.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/register-company', $registrationData);

        // Assert
        $response->assertStatus(201);

        // Verify defaults are applied
        $this->assertDatabaseHas('companies', [
            'name' => 'Minimal Company',
            'country' => 'US',
            'currency_id' => 1,
            'timezone' => 'UTC',
            'is_active' => true
        ]);
    }

    public function test_company_registration_validation_errors()
    {
        // Test missing required fields
        $response = $this->postJson('/api/v1/auth/register-company', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'company_name',
            'company_email',
            'first_name',
            'last_name',
            'email',
            'password'
        ]);
    }

    public function test_company_registration_duplicate_email()
    {
        // Arrange - Create existing user
        User::factory()->create(['email' => 'existing@example.com']);

        $registrationData = [
            'company_name' => 'Test Company',
            'company_email' => 'info@test.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com', // Duplicate email
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/register-company', $registrationData);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_company_registration_password_mismatch()
    {
        // Arrange
        $registrationData = [
            'company_name' => 'Test Company',
            'company_email' => 'info@test.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/register-company', $registrationData);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_company_registration_invalid_email_format()
    {
        // Arrange
        $registrationData = [
            'company_name' => 'Test Company',
            'company_email' => 'invalid-email',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'also-invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/register-company', $registrationData);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['company_email', 'email']);
    }

    public function test_company_registration_weak_password()
    {
        // Arrange
        $registrationData = [
            'company_name' => 'Test Company',
            'company_email' => 'info@test.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'password' => '123', // Too weak
            'password_confirmation' => '123'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/register-company', $registrationData);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_company_registration_database_transaction_rollback()
    {
        // Arrange - Force a database error by using invalid currency_id
        $registrationData = [
            'company_name' => 'Test Company',
            'company_email' => 'info@test.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'currency_id' => 999999 // Non-existent currency
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/register-company', $registrationData);

        // Assert - Should fail and rollback
        $response->assertStatus(500);

        // Verify no partial data was saved
        $this->assertDatabaseMissing('companies', [
            'name' => 'Test Company'
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'john@test.com'
        ]);
    }

    public function test_registered_user_has_correct_role_and_permissions()
    {
        // Arrange
        $registrationData = [
            'company_name' => 'Test Company',
            'company_email' => 'info@test.com',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/register-company', $registrationData);

        // Assert
        $response->assertStatus(201);

        $user = User::where('email', 'admin@test.com')->first();
        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->isManager()); // Admin is also considered manager
        $this->assertFalse($user->isEmployee());
    }

    public function test_registration_creates_employee_with_administrator_title()
    {
        // Arrange
        $registrationData = [
            'company_name' => 'Test Company',
            'company_email' => 'info@test.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/register-company', $registrationData);

        // Assert
        $response->assertStatus(201);

        $this->assertDatabaseHas('employees', [
            'email' => 'john@test.com',
            'job_title' => 'Administrator',
            'status' => 'active'
        ]);

        $employee = Employee::where('email', 'john@test.com')->first();
        $this->assertNotNull($employee->hire_date);
        $this->assertEquals(now()->toDateString(), $employee->hire_date->toDateString());
    }
}