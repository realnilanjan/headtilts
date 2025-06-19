<?php

use App\Helpers\SeoHelper;

$seoHelper = new SeoHelper();
$seoHelper->setDefault();

$featuredPosts = $postViewModel->getFeaturedPosts(5);
$heroPost = $featuredPosts[0] ?? null;
$gridPosts = array_slice($featuredPosts, 1, 4);

// Fetch posts by category
$newsPosts = $postViewModel->getPostsByCategory_2('news');
$reviewPosts = $postViewModel->getPostsByCategory_2('reviews');
$editorialPosts = $postViewModel->getPostsByCategory_2('editorials');
$interviewPosts = $postViewModel->getPostsByCategory_2('interviews');

ob_start();
?>
<!-- Hero Section -->
<?php if ($heroPost): ?>
    <section class="mb-12">
        <div class="relative h-[50vh] md:h-[70vh] bg-gray-900 rounded-lg shadow-xl overflow-hidden">
            <img src="<?= $postViewModel->getFeaturedImageUrl($heroPost['featured_image']) ?>"
                alt="<?= htmlspecialchars($heroPost['title']) ?>"
                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 hover:scale-105">

            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

            <div class="absolute bottom-0 left-0 p-6 max-w-2xl text-white">
                <h2 class="text-3xl md:text-5xl font-serif font-bold leading-tight mb-3">
                    <?= htmlspecialchars($heroPost['title']) ?>
                </h2>
                <p class="text-gray-200 text-sm md:text-base line-clamp-2 mb-4">
                    <?= substr($heroPost['excerpt'], 0, 200) ?>...
                </p>
                <a href="/post/<?= htmlspecialchars($heroPost['slug']) ?>"
                    class="inline-block px-6 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded shadow-sm transition">
                    Read More →
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>


<!-- Grid Section: Next 4 Featured Posts -->
<?php if (!empty($gridPosts)): ?>
    <div class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-10">
        <?php foreach ($gridPosts as $post): ?>
            <a href="/post/<?= htmlspecialchars($post['slug']) ?>"
                class="group block relative rounded-lg overflow-hidden shadow hover:shadow-md transition-all duration-200">

                <div class="aspect-video bg-cover bg-center"
                    style="background-image: url('<?= $postViewModel->getFeaturedImageUrl($post['featured_image']) ?>');">
                </div>

                <div class="p-3">
                    <h3 class="text-sm sm:text-base font-semibold text-gray-800 group-hover:text-red-600 line-clamp-2">
                        <?= htmlspecialchars($post['title']) ?>
                    </h3>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<!-- Category Sections -->
<?php
$categorySections = [
    ['name' => 'News', 'slug' => 'news', 'posts' => $newsPosts, 'color' => 'red'],
    ['name' => 'Reviews', 'slug' => 'reviews', 'posts' => $reviewPosts, 'color' => 'green'],
    ['name' => 'Editorials', 'slug' => 'editorials', 'posts' => $editorialPosts, 'color' => 'blue'],
    ['name' => 'Interviews', 'slug' => 'interviews', 'posts' => $interviewPosts, 'color' => 'purple'],
];
?>

<?php foreach ($categorySections as $section): ?>
    <?php if (!empty($section['posts'])): ?>
        <section class="mb-12">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl sm:text-2xl font-serif font-bold text-gray-800 border-l-4 border-<?= $section['color'] ?>-600 pl-3">
                    <?= $section['name'] ?>
                </h2>
                <a href="/category/<?= $section['slug'] ?>" class="text-<?= $section['color'] ?>-600 hover:underline text-sm">See all</a>
            </div>

            <div class="grid gap-6 sm:gap-8 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($section['posts'] as $post): ?>
                    <?= renderCategoryLayout($section['slug'], $postViewModel, $post) ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
<?php endforeach; ?>


<?php

// Helper function to render post card
function renderPostCard($postViewModel, $post)
{
    ob_start(); ?>
    <a href="/post/<?= htmlspecialchars($post['post_slug']) ?>"
        class="group block rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 bg-white border border-gray-100">

        <div class="aspect-video bg-cover bg-center bg-gray-200"
            style="background-image: url('<?= $postViewModel->getFeaturedImageUrl($post['post_featured_image']) ?>');">
        </div>

        <div class="p-4">
            <h3 class="text-sm sm:text-base font-serif font-semibold text-gray-800 group-hover:text-red-600 line-clamp-2">
                <?= htmlspecialchars($post['post_title']) ?>
            </h3>
            <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($post['post_excerpt'] ?: substr(strip_tags($post['post_content']), 0, 100)) ?></p>
        </div>
    </a>
<?php return ob_get_clean();
}

function renderCategoryLayout($categorySlug, $postViewModel, $post)
{
    switch ($categorySlug) {
        case 'news':
            return renderNewsCard($postViewModel, $post);
        case 'reviews':
            return renderReviewCard($postViewModel, $post);
        case 'editorials':
            return renderEditorialCard($postViewModel, $post);
        case 'interviews':
            return renderInterviewCard($postViewModel, $post);
        default:
            return renderPostCard($postViewModel, $post); // fallback
    }
}

function renderNewsCard($postViewModel, $post)
{
    ob_start(); ?>
    <a href="/post/<?= htmlspecialchars($post['post_slug']) ?>"
        class="group block rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 bg-white border border-gray-100">

        <div class="aspect-video bg-cover bg-center bg-gray-200"
            style="background-image: url('<?= $postViewModel->getFeaturedImageUrl($post['post_featured_image']) ?>');">
        </div>

        <div class="p-4">
            <h3 class="text-sm sm:text-base font-serif font-semibold text-gray-800 group-hover:text-red-600 line-clamp-2">
                <?= htmlspecialchars($post['post_title']) ?>
            </h3>
            <p class="text-xs text-gray-500 mt-1"><?= strip_tags(substr($post['post_content'], 0, 120)) ?>...</p>
        </div>
    </a>
<?php return ob_get_clean();
}

function renderReviewCard($postViewModel, $post)
{
    ob_start(); ?>
    <a href="/post/<?= htmlspecialchars($post['post_slug']) ?>"
        class="group block rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 bg-white border border-gray-100 p-3 flex space-x-3 items-start">

        <div class="w-16 h-16 flex-shrink-0 bg-cover bg-center rounded"
            style="background-image: url('<?= $postViewModel->getFeaturedImageUrl($post['post_featured_image']) ?>');">
        </div>

        <div>
            <h3 class="text-sm font-serif font-semibold text-gray-800 group-hover:text-red-600 line-clamp-2">
                <?= htmlspecialchars($post['post_title']) ?>
            </h3>
            <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= strip_tags(substr($post['post_content'], 0, 100)) ?>...</p>
            <!-- Fake Rating -->
            <div class="mt-1 text-yellow-500 text-xs">★★★★★</div>
        </div>
    </a>
<?php return ob_get_clean();
}

function renderEditorialCard($postViewModel, $post)
{
    ob_start(); ?>
    <a href="/post/<?= htmlspecialchars($post['post_slug']) ?>"
        class="group block rounded-lg shadow-md hover:shadow-lg transition-all duration-300 bg-white border border-gray-100 p-4">

        <h3 class="text-sm font-serif font-semibold text-gray-800 group-hover:text-red-600">
            <?= htmlspecialchars($post['post_title']) ?>
        </h3>
        <p class="text-xs text-gray-600 mt-1 line-clamp-3"><?= strip_tags(substr($post['post_content'], 0, 150)) ?>...</p>
    </a>
<?php return ob_get_clean();
}

function renderInterviewCard($postViewModel, $post)
{
    ob_start(); ?>
    <a href="/post/<?= htmlspecialchars($post['post_slug']) ?>"
        class="group block rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 bg-white border border-gray-100 h-full flex flex-col">

        <!-- Tall image container -->
        <div class="h-48 sm:h-64 bg-cover bg-center bg-gray-200"
            style="background-image: url('<?= $postViewModel->getFeaturedImageUrl($post['post_featured_image']) ?>');">
        </div>

        <!-- Card content -->
        <div class="p-4 flex-grow flex flex-col justify-between">
            <h4 class="text-sm font-serif font-semibold text-gray-800 group-hover:text-red-600 line-clamp-2">
                <?= htmlspecialchars($post['post_title']) ?>
            </h4>
            <p class="text-xs text-gray-500 mt-2 line-clamp-3 flex-grow">
                <?= strip_tags(substr($post['post_content'], 0, 150)) ?>...
            </p>
        </div>
    </a>
<?php return ob_get_clean();
}

$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>