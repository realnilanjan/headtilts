<div class="bg-white rounded shadow p-4">
    <h2 class="font-semibold text-lg mb-3">Categories</h2>
    <ul class="space-y-1 text-sm">
        <?php
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($allCategories as $category):
            $url = "/category/{$category['slug']}";
            $normalizedCurrent = rtrim($currentPath, '/');
            $normalizedUrl = rtrim($url, '/');
            $isActive = $normalizedCurrent === $normalizedUrl;

            $linkClass = "block px-3 py-2 rounded transition-all duration-200";
            if ($isActive) {
                $linkClass .= "bg-red-100 text-red-600";
            } else {
                $linkClass .= "text-gray-700 hover:bg-red-50 hover:text-red-600";
            }
        ?>
            <li>
                <a href="<?= htmlspecialchars($url) ?>" class="<?= $linkClass ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>