<?php
use App\Helpers\SeoHelper;
ob_start();
$seoHelper = new SeoHelper();
$seoHelper->setDefault('Menu Settings');
$menuId = $_GET['menu_id'] ?? null;
$mainMenu = [];
if ($menuId) {
    $mainMenu = $menuViewModel->getMenuTree($menuId);
}
function renderMenuItem($item)
{
    $html = '<div class="menu-item border p-4 rounded bg-gray-50" data-id="' . $item['id'] . '">';
    $html .= '<strong>' . htmlspecialchars($item['title']) . '</strong><br>';
    $html .= '<small class="text-gray-600">' . htmlspecialchars($item['url']) . '</small>';
    $html .= '<div class="mt-2 space-x-2">';
    $html .= '<a href="#" class="text-blue-500 hover:underline edit-item" data-id="' . $item['id'] . '">Edit</a>';
    $html .= '<a href="?delete=' . $item['id'] . '" onclick="return confirm(\'Are you sure?\')" class="text-red-500 hover:underline">Delete</a>';
    $html .= '</div>';
    if (!empty($item['children'])) {
        $html .= '<ul class="children mt-3 ml-6 border-l-2 pl-4 border-gray-200 sortable">';
        foreach ($item['children'] as $child) {
            $html .= '<li class="menu-item p-3 border rounded shadow-sm mt-2 bg-white" data-id="' . $child['id'] . '">';
            $html .= '<strong>' . htmlspecialchars($child['title']) . '</strong><br>';
            $html .= '<small class="text-gray-600">' . htmlspecialchars($child['url']) . '</small>';
            $html .= '<div class="mt-2 space-x-2">';
            $html .= '<a href="#" class="text-blue-500 hover:underline edit-item" data-id="' . $child['id'] . '">Edit</a>';
            $html .= '<a href="?delete=' . $child['id'] . '" onclick="return confirm(\'Are you sure?\')" class="text-red-500 hover:underline">Delete</a>';
            $html .= '</div>';
            $html .= '</li>';
        }
        $html .= '</ul>';
    }
    $html .= '</div>';
    return $html;
}
?>
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 border-b pb-2">Menu Settings</h2>

    <!-- Tab Navigation -->
    <div class="mb-4 border-b">
        <ul class="flex space-x-4 text-sm font-medium">
            <li><a href="#" data-tab="create" class="tab-link active text-blue-600 pb-2 border-b-2 border-blue-600">Create</a></li>
            <li><a href="#" data-tab="manage" class="tab-link text-gray-600 hover:text-blue-600 pb-2">Manage</a></li>
            <li><a href="#" data-tab="edit" class="tab-link text-gray-600 hover:text-blue-600 pb-2">Edit</a></li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div id="tab-create" class="tab-content active">
        <!-- Create New Menu -->
        <form method="POST" action="" class="mb-6">
            <label for="menu_name" class="block mb-2">Menu Name:</label>
            <input type="text" name="menu_name" id="menu_name" required class="border p-2 w-full" />
            <button type="submit" name="create_menu"
                class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create Menu
            </button>
        </form>
    </div>

    <div id="tab-manage" class="tab-content">
        <!-- Select Existing Menu -->
        <?php
        $menus = $menuViewModel->getMenusForSelect();
        if (!empty($menus)): ?>
            <label for="menu_select" class="block mb-2">Select Menu:</label>
            <select name="menu_select" id="menu_select"
                onchange="location.href='?menu_id='+this.value"
                class="border p-2 w-full mb-4">
                <option value="">-- Select Menu --</option>
                <?php foreach ($menus as $menuitem): ?>
                    <option value="<?= htmlspecialchars($menuitem['id']) ?>" <?= $menuId == $menuitem['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($menuitem['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <!-- Display Menu Tree -->
        <?php if ($menuId && $mainMenu): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xl font-semibold mb-4">Menu Structure</h3>
                    <div id="menuTree" class="space-y-4">
                        <?php foreach ($mainMenu as $item): ?>
                            <?= renderMenuItem($item) ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Edit Form Section -->
                <div id="editFormSection">
                    <h3 class="text-xl font-semibold mb-4">Edit Menu Item</h3>
                    <form id="editMenuItemForm" class="hidden bg-white p-4 rounded shadow">
                        <input type="hidden" id="editItemId" name="item_id">
                        <div class="mb-4">
                            <label class="block mb-1">Title</label>
                            <input type="text" id="editItemTitle" name="item_title" class="w-full border p-2">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1">URL</label>
                            <input type="text" id="editItemUrl" name="item_url" class="w-full border p-2">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1">Parent</label>
                            <select id="editItemParent" name="parent_id" class="w-full border p-2">
                                <option value="">None</option>
                                <?php foreach ($mainMenu as $item): ?>
                                    <?php if (empty($item['parent_id'])): ?>
                                        <option value="<?= $item['id'] ?>">
                                            <?= htmlspecialchars($item['title']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1">Order</label>
                            <input type="number" id="editItemOrder" name="order" class="w-full border p-2">
                        </div>
                        <button type="submit"
                            class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        <?php elseif ($menuId): ?>
            <p>No menu items found.</p>
        <?php endif; ?>
    </div>

    <div id="tab-edit" class="tab-content">
        <!-- Add New Menu Item Form -->
        <?php if ($menuId): ?>
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
                            <?php if (empty($item['parent_id'])): ?>
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
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow hidden z-30">
        Item updated successfully.
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script> 

<script>
$(document).ready(function () {
    // Tabs
    $('.tab-link').on('click', function (e) {
        e.preventDefault();
        $('.tab-content').removeClass('active');
        $('.tab-link').removeClass('active text-blue-600 border-blue-600').addClass('text-gray-600');
        const tab = $(this).data('tab');
        $('#' + tab).addClass('active');
        $(this).removeClass('text-gray-600').addClass('active text-blue-600 border-blue-600');
    });

    // Edit Item Click
    $('.edit-item').on('click', function (e) {
        e.preventDefault();
        var itemId = $(this).data('id');
        $.ajax({
            url: '?ajax=1',
            method: 'POST',
            data: { action: 'get_menu_item', item_id: itemId },
            success: function(response) {
                $('#editItemId').val(response.id);
                $('#editItemTitle').val(response.title);
                $('#editItemUrl').val(response.url);
                $('#editItemParent').val(response.parent_id);
                $('#editItemOrder').val(response.order);
                $('#editMenuItemForm').removeClass('hidden');
            }
        });
    });

    // Submit Edit Form
    $('#editMenuItemForm').on('submit', function (e) {
        e.preventDefault();
        var formData = {
            action: 'edit_menu_item',
            item_id: $('#editItemId').val(),
            item_title: $('#editItemTitle').val(),
            item_url: $('#editItemUrl').val(),
            parent_id: $('#editItemParent').val(),
            order: $('#editItemOrder').val()
        };

        $.ajax({
            url: window.location.href + '&ajax=1',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function (response) {
                if (response.status === 'success') {
                    showToast('Item updated successfully.');
                } else {
                    alert('Error updating item.');
                }
            }
        });
    });
});

// SortableJS
document.addEventListener("DOMContentLoaded", function () {
    new Sortable(document.getElementById('menuTree'), {
        animation: 150,
        handle: '.menu-item',
        onEnd: function () {
            updateOrder();
        }
    });

    document.querySelectorAll('.children').forEach(function(el) {
        new Sortable(el, {
            animation: 150,
            onEnd: function () {
                updateOrder();
            }
        });
    });
});

function updateOrder() {
    const orderedItems = [];
    document.querySelectorAll('.menu-item').forEach((item, index) => {
        const id = item.getAttribute('data-id');
        const parentId = item.closest('.children') ? item.closest('.menu-item').getAttribute('data-id') : null;
        orderedItems.push({ id: id, parent_id: parentId, order: index });
    });

    fetch(window.location.href + '&ajax=1', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'update_menu_order', items: orderedItems })
    }).then(res => res.json())
      .then(data => console.log('Order saved:', data));
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>