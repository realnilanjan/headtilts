<?php 
use App\Helpers\SeoHelper;

if (!isset($errorCode) || !isset($title)) {
    $errorCode = 500;
    $title = 'Unknown Error';
}
$pageTitle = "$errorCode - $title";
$request = $_SERVER['REQUEST_URI'];
$type = 'Page';

if ($errorCode === 404) {
    $type = match (true) {
        str_contains($request, '/post') => 'Post',
        str_contains($request, '/category') => 'Category',
        str_contains($request, '/tag') => 'Tag',
        str_contains($request, '/author') => 'Author',
        str_contains($request, '/page') => 'Page',
        default => 'Page'
    };
}
$seoHelper = new SeoHelper();
$seoHelper->setDefault(
$pageTitle
);
ob_start();
?>

<div class="min-h-[80vh] flex flex-col items-center justify-center text-center px-4 py-12">
    <h1 class="text-6xl font-bold <?= $errorCode === 404 ? 'text-red-600' : 'text-blue-600' ?> mb-4"><?= $errorCode ?></h1>

    <h2 class="text-3xl font-semibold text-gray-800 mb-4">
        <?= htmlspecialchars($errorCode === 404 ? "$type Not Found" : $title) ?>
    </h2>

    <p class="text-lg text-gray-600 mb-2"><?= htmlspecialchars($message ?? '') ?></p>

    <?php if ($errorCode === 404): ?>
        <p class="text-sm text-gray-500 mt-1">Please check the URL or return to the homepage.</p>
    <?php endif; ?>

    <?php if ($errorCode !== 500): ?>
        <a href="/"
            class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 transition duration-200">
            Go to Homepage
        </a>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/main.php'; ?>