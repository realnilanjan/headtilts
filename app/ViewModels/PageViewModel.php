<?php
// app/ViewModels/PageViewModel.php

namespace App\ViewModels;

use App\Models\PageModel;

class PageViewModel
{
    private PageModel $pageModel;

    public function __construct(PageModel $pageModel)
    {
        $this->pageModel = $pageModel;
    }

    public function getAllPages(): array
    {
        return $this->pageModel->getAllPages();
    }

    public function getPageBySlug(string $slug): ?array
    {
        $page = $this->pageModel->getPageBySlug($slug);

        if (!$page) {
            return null;
        }

        return [
            'id' => $page['id'],
            'title' => htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8'),
            'content' => nl2br(htmlspecialchars($page['content'], ENT_QUOTES, 'UTF-8')),
            'slug' => $page['slug'],
            'created_at' => $page['created_at'],
            'updated_at' => $page['updated_at']
        ];
    }
}