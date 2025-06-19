<?php
use App\Helpers\SeoHelper;

ob_start();
$seoHelper = new SeoHelper();
$seoHelper = new SeoHelper();
$seoHelper->setDefault(
    'Contact Us | Headtilts - Global Entertainment News',
    'Let Heads Tilt — Your source for global entertainment news, celebrity updates, movies, music, and viral trends.',
    [
        'headtilts contact',
        'headtilts contact us',
        'contact headtilts',
        'submit article headtilts',
        'independent rock artists',
        'metal scene news',
        'cult metal bands',
        'global rock and metal news',
        'music curation platform',
        'submit music for review'
    ]
);
?>

<!-- Contact Header -->
<section class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-3xl font-bold mb-6">Contact Us</h1>

    <p class="mb-6">
        Have questions or need support? Feel free to reach out using the form below or connect with us via email or phone.
    </p>
</section>

<!-- Contact Form -->
<section class="max-w-4xl mx-auto p-6 mt-6 bg-white rounded shadow">
    <h2 class="text-2xl font-semibold mb-4">Send Us a Message</h2>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/contact/submit" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Your Name</label>
            <input type="text" name="name" id="name" required
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input type="email" name="email" id="email" required
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Your Message</label>
            <textarea name="message" id="message" rows="5" required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>

        <button type="submit"
                class="w-full md:w-auto px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-md transition">
            Send Message
        </button>
    </form>
</section>

<!-- Contact Info Section -->
<section class="max-w-4xl mx-auto p-6 mt-8 bg-white rounded shadow">
    <h2 class="text-2xl font-semibold mb-4">Other Ways to Reach Us</h2>
    <ul class="space-y-2 text-gray-700">
        <li><strong>Email:</strong> <a href="mailto:support@example.com" class="text-red-500 hover:underline">support@example.com</a></li>
        <li><strong>Phone:</strong> +1 (555) 123-4567</li>
        <li><strong>Address:</strong> 123 Blog Street, Tech City, USA</li>
    </ul>
</section>

<?php
$content = ob_get_clean(); // Save buffered HTML into $content variable
include __DIR__ . '/../layouts/main.php'; // Load layout which echoes $content
?>