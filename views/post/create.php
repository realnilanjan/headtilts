<?php

use App\Helpers\SeoHelper;

$seoHelper = new SeoHelper();
$seoHelper->setDefault(
    'Create New Post'
);
ob_start();
?>

<script>
    tinymce.init({
        selector: '#create_editor',
        plugins: 'image link code table lists media emoticons searchreplace visualchars fullscreen',
        toolbar: `undo redo | blocks bold italic underline | alignleft aligncenter alignright |
        bullist numlist outdent indent | blockquote link image table | code`,
        valid_elements: '*[*]',
        extended_valid_elements: '*[*]',
        verify_html: false,
        cleanup: false,
        images_upload_url: '/upload-image',
        automatic_uploads: true,
        document_base_url: '/',
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        file_picker_types: 'image',
        file_picker_callback: function(cb, value, meta) {
            const input = document.createElement('input');
            const modal = document.createElement('div');
            const iframe = document.createElement('iframe');

            // Modal styling and structure
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
            modal.style.zIndex = '9999';

            const inner = document.createElement('div');
            inner.style.background = '#fff';
            inner.style.margin = '5% auto';
            inner.style.width = '80%';
            inner.style.maxWidth = '800px';
            inner.style.height = '600px';
            inner.style.overflow = 'auto';
            inner.style.borderRadius = '4px';
            inner.style.position = 'relative';

            const closeBtn = document.createElement('button');
            closeBtn.innerText = 'Close';
            closeBtn.style.position = 'absolute';
            closeBtn.style.top = '10px';
            closeBtn.style.right = '10px';
            closeBtn.onclick = () => document.body.removeChild(modal);

            // Load image browser
            iframe.src = '/image-browser'; // Your custom image browser page URL
            iframe.style.width = '100%';
            iframe.style.height = '500px';
            iframe.style.border = 'none';

            // Handle selected image from iframe
            window.addEventListener('message', function(e) {
                if (e.origin !== window.location.origin) return;
                if (e.data && e.data.type === 'tinymce-image-selected') {
                    cb(e.data.url, {
                        title: e.data.title
                    });
                    document.body.removeChild(modal);
                }
            });

            inner.appendChild(closeBtn);
            inner.appendChild(iframe);
            modal.appendChild(inner);
            document.body.appendChild(modal);
        },
        height: 1000
    });
</script>

<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Create New Post</h2>

    <form method="post" action="/store" enctype="multipart/form-data" class="space-y-8 bg-white p-6 rounded-lg shadow-md">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="md:col-span-2 space-y-6">

                <div>
                    <input type="text" id="title" name="title"
                        class="w-full border border-gray-300 rounded-md p-3 focus:ring-red-500 focus:border-red-500 outline-none transition duration-200"
                        placeholder="Title (must be at least 60 characters)"
                        required autofocus
                        oninput="generateSlug()">
                </div>

                <div>
                    <input type="text" id="slug" name="slug"
                        class="w-full border border-gray-300 rounded-md p-3 focus:ring-red-500 focus:border-red-500 outline-none transition duration-200">
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <textarea id="create_editor" name="content"></textarea>
                </div>
            </div>


            <aside class="lg:col-span-1 p-6 space-y-6 bg-gray-50 border-l border-gray-200">

                <div class="bg-white p-4 rounded-lg shadow-sm border">
                    <label for="featured_image_new" class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                    <input type="file" name="featured_image_new" class="mt-2 w-full" accept="image/*">
                </div>


                <div class="bg-white p-4 rounded-lg shadow-sm border space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700 border-b pb-2">Post Meta</h3>


                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" id="category_id"
                            class="w-full border text-sm border-gray-300 rounded-lg p-2 focus:ring-red-500 focus:border-red-500 outline-none">
                            <?php foreach ($postCategories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                        <p class="text-xs text-gray-500 mb-1">Enter tags separated by commas.</p>
                        <input type="text" name="tags" id="tags"
                            value="<?= htmlspecialchars($_POST['tags'] ?? '') ?>"
                            class="w-full border text-sm border-gray-300 rounded-lg p-2 focus:ring-red-500 focus:border-red-500 outline-none"
                            placeholder="e.g. india, metal, rock">
                    </div>


                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                        <select name="user_id" id="user_id"
                            class="w-full border text-sm border-gray-300 rounded-lg p-2 focus:ring-red-500 focus:border-red-500 outline-none">
                            <?php
                            $currentUserId = $_SESSION['user']['id'] ?? null;
                            ?>
                            <?php foreach ($allAuthors as $author): ?>
                                <option value="<?= htmlspecialchars($author['id']) ?>"
                                    <?= ($author['id'] == $currentUserId) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($author['name'] ?? $author['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <div class="bg-white p-4 rounded-lg shadow-sm border space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700 border-b pb-2">Publishing</h3>


                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="flex items-center space-x-2">

                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="status" value="published" class="sr-only peer">
                                <div class="px-4 py-2 text-sm rounded-full border transition-all duration-200 
                                    peer-checked:bg-green-100 peer-checked:border-green-500 
                                    peer-checked:text-green-700 hover:bg-green-50 text-gray-700 border-gray-300 bg-white">
                                    Published
                                </div>
                            </label>

                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="status" value="draft" class="sr-only peer" checked>
                                <div class="px-4 py-2 text-sm rounded-full border transition-all duration-200 
                                    peer-checked:bg-yellow-100 peer-checked:border-yellow-500 
                                    peer-checked:text-yellow-700 hover:bg-yellow-50 text-gray-700 border-gray-300 bg-white">
                                    Draft
                                </div>
                            </label>
                        </div>
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                        <div class="flex items-center space-x-2">

                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="visibility" value="public" class="sr-only peer" checked>
                                <div class="px-4 py-2 text-sm rounded-full border transition-all duration-200 
                                    peer-checked:bg-blue-100 peer-checked:border-blue-500 
                                    peer-checked:text-blue-700 hover:bg-blue-50 text-gray-700 border-gray-300 bg-white">
                                    Public
                                </div>
                            </label>

                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="visibility" value="private" class="sr-only peer">
                                <div class="px-4 py-2 text-sm rounded-full border transition-all duration-200 
                                    peer-checked:bg-red-100 peer-checked:border-red-500 
                                    peer-checked:text-red-700 hover:bg-red-50 text-gray-700 border-gray-300 bg-white">
                                    Private
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="created_at" class="block text-sm font-medium text-gray-700 mb-1">Published At</label>
                        <input type="datetime-local" name="created_at" id="created_at"
                            class="w-full border text-sm border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <p class="text-xs text-gray-500 mt-1">Set the publication date and time.</p>
                    </div>
                </div>


                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-4">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-sm text-white hover:bg-green-700 rounded-full transition-all duration-200 shadow hover:shadow-md w-full">
                        <i class="fas fa-plus mr-2"></i> Add Post
                    </button>
                    <a href="/"
                        class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-sm text-gray-800 hover:bg-gray-300 rounded-full transition-all duration-200 w-full">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                </div>
            </aside>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const featuredImageInput = document.querySelector('input[name="featured_image_new"]');
        if (featuredImageInput) {
            const featuredImageContainer = featuredImageInput.closest('div');
            const existingImage = featuredImageContainer.querySelector('img');

            featuredImageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        if (existingImage) {
                            existingImage.src = e.target.result;
                        } else {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = "Preview";
                            img.className = "w-full h-auto mb-2 rounded shadow max-h-48 object-cover";
                            featuredImageInput.parentNode.insertBefore(img, featuredImageInput);
                        }
                    };

                    reader.readAsDataURL(file);
                }
            });
        }
    });

    function generateSlug() {
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');

        let title = titleInput.value;
        let slug = title.toLowerCase()
            .trim()
            .replace(/[\s\W]+/g, '-')
            .replace(/^-+|-+$/g, '');

        slugInput.value = slug;
    }

    let currentSlug = '';

    function openDeleteModal(slug) {
        currentSlug = slug;
        document.getElementById('confirm-delete-link').href = '/delete/' + slug;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>