<?php
use App\Helpers\SeoHelper;

ob_start();

$seoHelper = new SeoHelper();
$seoHelper->setDefault(
    'Pricing | Headtilts - Global Entertainment News',
    'Let Heads Tilt — Your source for global entertainment news, celebrity updates, movies, music, and viral trends.',
    [
        'rock music discovery',
        'metal music discovery',
        'underground metal bands',
        'new rock releases',
        'independent rock artists',
        'metal scene news',
        'cult metal bands',
        'global rock and metal news',
        'music curation platform',
        'submit music for review'
    ]
);
?>

<!-- Pricing Section -->
<section class="py-12 bg-gray-50 sm:py-16 lg:py-20">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                Choose Your Plan
            </h2>
            <p class="mt-4 text-lg leading-7 text-gray-600">
                Unlock exclusive content and features based on your needs.
            </p>
        </div>

        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 gap-6 mt-12 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">

            <!-- Basic Plan -->
            <div class="flex flex-col p-6 overflow-hidden bg-white border rounded shadow-sm">
                <h3 class="text-xl font-semibold">Basic</h3>
                <p class="mt-2 text-gray-600">Perfect for casual readers.</p>
                <p class="mt-4 text-4xl font-extrabold">$0</p>
                <ul class="flex-1 mt-6 space-y-4">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-2 text-gray-700">Read all articles</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-2 text-gray-700">Limited downloads</span>
                    </li>
                </ul>
                <a href="#" class="inline-flex items-center justify-center px-4 py-2 mt-8 text-base font-medium text-white bg-gray-400 border border-transparent rounded hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 cursor-not-allowed">
                    Current Plan
                </a>
            </div>

            <!-- Pro Plan -->
            <div class="flex flex-col p-6 overflow-hidden bg-white border rounded shadow-sm">
                <h3 class="text-xl font-semibold">Pro</h3>
                <p class="mt-2 text-gray-600">For power users and enthusiasts.</p>
                <p class="mt-4 text-4xl font-extrabold">$9<span class="text-lg text-gray-500">/mo</span></p>
                <ul class="flex-1 mt-6 space-y-4">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-2 text-gray-700">Unlimited article reads</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-2 text-gray-700">Download articles</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-2 text-gray-700">Ad-free experience</span>
                    </li>
                </ul>
                <form action="/subscribe/pro" method="POST">
                    <input type="hidden" name="plan_id" value="pro">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 mt-8 text-base font-medium text-white bg-indigo-600 border border-transparent rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Subscribe Now
                    </button>
                </form>
            </div>

            <!-- Enterprise Plan -->
            <div class="flex flex-col p-6 overflow-hidden bg-white border rounded shadow-sm">
                <h3 class="text-xl font-semibold">Enterprise</h3>
                <p class="mt-2 text-gray-600">For businesses and teams.</p>
                <p class="mt-4 text-4xl font-extrabold">$49<span class="text-lg text-gray-500">/mo</span></p>
                <ul class="flex-1 mt-6 space-y-4">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-2 text-gray-700">All Pro features</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-2 text-gray-700">Team access</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-2 text-gray-700">Dedicated support</span>
                    </li>
                </ul>
                <form action="/subscribe/enterprise" method="POST">
                    <input type="hidden" name="plan_id" value="enterprise">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 mt-8 text-base font-medium text-white bg-purple-600 border border-transparent rounded hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Subscribe Now
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>