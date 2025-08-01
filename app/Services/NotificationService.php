<?php

namespace App\Services;

use App\Models\ReportsModel;
use App\Models\UserModel;

class NotificationService
{
    private $reportsModel;
    private $userModel;

    public function __construct()
    {
        $this->reportsModel = new ReportsModel();
        $this->userModel = new UserModel();
    }

    /**
     * Получить данные уведомлений для пользователя
     */
    public function getReportNotifications($userId, $role)
    {
        // Для superadmin показываем все отчеты в системе
        if ($role === 'superadmin') {
            return $this->getSuperadminNotifications();
        }
        
        // Проверяем, является ли пользователь parent'ом для кого-либо
        $isParent = $this->isUserParent($userId);
        
        if ($isParent) {
            return $this->getParentNotifications($userId, $role);
        } else {
            return $this->getRegularUserNotifications($userId);
        }
    }

    /**
     * Проверить, является ли пользователь parent'ом
     */
    private function isUserParent($userId)
    {
        $childUsers = $this->userModel->where("FIND_IN_SET('$userId', parent) >", 0)->findAll();
        return !empty($childUsers);
    }

    /**
     * Получить уведомления для обычного пользователя (не parent)
     */
    private function getRegularUserNotifications($userId)
    {
        $reports = $this->reportsModel->where('user_id', $userId)
                                     ->where('is_report', 1)
                                     ->findAll();

        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($reports as $report) {
            switch ($report['status']) {
                case 0: // В обработке
                    $pendingCount++;
                    break;
                case 1: // Подтвержден
                    $approvedCount++;
                    break;
                case 2: // Отклонен
                    $rejectedCount++;
                    break;
            }
        }

        return [
            'approved' => $approvedCount,
            'pending' => $pendingCount,
            'rejected' => $rejectedCount,
        ];
    }

    /**
     * Получить уведомления для superadmin (все отчеты в системе)
     */
    private function getSuperadminNotifications()
    {
        // Получаем все отчеты в системе
        $reports = $this->reportsModel->where('is_report', 1)->findAll();

        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($reports as $report) {
            switch ($report['status']) {
                case 0: // В обработке
                    $pendingCount++;
                    break;
                case 1: // Подтвержден
                    $approvedCount++;
                    break;
                case 2: // Отклонен
                    $rejectedCount++;
                    break;
            }
        }

        return [
            'approved' => $approvedCount,
            'pending' => $pendingCount,
            'rejected' => $rejectedCount,
        ];
    }

    /**
     * Получить уведомления для parent пользователя
     */
    private function getParentNotifications($userId, $role)
    {
        // Получаем всех дочерних пользователей
        $childUsers = $this->userModel->where("FIND_IN_SET('$userId', parent) >", 0)->findAll();
        $childUserIds = array_column($childUsers, 'user_id');
        
        // Добавляем ID самого пользователя
        $allUserIds = array_merge([$userId], $childUserIds);

        // Получаем отчеты для всех пользователей
        $reports = $this->reportsModel->whereIn('user_id', $allUserIds)
                                     ->where('is_report', 1)
                                     ->findAll();

        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($reports as $report) {
            switch ($report['status']) {
                case 0: // В обработке
                    $pendingCount++;
                    break;
                case 1: // Подтвержден
                    $approvedCount++;
                    break;
                case 2: // Отклонен
                    $rejectedCount++;
                    break;
            }
        }

        return [
            'approved' => $approvedCount,
            'pending' => $pendingCount,
            'rejected' => $rejectedCount,
        ];
    }

    /**
     * Получить детальную статистику отчетов (для отладки)
     */
    public function getDetailedReportStats($userId, $role)
    {
        // Для superadmin показываем все отчеты в системе
        if ($role === 'superadmin') {
            $reports = $this->reportsModel->where('is_report', 1)->findAll();
            $isParent = false; // superadmin не считается parent в этом контексте
        } else {
            $isParent = $this->isUserParent($userId);
            
            if ($isParent) {
                $childUsers = $this->userModel->where("FIND_IN_SET('$userId', parent) >", 0)->findAll();
                $childUserIds = array_column($childUsers, 'user_id');
                $allUserIds = array_merge([$userId], $childUserIds);
                
                $reports = $this->reportsModel->whereIn('user_id', $allUserIds)
                                             ->where('is_report', 1)
                                             ->findAll();
            } else {
                $reports = $this->reportsModel->where('user_id', $userId)
                                             ->where('is_report', 1)
                                             ->findAll();
            }
        }

        $stats = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'total' => count($reports),
            'is_parent' => $isParent
        ];

        foreach ($reports as $report) {
            switch ($report['status']) {
                case 0:
                    $stats['pending']++;
                    break;
                case 1:
                    $stats['approved']++;
                    break;
                case 2:
                    $stats['rejected']++;
                    break;
            }
        }

        return $stats;
    }
}