<?php
// Fetch recent posts using the view model's method
$viewModel['recent_posts'] = $postViewModel->getRecentPosts(5);

// Only render widget if there are posts
if (!empty($viewModel['recent_posts'])):
?>
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold text-lg mb-4">Recent Posts</h2>
        <ul class="space-y-4">
            <?php foreach ($viewModel['recent_posts'] as $post): ?>
                <li class="flex gap-3">
                    <?php if (!empty($post['featured_image'])): ?>
                        <img src="<?= $postViewModel->getFeaturedImageUrl($post['featured_image']) ?>"
                             alt="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>"
                             class="w-16 h-16 object-cover rounded">
                    <?php else: ?>
                        <div class="w-16 h-16 bg-gray-200 rounded"></div>
                    <?php endif; ?>
                    <a href="/post/<?= htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8') ?>"
                       class="text-sm font-medium hover:text-red-600 line-clamp-2">
                        <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>