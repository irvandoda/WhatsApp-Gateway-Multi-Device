# Docker Setup Guide

This guide will help you set up and run WAGW using Docker and Docker Compose.

## 📋 Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- At least 4GB RAM available
- At least 10GB free disk space

## 🚀 Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/irvandoda/WhatsApp-Gateway-Multi-Device.git wagw
cd wagw
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.docker.example .env

# Edit .env file with your configuration
nano .env
```

**Important variables to configure:**

- `APP_KEY` - Generate with: `php artisan key:generate` (or run `make migrate` which will generate it)
- `DB_PASSWORD` - Set a secure database password
- `DB_ROOT_PASSWORD` - Set MySQL root password
- `API_KEY_SECRET` - Generate a random secret key

### 3. Build and Start Services

```bash
# Build all images
make build

# Start all services
make up

# Or use docker-compose directly
docker-compose up -d
```

### 4. Run Migrations

```bash
# Run migrations and seeders
make migrate-fresh

# Or manually
docker-compose exec app php artisan migrate --seed
```

### 5. Access the Application

- **Web Application**: http://localhost
- **Node.js Worker**: http://localhost:3000
- **phpMyAdmin**: http://localhost:8080

**Default Admin Credentials:**
- Email: `admin@admin.com`
- Password: `password`

## 🐳 Docker Services

The Docker Compose setup includes the following services:

| Service | Container Name | Port | Description |
|---------|---------------|------|-------------|
| **nginx** | wagw_nginx | 80, 443 | Web server |
| **app** | wagw_app | 9000 | Laravel PHP-FPM |
| **node** | wagw_node | 3000 | Node.js WhatsApp Worker |
| **mysql** | wagw_mysql | 3306 | MySQL Database |
| **redis** | wagw_redis | 6379 | Redis Cache |
| **queue** | wagw_queue | - | Laravel Queue Worker |
| **scheduler** | wagw_scheduler | - | Laravel Scheduler (Cron) |
| **phpmyadmin** | wagw_phpmyadmin | 8080 | Database Management |

## 📝 Common Commands

### Using Makefile (Recommended)

```bash
make help          # Show all available commands
make build         # Build all Docker images
make up            # Start all containers
make down          # Stop all containers
make restart       # Restart all containers
make logs          # View logs from all containers
make logs-app      # View Laravel app logs
make logs-node     # View Node.js worker logs
make shell         # Open shell in Laravel container
make shell-node    # Open shell in Node.js container
make migrate       # Run database migrations
make migrate-fresh  # Fresh migration with seeding
make cache-clear   # Clear all caches
make cache-optimize # Optimize caches
make clean         # Remove all containers and volumes
```

### Using Docker Compose Directly

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Execute commands
docker-compose exec app php artisan migrate
docker-compose exec node npm install
docker-compose exec app composer install
```

## 🔧 Configuration

### Environment Variables

Edit `.env` file to configure:

```env
# Application
APP_NAME="WAGW"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=wagw_db
DB_USERNAME=wagw_user
DB_PASSWORD=your_password

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Node.js Worker
WA_URL_SERVER=http://node:3000
SOCKET_SERVER=http://node:3000
PORT_NODE=3000
```

### Nginx Configuration

Nginx configuration files are located in:
- `docker/nginx/nginx.conf` - Main Nginx config
- `docker/nginx/default.conf` - Virtual host config

### PHP Configuration

PHP configuration is in:
- `docker/php/php.ini` - PHP settings

### MySQL Configuration

MySQL configuration is in:
- `docker/mysql/my.cnf` - MySQL settings

## 🗄️ Database Management

### Access MySQL

```bash
# Using docker-compose
docker-compose exec mysql mysql -u wagw_user -p wagw_db

# Or use phpMyAdmin
# Open http://localhost:8080 in browser
```

### Backup Database

```bash
docker-compose exec mysql mysqldump -u wagw_user -p wagw_db > backup.sql
```

### Restore Database

```bash
docker-compose exec -T mysql mysql -u wagw_user -p wagw_db < backup.sql
```

## 🔍 Troubleshooting

### Check Container Status

```bash
docker-compose ps
```

### View Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f node
docker-compose logs -f mysql
```

### Container Not Starting

```bash
# Check logs
docker-compose logs service_name

# Restart service
docker-compose restart service_name

# Rebuild service
docker-compose up -d --build service_name
```

### Permission Issues

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

### Clear All Caches

```bash
make cache-clear
# Or
docker-compose exec app php artisan optimize:clear
```

### Database Connection Issues

```bash
# Check MySQL is running
docker-compose ps mysql

# Check connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Node.js Worker Not Connecting

```bash
# Check Node.js container
docker-compose ps node

# Check logs
docker-compose logs -f node

# Restart Node.js
docker-compose restart node
```

## 🚀 Production Deployment

### 1. Update Environment

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

### 2. Generate Application Key

```bash
docker-compose exec app php artisan key:generate
```

### 3. Optimize Application

```bash
make cache-optimize
```

### 4. Set Proper Permissions

```bash
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 755 /var/www/html/storage
```

### 5. SSL/HTTPS Setup

Update `docker/nginx/default.conf` to include SSL configuration:

```nginx
server {
    listen 443 ssl http2;
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    # ... rest of config
}
```

## 📊 Monitoring

### Health Checks

All services have health checks configured. Check status:

```bash
docker-compose ps
```

### Resource Usage

```bash
docker stats
```

## 🔄 Updates

### Update Application

```bash
# Pull latest code
git pull origin main

# Rebuild containers
make build

# Restart services
make restart

# Run migrations if needed
make migrate
```

## 🧹 Cleanup

### Remove All Containers and Volumes

```bash
make clean
# Or
docker-compose down -v
```

### Remove Images

```bash
docker-compose down --rmi all
```

## 📚 Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Documentation](https://laravel.com/docs)
- [Node.js Documentation](https://nodejs.org/docs/)

## 🆘 Support

If you encounter any issues:

1. Check the logs: `make logs`
2. Verify environment configuration
3. Check container status: `docker-compose ps`
4. Open an issue on GitHub

---

**Happy Dockerizing! 🐳**
