<?php

use App\Helpers\SeoHelper;

// Ensure $pdo is available globally
global $pdo;

ob_start();

// Initialize helpers
$seoHelper = new SeoHelper();
$seoHelper->setDefault('Admin Dashboard');

$menuHelper = new \App\Helpers\MenuHelper($pdo);
$menuModel = new \App\Models\MenuModel($pdo);
$menuViewModel = new \App\ViewModels\MenuViewModel($menuModel);

// Handle POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_menu'])) {
        $name = $_POST['menu_name'] ?? '';
        if ($name) {
            $menuModel->createMenu(['name' => $name]);
        }
    } elseif (isset($_POST['add_menu_item'])) {
        $title = $_POST['item_title'] ?? '';
        $url = $_POST['item_url'] ?? '';
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $order = (int)($_POST['order'] ?? 0);
        $selectedMenuId = $_GET['menu_id'] ?? null;

        if ($selectedMenuId && $title && $url) {
            $menuModel->saveMenuItem([
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
}

$selectedMenuId = $_GET['menu_id'] ?? null;
$mainMenu = $selectedMenuId ? $menuHelper->get_active_menu('header') : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($seoHelper->get()['title']) ?></title>
    <?= $seoHelper->render(); ?>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"  rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="p-6 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 border-b pb-2">Menu Settings</h2>

        <!-- Create New Menu -->
        <form method="POST" action="" class="mb-6">
            <label for="menu_name" class="block mb-2">Menu Name:</label>
            <input type="text" name="menu_name" id="menu_name" required class="border p-2 w-full" />
            <button type="submit" name="create_menu"
                    class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create Menu
            </button>
        </form>

        <hr class="my-6">

        <!-- Select Existing Menu -->
        <?php
        $menus = $menuViewModel->getMenusForSelect();
        if (!empty($menus)): ?>
            <label for="menu_select" class="block mb-2">Select Menu:</label>
            <select name="menu_select" id="menu_select"
                    onchange="location.href='?menu_id='+this.value"
                    class="border p-2 w-full mb-4">
                <option value="">-- Select Menu --</option>
                <?php foreach ($menus as $id => $name): ?>
                    <option value="<?= htmlspecialchars($id) ?>" <?= $selectedMenuId == $id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <!-- Display Menu Tree -->
        <?php if ($selectedMenuId && $mainMenu): ?>
            <ul class="list-disc ml-5 mt-4">
                <?php foreach ($mainMenu as $item): ?>
                    <li><?= htmlspecialchars($item['title']) ?>
                        <?php if (!empty($item['children'])): ?>
                            <ul class="list-circle ml-5">
                                <?php foreach ($item['children'] as $child): ?>
                                    <li><?= htmlspecialchars($child['title']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($selectedMenuId): ?>
            <p>No menu items found.</p>
        <?php endif; ?>

        <!-- Add New Menu Item Form -->
        <?php if ($selectedMenuId): ?>
            <h3 class="text-lg font-semibold mt-6 mb-4">Add New Menu Item</h3>
            <form method="POST" action="" class="mb-6">
                <div class="mb-4">
                    <label for="item_title" class="block mb-1">Item Title:</label>
                    <input type="text" name="item_title" id="item_title" required class="border p-2 w-full" />
                </div>
                <div class="mb-4">
                    <label for="type" class="block mb-1">Link Type:</label>
                    <select name="type" id="type" class="border p-2 w-full" onchange="handleTypeChange(this.value)">
                        <option value="custom">Category</option>
                        <option value="post">Post</option>
                        <option value="page">Page</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="item_url" class="block mb-1">URL:</label>
                    <input type="text" name="item_url" id="item_url" required class="border p-2 w-full" />
                </div>
                <div class="mb-4">
                    <label for="parent_id" class="block mb-1">Parent Item (Optional):</label>
                    <select name="parent_id" id="parent_id" class="border p-2 w-full">
                        <option value="">None</option>
                        <?php foreach ($mainMenu as $item): ?>
                            <?php if (empty($item['parent_id'])): // Only top-level items ?>
                                <option value="<?= htmlspecialchars($item['id']) ?>">
                                    <?= htmlspecialchars($item['title']) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="order" class="block mb-1">Order:</label>
                    <input type="number" name="order" id="order" value="0" min="0" class="border p-2 w-full" />
                </div>
                <button type="submit" name="add_menu_item"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Add Menu Item
                </button>
            </form>
        <?php endif; ?>

        <a href="#" class="text-blue-500 underline">Add Menu Item</a>
    </div>
</body>
</html>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>