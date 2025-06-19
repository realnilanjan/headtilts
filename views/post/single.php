<?php

use App\Helpers\Helpers;
use App\Helpers\SeoHelper;

ob_start();

$seoHelper = new SeoHelper();
$seoHelper->setPost(
    $viewModel,
    $postViewModel->getFeaturedImageUrl($viewModel['featured_image']),
    $viewModel['tags']
);
?>

<!-- Breadcrumbs -->
<nav class="px-4 pb-2">
    <ol class="list-none p-0 inline-flex space-x-2 text-sm text-gray-600">
        <li><a href="/" class="hover:underline">Home</a></li>
        <li>›</li>
        <li>
            <a href="/category/<?= htmlspecialchars($viewModel['category_slug']) ?>" class="hover:underline">
                <?= htmlspecialchars($viewModel['category_name']) ?>
            </a>
        </li>
    </ol>
</nav>

<!-- Two Column Layout -->
<div class="px-4">
    <div class="flex flex-col md:flex-row gap-8">

        <!-- Left Column - Scrollable Article -->
        <div class="w-full md:w-2/3">
            <article class="bg-white rounded shadow">
                <!-- Featured Image -->
                <?php if (!empty($viewModel['featured_image'])): ?>
                    <div class="relative w-full h-64 md:h-96 overflow-hidden rounded-t">
                        <img
                            src="<?= $postViewModel->getFeaturedImageUrl($viewModel['featured_image']) ?>"
                            alt="<?= htmlspecialchars($viewModel['title']) ?>"
                            class="absolute inset-0 w-full h-full object-cover">
                    </div>
                <?php endif; ?>

                <!-- Post Title -->
                <div class="p-6">
                    <h1 class="text-4xl font-bold mb-2"><?= htmlspecialchars($viewModel['title']) ?></h1>
                    <!-- Author Info + Edit Link -->
                    <p class="text-sm text-gray-600 mb-4 flex items-center space-x-2">
                        <span class="whitespace-nowrap mr-1">Written By</span>
                        <span class="font-medium"><?= $viewModel['author_username'] ?? 'Unknown' ?></span>
                        <?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                            <a href="/edit/<?= htmlspecialchars($viewModel['slug']) ?>"
                                class="text-blue-600 hover:text-blue-800 group inline-flex items-center space-x-1">
                                <i class="fas fa-edit text-sm group-hover:text-blue-700"></i>
                                <span class="group-hover:underline">Edit</span>
                            </a>
                        <?php endif; ?>
                    </p>

                    <!-- Content -->
                    <div class="prose max-w-none mt-4">
                        <?= Helpers::embedYouTubeVideos($viewModel['content']) ?>
                    </div>
                    <!-- Tags Section -->
                    <?php if (!empty($viewModel['tags']) && is_array($viewModel['tags'])): ?>
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <h3 class="text-sm uppercase tracking-wide font-semibold text-gray-500 mb-3">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($viewModel['tags'] as $tag): ?>
                                    <a href="/tag/<?= htmlspecialchars($tag['slug']) ?>"
                                        class="inline-block px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 hover:text-blue-900 text-sm font-medium rounded-full transition duration-200 ease-in-out transform hover:scale-105">
                                        <?= htmlspecialchars($tag['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        </div>

        <!-- Right Column - Sticky Sidebar (Hidden on Mobile) 
        TODO: make a separate Widget Page -->
        <aside class="md:w-1/3 w-full hidden md:block">
            <div class="sticky top-6 space-y-6">
                <?php
                $widgets = ['recent-posts', 'categories', 'newsletter'];
                foreach ($widgets as $widget) {
                    include __DIR__ . "/../widgets/{$widget}.php";
                }
                ?>
            </div>
        </aside>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>