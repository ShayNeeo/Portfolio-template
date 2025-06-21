<?php

namespace PortfolioTools\Utils;

/**
 * File handling utilities
 */
class FileHandler
{
    /**
     * Save uploaded file securely
     */
    public static function saveUploadedFile(array $file, string $uploadDir): array
    {
        $result = ['success' => false, 'filename' => null, 'error' => null];

        try {
            // Generate secure filename
            $filename = \PortfolioTools\Security\Security::generateSecureFilename($file['name']);
            $filepath = $uploadDir . '/' . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $result['success'] = true;
                $result['filename'] = $filename;
            } else {
                $result['error'] = 'Failed to save file';
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Delete file safely
     */
    public static function deleteFile(string $filepath): bool
    {
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    /**
     * Get file size in human readable format
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Check if directory is writable and create if needed
     */
    public static function ensureDirectoryExists(string $directory): bool
    {
        if (!is_dir($directory)) {
            return mkdir($directory, 0755, true);
        }
        return is_writable($directory);
    }

    /**
     * Clean old files from directory
     */
    public static function cleanOldFiles(string $directory, int $maxAge = 86400): int
    {
        $deletedCount = 0;
        $now = time();

        if (!is_dir($directory)) {
            return $deletedCount;
        }

        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                continue;
            }

            $filepath = $directory . '/' . $file;
            if (is_file($filepath) && ($now - filemtime($filepath)) > $maxAge) {
                if (unlink($filepath)) {
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }
}
