<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $page['title'] ?></title>
</head>
<body>
    <h1><?= $page['title'] ?></h1>

    <div>
        <?= $page['content'] ?>
    </div>

    <!-- Optional: Show categories -->
    <?php if (!empty($allCategories)): ?>
        <aside>
            <h3>Categories</h3>
            <ul>
                <?php foreach ($allCategories as $category): ?>
                    <li><a href="/category/<?= urlencode($category['name']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </a></li>
                <?php endforeach; ?>
            </ul>
        </aside>
    <?php endif; ?>
</body>
</html>