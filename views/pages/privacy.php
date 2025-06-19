<?php
use App\Helpers\SeoHelper;

ob_start();
$seoHelper = new SeoHelper();
$seoHelper->setDefault(
    'Privacy Policy | Headtilts - Global Entertainment News'
);
?>

<!-- Privacy Policy -->
<section class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-3xl font-bold mb-6">Privacy Policy</h1>

    <p class="mb-4">
        Your privacy is important to us. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our blog.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Information We Collect</h2>
    <p class="mb-4">
        We may collect personal information that you voluntarily provide to us when registering on the website, subscribing to a newsletter, or contacting us.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">How We Use Your Information</h2>
    <p class="mb-4">
        We use the information we collect to:
    </p>
    <ul class="list-disc pl-5 mb-4 space-y-1">
        <li>Provide, operate, and maintain our blog</li>
        <li>Improve, personalize, and expand our services</li>
        <li>Communicate with you, either directly or through one of our partners</li>
        <li>Send you emails</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">Log Files</h2>
    <p class="mb-4">
        Like many websites, we use log files. This includes internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date/time stamp, referring/exit pages, and possibly the number of clicks.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Cookies and Web Beacons</h2>
    <p class="mb-4">
        We may use cookies to store information about visitors' preferences and to record user-specific information on how they interact with our site.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Third-Party Services</h2>
    <p class="mb-4">
        We may employ third-party companies and individuals due to the following reasons: to facilitate our Blog; to provide services on our behalf; to perform blog-related services; or to assist us in analyzing how our blog is used.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Children’s Privacy</h2>
    <p class="mb-4">
        Our blog does not address anyone under the age of 13. We do not knowingly collect personal information from children under 13 years of age.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Changes to This Privacy Policy</h2>
    <p>
        We may update our Privacy Policy from time to time. Thus, we advise you to review this page periodically for any changes.
    </p>
</section>

<?php
$content = ob_get_clean(); // Save buffered HTML into $content variable
include __DIR__ . '/../layouts/main.php'; // Load layout which echoes $content
?>