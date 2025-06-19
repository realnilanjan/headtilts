<?php

namespace App\Models;

use App\Models\Database;

class PostModel extends Database
{
    public function getAllPosts()
    {
        try {
            $stmt = $this->pdo->query("CALL GetAllPosts()");
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $results;
        } catch (\PDOException $e) {
            error_log("Error fetching posts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Toggle post status between 'published' and 'draft'
     *
     * @param int $postId
     * @return bool|string Returns new status on success, or error message
     * @throws \Exception
     */
    public function togglePostStatus(int $postId)
    {
        try {
            // Get current status
            $stmt = $this->pdo->prepare("SELECT status FROM posts WHERE id = ?");
            $stmt->execute([$postId]);
            $post = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$post) {
                throw new \Exception("Post not found.");
            }

            // Determine new status
            $newStatus = ($post['status'] === 'published') ? 'draft' : 'published';

            // Update status in database
            $updateStmt = $this->pdo->prepare("UPDATE posts SET status = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $postId]);

            return $newStatus;
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new \Exception("Database error occurred.");
        }
    }

    public function getPostBySlug(string $slug): ?array
    {
        try {
            $stmt = $this->pdo->prepare("CALL GetPostBySlug(:slug)");
            $stmt->execute([':slug' => $slug]);
            $stmt->closeCursor();

            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("Error fetching posts: " . $e->getMessage());
            return [];
        }
    }

    public function getPostDetails(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("CALL GetPostDetailsBySlug(:slug)");
        $stmt->execute([':slug' => $slug]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getFeaturedPosts(int $limit = 4)
    {
        try {
            $stmt = $this->pdo->prepare("CALL GetFeaturedPosts(:limit)");
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function getAllCategories(bool $onlyIncluded = true): array
    {
        $flag = $onlyIncluded ? 1 : 0;

        $stmt = $this->pdo->prepare("CALL GetAllCategories(?)");
        $stmt->execute([$flag]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPostsByCategorySlug(string $categorySlug): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL GetPostsByCategorySlug(:category_slug)");

            $success = $stmt->execute([
                ':category_slug' => $categorySlug
            ]);

            if (!$success) {
                throw new \RuntimeException("Failed to execute stored procedure.");
            }

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function getCategoryBySlug(string $categorySlug): ?array
    {
        try {
            $stmt = $this->pdo->prepare("CALL GetCategoryBySlug(:category_slug)");
            $success = $stmt->execute([
                ':category_slug' => $categorySlug
            ]);

            if (!$success) {
                throw new \RuntimeException("Failed to execute stored procedure.");
            }

            $category = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $category ?: null;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function getPostsByCategory_2(string $categorySlug, int $limit = 4): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL GetPostsByCategorySlug2(:category_slug, :limit)");
            $success = $stmt->execute([
                ':category_slug' => $categorySlug,
                ':limit' => $limit
            ]);

            if (!$success) {
                throw new \RuntimeException("Failed to fetch posts by category.");
            }

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function getPaginatedPosts($offset, $limit)
    {
        $sql = "SELECT p.*, c.name as category_name 
            FROM posts p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.created_at DESC 
            LIMIT :limit 
            OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        // Bind parameters as integers
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countAllPosts()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
        return (int)$stmt->fetchColumn();
    }

    public function getPostsByCategory(string $categorySlug): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL GetPostsByCategorySlug(:category_slug)");
            $success = $stmt->execute([
                ':category_slug' => $categorySlug
            ]);

            if (!$success) {
                throw new \RuntimeException("Failed to execute stored procedure.");
            }

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function updatePost(int $postId, array $postData, ?string $imageName = null): bool
    {
        $requiredFields = ['title', 'content', 'category_id', 'status', 'slug', 'user_id', 'tags'];
        foreach ($requiredFields as $field) {
            if (!isset($postData[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }

        $createdAt = null;
        if (!empty($postData['created_at'])) {
            $createdAt = date('Y-m-d H:i:s', strtotime($postData['created_at']));
        }

        $stmt = $this->pdo->prepare("
        CALL UpdatePost(
            :post_id,
            :post_title,
            :post_content,
            :post_category_id,
            :post_status,
            :post_visibility,
            :post_slug,
            :post_user_id,
            :post_image_name,
            :post_created_at
        )
    ");
        $params = [
            ':post_id'          => $postId,
            ':post_title'       => $postData['title'],
            ':post_content'     => $postData['content'],
            ':post_category_id' => $postData['category_id'],
            ':post_status'      => $postData['status'],
            ':post_visibility'  => $postData['visibility'],
            ':post_slug'        => $postData['slug'],
            ':post_user_id'     => $postData['user_id'],
            ':post_image_name'  => $imageName,
            ':post_created_at'  => $createdAt
        ];

        if (!$stmt->execute($params)) {
            return false;
        }

        $tagNames = array_filter(array_map('trim', explode(',', $postData['tags'])));
        $stmt = $this->pdo->prepare("DELETE FROM post_tags WHERE post_id = ?");
        $stmt->execute([$postId]);

        foreach ($tagNames as $tagName) {
            $tagId = $this->getOrCreateTagId($this->pdo, $tagName);

            $stmt = $this->pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$postId, $tagId]);
        }

        return true;
    }

    private function getOrCreateTagId(\PDO $pdo, string $tagName): int
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tagName), '-'));

        $stmt = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
        $stmt->execute([$slug]);

        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return (int)$row['id'];
        }

        $stmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
        $stmt->execute([$tagName, $slug]);

        return (int)$pdo->lastInsertId();
    }

    function getRecentPosts($limit)
    {
        $limit = (int) $limit;
        $stmt = $this->pdo->prepare("CALL GetRecentPosts(?)");
        $stmt->execute([$limit]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $results;
    }

    public function createPost(array $postData, ?string $imageName = null): bool
    {
        $requiredFields = ['title', 'content', 'category_id', 'user_id', 'status', 'slug'];
        foreach ($requiredFields as $field) {
            if (!isset($postData[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }

        try {
            $stmt = $this->pdo->prepare("
            CALL CreatePost(
                :post_title,
                :post_content,
                :post_category_id,
                :post_user_id,
                :post_status,
                :post_visibility,
                :post_slug,
                :post_image_name,
                @post_id
            )
        ");

            $params = [
                ':post_title'        => $postData['title'],
                ':post_content'      => $postData['content'],
                ':post_category_id'  => $postData['category_id'],
                ':post_user_id'      => $postData['user_id'],
                ':post_status'       => $postData['status'],
                ':post_visibility'   => $postData['visibility'],
                ':post_slug'         => $postData['slug'],
                ':post_image_name'   => $imageName
            ];

            if (!$stmt->execute($params)) {
                return false;
            }

            $result = $this->pdo->query("SELECT @post_id AS post_id")->fetch(\PDO::FETCH_ASSOC);
            $postId = (int)($result['post_id'] ?? 0);

            if ($postId <= 0) {
                return false;
            }

            if (!empty($postData['tags'])) {
                $tagNames = array_filter(array_map('trim', explode(',', $postData['tags'])));

                $stmt = $this->pdo->prepare("DELETE FROM post_tags WHERE post_id = ?");
                $stmt->execute([$postId]);

                foreach ($tagNames as $tagName) {
                    $tagId = $this->getOrCreateTagId($this->pdo, $tagName);

                    $stmt = $this->pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
                    if (!$stmt->execute([$postId, $tagId])) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\PDOException $e) {
            error_log("Database error in createPost(): " . $e->getMessage());
            return false;
        }
    }

    public function getPostIdBySlug(string $slug): ?int
    {
        try {
            $stmt = $this->pdo->prepare("CALL GetPostIdBySlug(:post_slug)");
            $success = $stmt->execute([
                ':post_slug' => $slug
            ]);

            if (!$success) {
                throw new \RuntimeException("Failed to execute stored procedure.");
            }

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ? (int)$row['id'] : null;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function deletePost(int $postId): bool
    {
        try {
            $stmt = $this->pdo->prepare("CALL DeletePost(:post_id)");

            $success = $stmt->execute([
                ':post_id' => $postId
            ]);

            return $success;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function getPostsByTag(string $tagSlug): ?array
    {
        try {
            $stmt = $this->pdo->prepare("CALL GetPostsByTag(:tag_slug)");

            $success = $stmt->execute([
                ':tag_slug' => $tagSlug
            ]);

            if (!$success) {
                throw new \RuntimeException("Failed to execute stored procedure.");
            }

            $posts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $posts ?: null;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function getTagBySlug(string $tagSlug): ?array
    {
        try {
            $stmt = $this->pdo->prepare("CALL GetTagBySlug(:tag_slug)");

            $success = $stmt->execute([
                ':tag_slug' => $tagSlug
            ]);

            if (!$success) {
                throw new \RuntimeException("Failed to execute stored procedure.");
            }

            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }
}
