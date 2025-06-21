#!/bin/bash
# Simple CI test script to verify everything works

echo "🔍 Testing Portfolio Web Tools..."

# Check if composer.json is valid
echo "✅ Checking composer.json syntax..."
if command -v composer &> /dev/null; then
    composer validate --no-check-all --strict
else
    echo "⚠️  Composer not available, skipping validation"
fi

# Check PHP syntax
echo "✅ Checking PHP syntax..."
if command -v php &> /dev/null; then
    find src -name "*.php" -exec php -l {} \; | grep -v "No syntax errors"
    find tests -name "*.php" -exec php -l {} \; | grep -v "No syntax errors"
    php -l config/config.php
else
    echo "⚠️  PHP not available, skipping syntax check"
fi

# Create test directories
echo "✅ Creating test directories..."
mkdir -p private/data/qr-generated
mkdir -p private/data/qr-uploads
mkdir -p logs
mkdir -p cache

# Test environment setup
echo "✅ Setting up test environment..."
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || echo "DEBUG_MODE=true" > .env
fi

echo "🎉 Basic checks completed!"
echo "📝 To run full tests:"
echo "   composer install"
echo "   composer test"
