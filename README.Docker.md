# MPWA Docker Deployment Guide

Panduan lengkap untuk deploy MPWA menggunakan Docker.

## Persyaratan Sistem

### Minimum Requirements
- Docker 20.10+
- Docker Compose 2.0+
- RAM: 2GB minimum, 4GB recommended
- Disk Space: 5GB minimum
- OS: Linux, macOS, atau Windows 10/11 dengan WSL2

### Port Requirements
- **8000**: Web Application (Laravel)
- **3100**: Node.js Server (WhatsApp Gateway)
- **3306**: MySQL Database

## Quick Start

### Linux/macOS

```bash
# 1. Berikan permission execute pada script
chmod +x deploy.sh

# 2. Jalankan deployment
./deploy.sh
```

### Windows (PowerShell)

```powershell
# 1. Buka PowerShell sebagai Administrator

# 2. Set execution policy (jika belum)
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# 3. Jalankan deployment
.\deploy.ps1
```

## Custom Port Configuration

### Linux/macOS
```bash
# Set custom ports via environment variables
export APP_PORT=9000
export NODE_PORT=3200
export DB_PORT=3307
./deploy.sh
```

### Windows
```powershell
.\deploy.ps1 -AppPort 9000 -NodePort 3200 -DbPort 3307
```

## Manual Deployment Steps

Jika Anda ingin melakukan deployment manual:

### 1. Setup Environment

```bash
# Copy environment file
cp .env.docker .env

# Generate APP_KEY
# Linux/macOS:
sed -i "s|APP_KEY=|APP_KEY=base64:$(openssl rand -base64 32)|g" .env

# Windows (PowerShell):
# Edit .env manually dan generate key dengan:
# [Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Maximum 256 }))
```

### 2. Build Containers

```bash
docker-compose build --no-cache
```

### 3. Start Services

```bash
docker-compose up -d
```

### 4. Wait for MySQL

```bash
# Wait until MySQL is ready
docker-compose exec mysql mysqladmin ping -h localhost --silent
```

### 5. Setup Application

```bash
# Install dependencies
docker-compose exec app composer install --no-dev --optimize-autoloader
docker-compose exec app npm ci --production

# Run migrations
docker-compose exec app php artisan migrate --force

# Run seeders
docker-compose exec app php artisan db:seed --force

# Create storage link
docker-compose exec app php artisan storage:link

# Optimize application
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Set permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/bootstrap/cache
```

## Accessing the Application

Setelah deployment selesai:

- **Web Interface**: http://localhost:8000
- **Node.js API**: http://localhost:3100
- **Database**: localhost:3306

### Default Database Credentials
- Database: `mpwa`
- Username: `mpwa_user`
- Password: `mpwa_pass`

## Common Commands

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f mysql

# Last 100 lines
docker-compose logs --tail=100 app
```

### Container Management
```bash
# Stop containers
docker-compose stop

# Start containers
docker-compose start

# Restart containers
docker-compose restart

# Restart specific service
docker-compose restart app

# Remove containers (data preserved)
docker-compose down

# Remove containers and volumes (data deleted)
docker-compose down -v
```

### Execute Commands in Container
```bash
# Enter container shell
docker-compose exec app sh

# Run artisan commands
docker-compose exec app php artisan list
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear

# Run composer commands
docker-compose exec app composer update

# Run npm commands
docker-compose exec app npm install
```

### Database Management
```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u mpwa_user -p mpwa

# Backup database
docker-compose exec mysql mysqldump -u mpwa_user -p mpwa > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u mpwa_user -p mpwa < backup.sql

# Export database to container
docker cp backup.sql mpwa-mysql:/tmp/backup.sql
docker-compose exec mysql mysql -u mpwa_user -p mpwa < /tmp/backup.sql
```

## Troubleshooting

### Port Already in Use
```bash
# Check what's using the port (Linux/macOS)
lsof -i :8000
lsof -i :3100

# Check what's using the port (Windows)
netstat -ano | findstr :8000
netstat -ano | findstr :3100

# Kill the process or change port in .env
```

### Container Won't Start
```bash
# Check container status
docker-compose ps

# View detailed logs
docker-compose logs app

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage

# Fix bootstrap cache permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/bootstrap/cache
```

### Database Connection Issues
```bash
# Check MySQL is running
docker-compose ps mysql

# Check MySQL logs
docker-compose logs mysql

# Test connection
docker-compose exec app php artisan tinker
# Then run: DB::connection()->getPdo();

# Verify .env settings
docker-compose exec app cat .env | grep DB_
```

### Node.js Server Not Starting
```bash
# Check Node.js logs
docker-compose logs app | grep node

# Restart Node.js service
docker-compose restart app

# Check if port is available
docker-compose exec app netstat -tuln | grep 3100

# Manually start Node.js (for debugging)
docker-compose exec app node server.js
```

### Clear All Caches
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app composer dump-autoload
```

### Reset Everything
```bash
# Stop and remove everything
docker-compose down -v

# Remove all images
docker-compose down --rmi all

# Start fresh
./deploy.sh  # or .\deploy.ps1 on Windows
```

## Production Deployment

### Security Recommendations

1. **Change Default Credentials**
   ```bash
   # Edit .env file
   DB_PASSWORD=your_secure_password
   ```

2. **Disable Debug Mode**
   ```bash
   APP_DEBUG=false
   APP_ENV=production
   ```

3. **Use HTTPS**
   - Setup reverse proxy (Nginx/Apache)
   - Configure SSL certificates
   - Update APP_URL to https://

4. **Secure Database**
   - Use strong passwords
   - Limit database access
   - Regular backups

5. **Environment Variables**
   - Never commit .env to git
   - Use secrets management
   - Rotate keys regularly

### Performance Optimization

1. **Enable OPcache** (Already configured in docker/php/php.ini)

2. **Use Redis for Cache** (Optional)
   ```yaml
   # Add to docker-compose.yml
   redis:
     image: redis:alpine
     ports:
       - "6379:6379"
   ```
   
   ```bash
   # Update .env
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   REDIS_HOST=redis
   ```

3. **Database Optimization**
   - Regular maintenance
   - Index optimization
   - Query optimization

### Backup Strategy

```bash
# Automated backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker-compose exec mysql mysqldump -u mpwa_user -p mpwa > backup_$DATE.sql
tar -czf backup_$DATE.tar.gz backup_$DATE.sql storage/ credentials/
```

## Monitoring

### Health Checks
```bash
# Check application health
curl http://localhost:8000

# Check Node.js health
curl http://localhost:3100

# Check MySQL health
docker-compose exec mysql mysqladmin ping -h localhost
```

### Resource Usage
```bash
# Container stats
docker stats

# Disk usage
docker system df

# Container resource usage
docker-compose top
```

## Updating Application

```bash
# Pull latest changes
git pull

# Rebuild containers
docker-compose build --no-cache

# Restart services
docker-compose down
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear caches
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Support

Jika mengalami masalah:

1. Check logs: `docker-compose logs -f`
2. Verify configuration: `docker-compose config`
3. Check container status: `docker-compose ps`
4. Review this documentation
5. Contact support with logs and error messages

## License

This deployment configuration is provided as-is for MPWA application.
