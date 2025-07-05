<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// Authentication Routes (Web Interface)
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password.submit');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('reset-password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password.submit');
    Route::get('verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verify-email');
});

// Protected Web Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Admin Panel Routes (for web interface if needed)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    });
});

// API Documentation Routes
Route::prefix('docs')->name('docs.')->group(function () {
    Route::get('/', [DocsController::class, 'index'])->name('index');
    Route::get('/api', [DocsController::class, 'api'])->name('api');
    Route::get('/getting-started', [DocsController::class, 'gettingStarted'])->name('getting-started');
    Route::get('/authentication', [DocsController::class, 'authentication'])->name('authentication');
    Route::get('/employees', [DocsController::class, 'employees'])->name('employees');
    Route::get('/payroll', [DocsController::class, 'payroll'])->name('payroll');
    Route::get('/attendance', [DocsController::class, 'attendance'])->name('attendance');
});

// Webhook Routes (for external integrations)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('stripe', [WebhookController::class, 'stripe'])->name('stripe');
    Route::post('slack', [WebhookController::class, 'slack'])->name('slack');
    Route::post('zapier', [WebhookController::class, 'zapier'])->name('zapier');
});

// File Download Routes (with authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/download/payslip/{payroll}', [DownloadController::class, 'payslip'])->name('download.payslip');
    Route::get('/download/report/{report}', [DownloadController::class, 'report'])->name('download.report');
    Route::get('/download/document/{document}', [DownloadController::class, 'document'])->name('download.document');
});

// Health Check Route
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0')
    ]);
})->name('health');

// Terms and Privacy Pages
Route::get('/terms', [LegalController::class, 'terms'])->name('terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/security', [LegalController::class, 'security'])->name('security');
