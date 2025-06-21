#!/usr/bin/env php
<?php
/**
 * Portfolio Tools CLI - Command Line Interface for maintenance tasks
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

use PortfolioTools\Utils\FileHandler;

// Define CLI colors
class Colors {
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const MAGENTA = "\033[35m";
    const CYAN = "\033[36m";
    const WHITE = "\033[37m";
    const RESET = "\033[0m";
}

/**
 * Display help information
 */
function showHelp() {
    echo Colors::CYAN . "Portfolio Tools CLI" . Colors::RESET . "\n";
    echo "A command line interface for maintenance and management tasks.\n\n";
    
    echo Colors::YELLOW . "Usage:" . Colors::RESET . "\n";
    echo "  php cli.php <command> [options]\n\n";
    
    echo Colors::YELLOW . "Available Commands:" . Colors::RESET . "\n";
    echo "  " . Colors::GREEN . "clean" . Colors::RESET . "          Clean old files and temporary data\n";
    echo "  " . Colors::GREEN . "setup" . Colors::RESET . "          Setup directories and permissions\n";
    echo "  " . Colors::GREEN . "backup" . Colors::RESET . "         Create a backup of important data\n";
    echo "  " . Colors::GREEN . "check" . Colors::RESET . "          Check system requirements and health\n";
    echo "  " . Colors::GREEN . "update" . Colors::RESET . "         Update dependencies and clear cache\n";
    echo "  " . Colors::GREEN . "stats" . Colors::RESET . "          Display usage statistics\n";
    echo "  " . Colors::GREEN . "help" . Colors::RESET . "           Show this help message\n\n";
    
    echo Colors::YELLOW . "Options:" . Colors::RESET . "\n";
    echo "  --verbose, -v    Verbose output\n";
    echo "  --quiet, -q      Quiet mode (minimal output)\n";
    echo "  --dry-run        Show what would be done without making changes\n\n";
    
    echo Colors::YELLOW . "Examples:" . Colors::RESET . "\n";
    echo "  php cli.php clean --verbose\n";
    echo "  php cli.php backup --dry-run\n";
    echo "  php cli.php check\n";
}

/**
 * Clean old files and temporary data
 */
function cleanFiles($options = []) {
    $verbose = isset($options['verbose']);
    $dryRun = isset($options['dry-run']);
    
    echo Colors::BLUE . "Cleaning old files..." . Colors::RESET . "\n";
    
    $directories = [
        QR_GENERATED_DIR => 7 * 24 * 3600, // 7 days
        QR_UPLOADS_DIR => 30 * 24 * 3600,  // 30 days
        ROOT_DIR . '/logs' => 30 * 24 * 3600, // 30 days
        ROOT_DIR . '/cache' => 1 * 24 * 3600, // 1 day
    ];
    
    $totalCleaned = 0;
    
    foreach ($directories as $dir => $maxAge) {
        if (!is_dir($dir)) {
            if ($verbose) {
                echo "  Directory not found: $dir\n";
            }
            continue;
        }
        
        if ($dryRun) {
            echo "  [DRY RUN] Would clean: $dir\n";
        } else {
            $cleaned = FileHandler::cleanOldFiles($dir, $maxAge);
            $totalCleaned += $cleaned;
            
            if ($verbose || $cleaned > 0) {
                echo "  Cleaned $cleaned files from " . basename($dir) . "\n";
            }
        }
    }
    
    if (!$dryRun) {
        echo Colors::GREEN . "Cleanup completed. Removed $totalCleaned files." . Colors::RESET . "\n";
    }
}

/**
 * Setup directories and permissions
 */
function setupDirectories($options = []) {
    $verbose = isset($options['verbose']);
    
    echo Colors::BLUE . "Setting up directories..." . Colors::RESET . "\n";
    
    $directories = [
        QR_GENERATED_DIR,
        QR_UPLOADS_DIR,
        ROOT_DIR . '/logs',
        ROOT_DIR . '/cache',
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo Colors::GREEN . "  Created: $dir" . Colors::RESET . "\n";
            } else {
                echo Colors::RED . "  Failed to create: $dir" . Colors::RESET . "\n";
            }
        } else if ($verbose) {
            echo "  Already exists: $dir\n";
        }
        
        // Set permissions
        if (is_writable($dir)) {
            if ($verbose) {
                echo "  Permissions OK: $dir\n";
            }
        } else {
            echo Colors::YELLOW . "  Warning: $dir is not writable" . Colors::RESET . "\n";
        }
    }
    
    echo Colors::GREEN . "Setup completed." . Colors::RESET . "\n";
}

/**
 * Create backup of important data
 */
function createBackup($options = []) {
    $verbose = isset($options['verbose']);
    $dryRun = isset($options['dry-run']);
    
    echo Colors::BLUE . "Creating backup..." . Colors::RESET . "\n";
    
    $backupDir = ROOT_DIR . '/backups';
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = "$backupDir/portfolio_backup_$timestamp.tar.gz";
    
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $filesToBackup = [
        'private/data/links.json',
        'private/data/qr-uploads/*',
        '.env',
        'config/',
        'public/assets/images/',
    ];
    
    if ($dryRun) {
        echo "  [DRY RUN] Would create backup: $backupFile\n";
        foreach ($filesToBackup as $file) {
            echo "    - $file\n";
        }
    } else {
        // Create tar command
        $excludes = '--exclude=vendor --exclude=node_modules --exclude=.git --exclude=tests --exclude=coverage';
        $command = "tar -czf $backupFile $excludes " . implode(' ', $filesToBackup) . " 2>/dev/null";
        
        if ($verbose) {
            echo "  Running: $command\n";
        }
        
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($backupFile)) {
            $size = FileHandler::formatFileSize(filesize($backupFile));
            echo Colors::GREEN . "  Backup created: $backupFile ($size)" . Colors::RESET . "\n";
        } else {
            echo Colors::RED . "  Backup failed!" . Colors::RESET . "\n";
        }
    }
}

/**
 * Check system requirements and health
 */
function checkSystem($options = []) {
    $verbose = isset($options['verbose']);
    
    echo Colors::BLUE . "System Health Check" . Colors::RESET . "\n";
    echo "==================\n";
    
    // PHP Version
    $phpVersion = PHP_VERSION;
    $minVersion = '7.4.0';
    $isPhpOk = version_compare($phpVersion, $minVersion, '>=');
    
    echo "PHP Version: " . ($isPhpOk ? Colors::GREEN : Colors::RED) . 
         "$phpVersion" . Colors::RESET . 
         ($isPhpOk ? " ✓" : " ✗ (minimum: $minVersion)") . "\n";
    
    // Required Extensions
    $requiredExtensions = ['gd', 'json', 'mbstring'];
    echo "\nPHP Extensions:\n";
    
    foreach ($requiredExtensions as $ext) {
        $loaded = extension_loaded($ext);
        echo "  $ext: " . ($loaded ? Colors::GREEN . "loaded ✓" : Colors::RED . "missing ✗") . Colors::RESET . "\n";
    }
    
    // Optional Extensions
    if ($verbose) {
        $optionalExtensions = ['curl', 'zip', 'imagick'];
        echo "\nOptional Extensions:\n";
        
        foreach ($optionalExtensions as $ext) {
            $loaded = extension_loaded($ext);
            echo "  $ext: " . ($loaded ? Colors::GREEN . "loaded ✓" : Colors::YELLOW . "not loaded") . Colors::RESET . "\n";
        }
    }
    
    // Directory Permissions
    echo "\nDirectory Permissions:\n";
    $directories = [
        QR_GENERATED_DIR => 'QR Generated',
        QR_UPLOADS_DIR => 'QR Uploads',
        ROOT_DIR . '/logs' => 'Logs',
        ROOT_DIR . '/cache' => 'Cache',
    ];
    
    foreach ($directories as $dir => $name) {
        $exists = is_dir($dir);
        $writable = $exists && is_writable($dir);
        
        if (!$exists) {
            echo "  $name: " . Colors::RED . "not found ✗" . Colors::RESET . "\n";
        } else if (!$writable) {
            echo "  $name: " . Colors::YELLOW . "not writable ⚠" . Colors::RESET . "\n";
        } else {
            echo "  $name: " . Colors::GREEN . "OK ✓" . Colors::RESET . "\n";
        }
    }
    
    // Configuration
    echo "\nConfiguration:\n";
    echo "  Environment: " . (ENVIRONMENT === 'production' ? Colors::RED : Colors::GREEN) . ENVIRONMENT . Colors::RESET . "\n";
    echo "  Debug Mode: " . (DEBUG_MODE ? Colors::YELLOW . "enabled" : Colors::GREEN . "disabled") . Colors::RESET . "\n";
    echo "  Max Upload Size: " . FileHandler::formatFileSize(MAX_FILE_SIZE) . "\n";
    
    // Disk Space
    if ($verbose) {
        echo "\nDisk Space:\n";
        $freeBytes = disk_free_space(ROOT_DIR);
        $totalBytes = disk_total_space(ROOT_DIR);
        $usedBytes = $totalBytes - $freeBytes;
        $usedPercent = round(($usedBytes / $totalBytes) * 100, 1);
        
        echo "  Used: " . FileHandler::formatFileSize($usedBytes) . " ($usedPercent%)\n";
        echo "  Free: " . FileHandler::formatFileSize($freeBytes) . "\n";
        echo "  Total: " . FileHandler::formatFileSize($totalBytes) . "\n";
    }
}

/**
 * Display usage statistics
 */
function showStats($options = []) {
    echo Colors::BLUE . "Usage Statistics" . Colors::RESET . "\n";
    echo "================\n";
    
    // Count files
    $qrGenerated = 0;
    $qrUploads = 0;
    $totalSize = 0;
    
    if (is_dir(QR_GENERATED_DIR)) {
        $files = glob(QR_GENERATED_DIR . '/*.png');
        $qrGenerated = count($files);
    }
    
    if (is_dir(QR_UPLOADS_DIR)) {
        $files = glob(QR_UPLOADS_DIR . '/*');
        $qrUploads = count($files);
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
            }
        }
    }
    
    echo "QR Codes Generated: " . Colors::GREEN . $qrGenerated . Colors::RESET . "\n";
    echo "Files Uploaded: " . Colors::GREEN . $qrUploads . Colors::RESET . "\n";
    echo "Total Storage Used: " . Colors::GREEN . FileHandler::formatFileSize($totalSize) . Colors::RESET . "\n";
    
    // Log file analysis
    $logFile = ROOT_DIR . '/logs/app.log';
    if (file_exists($logFile)) {
        $logSize = filesize($logFile);
        $logLines = count(file($logFile));
        echo "Log Entries: " . Colors::GREEN . $logLines . Colors::RESET . "\n";
        echo "Log File Size: " . Colors::GREEN . FileHandler::formatFileSize($logSize) . Colors::RESET . "\n";
    }
}

// Parse command line arguments
$arguments = array_slice($argv, 1);
$command = $arguments[0] ?? 'help';
$options = [];

// Parse options
foreach ($arguments as $arg) {
    if (strpos($arg, '--') === 0) {
        $options[substr($arg, 2)] = true;
    } elseif (strpos($arg, '-') === 0) {
        $short = substr($arg, 1);
        switch ($short) {
            case 'v':
                $options['verbose'] = true;
                break;
            case 'q':
                $options['quiet'] = true;
                break;
        }
    }
}

// Execute command
switch ($command) {
    case 'clean':
        cleanFiles($options);
        break;
    
    case 'setup':
        setupDirectories($options);
        break;
    
    case 'backup':
        createBackup($options);
        break;
    
    case 'check':
        checkSystem($options);
        break;
    
    case 'update':
        echo Colors::BLUE . "Updating dependencies..." . Colors::RESET . "\n";
        system('composer update');
        cleanFiles(['dry-run' => false]);
        echo Colors::GREEN . "Update completed." . Colors::RESET . "\n";
        break;
    
    case 'stats':
        showStats($options);
        break;
    
    case 'help':
    default:
        showHelp();
        break;
}
