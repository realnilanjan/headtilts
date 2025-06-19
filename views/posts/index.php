<?php

use App\Helpers\SeoHelper;

ob_start();
$seoHelper = new SeoHelper();
$seoHelper->setDefault('All Posts');
?>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-3 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-base font-semibold text-gray-800">All Posts</h2>
        <a href="/create"
           class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-full shadow-sm transition">
            <i class="fas fa-plus mr-1"></i> New Post
        </a>
    </div>

    <!-- Responsive Table Wrapper -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50 uppercase tracking-wider text-left">
                <tr>
                    <th class="px-3 py-2 font-medium text-gray-700">Image</th>
                    <th class="px-3 py-2 font-medium text-gray-700">Title</th>
                    <th class="px-3 py-2 font-medium text-gray-700 hidden sm:table-cell">Excerpt</th>
                    <th class="px-3 py-2 font-medium text-gray-700">Date</th>
                    <th class="px-3 py-2 font-medium text-gray-700">Category</th>
                    <th class="px-3 py-2 font-medium text-gray-700">Status</th>
                    <th class="px-3 py-2 font-medium text-gray-700">Visibility</th>
                    <th class="px-3 py-2 font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 py-2 whitespace-nowrap">
                                <?php if (!empty($post['featured_image'])): ?>
                                    <img src="<?= $postViewModel->getFeaturedImageUrl($post['featured_image']) ?>"
                                         alt="<?= htmlspecialchars($post['title']) ?>" class="h-8 w-8 object-cover rounded" />
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-2 font-medium text-gray-900 truncate max-w-[120px] sm:max-w-xs">
                                <?= htmlspecialchars(substr($post['title'], 0, 50)) ?>
                            </td>
                            <td class="px-3 py-2 text-gray-700 hidden sm:table-cell truncate max-w-xs">
                                <?= substr(strip_tags($post['content']), 0, 80); ?>...
                            </td>
                            <td class="px-3 py-2 text-gray-700 whitespace-nowrap">
                                <?= date('M j', strtotime($post['created_at'])) ?>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-green-800">
                                    <?= ucfirst($post['category_name']) ?>
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    <?= $post['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                    <?= ucfirst($post['status']) ?>
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    <?= $post['visibility'] === 'public' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                    <?= ucfirst($post['visibility']) ?>
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap space-x-1">
                                <a href="/edit/<?= $post['slug'] ?>"
                                   class="inline-flex items-center justify-center px-1.5 py-1 text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none"
                                   title="Edit">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </a>

                                <a href="#"
                                   onclick="event.preventDefault(); openDeleteModal('<?= htmlspecialchars($post['slug']) ?>', 'post')"
                                   class="inline-flex items-center justify-center px-1.5 py-1 text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none"
                                   title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </a>

                                <a href="/toggle-status/<?= $post['id'] ?>"
                                   class="inline-flex items-center justify-center px-1.5 py-1 text-xs font-medium rounded shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none"
                                   title="<?= $post['status'] === 'published' ? 'Set Draft' : 'Publish' ?>">
                                    <i class="fas <?= $post['status'] === 'published' ? 'fa-eye-slash' : 'fa-bolt' ?> text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-3 py-4 text-center text-gray-500 italic">
                            No posts found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
        <?php if (!empty($totalPages) && $totalPages > 1): ?>
        <div class="flex items-center justify-between p-3 bg-gray-50 border-t border-gray-200">
            <div class="text-xs text-gray-700">
                Showing page <?= $currentPage ?? 1 ?> of <?= $totalPages ?>
            </div>
            <div class="flex space-x-1">
                <?php if (($currentPage ?? 1) > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>"
                       class="px-2 py-1 bg-white border border-gray-300 rounded-md text-xs text-gray-700 hover:bg-gray-100">
                        << Prev
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if (abs($i - $currentPage) <= 2 || $i === 1 || $i === $totalPages): ?>
                        <a href="?page=<?= $i ?>"
                           class="px-2 py-1 <?= $i == $currentPage ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100' ?> rounded-md text-xs">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i === $currentPage - 3 || $i === $currentPage + 3): ?>
                        <span class="px-1 text-gray-500">...</span>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if (($currentPage ?? 1) < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>"
                       class="px-2 py-1 bg-white border border-gray-300 rounded-md text-xs text-gray-700 hover:bg-gray-100">
                        Next >>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal"
     class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-4 shadow-lg max-w-sm w-full">
        <h3 class="text-sm font-semibold text-gray-800 mb-2">Confirm Deletion</h3>
        <p class="text-xs text-gray-600 mb-4">Are you sure you want to delete this post? This action cannot be undone.</p>
        <div class="flex justify-end gap-2">
            <button onclick="closeDeleteModal()" type="button"
                    class="px-3 py-1 text-xs text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                Cancel
            </button>
            <a id="confirm-delete-link" href="#"
               class="px-3 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700">
                Delete
            </a>
        </div>
    </div>
</div>

<script>
    let currentSlug = '';

    function openDeleteModal(slug, redirectto) {
        currentSlug = slug;
        document.getElementById('confirm-delete-link').href = '/delete/' + slug + redirectto;
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