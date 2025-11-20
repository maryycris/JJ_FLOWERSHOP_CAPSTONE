# Railway Deployment Guide - JJ Flowershop

## 🚀 Quick Start

### 1. Prerequisites
- Railway account (sign up at https://railway.app)
- GitHub repository with your code
- Database service (MySQL/PostgreSQL) - Railway provides this

### 2. Deploy to Railway

#### Option A: Deploy via Railway Dashboard
1. Go to https://railway.app
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Connect your GitHub account and select your repository
5. Railway will auto-detect it's a PHP/Laravel app
6. Add a MySQL or PostgreSQL database service
7. Configure environment variables (see below)
8. Deploy!

#### Option B: Deploy via Railway CLI
```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Initialize project
railway init

# Link to existing project
railway link

# Deploy
railway up
```

---

## 📋 Environment Variables

Set these in Railway Dashboard → Your Service → Variables:

### Required Variables

```env
APP_NAME="JJ Flowershop"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app

# Database (Railway provides these automatically)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# Mail Configuration (Update with your SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Social Login (if using)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://your-app-name.railway.app/auth/google/callback

FACEBOOK_CLIENT_ID=your-facebook-app-id
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret
FACEBOOK_REDIRECT_URI=https://your-app-name.railway.app/auth/facebook/callback

# PayMongo (if using)
PAYMONGO_SECRET_KEY=your-paymongo-secret-key
PAYMONGO_PUBLIC_KEY=your-paymongo-public-key
```

### Generate APP_KEY
```bash
# Locally run:
php artisan key:generate --show

# Copy the output and set it as APP_KEY in Railway
```

---

## 🗄️ Database Setup

### Using Railway MySQL Service
1. Add MySQL service in Railway
2. Railway automatically provides connection variables
3. Use the template variables shown above: `${{MySQL.MYSQLHOST}}` etc.
4. Migrations will run automatically via `start.sh`

### Manual Database Setup
If you prefer external database:
```env
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=jj_flowershop
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

---

## 📁 File Storage

Railway uses ephemeral storage. For file uploads, use:

### Option 1: Railway Volume (Recommended for small files)
1. Add a Volume service in Railway
2. Mount it to `/app/storage/app/public`
3. Update `config/filesystems.php` if needed

### Option 2: Cloud Storage (Recommended for production)
Use AWS S3, DigitalOcean Spaces, or similar:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Update `config/filesystems.php`:
```php
'disks' => [
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    ],
],
```

---

## 🔧 Build Configuration

Railway uses `nixpacks.toml` for build configuration. The file includes:
- PHP 8.3 setup
- Composer install
- Cache optimization
- Start script execution

---

## 🚦 Deployment Process

1. **Build Phase:**
   - Installs PHP 8.3 and Composer
   - Runs `composer install --no-dev --optimize-autoloader`
   - Caches config, routes, and views

2. **Start Phase:**
   - Runs `start.sh` script
   - Creates storage directories
   - Clears caches
   - Runs database migrations
   - Starts PHP server on Railway's PORT

---

## 🔍 Troubleshooting

### Migration Errors
- Check database connection variables
- Ensure database service is running
- Check logs: Railway Dashboard → Deployments → View Logs

### Storage Permission Errors
- Storage directories are created automatically
- If issues persist, check Railway logs

### APP_KEY Missing
- Generate key locally: `php artisan key:generate --show`
- Set it in Railway environment variables

### Port Issues
- Railway sets PORT automatically
- `start.sh` uses `$PORT` environment variable
- Don't hardcode port numbers

### File Upload Issues
- Use cloud storage (S3, Spaces) for production
- Or configure Railway Volume for persistent storage

---

## 📊 Monitoring

- **Logs:** Railway Dashboard → Your Service → Logs
- **Metrics:** Railway Dashboard → Your Service → Metrics
- **Deployments:** Railway Dashboard → Your Service → Deployments

---

## 🔐 Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` set
- [ ] Database credentials secure
- [ ] Mail credentials secure
- [ ] Social login credentials secure
- [ ] PayMongo keys secure
- [ ] HTTPS enabled (Railway provides automatically)
- [ ] Environment variables not committed to git

---

## 📝 Post-Deployment

1. **Test the application:**
   - Visit your Railway URL
   - Test login/registration
   - Test file uploads
   - Test database operations

2. **Set up custom domain (optional):**
   - Railway Dashboard → Your Service → Settings → Domains
   - Add your domain
   - Configure DNS records

3. **Enable monitoring:**
   - Set up error tracking (Sentry, etc.)
   - Configure uptime monitoring

---

## 🆘 Support

- Railway Docs: https://docs.railway.app
- Railway Discord: https://discord.gg/railway
- Laravel Docs: https://laravel.com/docs

---

**Last Updated:** 2025-11-20

