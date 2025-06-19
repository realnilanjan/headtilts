<?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
<div id="adminSidebar" class="fixed left-0 top-0 bottom-0 w-64 bg-white shadow-lg transform -translate-x-full z-50 transition-transform duration-300 ease-in-out overflow-y-auto border-r">
    <div class="flex items-center justify-between p-4 border-b">
        <a href="/admin/dashboard" class="flex items-center">
            <img src="/assets/images/logo.png" alt="Headtilts Logo" class="h-14">
            <h2 class="text-lg font-bold text-gray-800">Admin Panel</h2>
        </a>

        <button id="toggleSidebar" class="md:hidden p-2 rounded-md hover:bg-gray-100 text-gray-600 hover:text-gray-900 focus:outline-none relative group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <nav class="p-4 space-y-6">
        <!-- Posts -->
        <div>
            <h3 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-newspaper text-blue-500 transition-colors duration-200 hover:text-blue-600"></i> Posts
            </h3>
            <ul class="mt-2 space-y-2 pl-4">
                <li>
                    <a href="/create" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-plus text-green-500 transition-colors duration-200 hover:text-green-600"></i> Add New Post
                    </a>
                </li>
                <li>
                    <a href="/post" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-th-list text-purple-500 transition-colors duration-200 hover:text-purple-600"></i> All Posts
                    </a>
                </li>
            </ul>
        </div>

        <!-- Categories -->
        <div>
            <h3 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-folder-open text-yellow-500 transition-colors duration-200 hover:text-yellow-600"></i> Categories
            </h3>
            <ul class="mt-2 space-y-2 pl-4">
                <li>
                    <a href="/admin/category/create" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-plus text-green-500 transition-colors duration-200 hover:text-green-600"></i> Add New Category
                    </a>
                </li>
                <li>
                    <a href="/admin/categories" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-list-ul text-indigo-500 transition-colors duration-200 hover:text-indigo-600"></i> All Categories
                    </a>
                </li>
            </ul>
        </div>

        <!-- Tags -->
        <div>
            <h3 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-tags text-pink-500 transition-colors duration-200 hover:text-pink-600"></i> Tags
            </h3>
            <ul class="mt-2 space-y-2 pl-4">
                <li>
                    <a href="/admin/tag/create" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-plus text-green-500 transition-colors duration-200 hover:text-green-600"></i> Add New Tag
                    </a>
                </li>
                <li>
                    <a href="/admin/tags" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-tag text-orange-500 transition-colors duration-200 hover:text-orange-600"></i> All Tags
                    </a>
                </li>
            </ul>
        </div>

        <!-- Setup -->
        <div>
            <h3 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-cog text-red-500 transition-colors duration-200 hover:text-red-600"></i> Setup
            </h3>
            <ul class="mt-2 space-y-2 pl-4">
                <li>
                    <a href="/admin/setup/users" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-users text-blue-400 transition-colors duration-200 hover:text-blue-600"></i> Users
                    </a>
                </li>
                <li>
                    <a href="/admin/setup/sitesettings" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-sliders-h text-teal-500 transition-colors duration-200 hover:text-teal-600"></i> Site Settings
                    </a>
                </li>
                <li>
                    <a href="/admin/setup/menus" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-bars text-gray-500 transition-colors duration-200 hover:text-gray-600"></i> Menus
                    </a>
                </li>
                <li>
                    <a href="/admin/setup/analytics" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-chart-line text-emerald-500 transition-colors duration-200 hover:text-emerald-600"></i> Analytics
                    </a>
                </li>
                <li>
                    <a href="/admin/setup/subscribers" class="block text-xs px-2 py-2 rounded hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-envelope text-red-400 transition-colors duration-200 hover:text-red-600"></i> Subscribers
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<?php endif; ?>

<div id="sidebarOverlay"    
    class="fixed inset-0 bg-white/5 backdrop-blur-sm z-40 hidden 
           before:absolute before:inset-0 before:bg-[url('/assets/images/asfalt-dark.png')]  
           before:bg-repeat before:w-full before:h-full before:content-['']">
</div>