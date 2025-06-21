<?php

namespace App\Controllers;

use App\ViewModels\MenuViewModel;
use App\Helpers\MenuHelper;
use App\Helpers\Helpers;

class MenuController
    {
    private MenuViewModel $menuViewModel;
    private MenuHelper $menuHelper;

    public function __construct(MenuViewModel $menuViewModel, MenuHelper $menuHelper)
    {
        $this->menuViewModel = $menuViewModel;
        $this->menuHelper = $menuHelper;
    }

    public function index()
    {
        Helpers::authMiddleware(['admin']);
        $selectedMenuId = $_GET['menu_id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_menu_item'])) {
            $title = $_POST['item_title'] ?? '';
            $url = $_POST['item_url'] ?? '';
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $order = (int)($_POST['order'] ?? 0);

            if ($selectedMenuId && $title && $url) {
                $this->menuViewModel->getModel()->saveMenuItem([
                    'menu_id' => $selectedMenuId,
                    'title' => $title,
                    'url' => $url,
                    'parent_id' => $parentId,
                    'order' => $order
                ]);

                header("Location: ?menu_id=$selectedMenuId");
                exit;
            }
        }

        include __DIR__ . '/../../views/admin/settings/menus.php';
    }
}