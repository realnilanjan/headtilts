<?php

namespace App\Helpers;

class SeoHelper
{
    private array $seoData = [];
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = $this->getBaseUrl();
    }

    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            ($_SERVER['SERVER_PORT'] === 443) ? 'https://'  : 'http://';

        $host = $_SERVER['HTTP_HOST']; // e.g., localhost or example.com
        return $protocol . $host;
    }

    /**
     * Set SEO data for a post or page
     */
    public function setPost(array $post, ?string $defaultImage = null, array $keywords = []): self
    {
        $keywords = !empty($keywords) ? $keywords : ($post['tags'] ?? []);
        if (!empty($post['tags']) && is_array($post['tags'][0])) {
            $keywords = array_column($post['tags'], 'name');
        }

        $this->seoData = [
            'title' => htmlspecialchars($post['title'] ?? 'Untitled Post'),
            'description' => $this->generateExcerpt($post['content'] ?? '', 160),
            'url' => $this->getUrlFromSlug($post['slug'] ?? ''),
            'image' => $this->getImageUrl($post['featured_image'] ?? null, $defaultImage),
            'type' => 'article',
            'updated_at' => $post['updated_at'] ?? $post['created_at'] ?? date('c'),
            'published_at' => $post['created_at'] ?? date('c'),
            'author' => $post['author_username'] ?? 'Headtilts Team',
            'keywords' => is_array($keywords) ? implode(', ', $keywords) : '',
        ];

        return $this;
    }

    /**
     * Set SEO data for a category
     */
    public function setCategory(
        string $name,
        ?string $description = null,
        ?string $defaultImage = null,
        array $keywords = []
    ): self {
        $keywords = !empty($keywords) ? $keywords : ['entertainment', 'celebrity', 'music', 'news'];

        $this->seoData = [
            'title' => "Category: '$name | Headtilts - Global Entertainment News",
            'description' => $description ? substr(strip_tags($description), 0, 160) : "Explore the latest news in $name.",
            'url' => "/category/$name",
            'image' => $defaultImage ?: '/assets/images/default.jpg',
            'type' => 'website',
            'keywords' => implode(', ', $keywords),
        ];

        return $this;
    }

    /**
     * Set SEO data for a tags
     */
    public function setTags(
        string $name,
        string $description = 'Explore the latest news tagged in this word.',
        ?string $defaultImage = null,
        array $keywords = []
    ): self {
        $keywords = !empty($keywords) ? $keywords : ['entertainment', 'celebrity', 'music', 'news'];

        $this->seoData = [
            'title' => "Tag: $name | Headtilts - Global Entertainment News",
            'description' => $description ? substr(strip_tags($description), 0, 160) : "Explore the latest news in $name.",
            'url' => "/tag/$name",
            'image' => $defaultImage ?: '/assets/images/default.jpg',
            'type' => 'website',
            'keywords' => implode(', ', $keywords),
        ];

        return $this;
    }

    /**
     * Set SEO data for homepage or static page
     */
    public function setDefault(
        string $title = 'Headtilts - Global Entertainment News',
        string $description = 'Let Heads Tilt',
        array $keywords = []
    ): self {
        $keywords = !empty($keywords) ? $keywords : [
            'rock music discovery',
            'metal music discovery',
            'underground metal bands',
            'new rock releases',
            'independent rock artists',
            'metal scene news',
            'cult metal bands',
            'global rock and metal news',
            'music curation platform',
            'submit music for review'
        ];
        $this->seoData = [
            'title' => $title,
            'description' => $description,
            'url' => '/',
            'image' => '/assets/images/default.jpg',
            'type' => 'website',
            'keywords' => implode(', ', $keywords),
        ];

        return $this;
    }

    /**
     * Get all SEO meta tags as an associative array
     */
    public function get(): array
    {
        return $this->seoData;
    }

    /**
     * Generate meta tags HTML to echo in <head>
     */
    /**
     * Generate meta tags HTML to echo in <head>
     */
    public function render(): string
    {
        $seo = $this->seoData;

        $html = "<meta name=\"description\" content=\"" . htmlspecialchars($seo['description'] ?? '') . "\">\n";

        // Keywords (optional)
        if (!empty($seo['keywords'])) {
            $html .= "<meta name=\"keywords\" content=\"" . htmlspecialchars($seo['keywords']) . "\">\n";
        }

        // Open Graph
        $html .= "<meta property=\"og:title\" content=\"" . htmlspecialchars($seo['title']) . "\">\n";
        $html .= "<meta property=\"og:description\" content=\"" . htmlspecialchars($seo['description']) . "\">\n";
        $html .= "<meta property=\"og:type\" content=\"" . htmlspecialchars($seo['type']) . "\">\n";
        $html .= "<meta property=\"og:url\" content=\"" . htmlspecialchars($seo['url']) . "\">\n";
        $html .= "<meta property=\"og:image\" content=\"" . htmlspecialchars($seo['image']) . "\">\n";

        // Twitter Card
        $html .= "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
        $html .= "<meta name=\"twitter:title\" content=\"" . htmlspecialchars($seo['title']) . "\">\n";
        $html .= "<meta name=\"twitter:description\" content=\"" . htmlspecialchars($seo['description']) . "\">\n";
        $html .= "<meta name=\"twitter:image\" content=\"" . htmlspecialchars($seo['image']) . "\">\n";

        return $html;
    }

    /**
     * Generate excerpt from content
     */
    private function generateExcerpt(string $content, int $length = 160): string
    {
        $text = strip_tags($content);
        return substr($text, 0, $length);
    }

    /**
     * Build URL from slug
     */
    private function getUrlFromSlug(string $slug): string
    {
        return "/post/" . htmlspecialchars($slug);
    }

    /**
     * Resolve image URL
     */
    private function getImageUrl(?string $imageName, ?string $defaultImage = null): string
    {
        if ($imageName && str_starts_with($imageName, 'http')) {
            return $imageName; // Already a full URL
        }

        if ($imageName) {
            return "../uploads/" . htmlspecialchars($imageName);
        }

        return $defaultImage ?: '/assets/images/default.jpg';
    }
}
