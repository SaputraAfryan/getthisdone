# Production Deployment Guide

## Pre-deployment Checklist

### 1. Environment Configuration
- [ ] Copy `.env.example` to `.env`
- [ ] Set `CI_ENVIRONMENT = production`
- [ ] Update `app.baseURL` with your domain
- [ ] Generate secure encryption key: `php spark key:generate`
- [ ] Configure database credentials
- [ ] Enable HTTPS with `app.forceGlobalSecureRequests = true`

### 2. Security Configuration
- [ ] Enable CSRF protection
- [ ] Configure Content Security Policy
- [ ] Set up proper file permissions
- [ ] Hide sensitive files via .htaccess
- [ ] Enable security headers

### 3. Performance Optimization
- [ ] Enable config caching
- [ ] Enable locator caching
- [ ] Use database sessions instead of file sessions
- [ ] Configure proper cache settings
- [ ] Optimize autoloader with `composer install --optimize-autoloader --no-dev`

### 4. Database Setup
- [ ] Create production database
- [ ] Run migrations: `php spark migrate`
- [ ] Seed initial data: `php spark db:seed ProductionSeeder`
- [ ] Create database session table

### 5. Web Server Configuration

#### Apache
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /path/to/your/app/public
    
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    
    <Directory /path/to/your/app/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security headers
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /path/to/your/app/public;
    index index.php;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Content-Type-Options nosniff always;
    add_header X-Frame-Options DENY always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to sensitive files
    location ~ /\.(env|git) {
        deny all;
    }
}
```

### 6. File Permissions
```bash
# Set proper ownership
chown -R www-data:www-data /path/to/your/app

# Set directory permissions
find /path/to/your/app -type d -exec chmod 755 {} \;

# Set file permissions
find /path/to/your/app -type f -exec chmod 644 {} \;

# Writable directories
chmod -R 755 /path/to/your/app/writable
```

### 7. Monitoring & Logging
- [ ] Set up log rotation for CodeIgniter logs
- [ ] Configure error monitoring (e.g., Sentry)
- [ ] Set up uptime monitoring
- [ ] Configure database backup schedule

### 8. SSL Certificate
- [ ] Install SSL certificate
- [ ] Configure automatic renewal (Let's Encrypt)
- [ ] Test SSL configuration

### 9. Backup Strategy
- [ ] Database backups
- [ ] File system backups
- [ ] Test restore procedures

## Deployment Commands

```bash
# Make deployment script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

## Post-deployment Testing
- [ ] Test all major functionality
- [ ] Verify SSL certificate
- [ ] Check security headers
- [ ] Test form submissions (CSRF)
- [ ] Verify database connections
- [ ] Check error logging

## Maintenance Commands

```bash
# Clear all caches
php spark cache:clear

# Clear config cache
php spark config:clear

# Rebuild config cache
php spark config:cache

# Run migrations
php spark migrate

# Check system status
php spark about
```

## Security Best Practices
1. Keep CodeIgniter and dependencies updated
2. Regular security audits
3. Monitor logs for suspicious activity
4. Use strong passwords and 2FA
5. Regular database backups
6. Implement rate limiting
7. Use HTTPS everywhere
8. Validate and sanitize all inputs

## Performance Tips
1. Enable OPcache in PHP
2. Use Redis/Memcached for sessions
3. Implement CDN for static assets
4. Optimize database queries
5. Use database indexing
6. Enable gzip compression
7. Optimize images and assets