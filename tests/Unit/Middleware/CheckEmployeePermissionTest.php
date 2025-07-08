<?php

namespace Tests\Unit\Middleware;

use Tests\TestCase;
use App\Http\Middleware\CheckEmployeePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;

class CheckEmployeePermissionTest extends TestCase
{
    private CheckEmployeePermission $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new CheckEmployeePermission();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_unauthenticated_user_returns_401()
    {
        // Arrange
        $request = Request::create('/test');
        $next = function () {
            return new Response('Success');
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertStringContainsString('Unauthenticated', $response->getContent());
    }

    public function test_user_without_role_returns_403()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('role')->andReturn(null);
        $user->role = null;

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('User role not assigned', $response->getContent());
    }

    public function test_super_admin_has_all_permissions()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'super_admin';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'any.permission');

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    public function test_admin_has_most_permissions()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'admin';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'employees.create');

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    public function test_admin_restricted_from_certain_actions()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'admin';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'company.delete');

        // Assert
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('Insufficient permissions', $response->getContent());
    }

    public function test_hr_role_has_specific_permissions()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'hr';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act - Test allowed permission
        $response = $this->middleware->handle($request, $next, 'employees.view');

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_hr_role_restricted_from_unauthorized_permissions()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'hr';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act - Test disallowed permission
        $response = $this->middleware->handle($request, $next, 'company.delete');

        // Assert
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('Insufficient permissions', $response->getContent());
    }

    public function test_manager_role_has_specific_permissions()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'manager';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act - Test allowed permission
        $response = $this->middleware->handle($request, $next, 'leaves.approve');

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_manager_role_restricted_from_unauthorized_permissions()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'manager';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act - Test disallowed permission
        $response = $this->middleware->handle($request, $next, 'payroll.process');

        // Assert
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('Insufficient permissions', $response->getContent());
    }

    public function test_employee_role_has_limited_permissions()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'employee';
        $user->employee_id = 'employee-123';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Mock route to avoid checkOwnDataAccess issues
        $route = Mockery::mock();
        $route->shouldReceive('parameter')->with('employee')->andReturn(null);
        $route->shouldReceive('parameter')->with('id')->andReturn(null);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act - Test allowed permission
        $response = $this->middleware->handle($request, $next, 'profile.view');

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_employee_role_restricted_from_unauthorized_permissions()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'employee';
        $user->employee_id = 'employee-123';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Mock route to avoid checkOwnDataAccess issues
        $route = Mockery::mock();
        $route->shouldReceive('parameter')->with('employee')->andReturn(null);
        $route->shouldReceive('parameter')->with('id')->andReturn(null);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act - Test disallowed permission
        $response = $this->middleware->handle($request, $next, 'employees.create');

        // Assert
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('Insufficient permissions', $response->getContent());
    }

    public function test_invalid_role_returns_403()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'invalid_role';

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('Invalid user role', $response->getContent());
    }

    public function test_employee_own_data_access_check()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->role = 'employee';
        $user->employee_id = 'employee-123';

        $request = Request::create('/test/employee-456');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Mock route with different employee ID
        $route = Mockery::mock();
        $route->shouldReceive('parameter')->with('employee')->andReturn('employee-456');
        $route->shouldReceive('parameter')->with('id')->andReturn(null);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $next = function () {
            return new Response('Success');
        };

        // Act & Assert - Should abort with 403
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        
        $this->middleware->handle($request, $next, 'profile.view');
    }
}