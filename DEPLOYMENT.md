# Deployment Configuration

This document outlines various deployment strategies and configurations for the Universal Portfolio & Web Tools platform.

## Quick Deploy Options

### Railway (Recommended for Beginners)

[![Deploy on Railway](https://railway.app/button.svg)](https://railway.app/new/template?template=https://github.com/ShayNeeo/Portfolio-template)

**Features:**
- ✅ Automatic SSL certificates
- ✅ Git-based deployments
- ✅ Environment variable management
- ✅ Built-in monitoring
- ✅ Custom domains

**Setup:**
1. Click the Railway deploy button
2. Connect your GitHub account
3. Configure environment variables
4. Deploy automatically

### Heroku

[![Deploy to Heroku](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/ShayNeeo/Portfolio-template)

**Required Add-ons:**
- Heroku Postgres (for database features)
- Heroku Redis (for caching)

### Original Deployment Guide

## Pre-deployment Steps

1. **Run Composer Install**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. **Check File Permissions**
   - Ensure the `private/data/qr-generated/` directory is writable
   - Ensure the `private/data/qr-uploads/` directory is writable
   - Set appropriate permissions: `chmod 755` for directories, `chmod 644` for files

3. **Verify Directory Structure**
   ```
   project-root/
   ├── config/
   ├── private/
   ├── public/
   └── vendor/
   ```

## Post-deployment Verification

1. **Test QR Code Functionality**
   - Visit `/qr-code/debug.php` to check paths and autoloader
   - Delete `debug.php` after verification

2. **Check Error Logs**
   - Monitor PHP error logs for any path-related issues
   - Check Apache/Nginx error logs

3. **Test All Features**
   - QR code generation for links
   - QR code generation for file uploads
   - File downloads
   - URL shortener

## Common Issues and Solutions

### Autoloader Not Found
- Ensure `composer install` was run in the project root
- Check that `vendor/` directory exists and contains `autoload.php`
- Verify file permissions on vendor directory

### Path Resolution Issues
- The config.php now auto-detects the correct root directory
- If issues persist, manually set the correct path in config.php

### File Upload/QR Generation Issues
- Check write permissions on `private/data/qr-generated/` and `private/data/qr-uploads/`
- Ensure sufficient disk space

## Environment-specific Notes

### Shared Hosting
- Some shared hosts may have restrictions on file uploads or directory permissions
- Contact hosting provider if path resolution issues persist

### VPS/Dedicated Server
- Ensure PHP has sufficient memory and execution time for QR generation
- Consider setting up proper error logging

## Security Notes

- Never commit vendor/ directory to version control
- Always run `composer install` on the deployment server
- Remove debug.php file after troubleshooting
- Ensure private/ directory is not accessible via web browser
