#!/bin/bash
# Comprehensive CI test script to verify everything works

echo "🔍 Testing Portfolio Web Tools - Local CI Check..."
echo "================================================="

# Function to print status
print_status() {
    if [ $1 -eq 0 ]; then
        echo "✅ $2"
    else
        echo "❌ $2"
    fi
}

# Check if composer.json is valid
echo "📋 Checking composer.json syntax..."
if command -v composer &> /dev/null; then
    composer validate --no-check-all --strict
    print_status $? "Composer validation"
else
    echo "⚠️  Composer not available, skipping validation"
fi

# Check PHP syntax
echo ""
echo "🔍 Checking PHP syntax..."
if command -v php &> /dev/null; then
    syntax_errors=0
    
    echo "  Checking src/ directory..."
    find src -name "*.php" -exec php -l {} \; | grep -v "No syntax errors" && syntax_errors=1
    
    echo "  Checking tests/ directory..."
    find tests -name "*.php" -exec php -l {} \; | grep -v "No syntax errors" && syntax_errors=1
    
    echo "  Checking config/config.php..."
    php -l config/config.php | grep -v "No syntax errors" && syntax_errors=1
    
    if [ $syntax_errors -eq 0 ]; then
        echo "✅ All PHP files have valid syntax"
    else
        echo "❌ PHP syntax errors found"
    fi
else
    echo "⚠️  PHP not available, skipping syntax check"
fi

# Create test directories
echo ""
echo "📁 Creating test directories..."
mkdir -p private/data/qr-generated
mkdir -p private/data/qr-uploads  
mkdir -p logs
mkdir -p cache
print_status $? "Directory creation"

# Test environment setup
echo ""
echo "⚙️  Setting up test environment..."
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || echo "DEBUG_MODE=true" > .env
    print_status $? "Environment file created"
else
    echo "✅ Environment file already exists"
fi

# Check YAML syntax if available
echo ""
echo "📝 Checking YAML syntax..."
if command -v yamllint &> /dev/null; then
    yamllint .github/workflows/ci.yml
    print_status $? "CI workflow YAML syntax"
elif command -v python3 &> /dev/null; then
    python3 -c "import yaml; yaml.safe_load(open('.github/workflows/ci.yml'))"
    print_status $? "CI workflow YAML syntax (Python)"
else
    echo "⚠️  YAML validator not available, skipping check"
fi

# Test autoloading if composer is available
echo ""
echo "🔄 Testing autoload..."
if command -v composer &> /dev/null && [ -f vendor/autoload.php ]; then
    php -r "require 'vendor/autoload.php'; echo 'Autoload works!' . PHP_EOL;"
    print_status $? "Composer autoload"
elif command -v composer &> /dev/null; then
    echo "⚠️  Run 'composer install' first"
else
    echo "⚠️  Composer not available"
fi

echo ""
echo "🎉 Basic checks completed!"
echo "========================================="
echo "📝 To run full tests:"
echo "   composer install"
echo "   composer test"
echo ""
echo "🚀 To start development server:"
echo "   php -S localhost:8000 -t public/"
