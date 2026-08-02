<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Secure image upload: validates real MIME via finfo, size, and extension.
 */
final class Upload
{
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    /**
     * @param array $file  A single $_FILES entry.
     * @return string|null  Stored relative path (e.g. "products/abc.jpg") or null on failure.
     */
    public static function image(array $file, string $subdir): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        $maxSize = (int) env('UPLOAD_MAX_SIZE', 3145728);
        if ($file['size'] <= 0 || $file['size'] > $maxSize) {
            return null;
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        if (!isset(self::ALLOWED[$mime])) {
            return null;
        }
        // Confirm it is a real image
        if (@getimagesize($file['tmp_name']) === false) {
            return null;
        }

        $ext = self::ALLOWED[$mime];
        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dir = PUBLIC_PATH . '/uploads/' . trim($subdir, '/');
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return null;
        }
        $target = $dir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return null;
        }
        @chmod($target, 0644);
        return trim($subdir, '/') . '/' . $name;
    }

    /** @return array<int,string> stored paths for a multi-file field ($_FILES['x'] with arrays). */
    public static function gallery(array $files, string $subdir, int $max = 8): array
    {
        $out = [];
        if (!isset($files['tmp_name']) || !is_array($files['tmp_name'])) {
            return $out;
        }
        $count = min(count($files['tmp_name']), $max);
        for ($i = 0; $i < $count; $i++) {
            $single = [
                'name'     => $files['name'][$i] ?? '',
                'type'     => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error'    => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size'     => $files['size'][$i] ?? 0,
            ];
            $path = self::image($single, $subdir);
            if ($path) {
                $out[] = $path;
            }
        }
        return $out;
    }
}
