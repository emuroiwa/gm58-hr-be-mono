<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Get user notifications
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $filters = $request->only(['type', 'is_read', 'per_page']);

            $query = \App\Models\Notification::where('user_id', $userId);

            if (isset($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            if (isset($filters['is_read'])) {
                $query->where('is_read', $filters['is_read']);
            }

            $notifications = $query->orderBy('created_at', 'desc')
                ->paginate($filters['per_page'] ?? 20);

            return response()->json([
                'data' => $notifications->items(),
                'meta' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'unread_count' => \App\Models\Notification::where('user_id', $userId)
                        ->where('is_read', false)->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread notifications
     */
    public function getUnread(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $notifications = $this->notificationService->getUserNotifications($userId, true);

            return response()->json([
                'data' => $notifications,
                'unread_count' => $notifications->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve unread notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        try {
            $notification = $this->notificationService->markAsRead($notificationId);

            if (!$notification) {
                return response()->json(['message' => 'Notification not found'], 404);
            }

            return response()->json([
                'message' => 'Notification marked as read',
                'data' => $notification
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            
            \App\Models\Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json([
                'message' => 'All notifications marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark all notifications as read',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, string $notificationId): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $notification = \App\Models\Notification::where('id', $notificationId)
                ->where('user_id', $userId)
                ->first();

            if (!$notification) {
                return response()->json(['message' => 'Notification not found'], 404);
            }

            $notification->delete();

            return response()->json([
                'message' => 'Notification deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get notification settings
     */
    public function getSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Get user's notification preferences (this would be stored in user settings)
            $settings = [
                'email_notifications' => $user->email_notifications ?? true,
                'sms_notifications' => $user->sms_notifications ?? false,
                'push_notifications' => $user->push_notifications ?? true,
                'notification_types' => [
                    'payroll' => true,
                    'leave_approval' => true,
                    'attendance_reminders' => true,
                    'performance_reviews' => true,
                    'company_announcements' => true,
                ]
            ];

            return response()->json([
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve notification settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'notification_types' => 'sometimes|array',
        ]);

        try {
            $user = $request->user();
            $settings = $request->only(['email_notifications', 'sms_notifications', 'push_notifications']);
            
            // Update user notification preferences
            $user->update($settings);

            return response()->json([
                'message' => 'Notification settings updated successfully',
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update notification settings',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
