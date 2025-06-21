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
        $options = [];

        foreach ($menus as $menu) {
            $options[$menu['id']] = $menu['name'];
        }

        return $options;
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
}