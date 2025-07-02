<?php

namespace App\ViewModels;

use App\Models\MenuModel;

class MenuViewModel
{
    private $model;

    public function __construct(MenuModel $model)
    {
        $this->model = $model;
    }

    public function getMenusForSelect()
    {
        $menus = $this->model->getAllMenus();
        return $menus;
    }

    public function getMenuSlugByID($id)
    {
        $menuslug = $this->model->getMenuSlugByID($id);
        return $menuslug;
    }

    public function getMenuTree($menu_id)
    {
        $items = $this->model->getMenuItems($menu_id);
        $tree = [];

        foreach ($items as $item) {
            if (!$item['parent_id']) {
                $tree[$item['id']] = $item;
                $tree[$item['id']]['children'] = [];
            } else {
                $tree[$item['parent_id']]['children'][] = $item;
            }
        }

        return $tree;
    }

    public function getModel(): MenuModel
    {
        return $this->model;
    }

    public function get_active_menu($menuSlug)
    {
        try {
            $menu = $this->model->getMenuBySlug($menuSlug);
            if ($menu) {
                return $this->getMenuTree($menu['id']);
            }
        } catch (\Exception $e) {
            error_log("Menu load failed: " . $e->getMessage());
            return [];
        }

        return [];
    }
}
