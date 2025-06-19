<?php

namespace App\Controllers;

use App\ViewModels\PostViewModel;
use App\Helpers\Helpers;
use App\Services\ImageUploader;

use PDOException;

class PostController
{
    private PostViewModel $viewModel;

    public function __construct(PostViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
    }

    public function home()
    {
        $maintainence_mode = "off";
        if ($maintainence_mode === "on") {
            require_once __DIR__ . '/../../views/pages/maintainence_mode.php';
        } else {
            $categories = $this->viewModel->getAllCategories(true); //Toggles is_included to show
            $postViewModel = $this->viewModel;
            $allCategories = $categories;

            require_once __DIR__ . '/../../views/pages/home.php';
        }
    }

    public function dashboard()
    {
        Helpers::authMiddleware(['admin']);
        $viewModel = $this->viewModel;
        $totalPosts = $viewModel->getAllPosts(); // Or count only
        $totalCategories = $viewModel->getAllCategories();
        //$totalTags = $viewModel->getAllTags();

        include __DIR__ . '/../../views/admin/dashboard.php';
    }

    public function showPost(string $slug)
    {
        $viewModel = $this->viewModel->getFormattedPost($slug);
        $postViewModel = $this->viewModel;
        $allCategories = $this->viewModel->getAllCategories(true);
        if (!$viewModel) {
            Helpers::showErrorPage(404, "Post Not Found");
        }

        require_once __DIR__ . '/../../views/post/single.php';
    }

    public function viewAllPosts()
    {
        Helpers::authMiddleware(['admin']);

        $perPage = 15; // Number of posts per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1

        // Fetch only the current page's posts (with LIMIT and OFFSET)
        $offset = ($page - 1) * $perPage;
        $posts = $this->viewModel->getPaginatedPosts($offset, $perPage);

        // Get total number of posts from the database
        $totalPosts = $this->viewModel->countAllPosts(); // Should return an integer
        $currentPage = $_GET['page'] ?? 1; // Get page from URL
        // Calculate total pages
        $totalPages = ceil($totalPosts / $perPage);

        $postViewModel = $this->viewModel;

        require_once __DIR__ . '/../../views/posts/index.php';
    }

    public function subscribe($planId)
    {
        Helpers::authMiddleware(['admin', 'subscriber']);

        if (!isset($_SESSION['user'])) {
            $_SESSION['flash_message'] = "Please log in to subscribe.";
            Helpers::redirect('/login');
            return;
        }

        $prices = [
            'pro' => 'price_pro_monthly',
            'enterprise' => 'price_enterprise_monthly'
        ];

        if (!array_key_exists($planId, $prices)) {
            $_SESSION['flash_message'] = "Invalid plan selected.";
            Helpers::redirect('/pricing');
            return;
        }

        // Use Stripe SDK to create a checkout session
        //\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // $session = \Stripe\Checkout\Session::create([
        //     'payment_method_types' => ['card'],
        //     'line_items' => [[
        //         'price' => $prices[$planId],
        //         'quantity' => 1,
        //     ]],
        //     'mode' => 'subscription',
        //     'success_url' => 'https://ht-blog-engine/account?status=success',
        //     'cancel_url' => 'https://ht-blog-engine/pricing?status=cancelled',
        // ]);

        //header("Location: " . $session->url);
        exit;
    }

    public function category(string $slug)
    {
        $posts = $this->viewModel->getPostsByCategory($slug);
        $postViewModel = $this->viewModel;
        $currentCategory = $this->viewModel->getCategoryBySlug($slug);
        $allCategories = $this->viewModel->getAllCategories(true);
        $categoryName = $currentCategory['name'] ?? ucfirst($slug);
        $categoryDescription = $currentCategory['description'] ?? null;

        require_once __DIR__ . '/../../views/pages/category.php';
    }

    public function edit(string $slug)
    {
        Helpers::authMiddleware(['admin']);

        $viewModel = $this->viewModel->getFormattedPost($slug);

        if (!$viewModel) {
            Helpers::showErrorPage(404, "Post Not Found");
            return;
        }

        $allCategories = $this->viewModel->getAllCategories();
        $postCategories = $this->viewModel->getAllCategories(false);
        $allAuthors = $this->viewModel->getAllAuthors();
        $tags = $viewModel['tags'];
        $postViewModel = $this->viewModel;

        include __DIR__ . '/../../views/post/edit.php';
    }

    public function togglePostStatus(int $postId)
    {
        Helpers::authMiddleware(['admin']);

        $newStatus = $this->viewModel->togglePostStatus($postId);
        Helpers::redirect('/post');
    }

    public function update()
    {
        Helpers::authMiddleware(['admin']);

        $postData = $_POST;
        $postId = $postData['id'] ?? null;

        if (!$postId || !is_numeric($postId)) {
            http_response_code(400);
            $_SESSION['flash_message'] = "Invalid post ID.";
            Helpers::redirect('/');
            exit;
        }

        $featuredImage = $_FILES['featured_image_new'] ?? null;
        $imageName = $_POST['featured_image'] ?? null;

        if ($featuredImage && $featuredImage['error'] !== UPLOAD_ERR_NO_FILE) {
            $imageResult = $this->handleImageUpload($featuredImage);

            if (!$imageResult['success']) {
                $_SESSION['flash_message'] = $imageResult['error'];

                Helpers::redirect('/edit/' . urlencode($postData['slug']));
                exit;
            }

            $imageName = $imageResult['filename'];
        }

        try {
            $success = $this->viewModel->updatePost($postId, $postData, $imageName);

            if ($success) {
                $_SESSION['flash_message'] = "Post updated successfully.";
                Helpers::redirect('/post/' . urlencode($postData['slug']));
                exit;
            } else {
                throw new \Exception("Failed to update post.");
            }
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Database error: " . $e->getMessage();
            Helpers::redirect('/edit/' . urlencode($postData['slug']));
            exit;
        } catch (\Exception $e) {
            $_SESSION['flash_message'] = "Error: " . $e->getMessage();
            Helpers::redirect('/edit/' . urlencode($postData['slug']));
            exit;
        }
    }

    public function uploadImage()
    {
        Helpers::authMiddleware(['admin']);

        header('Content-Type: application/json');

        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/';
        $baseUrl = Helpers::getBaseUrl();
        $uploader = new ImageUploader($uploadDir);
        $result = $uploader->upload($_FILES['file']);

        if ($result['success']) {
            $fileUrl = "$baseUrl/uploads/" . $result['filename'];
            echo json_encode(['location' => $fileUrl]);
        } else {
            http_response_code($result['error'] ? 400 : 500);
            echo json_encode(['error' => $result['error'] ?: 'Unknown error']);
        }

        exit; // Always call exit after outputting JSON responses
    }

    public function create()
    {
        Helpers::authMiddleware(['admin']);

        $viewModel = [];
        $allCategories = $this->viewModel->getAllCategories();
        $postCategories = $this->viewModel->getAllCategories(false);
        $allAuthors = $this->viewModel->getAllAuthors();

        include __DIR__ . '/../../views/post/create.php';
    }

    public function store()
    {
        Helpers::authMiddleware(['admin']);

        $postData = $_POST;
        $featuredImage = $_FILES['featured_image_new'] ?? null;

        $imageResult = ['filename' => null];
        if ($featuredImage) {
            $imageResult = $this->handleImageUpload($featuredImage);

            if (!$imageResult['success']) {
                $_SESSION['flash_message'] = $imageResult['error'];
                Helpers::redirect('/create');
                exit;
            }
        }

        try {
            $imageName = $imageResult['filename'];
            $success = $this->viewModel->createPost($postData, $imageName);

            if ($success) {
                $_SESSION['flash_message'] = "Post created successfully.";
                Helpers::redirect('/post/' . urlencode($postData['slug']));
                exit;
            } else {
                throw new \Exception("Failed to create post.");
            }
        } catch (\Exception $e) {
            $_SESSION['flash_message'] = "Error: " . $e->getMessage();
            Helpers::redirect('/create');
            exit;
        }
    }

    public function delete(string $slug, string $redirecturl="/")
    {
        Helpers::authMiddleware(['admin']);

        $token = $_GET['token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $_SESSION['flash_message'] = "Invalid or missing token.";
            Helpers::redirect($redirecturl);
            exit;
        }

        try {
            $postId = $this->viewModel->getPostIdBySlug($slug);
            if (!$postId) {
                throw new \Exception("Post not found.");
            }

            $success = $this->viewModel->deletePost($slug);
            if ($success) {
                $_SESSION['flash_message'] = "Post deleted successfully.";
            } else {
                throw new \Exception("Failed to delete post.");
            }
        } catch (\Exception $e) {
            $_SESSION['flash_message'] = "Error: " . $e->getMessage();
        }

        Helpers::redirect($redirecturl);
        exit;
    }

    private function handleImageUpload(array $file): array
    {
        $uploader = new ImageUploader(__DIR__ . '/../../public/uploads/');
        return $uploader->upload($file);
    }

    public function tag(string $tagSlug): void
    {
        $posts = $this->viewModel->getPostsByTag($tagSlug);
        $postViewModel = $this->viewModel;
        $tag = $this->viewModel->getTagBySlug($tagSlug);
        $allCategories = $this->viewModel->getAllCategories(true);
        $tagName = $tag ? htmlspecialchars($tag['name']) : 'Unknown Tag';
        require_once __DIR__ . '/../../views/pages/tag.php';
    }

    public function imageBrowser()
    {
        Helpers::authMiddleware(['admin']);
        include __DIR__ . '/../../views/partials/image-browser.php';
        exit;
    }

    public function getPosts()
    {
        $posts = $this->viewModel->getAllPosts();
        header('Content-Type: application/json');
        echo json_encode(['data' => $posts]);
    }

    public function createPostApi()
    {
        Helpers::authMiddleware(['admin', 'editor']);

        $input = json_decode(file_get_contents('php://input'), true);
        $requiredFields = ['title', 'content', 'slug', 'category_id'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Missing field: $field"]);
                return;
            }
        }

        $imageName = null;

        // Optional image handling
        if (!empty($input['featured_image'])) {
            $imageName = basename($input['featured_image']);
        }

        try {
            $postId = $this->viewModel->createPost($input, $imageName);
            if ($postId) {
                http_response_code(201);
                echo json_encode([
                    'message' => 'Post created successfully',
                    'id' => $postId
                ]);
            } else {
                throw new \Exception("Failed to create post");
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updatePostApi(int $id)
    {
        Helpers::authMiddleware(['admin', 'editor']);

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (empty($input)) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided']);
            return;
        }

        $imageName = null;

        // Handle featured image from request
        if (!empty($input['featured_image'])) {
            $imageName = basename($input['featured_image']);
        }

        try {
            $success = $this->viewModel->updatePost($id, $input, $imageName);
            if ($success) {
                echo json_encode(['message' => 'Post updated successfully']);
            } else {
                throw new \Exception("Update failed");
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deletePostApi($slug)
    {
        Helpers::authMiddleware(['admin', 'editor']);

        try {
            $success = $this->viewModel->deletePost($slug);
            if ($success) {
                echo json_encode(['message' => 'Post deleted successfully']);
            } else {
                throw new \Exception("Delete failed or post not found");
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
