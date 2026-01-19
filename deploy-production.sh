#!/bin/bash

# MPWA Production Deployment Script
# This script deploys MPWA with production configurations including SSL

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_header() {
    echo -e "${BLUE}================================================${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}================================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# Pre-deployment checks
check_requirements() {
    print_header "Pre-Deployment Checks"
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed"
        exit 1
    fi
    print_success "Docker is installed"
    
    # Check Docker Compose
    if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
        print_error "Docker Compose is not installed"
        exit 1
    fi
    print_success "Docker Compose is installed"
    
    # Check SSL certificates
    if [ ! -f "docker/ssl/cert.pem" ] || [ ! -f "docker/ssl/key.pem" ]; then
        print_warning "SSL certificates not found"
        print_info "Run ./setup-ssl.sh to generate certificates"
        read -p "Continue without SSL? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    else
        print_success "SSL certificates found"
    fi
}

# Setup production environment
setup_production_env() {
    print_header "Setting Up Production Environment"
    
    if [ ! -f .env ]; then
        if [ -f .env.docker ]; then
            cp .env.docker .env
            print_success "Created .env from template"
        else
            print_error ".env.docker template not found"
            exit 1
        fi
    fi
    
    # Update production settings
    print_info "Configuring production settings..."
    
    # Set production environment
    sed -i.bak 's/APP_ENV=.*/APP_ENV=production/' .env
    sed -i.bak 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
    sed -i.bak 's/LOG_LEVEL=.*/LOG_LEVEL=warning/' .env
    
    # Generate strong passwords if using defaults
    if grep -q "DB_PASSWORD=mpwa_pass" .env; then
        print_warning "Using default database password"
        read -p "Generate strong password? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            NEW_PASS=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-25)
            sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=${NEW_PASS}/" .env
            print_success "Strong password generated"
        fi
    fi
    
    # Generate APP_KEY if not set
    if ! grep -q "APP_KEY=base64:" .env; then
        print_info "Generating APP_KEY..."
        APP_KEY=$(openssl rand -base64 32)
        sed -i.bak "s|APP_KEY=|APP_KEY=base64:${APP_KEY}|g" .env
        print_success "APP_KEY generated"
    fi
    
    # Prompt for domain
    read -p "Enter your domain name (e.g., mpwa.example.com): " domain
    if [ ! -z "$domain" ]; then
        sed -i.bak "s|APP_URL=.*|APP_URL=https://${domain}|" .env
        sed -i.bak "s|WA_URL_SERVER=.*|WA_URL_SERVER=https://${domain}|" .env
        print_success "Domain configured: $domain"
    fi
    
    # Clean up backup files
    rm -f .env.bak
    
    print_success "Production environment configured"
}

# Build and deploy
deploy_containers() {
    print_header "Building and Deploying Containers"
    
    print_info "Building containers..."
    docker-compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache
    
    print_info "Starting containers..."
    docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
    
    print_success "Containers deployed"
    
    # Wait for MySQL
    print_info "Waiting for MySQL to be ready..."
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
    
    print_success "MySQL is ready"
}

# Setup application
setup_application() {
    print_header "Setting Up Application"
    
    print_info "Installing dependencies..."
    docker-compose exec -T app composer install --no-dev --optimize-autoloader --no-interaction
    docker-compose exec -T app npm ci --production
    
    print_info "Running migrations..."
    docker-compose exec -T app php artisan migrate --force
    
    print_info "Running seeders..."
    docker-compose exec -T app php artisan db:seed --force || true
    
    print_info "Creating storage link..."
    docker-compose exec -T app php artisan storage:link || true
    
    print_info "Optimizing application..."
    docker-compose exec -T app php artisan config:cache
    docker-compose exec -T app php artisan route:cache
    docker-compose exec -T app php artisan view:cache
    docker-compose exec -T app php artisan optimize
    
    print_info "Setting permissions..."
    docker-compose exec -T app chown -R www-data:www-data /var/www/html/storage
    docker-compose exec -T app chown -R www-data:www-data /var/www/html/bootstrap/cache
    docker-compose exec -T app chmod -R 775 /var/www/html/storage
    docker-compose exec -T app chmod -R 775 /var/www/html/bootstrap/cache
    
    print_success "Application setup completed"
}

# Security hardening
security_hardening() {
    print_header "Security Hardening"
    
    print_info "Applying security configurations..."
    
    # Disable directory listing
    docker-compose exec -T app sh -c "echo 'Options -Indexes' > /var/www/html/public/.htaccess"
    
    # Set secure permissions
    docker-compose exec -T app find /var/www/html -type f -exec chmod 644 {} \;
    docker-compose exec -T app find /var/www/html -type d -exec chmod 755 {} \;
    docker-compose exec -T app chmod -R 775 /var/www/html/storage
    docker-compose exec -T app chmod -R 775 /var/www/html/bootstrap/cache
    
    print_success "Security hardening completed"
}

# Setup monitoring
setup_monitoring() {
    print_header "Setting Up Monitoring"
    
    print_info "Configuring log rotation..."
    
    # Create logrotate configuration
    cat > /tmp/mpwa-logrotate << 'EOF'
/var/log/nginx/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 nginx nginx
    sharedscripts
    postrotate
        docker-compose exec nginx-proxy nginx -s reload
    endscript
}
EOF
    
    print_success "Monitoring configured"
}

# Setup backup cron
setup_backup() {
    print_header "Setting Up Automated Backups"
    
    read -p "Setup daily automated backups? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        SCRIPT_DIR=$(pwd)
        (crontab -l 2>/dev/null; echo "0 2 * * * cd ${SCRIPT_DIR} && ./backup.sh >> ${SCRIPT_DIR}/backups/backup.log 2>&1") | crontab -
        print_success "Daily backup scheduled at 2:00 AM"
    fi
}

# Verify deployment
verify_deployment() {
    print_header "Verifying Deployment"
    
    print_info "Running verification tests..."
    
    if [ -f verify-deployment.sh ]; then
        chmod +x verify-deployment.sh
        ./verify-deployment.sh
    else
        print_warning "Verification script not found"
    fi
}

# Show deployment info
show_deployment_info() {
    print_header "Production Deployment Complete!"
    
    DOMAIN=$(grep "APP_URL=" .env | cut -d'=' -f2)
    
    echo ""
    echo -e "${GREEN}MPWA has been successfully deployed in production mode!${NC}"
    echo ""
    echo -e "${BLUE}Access Information:${NC}"
    echo -e "  Application URL: ${GREEN}${DOMAIN}${NC}"
    echo ""
    echo -e "${BLUE}Important Security Notes:${NC}"
    echo -e "  ${YELLOW}1. Change default admin password immediately${NC}"
    echo -e "  ${YELLOW}2. Review and update .env file${NC}"
    echo -e "  ${YELLOW}3. Setup firewall rules${NC}"
    echo -e "  ${YELLOW}4. Configure regular backups${NC}"
    echo -e "  ${YELLOW}5. Monitor application logs${NC}"
    echo ""
    echo -e "${BLUE}Useful Commands:${NC}"
    echo -e "  View logs:     ${YELLOW}docker-compose logs -f${NC}"
    echo -e "  Backup:        ${YELLOW}./backup.sh${NC}"
    echo -e "  Monitor:       ${YELLOW}./monitor.sh${NC}"
    echo -e "  Stop:          ${YELLOW}docker-compose -f docker-compose.yml -f docker-compose.prod.yml down${NC}"
    echo ""
    echo -e "${BLUE}Next Steps:${NC}"
    echo -e "  1. Access your application and complete setup"
    echo -e "  2. Configure WhatsApp devices"
    echo -e "  3. Test all functionality"
    echo -e "  4. Setup monitoring and alerts"
    echo ""
}

# Main execution
main() {
    clear
    print_header "MPWA Production Deployment"
    echo ""
    
    print_warning "This will deploy MPWA in PRODUCTION mode"
    print_warning "Make sure you have:"
    echo "  - Valid SSL certificates"
    echo "  - Proper domain configuration"
    echo "  - Firewall rules configured"
    echo "  - Backup strategy planned"
    echo ""
    read -p "Continue with production deployment? (yes/no): " -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
        print_info "Deployment cancelled"
        exit 0
    fi
    
    check_requirements
    setup_production_env
    deploy_containers
    setup_application
    security_hardening
    setup_monitoring
    setup_backup
    verify_deployment
    show_deployment_info
}

# Run main function
main
