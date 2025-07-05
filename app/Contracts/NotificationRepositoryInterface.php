<?php

namespace App\Contracts;

use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface
{
    public function getUserNotifications(int $userId, array $filters = []): LengthAwarePaginator;
    public function createNotification(array $data): Notification;
    public function markAsRead(int $id, int $userId): bool;
    public function markAllAsRead(int $userId): bool;
    public function deleteNotification(int $id, int $userId): bool;
    public function getUnreadCount(int $userId): int;
    public function createBulkNotifications(array $userIds, array $data): bool;
    public function getCompanyNotifications(int $companyId, array $filters = []): LengthAwarePaginator;
}
