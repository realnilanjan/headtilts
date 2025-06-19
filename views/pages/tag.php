<?php
ob_start();
use App\Helpers\SeoHelper;
$seoHelper = new SeoHelper();
$seoHelper->setTags(
    $tagName,
    substr(strip_tags($post['content']), 0, 150),
    $postViewModel->getFeaturedImageUrl($post['featured_image']),
);
?>

<h1 class="text-4xl font-bold mb-6 text-gray-800 border-b pb-2">Posts tagged by: <?= htmlspecialchars($tagName) ?></h1>

<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <article class="bg-white rounded shadow overflow-hidden">
                
                <?php if (!empty($post['featured_image'])): ?>
                    <img src="<?= $postViewModel->getFeaturedImageUrl($post['featured_image']) ?>"
                         alt="<?= htmlspecialchars($post['title']) ?>"
                         class="w-full h-40 object-cover">
                <?php endif; ?>
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">
                        <a href="/post/<?= htmlspecialchars($post['slug']) ?>" class="hover:underline">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h2>
                    <p class="text-gray-600 text-sm">
                        <?= date('F j, Y', strtotime($post['created_at'])) ?>
                    </p>
                    <p class="mt-2 text-gray-700 line-clamp-3">
                        <?= substr(strip_tags($post['content']), 0, 150) ?>...
                    </p>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts found for this tag.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>