<?php

use App\Helpers\SeoHelper;

ob_start();
$seoHelper = new SeoHelper();
$seoHelper->setDefault(
    'Admin Dashboard'
);
?>
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Admin Dashboard</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Tile: Add New Post -->
        <a href="/create" class="bg-white p-6 rounded shadow hover:shadow-md transition flex items-center gap-4">
            <div class="text-blue-500"><i class="fas fa-plus fa-2x"></i></div>
            <div>
                <h3 class="text-lg font-semibold">Add New Post</h3>
                <p class="text-sm text-gray-500">Create a new blog post</p>
            </div>
        </a>

        <!-- Tile: All Posts -->
        <a href="/post" class="bg-white p-6 rounded shadow hover:shadow-md transition flex items-center gap-4">
            <div class="text-green-500"><i class="fas fa-th-list fa-2x"></i></div>
            <div>
                <h3 class="text-lg font-semibold">All Posts</h3>
                <p class="text-sm text-gray-500">Manage existing posts</p>
            </div>
        </a>

        <!-- Tile: Categories -->
        <a href="/admin/categories" class="bg-white p-6 rounded shadow hover:shadow-md transition flex items-center gap-4">
            <div class="text-purple-500"><i class="fas fa-folder-open fa-2x"></i></div>
            <div>
                <h3 class="text-lg font-semibold">Categories</h3>
                <p class="text-sm text-gray-500">Organize by categories</p>
            </div>
        </a>

        <!-- Tile: Tags -->
        <a href="/admin/tags" class="bg-white p-6 rounded shadow hover:shadow-md transition flex items-center gap-4">
            <div class="text-yellow-500"><i class="fas fa-tags fa-2x"></i></div>
            <div>
                <h3 class="text-lg font-semibold">Tags</h3>
                <p class="text-sm text-gray-500">Manage tags</p>
            </div>
        </a>

        <!-- Tile: Users -->
        <a href="/admin/setup/users" class="bg-white p-6 rounded shadow hover:shadow-md transition flex items-center gap-4">
            <div class="text-red-500"><i class="fas fa-users fa-2x"></i></div>
            <div>
                <h3 class="text-lg font-semibold">Users</h3>
                <p class="text-sm text-gray-500">Manage users</p>
            </div>
        </a>

        <!-- Tile: Settings -->
        <a href="/admin/setup/sitesettings" class="bg-white p-6 rounded shadow hover:shadow-md transition flex items-center gap-4">
            <div class="text-indigo-500"><i class="fas fa-cog fa-2x"></i></div>
            <div>
                <h3 class="text-lg font-semibold">Site Settings</h3>
                <p class="text-sm text-gray-500">Configure site settings</p>
            </div>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean(); // Capture buffered HTML into $content variable
include __DIR__ . '/../layouts/main.php'; // Load layout which echoes $content
?>