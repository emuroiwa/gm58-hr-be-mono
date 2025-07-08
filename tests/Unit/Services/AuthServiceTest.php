<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\CompanyRepositoryInterface;
use App\Contracts\EmployeeRepositoryInterface;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\AuthenticationException;
use Mockery;

class AuthServiceTest extends TestCase
{
    private AuthService $authService;
    private $userRepository;
    private $companyRepository;
    private $employeeRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->companyRepository = Mockery::mock(CompanyRepositoryInterface::class);
        $this->employeeRepository = Mockery::mock(EmployeeRepositoryInterface::class);
        
        $this->authService = new AuthService(
            $this->userRepository,
            $this->companyRepository,
            $this->employeeRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_login_with_valid_credentials()
    {
        // Arrange
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        // Mock the user with proper ID and createToken method
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 'user-123';
        $user->email = 'test@example.com';
        $user->password = Hash::make('password123');
        $user->is_active = true;
        $user->role = 'admin';
        
        $user->shouldReceive('createToken')
            ->with('auth-token')
            ->once()
            ->andReturn((object)['plainTextToken' => 'mock-token']);

        $this->userRepository
            ->shouldReceive('findUserByEmail')
            ->with($credentials['email'])
            ->once()
            ->andReturn($user);

        $this->userRepository
            ->shouldReceive('updateLastLogin')
            ->with($user->id)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->authService->login($credentials);

        // Assert
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('token_type', $result);
        $this->assertEquals('Bearer', $result['token_type']);
        $this->assertEquals('mock-token', $result['token']);
    }

    public function test_login_with_invalid_email()
    {
        // Arrange
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ];

        $this->userRepository
            ->shouldReceive('findUserByEmail')
            ->with($credentials['email'])
            ->once()
            ->andReturn(null);

        // Assert
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        // Act
        $this->authService->login($credentials);
    }

    public function test_login_with_invalid_password()
    {
        // Arrange
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $user = Mockery::mock(User::class)->makePartial();
        $user->email = 'test@example.com';
        $user->password = Hash::make('correctpassword');
        $user->is_active = true;

        $this->userRepository
            ->shouldReceive('findUserByEmail')
            ->with($credentials['email'])
            ->once()
            ->andReturn($user);

        // Assert
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        // Act
        $this->authService->login($credentials);
    }

    public function test_login_with_inactive_user()
    {
        // Arrange
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $user = Mockery::mock(User::class)->makePartial();
        $user->email = 'test@example.com';
        $user->password = Hash::make('password123');
        $user->is_active = false;

        $this->userRepository
            ->shouldReceive('findUserByEmail')
            ->with($credentials['email'])
            ->once()
            ->andReturn($user);

        // Assert
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Account is inactive');

        // Act
        $this->authService->login($credentials);
    }

    public function test_register_company_success()
    {
        // Arrange
        $data = [
            'company_name' => 'Test Company',
            'company_email' => 'company@test.com',
            'company_phone' => '123-456-7890',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'password' => 'password123',
            'phone' => '123-456-7890'
        ];

        $company = Mockery::mock(Company::class)->makePartial();
        $company->id = 'company-123';
        $company->name = 'Test Company';
        
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 'user-123';
        $user->email = 'john@test.com';
        $user->role = 'admin';
        
        $employee = Mockery::mock(Employee::class)->makePartial();
        $employee->id = 'employee-123';
        $employee->first_name = 'John';

        // Mock database transaction
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) use ($company, $user, $employee) {
                $this->companyRepository
                    ->shouldReceive('create')
                    ->once()
                    ->andReturn($company);

                $this->userRepository
                    ->shouldReceive('createUser')
                    ->once()
                    ->andReturn($user);

                $this->employeeRepository
                    ->shouldReceive('create')
                    ->once()
                    ->andReturn($employee);

                $this->userRepository
                    ->shouldReceive('updateUser')
                    ->once()
                    ->andReturn(true);

                $this->userRepository
                    ->shouldReceive('findWithRelations')
                    ->once()
                    ->andReturn($user);

                return $callback();
            });

        // Act
        $result = $this->authService->registerCompany($data);

        // Assert
        $this->assertArrayHasKey('company', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('Company registered successfully', $result['message']);
    }

    public function test_logout_success()
    {
        // Arrange
        $token = Mockery::mock();
        $token->shouldReceive('delete')->once();

        $user = Mockery::mock(User::class);
        $user->shouldReceive('currentAccessToken')
            ->once()
            ->andReturn($token);

        // Act
        $result = $this->authService->logout($user);

        // Assert
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('Logged out successfully', $result['message']);
    }

    public function test_refresh_token_success()
    {
        // Arrange
        $oldToken = Mockery::mock();
        $oldToken->shouldReceive('delete')->once();

        $newToken = Mockery::mock();
        $newToken->plainTextToken = 'new-token';

        $user = Mockery::mock(User::class);
        $user->shouldReceive('currentAccessToken')
            ->once()
            ->andReturn($oldToken);
        $user->shouldReceive('createToken')
            ->with('auth-token')
            ->once()
            ->andReturn($newToken);

        // Act
        $result = $this->authService->refreshToken($user);

        // Assert
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('token_type', $result);
        $this->assertEquals('Bearer', $result['token_type']);
        $this->assertEquals('new-token', $result['token']);
    }
}