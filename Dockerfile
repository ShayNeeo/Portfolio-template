FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache modules
RUN a2enmod rewrite headers

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application code
COPY --chown=www-data:www-data . /var/www/html

# Create required directories
RUN mkdir -p private/data/qr-generated private/data/qr-uploads logs cache \
    && chown -R www-data:www-data private/data logs cache \
    && chmod -R 755 private/data logs cache

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create .htaccess for clean URLs
RUN echo "RewriteEngine On\n\
RewriteCond %{REQUEST_FILENAME} !-f\n\
RewriteCond %{REQUEST_FILENAME} !-d\n\
RewriteRule ^(.*)$ index.php [QSA,L]\n\
\n\
# Security headers\n\
Header always set X-Content-Type-Options nosniff\n\
Header always set X-Frame-Options DENY\n\
Header always set X-XSS-Protection \"1; mode=block\"\n\
Header always set Referrer-Policy \"strict-origin-when-cross-origin\"\n\
Header always set Permissions-Policy \"geolocation=(), microphone=(), camera=()\"\n\
\n\
# Cache static assets\n\
<IfModule mod_expires.c>\n\
    ExpiresActive On\n\
    ExpiresByType text/css \"access plus 1 year\"\n\
    ExpiresByType application/javascript \"access plus 1 year\"\n\
    ExpiresByType image/png \"access plus 1 year\"\n\
    ExpiresByType image/jpg \"access plus 1 year\"\n\
    ExpiresByType image/jpeg \"access plus 1 year\"\n\
    ExpiresByType image/gif \"access plus 1 year\"\n\
</IfModule>" > /var/www/html/public/.htaccess

# Set production environment
ENV ENVIRONMENT=production
ENV DEBUG_MODE=false

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
