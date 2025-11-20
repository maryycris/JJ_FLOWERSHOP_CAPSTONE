# 🚀 Railway Deployment Checklist

## Pre-Deployment

- [ ] Code is committed and pushed to GitHub
- [ ] All migrations are tested locally
- [ ] Environment variables list is ready
- [ ] APP_KEY is generated (`php artisan key:generate --show`)

## Railway Setup

- [ ] Create Railway account
- [ ] Create new project
- [ ] Connect GitHub repository
- [ ] Add MySQL/PostgreSQL database service
- [ ] Configure environment variables (see RAILWAY_DEPLOYMENT.md)

## Environment Variables to Set

- [ ] `APP_NAME`
- [ ] `APP_ENV=production`
- [ ] `APP_KEY` (generated)
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` (your Railway URL)
- [ ] Database variables (auto-provided by Railway)
- [ ] `SESSION_DRIVER=database`
- [ ] `CACHE_DRIVER=database`
- [ ] Mail configuration
- [ ] Social login credentials (if used)
- [ ] PayMongo keys (if used)

## Post-Deployment

- [ ] Test homepage loads
- [ ] Test user registration
- [ ] Test user login
- [ ] Test file uploads (if applicable)
- [ ] Test database operations
- [ ] Check Railway logs for errors
- [ ] Set up custom domain (optional)
- [ ] Configure monitoring/error tracking

## Important Notes

⚠️ **Storage:** Railway uses ephemeral storage. For file uploads, use:
- Cloud storage (S3, DigitalOcean Spaces) - **Recommended**
- Railway Volume - For small files only

⚠️ **Database:** Migrations run automatically via `start.sh`

⚠️ **APP_KEY:** Must be set before first deployment

---

**Quick Deploy Command:**
```bash
railway up
```

