#!/bin/bash

# MPWA Restore Script
# Restores backup created by backup.sh

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Check if backup file is provided
if [ -z "$1" ]; then
    print_error "Please provide backup file path"
    echo "Usage: ./restore.sh <backup_file.tar.gz>"
    echo ""
    echo "Available backups:"
    ls -lh backups/*.tar.gz 2>/dev/null || echo "No backups found"
    exit 1
fi

BACKUP_FILE="$1"

# Check if backup file exists
if [ ! -f "$BACKUP_FILE" ]; then
    print_error "Backup file not found: $BACKUP_FILE"
    exit 1
fi

print_info "Starting restore process..."
print_info "Backup file: $BACKUP_FILE"

# Confirm restore
echo ""
print_warning "This will overwrite existing data!"
read -p "Are you sure you want to continue? (yes/no): " -r
echo ""
if [[ ! $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
    print_info "Restore cancelled"
    exit 0
fi

# Create temporary directory
TEMP_DIR=$(mktemp -d)
print_info "Extracting backup to temporary directory..."
tar -xzf "$BACKUP_FILE" -C "$TEMP_DIR"

# Find the backup directory
BACKUP_DIR=$(find "$TEMP_DIR" -maxdepth 1 -type d -name "mpwa_backup_*" | head -n 1)

if [ -z "$BACKUP_DIR" ]; then
    print_error "Invalid backup file structure"
    rm -rf "$TEMP_DIR"
    exit 1
fi

print_success "Backup extracted"

# Stop containers
print_info "Stopping containers..."
docker-compose stop

# Restore database
if [ -f "$BACKUP_DIR/database.sql" ]; then
    print_info "Restoring database..."
    docker-compose start mysql
    sleep 5
    
    # Wait for MySQL
    MAX_TRIES=30
    COUNTER=0
    until docker-compose exec -T mysql mysqladmin ping -h localhost --silent || [ $COUNTER -eq $MAX_TRIES ]; do
        printf '.'
        sleep 2
        COUNTER=$((COUNTER+1))
    done
    echo ""
    
    if [ $COUNTER -eq $MAX_TRIES ]; then
        print_error "MySQL failed to start"
        rm -rf "$TEMP_DIR"
        exit 1
    fi
    
    docker-compose exec -T mysql mysql -u mpwa_user -pmpwa_pass mpwa < "$BACKUP_DIR/database.sql"
    print_success "Database restored"
else
    print_warning "No database backup found"
fi

# Restore .env file
if [ -f "$BACKUP_DIR/.env" ]; then
    print_info "Restoring .env file..."
    cp "$BACKUP_DIR/.env" .env
    print_success ".env file restored"
else
    print_warning "No .env backup found"
fi

# Restore credentials
if [ -d "$BACKUP_DIR/credentials" ]; then
    print_info "Restoring credentials..."
    rm -rf credentials
    cp -r "$BACKUP_DIR/credentials" .
    print_success "Credentials restored"
else
    print_warning "No credentials backup found"
fi

# Restore storage
if [ -f "$BACKUP_DIR/storage.tar.gz" ]; then
    print_info "Restoring storage..."
    rm -rf storage
    tar -xzf "$BACKUP_DIR/storage.tar.gz"
    print_success "Storage restored"
else
    print_warning "No storage backup found"
fi

# Clean up temporary directory
rm -rf "$TEMP_DIR"

# Start all containers
print_info "Starting all containers..."
docker-compose up -d

# Wait for services to be ready
print_info "Waiting for services to be ready..."
sleep 10

# Set permissions
print_info "Setting permissions..."
docker-compose exec -T app chown -R www-data:www-data /var/www/html/storage
docker-compose exec -T app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec -T app chown -R www-data:www-data /var/www/html/credentials
docker-compose exec -T app chmod -R 775 /var/www/html/storage
docker-compose exec -T app chmod -R 775 /var/www/html/bootstrap/cache
docker-compose exec -T app chmod -R 775 /var/www/html/credentials

# Clear caches
print_info "Clearing caches..."
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan view:clear

print_success "Restore completed successfully!"
echo ""
echo -e "${GREEN}Your application has been restored from backup${NC}"
echo -e "Access your application at: ${BLUE}http://localhost:8000${NC}"
echo ""
