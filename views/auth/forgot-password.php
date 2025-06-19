<?php

use App\Helpers\SeoHelper;

$seoHelper = new SeoHelper();
$seoHelper->setDefault("Forgot Password | Headtilts");
ob_start();
?>

<section class="max-w-md mx-auto mt-10 p-6 bg-white rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-6 text-center">Forgot Password</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/forgot-password" class="space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 sr-only">Email Address</label>
            <input type="email" name="email" id="email" required
                   placeholder="Enter your email"
                   class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
        <button type="submit"
                class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-full transition duration-200">
            Send Reset Link
        </button>
    </form>

    <p class="mt-4 text-center text-sm">
        <a href="/login" class="text-red-500 hover:underline">Back to Login</a>
    </p>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>