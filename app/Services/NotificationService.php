<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function sendNotification($userId, $title, $message, $type = 'info', $data = null)
    {
        // In a real implementation, you would have a NotificationRepository
        // For now, we'll keep the direct model usage as it's a simple case
        $notification = \App\Models\Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'is_read' => false,
        ]);

        // Send email notification if enabled
        $user = $this->userRepository->find($userId);
        if ($user && $user->email_notifications_enabled) {
            $this->sendEmailNotification($user, $title, $message);
        }

        return $notification;
    }

    public function markAsRead($notificationId)
    {
        $notification = \App\Models\Notification::find($notificationId);
        if ($notification) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
        }
        return $notification;
    }

    public function getUserNotifications($userId, $unreadOnly = false)
    {
        $query = \App\Models\Notification::where('user_id', $userId);
        
        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    private function sendEmailNotification($user, $title, $message)
    {
        // Implementation for email notification
        // Mail::to($user->email)->send(new NotificationMail($title, $message));
    }
}
