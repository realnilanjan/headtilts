<?php
// app/Controllers/PageController.php

namespace App\Controllers;

use App\ViewModels\PageViewModel;
use App\ViewModels\PostViewModel;
use PDO;

class PageController
{
    private PageViewModel $pageViewModel;
    private PostViewModel $postViewModel;

    public function __construct(PageViewModel $pageViewModel, PostViewModel $postViewModel)
    {
        $this->pageViewModel = $pageViewModel;
        $this->postViewModel = $postViewModel;
    }

    public function show(string $slug)
    {
        $page = $this->pageViewModel->getPageBySlug($slug);

        if (!$page) {
            http_response_code(404);
            echo "Page not found";
            return;
        }

        $allCategories = $this->postViewModel->getAllCategories(true);

        include __DIR__ . '/../../views/pages/page.php';
    }
}