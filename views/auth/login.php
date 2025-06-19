<?php

use App\Helpers\SeoHelper;
use App\Helpers\Helpers;
Helpers::guestOnly();
$seoHelper = new SeoHelper();
$seoHelper->setDefault("Login to Headtilts | Headtilts | Global Entertainment News");
ob_start();
?>

<section class="max-w-md mx-auto mt-10 p-6 bg-white rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login" class="space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 sr-only">Email</label>
            <input type="email" name="email" id="email" required
                placeholder="Email"
                class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 sr-only">Password</label>
            <input type="password" name="password" id="password" required
                placeholder="Password"
                class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="remember" class="rounded text-red-500">
                <span class="text-gray-600">Remember Me</span>
            </label>
            <a href="/forgot-password" class="text-red-500 hover:underline">Forgot Password?</a>
        </div>
        <button type="submit"
            class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-full transition duration-200">
            Login
        </button>
    </form>

    <p class="mt-4 text-center text-sm">
        Don't have an account?
        <a href="/register" class="text-red-500 hover:underline font-medium">Register</a>
    </p>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>