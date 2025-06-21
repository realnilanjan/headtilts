<?php

namespace App\Helpers;

use App\Models\Database;
use App\Models\MenuModel;
use App\ViewModels\MenuViewModel;

class MenuHelper extends Database
{
    public function get_active_menu($menuSlug = 'header')
    {
        try {

            $menuModel = new MenuModel($this->pdo);
            $menuViewModel = new MenuViewModel($menuModel);

            // Optional: Get by slug or hardcoded ID for now
            $menu = $menuModel->getMenuBySlug($menuSlug); // Add this method to MenuModel
            if ($menu) {
                return $menuViewModel->getMenuTree($menu['id']);
            }
        } catch (\Exception $e) {
            error_log("Menu load failed: " . $e->getMessage());
            return [];
        }

        return [];
    }
}
