<?php
// Safe cache clear script that only runs when environment is ready
// This script should be run from the backend directory

// Check if we're in a proper Symfony environment
if (!file_exists('vendor/autoload.php')) {
    echo "Vendor autoload not found, skipping cache clear\n";
    exit(0);
}

// Check if .env or .env.local exists
if (!file_exists('.env') && !file_exists('.env.local')) {
    echo "No environment file found, skipping cache clear\n";
    exit(0);
}

// Try to clear cache, but don't fail if it doesn't work
try {
    require_once 'vendor/autoload.php';
    
    // Set default environment if not set
    if (!isset($_ENV['APP_ENV'])) {
        $_ENV['APP_ENV'] = 'dev';
    }
    
    // Run cache clear
    $output = shell_exec('php bin/console cache:clear --env=' . $_ENV['APP_ENV'] . ' 2>&1');
    echo $output ?: "Cache cleared successfully\n";
} catch (Exception $e) {
    echo "Cache clear failed: " . $e->getMessage() . "\n";
    echo "Continuing with installation...\n";
    exit(0);
}
