<?php

namespace App\ViewModels;

use App\Models\PostModel;
use App\Models\UserModel;
use App\Helpers\Helpers;

require_once __DIR__ . '/../Helpers/helpers.php';

class PostViewModel
{
    public function __construct(
        private PostModel $model,
        private UserModel $userModel
    ) {}

    public function getAllPosts()
    {
        $posts = $this->model->getAllPosts();

        return array_map(function ($post) {
            $tags = !empty($post['tags']) ? json_decode($post['tags'], true) : [];

            return [
                'id'                => (int)$post['id'],
                'title'             => htmlspecialchars($post['title']),
                'slug'              => $post['slug'],
                'summary'           => $post['summary'] ?? '',
                'content'           => $post['content'],
                'excerpt'           => substr(strip_tags($post['content']), 0, 150) . '...',
                'featured_image'    => $post['featured_image'] ?? null,
                'author_id'         => (int)$post['author_id'],
                'published_at'      => $post['published_at'],
                'status'            => $post['status'],
                'category_name'     => $post['category_name'],
                'visibility'        => $post['visibility'],
                'created_at'        => $post['created_at'],
                'updated_at'        => $post['updated_at'],
                'tags'              => $tags
            ];
        }, $posts);
    }

    public function getPaginatedPosts($offset, $limit): array
    {
        return $this->model->getPaginatedPosts($offset, $limit);
    }

    public function countAllPosts()
    {
        return $this->model->countAllPosts();
    }

    public function getFormattedPost(string $slug): ?array
    {
        $post = $this->model->getPostDetails($slug);

        if (!$post) {
            return null;
        }

        $tags = !empty($post['tags']) ? json_decode($post['tags'], true) : [];
        return [
            'id'                    => $post['id'] ?? null,
            'title'                 => htmlspecialchars($post['title']),
            'content'               => Helpers::sanitizePostContent($post['content']),
            'featured_image'        => $post['featured_image'] ?? null,
            'slug'                  => htmlspecialchars($post['slug']) ?? 'unknown',
            'category_id'           => $post['category_id'] ?? null,
            'category_name'         => htmlspecialchars($post['category_name']) ?? 'Uncategorized',
            'category_slug'         => htmlspecialchars($post['category_slug']) ?? 'uncategorized',
            'category_description'  => htmlspecialchars($post['category_description']) ?? null,
            'author_id'             => $post['user_id'] ?? null,
            'author_username'       => htmlspecialchars($post['author_username']) ?? 'Unknown',
            'status'                => htmlspecialchars($post['status']) ?? 'draft',
            'visibility'            => htmlspecialchars($post['visibility']) ?? 'private',
            'created_at'            => $post['created_at'] ?? null,
            'updated_at'            => $post['updated_at'] ?? null,
            'tags'                  => $tags
        ];
    }

    public function getFeaturedPosts(int $limit = 4)
    {
        $posts = $this->model->getFeaturedPosts($limit);
        return array_map(function ($post) {
            return [
                'title' => htmlspecialchars($post['title']),
                'excerpt' => substr(strip_tags($post['content']), 0, 150) . '...',
                'slug' => $post['slug'],
                'featured_image' => $post['featured_image'] ?? null
            ];
        }, $posts);
    }

    public function getFeaturedImageUrl(?string $filename): string
    {
        if (!$filename) {
            return "/assets/images/default.jpg";
        }

        $uploadPath = __DIR__ . '/../../public/uploads/' . $filename;

        if (file_exists($uploadPath)) {
            return "../uploads/" . htmlspecialchars($filename);
        } else {
            return "/assets/images/default.jpg";
        }
    }

    public function getPostsByCategory_2(string $categoryName): array
    {
        return $this->model->getPostsByCategory_2($categoryName);
    }

    public function getAllCategories(bool $onlyIncluded = true): array
    {
        return $this->model->getAllCategories($onlyIncluded);
    }

    public function getPostsByCategory(string $categorySlug): array
    {
        return $this->model->getPostsByCategory($categorySlug);
    }

    public function getCategoryBySlug(string $categorySlug): ?array
    {
        return $this->model->getCategoryBySlug($categorySlug);
    }

    public function updatePost(int $postId, array $postData, ?string $imageName = null): bool
    {
        return $this->model->updatePost($postId, $postData, $imageName);
    }

    public function getAllAuthors(): array
    {
        return $this->userModel->getAllAuthors();
    }

    public function togglePostStatus($postid): string
    {
        return $this->model->togglePostStatus($postid);
    }

    public function getRecentPosts($limit)
    {
        return $this->model->getRecentPosts($limit);
    }

    public function createPost(array $postData, ?string $imageName = null): bool
    {
        return $this->model->createPost($postData, $imageName);
    }

    public function getPostIdBySlug(string $slug)
    {
        return $this->model->getPostIdBySlug($slug);
    }

    public function deletePost(string $slug)
    {
        return $this->model->deletePost($this->getPostIdBySlug($slug));
    }

    public function getPostsByTag(string $tagSlug): array
    {
        return $this->model->getPostsByTag($tagSlug);
    }

    public function getTagBySlug(string $categorySlug): array
    {
        return $this->model->getTagBySlug($categorySlug);
    }
}
