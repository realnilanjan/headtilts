<?php

use App\Helpers\Helpers;

session_start();

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../app/Models/PostModel.php';
    require_once __DIR__ . '/../app/Models/UserModel.php';
    require_once __DIR__ . '/../app/ViewModels/PostViewModel.php';
    require_once __DIR__ . '/../app/Controllers/PostController.php';
    require_once __DIR__ . '/../app/Controllers/SettingsController.php';
    require_once __DIR__ . '/../app/Controllers/AuthController.php';

    $url = isset($_GET['url']) ? explode('/', trim($_GET['url'], '/')) : [];
    $method = $_SERVER['REQUEST_METHOD'];

    $postModel = new App\Models\PostModel($pdo);
    $userModel = new App\Models\UserModel($pdo);

    $postViewModel = new App\ViewModels\PostViewModel($postModel, $userModel);
    $postController = new App\Controllers\PostController($postViewModel);

    $settingsController = new App\Controllers\SettingsController();
    $authController = new App\Controllers\AuthController($userModel);

    // --- PUBLIC ROUTES ---
    $publicRoutes = [
            '' => fn() => $postController->home(),
        'about' => function () use ($postViewModel) {
            $allCategories = $postViewModel->getAllCategories(true);
            include __DIR__ . '/../views/pages/about.php';
        },
        'privacy' => function () use ($postViewModel) {
            $allCategories = $postViewModel->getAllCategories(true);
            include __DIR__ . '/../views/pages/privacy.php';
        },
        'contact' => function () use ($postViewModel) {
            $allCategories = $postViewModel->getAllCategories(true);
            include __DIR__ . '/../views/pages/contact.php';
        },
        'terms' => function () use ($postViewModel) {
            $allCategories = $postViewModel->getAllCategories(true);
            include __DIR__ . '/../views/pages/terms.php';
        },
        'pricing' => function () use ($postViewModel) {
            $allCategories = $postViewModel->getAllCategories(true);
            include __DIR__ . '/../views/pages/pricing.php';
        },
        'category' => function () use ($url, $postController) {
            if (isset($url[1])) {
                $postController->category($url[1]);
            } else {
                http_response_code(404);
                Helpers::showErrorPage(404, "Category Not Found");
            }
        },
        'tag' => function () use ($url, $postController) {
            if (isset($url[1])) {
                $postController->tag($url[1]);
            } else {
                http_response_code(404);
                Helpers::showErrorPage(404, "Tag Not Found");
            }
        },
        'post' => function () use ($url, $postController) {
            switch (count($url)) {
                case 1:
                    $postController->viewAllPosts();
                    break;
                case 2:
                    if ($url[1] !== '') {
                        $postController->showPost($url[1]);
                    } else {
                        http_response_code(404);
                        Helpers::showErrorPage(404, "Post Not Found");
                    }
                    break;
                default:
                    http_response_code(404);
                    Helpers::showErrorPage(404, "Page Not Found");
            }
        },
    ];

    // --- AUTH ROUTES ---
    $authRoutes = [
        'login' => [
            'GET' => fn() => $authController->showLogin(),
            'POST' => fn() => $authController->login()
        ],
        'forgot-password' => [
            'GET' => fn() => $authController->showForgotPassword(),
            'POST' => fn() => $authController->handleForgotPassword()
        ],
        'reset-password' => [
            'GET' => fn() => $authController->showResetPassword(),
            'POST' => fn() => $authController->handleResetPassword()
        ],
        'register' => [
            'GET' => fn() => $authController->showRegister(),
            'POST' => fn() => $authController->register()
        ],
        'logout' => [
            'GET' => fn() => $authController->logout()
        ],
    ];

    // --- ADMIN CORE ROUTES (nested under /admin) ---
    $adminCoreRoutes = [
        'admin' => [
            'dashboard' => fn() => $postController->dashboard(),

            'setup' => [
                'users' => fn() => $settingsController->manageUsers(),
                'sitesettings' => fn() => $settingsController->siteSettings(),
                'menus' => fn() => $settingsController->manageMenus(),
                'analytics' => fn() => $settingsController->analytics(),
                'subscribers' => fn() => $settingsController->manageSubscribers(),
            ],
        ],
    ];

    // --- POST MANAGEMENT ROUTES (not nested under /admin) ---
    $postManagementRoutes = [
        'edit' => function () use ($url, $postController) {
            if (isset($url[1])) {
                $postController->edit($url[1]);
            } else {
                http_response_code(404);
                Helpers::redirect('/');
            }
        },
        'update' => [
            'POST' => fn() => $postController->update()
        ],
        'upload-image' => [
            'POST' => fn() => $postController->uploadImage()
        ],
        'create' => [
            'GET' => fn() => $postController->create()
        ],
        'store' => [
            'POST' => fn() => $postController->store()
        ],
        'delete' => function () use ($url, $postController) {
            if (isset($url[1])) {
                $postController->delete($url[1], $url[2]);
            } else {
                http_response_code(404);
                Helpers::showErrorPage(404, "Invalid post ID");
            }
        },
        'image-browser' => fn() => $postController->imageBrowser(),
        'toggle-status' => function () use ($url, $postController) {
            if (isset($url[1])) {
                $postController->togglePostStatus($url[1]);
            } else {
                http_response_code(404);
                Helpers::showErrorPage(404, "Post Not Found");
            }
        },
    ];

    $adminRoutes = array_merge($adminCoreRoutes, $postManagementRoutes);

    // --- API ROUTES ---
    if (!empty($url[0]) && $url[0] === 'api') {
        $apiPath = $url[1] ?? null;

        switch ($apiPath) {
            case 'posts':
                switch ($method) {
                    case 'GET':
                        $postController->getPosts();
                        break;
                    case 'POST':
                        $postController->createPostApi();
                        break;
                    case 'PUT':
                        if (!empty($url[2])) $postController->updatePostApi($url[2]);
                        else http_response_code(400);
                        break;
                    case 'DELETE':
                        if (!empty($url[2])) $postController->deletePostApi($url[2]);
                        else http_response_code(400);
                        break;
                    default:
                        http_response_code(405);
                        echo json_encode(['error' => 'Method not allowed']);
                }
                exit;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'API route not found']);
                exit;
        }
    }

    $routes = array_merge($publicRoutes, $authRoutes, $adminRoutes);

    $urlSegments = isset($_GET['url']) ? explode('/', trim($_GET['url'] ?? '', '/')) : [];

    $routeHandler = null;
    $currentLevel = $routes;

    if (empty($urlSegments)) {
        if (isset($routes['']) && is_callable($routes[''])) {
            $routes['']();
            exit;
        } else {
            http_response_code(404);
            Helpers::showErrorPage(404, "Homepage Not Found");
            exit;
        }
    }

    foreach ($urlSegments as $segment) {
        if (isset($currentLevel[$segment])) {
            if (is_array($currentLevel[$segment])) {
                $currentLevel = $currentLevel[$segment];
            } elseif (is_callable($currentLevel[$segment])) {
                $routeHandler = $currentLevel[$segment];
                break;
            } else {
                break;
            }
        } else {
            break;
        }
    }

    if ($routeHandler !== null) {
        $routeHandler();
    } else {
        if (isset($currentLevel[$method]) && is_callable($currentLevel[$method])) {
            $currentLevel[$method]();
        } else {
            http_response_code(404);
            Helpers::showErrorPage(404, "Page Not Found");
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    Helpers::showErrorPage(500, "Internal Server Error", $e->getMessage());
}
