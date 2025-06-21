<?php
/**
 * Environment-aware configuration system
 * Loads configuration from .env file and provides secure defaults
 */

// Load environment variables from .env file if it exists
$env_file = dirname(__DIR__) . '/.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, '"\'');
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

/**
 * Get environment variable with fallback
 */
function env($key, $default = null) {
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false) {
        return $default;
    }
    
    // Convert string booleans to actual booleans
    if (in_array(strtolower($value), ['true', 'false'])) {
        return strtolower($value) === 'true';
    }
    
    return $value;
}

// Define base paths with environment awareness
$possible_root_dirs = [
    dirname(__DIR__),
    '/home/shayneeo_isadev/htdocs/sgn.is-a.dev',
    realpath(dirname(__DIR__))
];

$root_dir = null;
foreach ($possible_root_dirs as $dir) {
    if (is_dir($dir) && file_exists($dir . '/vendor/autoload.php')) {
        $root_dir = $dir;
        break;
    }
}

// Fallback to the standard relative path if we can't find vendor
if (!$root_dir) {
    $root_dir = dirname(__DIR__);
}

define('ROOT_DIR', $root_dir);
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('PRIVATE_DIR', ROOT_DIR . '/private');
define('DATA_DIR', PRIVATE_DIR . '/data');
define('INCLUDES_DIR', PRIVATE_DIR . '/includes');
define('ASSETS_DIR', PUBLIC_DIR . '/assets');

// QR code generator paths
define('QR_GENERATED_DIR', DATA_DIR . '/qr-generated');
define('QR_UPLOADS_DIR', DATA_DIR . '/qr-uploads');

// Environment-aware configuration
define('ENVIRONMENT', env('ENVIRONMENT', 'development'));
define('DEBUG_MODE', env('DEBUG_MODE', true));
define('ERROR_REPORTING_LEVEL', env('ERROR_REPORTING', E_ALL));

// Site information from environment
define('SITE_NAME', env('SITE_NAME', 'Universal Portfolio & Web Tools'));
define('SITE_URL', env('SITE_URL', 'http://localhost'));
define('SITE_DESCRIPTION', env('SITE_DESCRIPTION', 'A complete web platform combining portfolio showcase with powerful web utilities'));
define('CONTACT_EMAIL', env('CONTACT_EMAIL', 'contact@localhost'));
define('CONTACT_NAME', env('CONTACT_NAME', 'Portfolio Owner'));

// Security settings
define('SECRET_KEY', env('SECRET_KEY', 'change-this-in-production-' . bin2hex(random_bytes(16))));
define('SESSION_LIFETIME', env('SESSION_LIFETIME', 7200));
define('SESSION_NAME', env('SESSION_NAME', 'portfolio_session'));

// Rate limiting
define('RATE_LIMIT_ENABLED', env('RATE_LIMIT_ENABLED', true));
define('RATE_LIMIT_MAX_REQUESTS', env('RATE_LIMIT_MAX_REQUESTS', 100));
define('RATE_LIMIT_TIME_WINDOW', env('RATE_LIMIT_TIME_WINDOW', 3600));

// File upload settings
define('MAX_FILE_SIZE', env('MAX_FILE_SIZE', 10485760)); // 10MB
define('ALLOWED_EXTENSIONS', explode(',', env('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf,txt,zip,7z,doc,docx')));

// QR code settings
define('QR_CODE_SIZE', env('QR_CODE_SIZE', 300));
define('QR_CODE_FORMAT', env('QR_CODE_FORMAT', 'png'));
define('QR_CODE_ERROR_CORRECTION', env('QR_CODE_ERROR_CORRECTION', 'M'));

// External services
define('GOOGLE_ANALYTICS_ID', env('GOOGLE_ANALYTICS_ID', ''));

// Error reporting based on environment
if (ENVIRONMENT === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_DIR . '/logs/error.log');
} else {
    error_reporting(ERROR_REPORTING_LEVEL);
    ini_set('display_errors', '1');
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => parse_url(SITE_URL, PHP_URL_HOST),
        'secure' => ENVIRONMENT === 'production',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// Allowed images for get_image.php (moved from original config)
$allowed_images = [
    'scene.jpg',
    'blogger.jpg',
    'logo.jpg',
    'facebook.jpg',
    'github.jpg',
    'instagram.jpg',
    'x.jpg',
    'reddit.jpg'
];

// Create necessary directories if they don't exist
$required_dirs = [
    QR_GENERATED_DIR,
    QR_UPLOADS_DIR,
    ROOT_DIR . '/logs',
    ROOT_DIR . '/cache'
];

foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}
    'instagram.jpg',
    'github.jpg',
    'x.jpg',
    'reddit.jpg'
];

// Database configuration if needed
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'your_db_name');
// define('DB_USER', 'your_db_user');
// define('DB_PASS', 'your_db_password');
