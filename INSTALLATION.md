# Installation Guide

## System Requirements

### Minimum Requirements
- **PHP 7.4+** (PHP 8.1+ recommended for best performance)
- **Composer** 2.0+
- **Web Server** (Apache, Nginx, or PHP built-in server)
- **PHP Extensions**:
  - `gd` - For image processing and QR code generation
  - `json` - For data handling
  - `mbstring` - For string manipulation
  - `curl` - For external API calls (optional)
  - `zip` - For file compression (optional)

### Recommended Requirements
- **PHP 8.1+**
- **MySQL 8.0+** or **SQLite** (for future database features)
- **Redis** (for caching and sessions)
- **SSL Certificate** (for production)

## Installation Methods

### Method 1: Docker Installation (Recommended)

#### Prerequisites
- Docker 20.0+
- Docker Compose 2.0+

#### Steps
1. **Clone the repository**
   ```bash
   git clone https://github.com/ShayNeeo/Portfolio-template.git
   cd Portfolio-template
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env file with your settings
   ```

3. **Start services**
   ```bash
   docker-compose up -d
   ```

4. **Access the application**
   - Main site: http://localhost:8080
   - phpMyAdmin: http://localhost:8081

#### Docker Benefits
- ✅ Isolated environment
- ✅ Consistent across different systems
- ✅ Includes database and admin tools
- ✅ Easy scaling and deployment

### Method 2: Manual Installation

#### Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4+ with required extensions
- Composer

#### Steps

1. **Clone and setup**
   ```bash
   git clone https://github.com/ShayNeeo/Portfolio-template.git
   cd Portfolio-template
   composer install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your configuration
   ```

3. **Set permissions**
   ```bash
   chmod -R 755 private/data
   chmod -R 755 logs
   chmod -R 755 cache
   ```

4. **Configure web server**
   
   **Apache (.htaccess already included)**
   ```apache
   <VirtualHost *:80>
       DocumentRoot /path/to/Portfolio-template/public
       ServerName your-domain.com
       
       <Directory /path/to/Portfolio-template/public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

   **Nginx**
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /path/to/Portfolio-template/public;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

5. **Test installation**
   ```bash
   # For development
   php -S localhost:8000 -t public/
   ```

### Method 3: Shared Hosting

#### Compatible Hosts
- ✅ **cPanel Hosting** - Most shared hosts
- ✅ **Plesk Hosting** - Modern shared hosting
- ✅ **DirectAdmin** - Alternative control panel

#### Steps
1. **Download and extract**
   - Download ZIP from GitHub
   - Extract to your hosting account's public_html folder

2. **Install dependencies**
   ```bash
   # If Composer is available
   composer install --no-dev --optimize-autoloader
   
   # If no Composer, download vendor.zip from releases
   ```

3. **Configure**
   - Upload `.env.example` as `.env`
   - Edit `.env` with your hosting details
   - Set folder permissions via cPanel File Manager

4. **Verify setup**
   - Visit your domain
   - Check error logs if issues occur

## Configuration

### Environment Variables

Create `.env` file from `.env.example`:

```bash
# Basic site settings
SITE_NAME="Your Portfolio"
SITE_URL="https://yourdomain.com"
CONTACT_EMAIL="your@email.com"

# Security
SECRET_KEY="generate-32-character-random-string"
DEBUG_MODE=false

# File uploads
MAX_FILE_SIZE=10485760
ALLOWED_EXTENSIONS="jpg,jpeg,png,gif,pdf,txt,zip"
```

### Security Checklist

- [ ] Change `SECRET_KEY` to a random 32-character string
- [ ] Set `DEBUG_MODE=false` in production
- [ ] Configure proper file permissions (755 for directories, 644 for files)
- [ ] Enable HTTPS with SSL certificate
- [ ] Set up regular backups
- [ ] Keep dependencies updated

### Performance Optimization

1. **Enable OPcache** (php.ini):
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.max_accelerated_files=4000
   ```

2. **Configure caching**:
   ```bash
   # Redis for sessions (optional)
   REDIS_HOST=localhost
   REDIS_PORT=6379
   ```

3. **Web server optimization**:
   - Enable gzip compression
   - Set cache headers for static assets
   - Use CDN for assets

## Troubleshooting

### Common Issues

**1. Composer install fails**
```bash
# Clear cache and retry
composer clear-cache
composer install --no-cache
```

**2. Permission errors**
```bash
# Fix permissions
sudo chown -R www-data:www-data /path/to/project
chmod -R 755 private/data logs cache
```

**3. QR codes not generating**
- Check GD extension: `php -m | grep -i gd`
- Verify write permissions on `private/data/qr-generated`

**4. File uploads failing**
- Check `upload_max_filesize` in php.ini
- Verify `private/data/qr-uploads` permissions

### Getting Help

- 📖 **Documentation**: Check the wiki
- 🐛 **Bug Reports**: Open an issue on GitHub
- 💬 **Community**: Join our Discord server
- 📧 **Email**: Contact maintainers

## Next Steps

After installation:

1. **Customize your portfolio** - Edit content and styling
2. **Configure tools** - Set up QR generation and file sharing
3. **Add content** - Upload your projects and information
4. **Set up analytics** - Add Google Analytics tracking
5. **Deploy to production** - Use Docker or traditional hosting

## Deployment Options

### Production Deployment

#### Using Docker
```bash
# Production compose
docker-compose -f docker-compose.prod.yml up -d
```

#### Traditional Hosting
- Upload files via FTP/SFTP
- Configure web server
- Set up SSL certificate
- Configure backups

#### Cloud Platforms
- **Railway**: One-click deployment
- **Heroku**: Git-based deployment
- **DigitalOcean**: Droplet or App Platform
- **AWS**: EC2 or Elastic Beanstalk

## Maintenance

### Regular Tasks
- Update dependencies monthly
- Clean old files weekly
- Monitor error logs
- Backup data regularly
- Update content as needed

### Security Updates
```bash
# Check for security updates
composer audit

# Update dependencies
composer update
```
