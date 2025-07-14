<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Helpers
{
    /**
     * Start session if not already started (static constructor alternative)
     */
    private static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Redirect to a given path.
     */
    public static function redirect(string $path): void
    {
        header("Location: $path");
        exit;
    }

    /**
     * Dump and die: Print data in readable format and exit.
     */
    public static function dd(mixed $data): never
    {
        echo "<pre>";
        echo htmlspecialchars(print_r($data, true));
        echo "</pre>";
        exit;
    }

    /**
     * Get the authenticated user from session.
     */
    public static function auth(): ?array
    {
        self::init();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Redirect if user is already logged in (guest-only access).
     */
    public static function guestOnly(): void
    {
        if (self::auth()) {
            self::redirect('/');
        }
    }

    /**
     * Middleware to enforce authentication and role-based access.
     */
    public static function authMiddleware(?array $requiredRoles = null): void
    {
        self::init();

        $jwtToken = null;

        // Try getting JWT from Authorization header
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $jwtToken = $matches[1];
            }
        }

        // Or try getting from session
        if (!$jwtToken && isset($_SESSION['jwt_token'])) {
            $jwtToken = $_SESSION['jwt_token'];
        }

        if ($jwtToken) {
            try {
                $decoded = JWT::decode($jwtToken, new Key($_ENV['JWT_SECRET'], 'HS256'));

                // Use data directly from JWT without querying DB
                $userData = (array)$decoded->data;

                // Populate minimal session for compatibility
                if (!isset($_SESSION['user'])) {
                    $_SESSION['user'] = [
                        'id' => $userData['id'],
                        'email' => $userData['email'],
                        'role' => $userData['role']
                    ];
                }
            } catch (\Exception $e) {
                // Invalid token — try remember_me fallback
                if (!self::handleRememberMeFallback()) {
                    self::redirect('/login');
                }
            }
        } else {
            // No JWT — fallback to session or remember_me
            if (!self::handleRememberMeFallback()) {
                self::redirect('/login');
            }
        }

        // Role-based access check
        if ($requiredRoles) {
            $userRole = $_SESSION['user']['role'];
            if (!in_array($userRole, $requiredRoles, true)) {
                header("HTTP/1.1 403 Forbidden");
                exit("Access Denied");
            }
        }
    }

    private static function handleRememberMeFallback(): bool
    {
        if (!isset($_COOKIE['remember_me'])) {
            return false;
        }

        $token = $_COOKIE['remember_me'];

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

            // Ensure required claims are present
            if (isset($decoded->data)) {
                $userData = (array)$decoded->data;

                // Populate session with user data from JWT
                $_SESSION['user'] = [
                    'id' => $userData['id'],
                    'email' => $userData['email'],
                    'role' => $userData['role']
                ];

                return true;
            }
        } catch (\Exception $e) {
            // Invalid or expired token
            return false;
        }

        return false;
    }

    /**
     * Render post form for both create and edit
     */
    public static function renderPostForm(array $viewModel = [], array $allCategories = [], array $allAuthors = [], bool $isEdit = false): string
    {
        ob_start();
        include __DIR__ . '/../../views/partials/post-form.php';
        return ob_get_clean();
    }

    public static function sanitizePostContent($html)
    {
        if (!$html) return '';
        $html = preg_replace('/<p[^>]*>\s*(&nbsp;)?\s*<\/p>/i', '', $html);
        $html = preg_replace('/\s+/', ' ', $html);

        // Strip unwanted tags (optional)
        // $html = strip_tags($html, '<p><strong><em><img><a><ul><li><ol><h1><h2><h3><div><span>');

        return trim($html);
    }

    public static function embedYouTubeVideos(string $content): string
    {
        $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\s&]+)/i';
        $replace = '<div class="video-wrapper"><iframe width="560" height="315"
                    src="https://www.youtube.com/embed/$1" 
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe></div>';

        return preg_replace($pattern, $replace, $content);
    }

    public static function showErrorPage(int $code, string $title, string $message = '')
    {
        http_response_code($code);

        $errorMessage = $message ?: match ($code) {
            404 => "The requested address could not be found.",
            405 => "Method not allowed for this route.",
            default => "An internal server error occurred."
        };

        extract([
            'errorCode' => $code,
            'title' => $title,
            'message' => $errorMessage
        ]);

        require __DIR__ . '/../../views/errors/error.php';
        exit;
    }

    public static function extractKeywords(string $text, int $limit = 10): array
    {
        // Load stopwords from file
        $stopwords = self::loadStopwords(__DIR__ . '/../../resources/stopwords.txt');

        // Clean text
        $text = strtolower(strip_tags($text));
        $text = preg_replace('/\bhttps?:\/\/[^\s]+|www\.[^\s]+|\S+@\S+\.\S+\b/', '', $text); // Remove URLs and emails
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        $words = explode(' ', trim($text));

        $wordCount = [];

        foreach ($words as $word) {
            $word = trim($word, ".,!?()[]{}");
            if (mb_strlen($word, 'UTF-8') > 2 && !in_array($word, $stopwords)) {
                $wordCount[$word] = ($wordCount[$word] ?? 0) + 1;
            }
        }

        arsort($wordCount);

        return array_slice(array_keys($wordCount), 0, $limit);
    }

    public static function extractBigrams(string $text, int $limit = 5): array
    {
        $text = strtolower(strip_tags($text));
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $bigrams = [];

        for ($i = 0; $i < count($words) - 1; $i++) {
            $phrase = $words[$i] . ' ' . $words[$i + 1];
            if (mb_strlen($phrase, 'UTF-8') > 3) {
                $bigrams[$phrase] = ($bigrams[$phrase] ?? 0) + 1;
            }
        }

        arsort($bigrams);

        return array_slice(array_keys($bigrams), 0, $limit);
    }

    public static function loadStopwords(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Stopwords file not found at: $filePath");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return array_filter($lines, function ($line) {
            return strpos(trim($line), '#') !== 0; // Skip comment lines starting with #
        });
    }

    public static function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            ($_SERVER['SERVER_PORT'] === 443) ? 'https://'  : 'http://';

        $host = $_SERVER['HTTP_HOST']; // e.g., localhost or example.com
        return $protocol . $host;
    }
}
