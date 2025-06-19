<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($seoHelper->get()['title']) ?></title>
    <?php echo $seoHelper->render(); ?>
    <link rel="icon" href="/assets/images/favicons/favicon.png" type="image/png">

    <!-- CSS / Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tiny.cloud/1/wzcjpotjx7fjwo994p6soary01p4wt9ztp5o7hqs2jrxjq9o/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=Lato&display=swap" rel="stylesheet">
    <style>
        <?= file_get_contents(__DIR__ . '/../../public/assets/css/global.css') ?>
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../partials/admin_sidebar.php'; ?>
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>

        <div id="adminSidebar"
            class="fixed left-0 top-0 bottom-0 w-64 bg-white shadow-lg transform -translate-x-full z-40 transition-transform duration-300 ease-in-out overflow-y-auto border-r">
        </div>

        <button id="toggleAdminSidebar"
            class="fixed left-0 top-20 z-50 px-4 py-3 rounded-r-md text-gray-600 bg-gray-100 hover:bg-gray-200 focus:outline-none group transition-all duration-300 shadow-sm origin-center whitespace-nowrap">

            <span id="toggleIcon" class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 2 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>

                <span id="toggleLabel" class="text-xs font-medium text-gray-800 select-none">
                    Admin Menu
                </span>
            </span>
        </button>
    <?php endif; ?>
    <header class="p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="flex items-center">
                <img src="/assets/images/logo.webp" alt="Headtilts Logo" class="h-14">
            </a>
            <nav class="text-lg">
                <ul id="desktop-menu" class="hidden md:flex flex-wrap gap-6 items-center">
                    <?php include __DIR__ . '/../partials/categories_menu.php'; ?>
                </ul>

                <button id="hamburger" class="md:hidden p-2 ml-auto focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </nav>
        </div>

        <div id="mobile-menu" class="fixed top-0 right-0 w-full sm:w-2/3 md:w-1/3 h-full bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">Menu</h3>
                <button id="close-menu" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="flex-grow p-4 overflow-y-auto">
                <ul class="flex flex-col gap-4">
                    <?php include __DIR__ . '/../partials/mobile_menu.php'; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6 pb-24">
        <?= $content ?>
    </main>

    <footer class="fixed bottom-0 left-0 w-full bg-white text-sm shadow p-4 z-40">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-600 mb-2 md:mb-0">Headtilts &copy; <?= date('Y') ?> - present | Developed with ❤️ in India</p>
                <nav>
                    <?php include __DIR__ . '/../partials/pages_menu.php'; ?>
                </nav>
            </div>
        </div>
    </footer>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById('toggleAdminSidebar');
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const innerCloseBtn = document.getElementById('toggleSidebar');

        if (!toggleBtn || !sidebar || !overlay) return;

        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            toggleBtn.classList.add('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            toggleBtn.classList.remove('hidden');
        });

        innerCloseBtn.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            toggleBtn.classList.remove('hidden');
        });

        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeBtn = document.getElementById('close-menu');

        if (hamburger && mobileMenu && closeBtn) {
            function openMenu() {
                document.body.classList.add('overflow-hidden');
                mobileMenu.classList.remove('translate-x-full');
            }

            function closeMenu() {
                document.body.classList.remove('overflow-hidden');
                mobileMenu.classList.add('translate-x-full');
            }

            hamburger.addEventListener('click', openMenu);
            closeBtn.addEventListener('click', closeMenu);

            document.addEventListener('click', function(event) {
                const isInside = mobileMenu.contains(event.target);
                const isHamburger = hamburger.contains(event.target);
                if (!isInside && !isHamburger && !mobileMenu.classList.contains('translate-x-full')) {
                    closeMenu();
                }
            });

            mobileMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', closeMenu);
            });
        }
    });
</script>

</html>