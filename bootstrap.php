<?php
/**
 * Bootstrap file for PHPUnit tests
 * Sets up necessary constants and environment for testing
 */

// Load Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load configuration for tests
require_once __DIR__ . '/config/config.php';

// Ensure required directories exist for testing
$testDirs = [
    __DIR__ . '/private/data/qr-generated',
    __DIR__ . '/private/data/qr-uploads',
    __DIR__ . '/logs',
    __DIR__ . '/cache'
];

foreach ($testDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Set test environment
$_ENV['APP_ENV'] = 'testing';
putenv('APP_ENV=testing');
