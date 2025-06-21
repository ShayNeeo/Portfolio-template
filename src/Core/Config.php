<?php

namespace PortfolioTools\Core;

/**
 * Configuration management class
 * Provides centralized access to configuration values
 */
class Config
{
    private static $instance = null;
    private $config = [];

    private function __construct()
    {
        $this->loadConfig();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig(): void
    {
        // Load from global constants set in config.php
        $this->config = [
            'site' => [
                'name' => defined('SITE_NAME') ? SITE_NAME : 'Portfolio',
                'url' => defined('SITE_URL') ? SITE_URL : 'http://localhost',
                'description' => defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : '',
                'contact_email' => defined('CONTACT_EMAIL') ? CONTACT_EMAIL : '',
                'contact_name' => defined('CONTACT_NAME') ? CONTACT_NAME : '',
            ],
            'paths' => [
                'root' => defined('ROOT_DIR') ? ROOT_DIR : dirname(__DIR__, 2),
                'public' => defined('PUBLIC_DIR') ? PUBLIC_DIR : '',
                'private' => defined('PRIVATE_DIR') ? PRIVATE_DIR : '',
                'data' => defined('DATA_DIR') ? DATA_DIR : '',
                'assets' => defined('ASSETS_DIR') ? ASSETS_DIR : '',
                'qr_generated' => defined('QR_GENERATED_DIR') ? QR_GENERATED_DIR : '',
                'qr_uploads' => defined('QR_UPLOADS_DIR') ? QR_UPLOADS_DIR : '',
            ],
            'security' => [
                'secret_key' => defined('SECRET_KEY') ? SECRET_KEY : '',
                'session_lifetime' => defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 7200,
                'session_name' => defined('SESSION_NAME') ? SESSION_NAME : 'portfolio_session',
                'rate_limit_enabled' => defined('RATE_LIMIT_ENABLED') ? RATE_LIMIT_ENABLED : true,
                'rate_limit_max_requests' => defined('RATE_LIMIT_MAX_REQUESTS') ? RATE_LIMIT_MAX_REQUESTS : 100,
                'rate_limit_time_window' => defined('RATE_LIMIT_TIME_WINDOW') ? RATE_LIMIT_TIME_WINDOW : 3600,
            ],
            'uploads' => [
                'max_file_size' => defined('MAX_FILE_SIZE') ? MAX_FILE_SIZE : 10485760,
                'allowed_extensions' => defined('ALLOWED_EXTENSIONS') ? ALLOWED_EXTENSIONS : ['jpg', 'png', 'pdf'],
            ],
            'qr' => [
                'size' => defined('QR_CODE_SIZE') ? QR_CODE_SIZE : 300,
                'format' => defined('QR_CODE_FORMAT') ? QR_CODE_FORMAT : 'png',
                'error_correction' => defined('QR_CODE_ERROR_CORRECTION') ? QR_CODE_ERROR_CORRECTION : 'M',
            ],
            'environment' => [
                'type' => defined('ENVIRONMENT') ? ENVIRONMENT : 'development',
                'debug' => defined('DEBUG_MODE') ? DEBUG_MODE : true,
            ]
        ];
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    public function all(): array
    {
        return $this->config;
    }
}
