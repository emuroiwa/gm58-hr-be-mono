<?php

namespace App\Repositories;

use App\Contracts\NotificationRepositoryInterface;
use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getUserNotifications(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Notification::where('user_id', $userId);
        
        if (isset($filters['read_status'])) {
            if ($filters['read_status'] === 'unread') {
                $query->whereNull('read_at');
            } else {
                $query->whereNotNull('read_at');
            }
        }
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        return $query->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createNotification(array $data): Notification
    {
        return Notification::create($data);
    }

    public function markAsRead(int $id, int $userId): bool
    {
        return Notification::where('id', $id)
                          ->where('user_id', $userId)
                          ->update(['read_at' => now()]);
    }

    public function markAllAsRead(int $userId): bool
    {
        return Notification::where('user_id', $userId)
                          ->whereNull('read_at')
                          ->update(['read_at' => now()]);
    }

    public function deleteNotification(int $id, int $userId): bool
    {
        return Notification::where('id', $id)
                          ->where('user_id', $userId)
                          ->delete();
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
                          ->whereNull('read_at')
                          ->count();
    }

    public function createBulkNotifications(array $userIds, array $data): bool
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = array_merge($data, [
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return Notification::insert($notifications);
    }

    public function getCompanyNotifications(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Notification::whereHas('user.companies', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        return $query->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }
}
