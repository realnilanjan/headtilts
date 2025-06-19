<?php
// views/partials/pages_menu.php

use App\Helpers\Helpers;

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$pages = [
    'Home' => '/',
    'About' => '/about',
    'Contact' => '/contact',
    'Privacy' => '/privacy',
    'Terms' => '/terms',
    //'Pricing' => '/pricing' //TODO:
];

$user = Helpers::auth();

if ($user) {
    $authPages = [
        'Logout' => '/logout'
    ];
} else {
    $authPages = [
        'Login' => '/login',
        'Register' => '/register'
    ];
}
?>

<ul class="flex flex-wrap items-center gap-6 text-sm">
    <!-- Main Pages -->
    <?php foreach ($pages as $title => $url):
        $normalizedCurrent = rtrim($currentPath, '/');
        $normalizedUrl = rtrim($url, '/');
        $isActive = $normalizedCurrent === $normalizedUrl;

        $linkClass = "relative inline-block transition-all duration-300 group";
        if ($isActive) {
            $linkClass .= " font-semibold text-red-600";
        } else {
            $linkClass .= " text-gray-700 hover:text-red-500";
        }
    ?>
        <li>
            <a href="<?= htmlspecialchars($url) ?>" class="<?= htmlspecialchars($linkClass) ?>">
                <?= htmlspecialchars($title) ?>
                <span class="absolute left-0 bottom-0 w-full h-0.5 bg-red-600 transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <?php if ($isActive): ?>
                    <span class="absolute left-0 bottom-0 w-full h-0.5 bg-red-600"></span>
                <?php endif; ?>
            </a>
        </li>
    <?php endforeach; ?>

    <li class="text-gray-400 select-none hidden md:inline"> | </li>

    <?php foreach ($authPages as $title => $url):
        $normalizedCurrent = rtrim($currentPath, '/');
        $normalizedUrl = rtrim($url, '/');
        $isActive = $normalizedCurrent === $normalizedUrl;

        $linkClass = "relative inline-block px-2 py-1 rounded transition-all duration-200";

        if ($isActive) {
            $linkClass .= "bg-red-100 text-red-600";
        } else {
            $linkClass .= "text-gray-700 hover:bg-red-50 hover:text-red-600";
        }

        $displayTitle = $title;
        if ($title === 'Logout' && isset($_SESSION['user'])) {
            $displayTitle = $_SESSION['user']['name'];
            $linkClass = "inline-block font-medium text-red-600 hover:text-red-700";
        }
    ?>
        <li>
            <a href="<?= htmlspecialchars($url) ?>" class="<?= htmlspecialchars($linkClass) ?>">
                <?= htmlspecialchars($displayTitle) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>