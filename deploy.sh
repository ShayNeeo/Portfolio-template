#!/bin/bash
# Production deployment script
# This script prepares the application for production deployment

set -e

echo "🚀 Preparing for production deployment..."

# Check if running as root (not recommended for production)
if [ "$EUID" -eq 0 ]; then
    echo "⚠️  Warning: Running as root is not recommended for production"
fi

# Create production environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating production environment file..."
    cp .env.example .env
    
    # Generate a random secret key
    SECRET_KEY=$(openssl rand -hex 32)
    sed -i "s/change-this-to-a-random-32-char-string/$SECRET_KEY/g" .env
    sed -i "s/ENVIRONMENT=development/ENVIRONMENT=production/g" .env
    sed -i "s/DEBUG_MODE=true/DEBUG_MODE=false/g" .env
    
    echo "✅ Created .env file with production settings"
    echo "⚠️  Please review and update .env file with your specific configuration"
fi

# Install production dependencies
echo "📦 Installing production dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Create required directories
echo "📁 Creating required directories..."
mkdir -p private/data/qr-generated private/data/qr-uploads logs cache
chmod -R 755 private/data logs cache

# Set secure permissions
echo "🔒 Setting secure permissions..."
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 755 private/data logs cache
chmod 600 .env

# Clear any existing cache
echo "🧹 Clearing cache..."
rm -rf cache/*

# Verify configuration
echo "🔍 Verifying configuration..."
php -r "
require 'config/config.php';
echo 'Environment: ' . env('ENVIRONMENT', 'not set') . PHP_EOL;
echo 'Debug mode: ' . (env('DEBUG_MODE', 'true') === 'true' ? 'enabled' : 'disabled') . PHP_EOL;
echo 'Secret key: ' . (env('SECRET_KEY') !== 'change-this-to-a-random-32-char-string' ? 'configured' : 'NOT CONFIGURED!') . PHP_EOL;
"

echo ""
echo "✅ Production deployment preparation complete!"
echo ""
echo "📋 Next steps:"
echo "  1. Review and update .env file with your production settings"
echo "  2. Configure your web server to point to the public/ directory"
echo "  3. Ensure your web server has proper SSL/TLS configuration"
echo "  4. Set up monitoring and logging"
echo "  5. Configure backup strategy"
echo ""
echo "🔗 Web server configuration:"
echo "  - Document root: $(pwd)/public"
echo "  - Ensure URL rewriting is enabled"
echo "  - Configure proper security headers"
echo ""
