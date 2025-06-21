<?php
/**
 * Legacy helper functions for backward compatibility
 * These functions bridge the old and new architecture
 */

// Include the new configuration system
require_once dirname(dirname(__DIR__)) . '/config/config.php';

// Include Composer autoloader for new classes
require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use PortfolioTools\Core\Config;
use PortfolioTools\Security\Security;
use PortfolioTools\Utils\FileHandler;

/**
 * Get the URL for an asset file
 * 
 * @param string $path Path relative to the assets directory
 * @return string URL to the asset
 */
function asset($path) {
    return '/assets/' . ltrim($path, '/');
}

/**
 * Get configuration value (using new Config class)
 * 
 * @param string $key Configuration key
 * @param mixed $default Default value
 * @return mixed Configuration value
 */
function config($key, $default = null) {
    return Config::getInstance()->get($key, $default);
}

/**
 * Get the absolute path to an image
 * 
 * @param string $image Image filename
 * @return string Absolute path to the image
 */
function get_image_path($image) {
    return ASSETS_DIR . '/images/' . basename($image);
}

/**
 * Load links from JSON file
 * 
 * @param string $filePath Path to the JSON file
 * @return array Array of links
 */
function getLinks($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    $json_data = file_get_contents($filePath);
    $links = json_decode($json_data, true);
    return is_array($links) ? $links : [];
}

/**
 * Include a template file with variables
 * 
 * @param string $template Path to template file relative to includes directory
 * @param array $vars Variables to extract into template scope
 * @return void
 */
function include_template($template, $vars = []) {
    $templatePath = INCLUDES_DIR . '/' . $template;
    
    if (!file_exists($templatePath)) {
        throw new Exception("Template not found: {$template}");
    }
    
    // Extract variables into the local scope
    extract($vars, EXTR_SKIP);
    
    include $templatePath;
}

/**
 * Sanitize user input (using new Security class)
 * 
 * @param string $input User input
 * @return string Sanitized input
 */
function sanitize_input($input) {
    return Security::sanitizeInput($input);
}

/**
 * Generate CSRF token (using new Security class)
 * 
 * @return string CSRF token
 */
function csrf_token() {
    return Security::generateCSRFToken();
}

/**
 * Verify CSRF token (using new Security class)
 * 
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verify_csrf_token($token) {
    return Security::verifyCSRFToken($token);
}

/**
 * Format file size (using new FileHandler class)
 * 
 * @param int $bytes File size in bytes
 * @return string Formatted file size
 */
function format_file_size($bytes) {
    return FileHandler::formatFileSize($bytes);
}

/**
 * Check if file extension is allowed
 * 
 * @param string $filename Filename to check
 * @return bool True if allowed, false otherwise
 */
function is_allowed_file_extension($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, ALLOWED_EXTENSIONS);
}

/**
 * Generate a secure random filename
 * 
 * @param string $originalName Original filename
 * @return string Secure filename
 */
function generate_secure_filename($originalName) {
    return Security::generateSecureFilename($originalName);
}

/**
 * Log message to file
 * 
 * @param string $message Message to log
 * @param string $level Log level (error, warning, info, debug)
 * @return void
 */
function log_message($message, $level = 'info') {
    if (!defined('ROOT_DIR')) {
        return;
    }
    
    $logFile = ROOT_DIR . '/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    
    // Ensure logs directory exists
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Check rate limit for current user
 * 
 * @param string $action Action identifier
 * @param int $maxRequests Maximum requests allowed
 * @param int $timeWindow Time window in seconds
 * @return bool True if allowed, false if rate limited
 */
function check_rate_limit($action, $maxRequests = null, $timeWindow = null) {
    $maxRequests = $maxRequests ?? RATE_LIMIT_MAX_REQUESTS;
    $timeWindow = $timeWindow ?? RATE_LIMIT_TIME_WINDOW;
    
    $identifier = $_SERVER['REMOTE_ADDR'] . '_' . $action;
    return Security::checkRateLimit($identifier, $maxRequests, $timeWindow);
}

/**
 * Redirect to URL
 * 
 * @param string $url URL to redirect to
 * @param int $statusCode HTTP status code
 * @return void
 */
function redirect($url, $statusCode = 302) {
    if (!headers_sent()) {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }
}

/**
 * Get current URL
 * 
 * @return string Current URL
 */
function current_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    return "{$protocol}://{$host}{$uri}";
}

/**
 * Check if request is AJAX
 * 
 * @return bool True if AJAX request, false otherwise
 */
function is_ajax_request() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Return JSON response
 * 
 * @param mixed $data Data to return
 * @param int $statusCode HTTP status code
 * @return void
 */
function json_response($data, $statusCode = 200) {
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
    }
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Get environment variable with fallback
 * 
 * @param string $key Environment variable key
 * @param mixed $default Default value
 * @return mixed Environment variable value or default
 */
function env($key, $default = null) {
    return config('environment.' . strtolower($key), $default);
}
