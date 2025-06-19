<?php
// App/Services/ImageUploader.php
namespace App\Services;

class ImageUploader
{
    private string $uploadDir;
    private array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private int $maxFileSize = 5 * 1024 * 1024; // 5MB

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function upload(array $file): array
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'error' => null];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error: ' . $file['error']];
        }

        $tmpName = $file['tmp_name'];
        $fileType = mime_content_type($tmpName);

        if (!in_array($fileType, $this->allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid image type. Only JPG, PNG, and GIF are allowed.'];
        }

        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'error' => 'Image size must be less than 5MB.'];
        }

        $fileName = uniqid('img_', true) . '-' . basename($file['name']);
        $uploadPath = $this->uploadDir . $fileName;

        if (!move_uploaded_file($tmpName, $uploadPath)) {
            return ['success' => false, 'error' => 'Failed to move uploaded file.'];
        }

        return ['success' => true, 'filename' => $fileName];
    }
}