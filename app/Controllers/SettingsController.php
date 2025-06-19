<?php

namespace App\Controllers;
use App\Helpers\Helpers;

class SettingsController {
    public function manageUsers() {
        Helpers::authMiddleware(['admin']);
        require_once __DIR__ . '/../../views/admin/settings/users.php';
    }

    public function siteSettings() {
        Helpers::authMiddleware(['admin']);
        require_once __DIR__ . '/../../views/admin/settings/site-settings.php';
    }

    public function manageMenus() {
        Helpers::authMiddleware(['admin']);
        require_once __DIR__ . '/../../views/admin/settings/menus.php';
    }

    public function analytics() {
        Helpers::authMiddleware(['admin']);
        require_once __DIR__ . '/../../views/admin/settings/analytics.php';
    }

    public function manageSubscribers() {
        Helpers::authMiddleware(['admin']);
        require_once __DIR__ . '/../../views/admin/settings/subscribers.php';
    }
}