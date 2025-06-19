<?php

use App\Helpers\SeoHelper;

use App\Helpers\Helpers;
Helpers::guestOnly();

$seoHelper = new SeoHelper();
$seoHelper->setDefault(
    "Login to Headtilts | Headtilts | Global Entertainment News"
);
ob_start();
?>

<section class="max-w-md mx-auto mt-10 p-6 bg-white rounded-xl shadow">
    <h2 class="text-2xl font-bold mb-2 text-center">Create an Account</h2>
    <p class="text-center text-gray-600 mb-6">Sign up to start posting and commenting.</p>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['success']);
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 sr-only">Full Name</label>
            <input type="text" name="name" id="name" required
                placeholder="Full Name"
                class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 sr-only">Username</label>
            <input type="text" name="username" id="username" required
                placeholder="Username"
                class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 sr-only">Email Address</label>
            <input type="email" name="email" id="email" required
                placeholder="Email Address"
                class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 sr-only">Password</label>
            <input type="password" name="password" id="password" required
                placeholder="Password"
                class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <button type="submit"
            class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-full transition duration-200">
            Register
        </button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600">
        Already have an account?
        <a href="/login" class="text-red-500 hover:underline font-medium">Log in</a>
    </p>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>