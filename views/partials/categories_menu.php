<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<ul class="hidden md:flex flex-wrap gap-6 items-center" id="desktop-menu">
    <?php foreach ($allCategories as $category):
        $url = "/category/{$category['slug']}";
        $normalizedCurrent = rtrim($currentPath, '/');
        $normalizedUrl = rtrim($url, '/');
        $isActive = $normalizedCurrent === $normalizedUrl;

        $linkClass = "relative inline-block px-1 transition-all duration-300 group";
        if ($isActive) {
            $linkClass .= " font-semibold text-red-600";
        } else {
            $linkClass .= " text-gray-700 hover:text-red-500";
        }
    ?>
        <li>
            <a href="<?= htmlspecialchars($url) ?>" class="<?= htmlspecialchars($linkClass) ?>">
                <?= htmlspecialchars($category['name']) ?>
                <!-- Hover underline -->
                <span class="absolute left-0 bottom-0 w-full h-0.5 bg-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 rounded-full"></span>
                <?php if ($isActive): ?>
                    <span class="absolute left-0 bottom-0 w-full h-0.5 bg-red-600"></span>
                <?php endif; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>