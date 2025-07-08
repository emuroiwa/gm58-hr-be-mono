<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test company and user
        $this->company = Company::factory()->create([
            'name' => 'Test Company',
            'is_active' => true
        ]);
        
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true
        ]);

        $this->employee = Employee::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'email' => 'test@example.com'
        ]);
    }

    public function test_login_with_valid_credentials()
    {
        // Arrange
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/login', $loginData);

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'is_active'
                ],
                'token',
                'token_type',
                'expires_in'
            ]
        ]);

        $this->assertEquals('Bearer', $response->json('data.token_type'));
        $this->assertNotEmpty($response->json('data.token'));

        // Verify last login was updated
        $this->user->refresh();
        $this->assertNotNull($this->user->last_login_at);
    }

    public function test_login_with_invalid_email()
    {
        // Arrange
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/login', $loginData);

        // Assert
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials'
        ]);
    }

    public function test_login_with_invalid_password()
    {
        // Arrange
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/login', $loginData);

        // Assert
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials'
        ]);
    }

    public function test_login_with_inactive_user()
    {
        // Arrange
        $this->user->update(['is_active' => false]);
        
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/login', $loginData);

        // Assert
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Account is inactive'
        ]);
    }

    public function test_login_validation_errors()
    {
        // Test missing email
        $response = $this->postJson('/api/v1/auth/login', [
            'password' => 'password123'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        // Test missing password
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);

        // Test invalid email format
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_with_inactive_company()
    {
        // Arrange
        $this->company->update(['is_active' => false]);
        
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/login', $loginData);

        // Assert - Login should succeed, but middleware should block subsequent requests
        $response->assertStatus(200);
        
        // Test protected route with inactive company
        $token = $response->json('data.token');
        $protectedResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/auth/me');
        
        $protectedResponse->assertStatus(403);
        $protectedResponse->assertJson([
            'message' => 'Company account is inactive'
        ]);
    }

    public function test_me_endpoint_with_valid_token()
    {
        // Arrange
        Sanctum::actingAs($this->user);

        // Act
        $response = $this->getJson('/api/v1/auth/me');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'is_active',
                'company',
                'employee'
            ]
        ]);
        $response->assertJson([
            'data' => [
                'email' => 'test@example.com',
                'role' => 'admin'
            ]
        ]);
    }

    public function test_me_endpoint_without_token()
    {
        // Act
        $response = $this->getJson('/api/v1/auth/me');

        // Assert
        $response->assertStatus(401);
    }

    public function test_logout_with_valid_token()
    {
        // Arrange
        Sanctum::actingAs($this->user);

        // Act
        $response = $this->postJson('/api/v1/auth/logout');

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logged out successfully'
        ]);

        // Verify token is no longer valid
        $meResponse = $this->getJson('/api/v1/auth/me');
        $meResponse->assertStatus(401);
    }

    public function test_logout_without_token()
    {
        // Act
        $response = $this->postJson('/api/v1/auth/logout');

        // Assert
        $response->assertStatus(401);
    }

    public function test_refresh_token_with_valid_token()
    {
        // Arrange
        Sanctum::actingAs($this->user);

        // Act
        $response = $this->postJson('/api/v1/auth/refresh');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'token_type',
                'expires_in'
            ]
        ]);
        $this->assertEquals('Bearer', $response->json('data.token_type'));
        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_refresh_token_without_token()
    {
        // Act
        $response = $this->postJson('/api/v1/auth/refresh');

        // Assert
        $response->assertStatus(401);
    }

    public function test_login_rate_limiting()
    {
        // Arrange
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        // Act - Make multiple failed login attempts
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/v1/auth/login', $loginData);
            $response->assertStatus(401);
        }

        // Make one more attempt
        $response = $this->postJson('/api/v1/auth/login', $loginData);

        // Assert - Should be rate limited
        $response->assertStatus(429);
    }

    public function test_user_with_different_roles_can_login()
    {
        // Test employee role
        $employee = User::factory()->create([
            'company_id' => $this->company->id,
            'email' => 'employee@example.com',
            'password' => Hash::make('password123'),
            'role' => 'employee',
            'is_active' => true
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'employee@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'user' => [
                    'role' => 'employee'
                ]
            ]
        ]);

        // Test manager role
        $manager = User::factory()->create([
            'company_id' => $this->company->id,
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'is_active' => true
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'manager@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'user' => [
                    'role' => 'manager'
                ]
            ]
        ]);
    }

    public function test_login_updates_last_login_timestamp()
    {
        // Arrange
        $originalLastLogin = $this->user->last_login_at;
        
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/login', $loginData);

        // Assert
        $response->assertStatus(200);
        
        $this->user->refresh();
        $this->assertNotEquals($originalLastLogin, $this->user->last_login_at);
        $this->assertNotNull($this->user->last_login_at);
    }
}