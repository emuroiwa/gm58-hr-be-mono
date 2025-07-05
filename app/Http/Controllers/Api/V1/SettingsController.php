<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Get all settings
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $company = \App\Models\Company::find($companyId);

            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            $settings = [
                'company' => [
                    'name' => $company->name,
                    'timezone' => $company->timezone,
                    'currency' => $company->currency,
                    'settings' => $company->settings,
                ],
                'system' => $this->getSystemSettings(),
                'payroll' => $this->getPayrollSettings($company),
                'attendance' => $this->getAttendanceSettings($company),
                'leave' => $this->getLeaveSettings($company),
            ];

            return response()->json([
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update settings
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'company' => 'sometimes|array',
            'payroll' => 'sometimes|array',
            'attendance' => 'sometimes|array',
            'leave' => 'sometimes|array',
        ]);

        try {
            $companyId = $request->get('company_id');
            $company = \App\Models\Company::find($companyId);

            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            $currentSettings = $company->settings ?? [];
            $newSettings = array_merge($currentSettings, $request->except('company_id'));

            $company->update(['settings' => $newSettings]);

            return response()->json([
                'message' => 'Settings updated successfully',
                'data' => $newSettings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update settings',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get available currencies
     */
    public function getCurrencies(Request $request): JsonResponse
    {
        try {
            $currencies = \App\Models\Currency::where('is_active', true)
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $currencies
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve currencies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available timezones
     */
    public function getTimezones(Request $request): JsonResponse
    {
        try {
            $timezones = collect(timezone_identifiers_list())
                ->map(function ($timezone) {
                    return [
                        'value' => $timezone,
                        'label' => $timezone,
                        'offset' => now($timezone)->format('P'),
                    ];
                })
                ->groupBy(function ($item) {
                    return explode('/', $item['value'])[0];
                });

            return response()->json([
                'data' => $timezones
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve timezones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getSystemSettings(): array
    {
        return [
            'app_name' => config('app.name'),
            'app_version' => '1.0.0',
            'maintenance_mode' => app()->isDownForMaintenance(),
            'features' => [
                'api_access' => true,
                'file_uploads' => true,
                'notifications' => true,
                'real_time_updates' => true,
            ]
        ];
    }

    private function getPayrollSettings($company): array
    {
        $settings = $company->settings['payroll'] ?? [];
        
        return array_merge([
            'auto_process' => false,
            'tax_rate' => 0.10,
            'overtime_rate' => 1.5,
            'currency' => $company->currency->code ?? 'USD',
        ], $settings);
    }

    private function getAttendanceSettings($company): array
    {
        $settings = $company->settings['attendance'] ?? [];
        
        return array_merge([
            'working_hours_start' => '09:00',
            'working_hours_end' => '17:00',
            'break_duration' => 60, // minutes
            'late_threshold' => 15, // minutes
            'auto_checkout' => false,
        ], $settings);
    }

    private function getLeaveSettings($company): array
    {
        $settings = $company->settings['leave'] ?? [];
        
        return array_merge([
            'auto_approve' => false,
            'max_consecutive_days' => 30,
            'advance_notice_days' => 7,
            'carry_forward_enabled' => true,
        ], $settings);
    }
}
