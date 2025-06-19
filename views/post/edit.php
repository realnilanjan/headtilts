<?php

use App\Helpers\SeoHelper;

$seoHelper = new SeoHelper();
$seoHelper->setDefault(
    'Editing - ' . $viewModel['title'],
);
ob_start();
?>

<script>
    tinymce.init({
        selector: '#edit_editor',
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

<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-5 py-5">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Edit Post</h2>

    <form method="post" action="/update" enctype="multipart/form-data"
        class="grid grid-cols-1 lg:grid-cols-7 gap-8 bg-white rounded-xl shadow-lg overflow-hidden">
        <input type="hidden" name="id" value="<?= htmlspecialchars($viewModel['id'] ?? '') ?>">
        <div class="lg:col-span-5 p-6 space-y-6">
            <div>
                <input type="text" id="title" name="title"
                    value="<?= htmlspecialchars($viewModel['title'] ?? '') ?>"
                    placeholder="Title (must be at least 60 characters)"
                    oninput="generateSlug()"
                    required
                    class="w-full border text-sm border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200">
            </div>
            <div>
                <input type="text" id="slug" name="slug"
                    value="<?= htmlspecialchars($viewModel['slug'] ?? '') ?>"
                    class="w-full border text-sm border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500 outline-none"
                    placeholder="Don't worrry, slug is auto-generated">
            </div>
            <div>
                <textarea id="edit_editor" name="content"><?= htmlspecialchars($viewModel['content'] ?? '') ?></textarea>
            </div>
        </div>

        <aside class="lg:col-span-2 p-6 space-y-6 bg-gray-50 border-l border-gray-200">

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <?php if (!empty($viewModel['featured_image'])): ?>
                    <img src="<?= $postViewModel->getFeaturedImageUrl($viewModel['featured_image']) ?>"
                        alt="Current Featured Image"
                        class="w-full h-auto mb-2 rounded object-cover max-h-48">
                    <input type="hidden" name="featured_image" value="<?= htmlspecialchars($viewModel['featured_image']) ?>">
                <?php endif; ?>
                <input type="file" name="featured_image_new" class="mt-2 w-full" accept="image/*">
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border space-y-4">
                <h3 class="text-sm font-semibold text-gray-700 border-b pb-2">Post Meta</h3>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" id="category_id"
                        class="w-full border text-sm border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <?php foreach ($postCategories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['id']) ?>"
                                <?= ($cat['id'] == ($viewModel['category_id'] ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                    <p class="text-xs text-gray-500 mb-1">Enter tags separated by commas.</p>
                    <?php
                    $tagNames = !empty($viewModel['tags']) ? array_column($viewModel['tags'], 'name') : [];
                    ?>
                    <input type="text" name="tags" id="tags"
                        value="<?= htmlspecialchars(implode(', ', $tagNames)) ?>"
                        class="w-full border text-sm border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        placeholder="e.g. india, metal, rock">
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                    <select name="user_id" id="user_id"
                        class="w-full border text-sm border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <?php foreach ($allAuthors as $author): ?>
                            <option value="<?= htmlspecialchars($author['id']) ?>"
                                <?= ($author['id'] == $viewModel['author_id']) ? 'selected' : '' ?>>
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
                            <input type="radio" name="status" value="published"
                                <?= ($viewModel['status'] ?? '') === 'published' ? 'checked' : '' ?>
                                class="sr-only peer">
                            <div class="px-4 py-2 text-sm rounded-full border transition-all duration-200 
                peer-checked:bg-green-100 peer-checked:border-green-500 
                peer-checked:text-green-700 hover:bg-green-50 text-gray-700 border-gray-300 bg-white">
                                Published
                            </div>
                        </label>

                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="status" value="draft"
                                <?= ($viewModel['status'] ?? '') === 'draft' ? 'checked' : '' ?>
                                class="sr-only peer">
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
                            <input type="radio" name="visibility" value="public"
                                <?= ($viewModel['visibility'] ?? 'public') === 'public' ? 'checked' : '' ?>
                                class="sr-only peer">
                            <div class="px-4 py-2 text-sm rounded-full border transition-all duration-200 
                peer-checked:bg-blue-100 peer-checked:border-blue-500 
                peer-checked:text-blue-700 hover:bg-blue-50 text-gray-700 border-gray-300 bg-white">
                                Public
                            </div>
                        </label>

                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="visibility" value="private"
                                <?= ($viewModel['visibility'] ?? '') === 'private' ? 'checked' : '' ?>
                                class="sr-only peer">
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
                        value="<?= !empty($viewModel['created_at']) ? date('Y-m-d\TH:i', strtotime($viewModel['created_at'])) : '' ?>"
                        class="w-full border text-sm border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <p class="text-xs text-gray-500 mt-1">Set the publication date and time.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-4">
                <a href="/post/<?= htmlspecialchars($viewModel['slug'] ?? '') ?>" target="_blank"
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-50 text-sm text-blue-600 hover:bg-blue-100 rounded-full transition-all duration-200 shadow-sm hover:shadow w-full">
                    <i class="fas fa-eye mr-2"></i> Preview
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-sm text-white hover:bg-green-700 rounded-full transition-all duration-200 shadow hover:shadow-md w-full">
                    <i class="fas fa-save mr-2"></i> Update
                </button>

                <a href="/post/<?= htmlspecialchars($viewModel['slug'] ?? '') ?>"
                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-sm text-gray-800 hover:bg-gray-300 rounded-full transition-all duration-200 w-full">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>

                <a href="#"
                    onclick="event.preventDefault(); openDeleteModal('<?= htmlspecialchars($viewModel['slug']) ?>')"
                    class="inline-flex items-center justify-center px-4 py-2 bg-red-600 text-sm text-white hover:bg-red-700 rounded-full transition-all duration-200 shadow hover:shadow-md w-full">
                    <i class="fas fa-trash mr-2"></i> Delete
                </a>
            </div>

            <div class="pt-4 text-xs text-gray-500 border-t border-gray-200 mt-4">
                <?php if (!empty($viewModel['updated_at'])): ?>
                    Last updated: <?= date('F j, Y g:i A', strtotime($viewModel['updated_at'])) ?>
                <?php endif; ?>
            </div>

        </aside>
    </form>
</div>

<div id="delete-modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 shadow-lg max-w-sm w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirm Deletion</h3>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this post? This action cannot be undone.</p>
        <div class="flex justify-end gap-2">
            <button onclick="closeDeleteModal()" type="button" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                Cancel
            </button>
            <a id="confirm-delete-link" href="#" class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700">
                Delete
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const featuredImageInput = document.querySelector('input[name="new_featured_image_new"]');
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