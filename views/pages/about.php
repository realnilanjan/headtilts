<?php

use App\Helpers\SeoHelper;

ob_start();
$seoHelper = new SeoHelper();
$seoHelper->setDefault(
    'About Us'
);
?>

<!-- About Section -->
<section class="max-w-4xl mx-auto p-6 bg-white rounded shadow mb-12">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Welcome to Headtilts (HT)</h1>

    <p class="mb-4 leading-relaxed">
        Your dedicated platform for rock and metal music discovery. Whether you're a lifelong headbanger or just discovering the power of distortion and rhythm, we bring you the most compelling sounds shaping today’s scene.
    </p>

    <p class="mb-4 leading-relaxed">
        We’re not just another music blog or streaming service. At HT, we dig deep into the underground, spotlighting bands before they break big, and bringing you exclusive content you won’t find anywhere else.
    </p>

    <h2 class="text-2xl font-semibold mt-8 mb-4 text-gray-800">Your Gateway to Underground Rock and Metal</h2>
    <p class="mb-4 leading-relaxed">
        If you're searching for authentic rock and metal music discovery, look no further. Our curated playlists, artist features, and in-depth interviews are designed for true fans who crave more than what mainstream platforms offer.
    </p>

    <ul class="list-disc list-inside mb-6 space-y-2 text-gray-700 pl-4">
        <li>Exclusive early listens</li>
        <li>Artist spotlights from around the world</li>
        <li>New releases, live sets, and behind-the-scenes stories</li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-4 text-gray-800">Why Choose Headtilts?</h2>
    <p class="mb-4 leading-relaxed">
        Unlike generic music platforms, HT focuses on quality over quantity. We handpick every track, interview, and article to ensure it aligns with our mission: to elevate the voices pushing boundaries in rock and metal music discovery.
    </p>

    <p class="mb-4 leading-relaxed">
        We believe in the future of heavy soundscapes, bold experimentation, and fearless creativity. That’s why we work directly with independent artists, producers, and labels to give you first access to what’s next.
    </p>

    <h2 class="text-2xl font-semibold mt-8 mb-4 text-gray-800">Start Discovering Today</h2>
    <p class="leading-relaxed mb-4">
        Whether you're looking for new rock and metal bands, deep cuts from cult favorites, or insight into the evolving scene, Headtilts is your compass. Join us in celebrating the music that moves mountains — and mosh pits.
    </p>

    <p class="mt-4">
        Explore our latest discoveries or
        <a href="/submit" class="text-blue-600 hover:text-blue-800 underline transition">submit your own music</a> to be featured.
    </p>

    <div class="mt-6 flex flex-col items-center space-y-4 sm:flex-row sm:justify-between sm:items-center">
        <p class="text-sm text-gray-600 text-center sm:text-left">
            Follow us on social media and become part of a growing community built around passion, power, and purpose — one headbang at a time.
        </p>

        <div class="flex space-x-4 text-gray-700">
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="hover:text-blue-600 transition duration-200" aria-label="Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="hover:text-blue-400 transition duration-200" aria-label="Twitter">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="hover:text-pink-500 transition duration-200" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="hover:text-red-600 transition duration-200" aria-label="YouTube">
                <i class="fab fa-youtube"></i>
            </a>
            <a href="https://open.spotify.com" target="_blank" rel="noopener noreferrer" class="hover:text-green-500 transition duration-200" aria-label="Spotify">
                <i class="fab fa-spotify"></i>
            </a>
        </div>
    </div>
</section>

<!-- Meet the Team Section -->
<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl shadow-lg p-8">
        <h2 class="text-3xl font-bold text-center mb-10 text-gray-800">Meet the Team</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            <!-- Team Member Card -->
            <?php include __DIR__ . '/../partials/team_member.php'; ?>
            <?php include __DIR__ . '/../partials/team_member.php'; ?>

        </div>
    </div>
</section>

<?php
$content = ob_get_clean(); // Capture buffered HTML into $content variable
include __DIR__ . '/../layouts/main.php'; // Load layout which echoes $content
?>