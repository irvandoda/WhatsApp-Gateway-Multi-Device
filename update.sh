#!/bin/bash

# MPWA Update Script
# Safely updates MPWA application with backup

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

print_header() {
    echo -e "${BLUE}================================================${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}================================================${NC}"
}

# Backup before update
create_backup() {
    print_header "Creating Backup Before Update"
    
    if [ -f backup.sh ]; then
        chmod +x backup.sh
        ./backup.sh
        print_success "Backup created successfully"
    else
        print_warning "Backup script not found, skipping backup"
        read -p "Continue without backup? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
}

# Pull latest changes
pull_updates() {
    print_header "Pulling Latest Updates"
    
    print_info "Checking for updates..."
    
    if [ -d .git ]; then
        # Stash local changes
        git stash
        
        # Pull latest changes
        git pull origin main || git pull origin master
        
        print_success "Updates pulled successfully"
    else
        print_warning "Not a git repository, skipping git pull"
    fi
}

# Update dependencies
update_dependencies() {
    print_header "Updating Dependencies"
    
    print_info "Updating Composer dependencies..."
    docker-compose exec -T app composer update --no-dev --optimize-autoloader
    
    print_info "Updating NPM dependencies..."
    docker-compose exec -T app npm update --production
    
    print_success "Dependencies updated"
}

# Rebuild containers
rebuild_containers() {
    print_header "Rebuilding Containers"
    
    print_info "Stopping containers..."
    docker-compose stop
    
    print_info "Building new images..."
    docker-compose build --no-cache
    
    print_info "Starting containers..."
    docker-compose up -d
    
    # Wait for MySQL
    print_info "Waiting for services to be ready..."
    sleep 10
    
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
        exit 1
    fi
    
    print_success "Containers rebuilt and started"
}

# Run migrations
run_migrations() {
    print_header "Running Database Migrations"
    
    print_info "Checking for new migrations..."
    docker-compose exec -T app php artisan migrate --force
    
    print_success "Migrations completed"
}

# Clear and rebuild caches
optimize_application() {
    print_header "Optimizing Application"
    
    print_info "Clearing caches..."
    docker-compose exec -T app php artisan config:clear
    docker-compose exec -T app php artisan cache:clear
    docker-compose exec -T app php artisan route:clear
    docker-compose exec -T app php artisan view:clear
    
    print_info "Rebuilding caches..."
    docker-compose exec -T app php artisan config:cache
    docker-compose exec -T app php artisan route:cache
    docker-compose exec -T app php artisan view:cache
    docker-compose exec -T app php artisan optimize
    
    print_info "Optimizing autoloader..."
    docker-compose exec -T app composer dump-autoload --optimize
    
    print_success "Application optimized"
}

# Verify update
verify_update() {
    print_header "Verifying Update"
    
    print_info "Checking application status..."
    
    # Check if containers are running
    if docker-compose ps | grep -q "Up"; then
        print_success "Containers are running"
    else
        print_error "Some containers are not running"
        docker-compose ps
        return 1
    fi
    
    # Check web server
    if curl -f -s -o /dev/null http://localhost:8000; then
        print_success "Web server is responding"
    else
        print_warning "Web server is not responding"
    fi
    
    # Check Node.js server
    if docker-compose logs app | grep -q "Server running and listening on port"; then
        print_success "Node.js server is running"
    else
        print_warning "Node.js server may not be running"
    fi
}

# Show update summary
show_summary() {
    print_header "Update Complete"
    
    echo ""
    echo -e "${GREEN}MPWA has been successfully updated!${NC}"
    echo ""
    echo -e "${BLUE}What was updated:${NC}"
    echo -e "  ✓ Application code"
    echo -e "  ✓ Dependencies (Composer & NPM)"
    echo -e "  ✓ Docker containers"
    echo -e "  ✓ Database migrations"
    echo -e "  ✓ Application caches"
    echo ""
    echo -e "${BLUE}Next steps:${NC}"
    echo -e "  1. Test your application thoroughly"
    echo -e "  2. Check logs for any errors: ${YELLOW}docker-compose logs -f${NC}"
    echo -e "  3. Verify all features are working"
    echo ""
    echo -e "${YELLOW}If you encounter issues:${NC}"
    echo -e "  1. Check logs: ${BLUE}docker-compose logs -f${NC}"
    echo -e "  2. Restore from backup: ${BLUE}./restore.sh backups/mpwa_backup_*.tar.gz${NC}"
    echo ""
}

# Rollback function
rollback() {
    print_error "Update failed!"
    print_warning "Rolling back to previous version..."
    
    # Find latest backup
    LATEST_BACKUP=$(ls -t backups/mpwa_backup_*.tar.gz 2>/dev/null | head -n 1)
    
    if [ ! -z "$LATEST_BACKUP" ]; then
        print_info "Found backup: $LATEST_BACKUP"
        
        if [ -f restore.sh ]; then
            chmod +x restore.sh
            ./restore.sh "$LATEST_BACKUP"
            print_success "Rollback completed"
        else
            print_error "Restore script not found"
            print_info "Please restore manually from: $LATEST_BACKUP"
        fi
    else
        print_error "No backup found for rollback"
        print_info "Please restore manually or redeploy"
    fi
    
    exit 1
}

# Main execution
main() {
    clear
    print_header "MPWA Update Process"
    echo ""
    
    print_warning "This will update your MPWA installation"
    print_info "A backup will be created before updating"
    echo ""
    read -p "Continue with update? (y/n) " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_info "Update cancelled"
        exit 0
    fi
    
    # Set trap for errors
    trap rollback ERR
    
    create_backup
    pull_updates
    update_dependencies
    rebuild_containers
    run_migrations
    optimize_application
    verify_update
    show_summary
    
    # Remove trap
    trap - ERR
}

# Run main function
main
