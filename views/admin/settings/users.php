<?php

use App\Helpers\SeoHelper;

ob_start();
$seoHelper = new SeoHelper();
$seoHelper->setDefault(
    'Admin Dashboard'
);
?>

<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">User Settings</h2>
    </div>

<?php
$content = ob_get_clean(); // Capture buffered HTML into $content variable
include __DIR__ . '/../../layouts/main.php'; // Load layout which echoes $content
?>