#!/bin/bash

# MPWA Docker Deployment Script
# This script automates the complete deployment of MPWA application

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="MPWA"
APP_PORT=${APP_PORT:-8000}
NODE_PORT=${NODE_PORT:-3100}
DB_PORT=${DB_PORT:-3306}

# Functions
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

check_requirements() {
    print_header "Checking Requirements"
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed. Please install Docker first."
        exit 1
    fi
    print_success "Docker is installed"
    
    # Check Docker Compose
    if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
        exit 1
    fi
    print_success "Docker Compose is installed"
    
    # Check if ports are available
    if lsof -Pi :${APP_PORT} -sTCP:LISTEN -t >/dev/null 2>&1; then
        print_warning "Port ${APP_PORT} is already in use. Please stop the service or change APP_PORT."
        read -p "Do you want to continue anyway? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
    
    if lsof -Pi :${NODE_PORT} -sTCP:LISTEN -t >/dev/null 2>&1; then
        print_warning "Port ${NODE_PORT} is already in use. Please stop the service or change NODE_PORT."
        read -p "Do you want to continue anyway? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
}

setup_environment() {
    print_header "Setting Up Environment"
    
    # Copy .env file if not exists
    if [ ! -f .env ]; then
        if [ -f .env.docker ]; then
            cp .env.docker .env
            print_success "Created .env from .env.docker"
        else
            print_error ".env.docker template not found"
            exit 1
        fi
    else
        print_info ".env file already exists, skipping..."
    fi
    
    # Generate APP_KEY if not set
    if ! grep -q "APP_KEY=base64:" .env; then
        print_info "Generating APP_KEY..."
        APP_KEY=$(openssl rand -base64 32)
        sed -i.bak "s|APP_KEY=|APP_KEY=base64:${APP_KEY}|g" .env
        rm -f .env.bak
        print_success "APP_KEY generated"
    else
        print_info "APP_KEY already set"
    fi
    
    # Update APP_URL
    sed -i.bak "s|APP_URL=.*|APP_URL=http://localhost:${APP_PORT}|g" .env
    rm -f .env.bak
    
    # Create required directories
    print_info "Creating required directories..."
    mkdir -p storage/app/public
    mkdir -p storage/framework/cache/data
    mkdir -p storage/framework/sessions
    mkdir -p storage/framework/views
    mkdir -p storage/logs
    mkdir -p bootstrap/cache
    mkdir -p credentials
    mkdir -p public/storage
    
    print_success "Directories created"
}

build_containers() {
    print_header "Building Docker Containers"
    
    print_info "Building application image..."
    docker-compose build --no-cache
    
    print_success "Docker containers built successfully"
}

start_containers() {
    print_header "Starting Docker Containers"
    
    print_info "Starting containers..."
    docker-compose up -d
    
    print_success "Containers started"
    
    # Wait for MySQL to be ready
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

setup_application() {
    print_header "Setting Up Application"
    
    # Install/update composer dependencies
    print_info "Installing Composer dependencies..."
    docker-compose exec -T app composer install --no-dev --optimize-autoloader
    
    # Install/update npm dependencies
    print_info "Installing NPM dependencies..."
    docker-compose exec -T app npm ci --production
    
    # Run migrations
    print_info "Running database migrations..."
    docker-compose exec -T app php artisan migrate --force
    
    # Run seeders
    print_info "Running database seeders..."
    docker-compose exec -T app php artisan db:seed --force || true
    
    # Create storage link
    print_info "Creating storage link..."
    docker-compose exec -T app php artisan storage:link || true
    
    # Clear and cache config
    print_info "Optimizing application..."
    docker-compose exec -T app php artisan config:clear
    docker-compose exec -T app php artisan cache:clear
    docker-compose exec -T app php artisan view:clear
    docker-compose exec -T app php artisan config:cache
    docker-compose exec -T app php artisan route:cache
    docker-compose exec -T app php artisan view:cache
    
    # Set permissions
    print_info "Setting permissions..."
    docker-compose exec -T app chown -R www-data:www-data /var/www/html/storage
    docker-compose exec -T app chown -R www-data:www-data /var/www/html/bootstrap/cache
    docker-compose exec -T app chown -R www-data:www-data /var/www/html/credentials
    docker-compose exec -T app chmod -R 775 /var/www/html/storage
    docker-compose exec -T app chmod -R 775 /var/www/html/bootstrap/cache
    docker-compose exec -T app chmod -R 775 /var/www/html/credentials
    
    print_success "Application setup completed"
}

verify_deployment() {
    print_header "Verifying Deployment"
    
    # Check if containers are running
    print_info "Checking container status..."
    if docker-compose ps | grep -q "Up"; then
        print_success "Containers are running"
    else
        print_error "Some containers are not running"
        docker-compose ps
        exit 1
    fi
    
    # Check web server
    print_info "Checking web server..."
    sleep 5
    if curl -f -s -o /dev/null http://localhost:${APP_PORT}; then
        print_success "Web server is responding"
    else
        print_warning "Web server is not responding yet, it may need more time to start"
    fi
    
    # Check Node.js server
    print_info "Checking Node.js server..."
    if docker-compose logs app | grep -q "Server running and listening on port"; then
        print_success "Node.js server is running"
    else
        print_warning "Node.js server may still be starting"
    fi
}

show_info() {
    print_header "Deployment Complete!"
    
    echo ""
    echo -e "${GREEN}${APP_NAME} has been successfully deployed!${NC}"
    echo ""
    echo -e "${BLUE}Access Information:${NC}"
    echo -e "  Web Application: ${GREEN}http://localhost:${APP_PORT}${NC}"
    echo -e "  Node.js Server:  ${GREEN}http://localhost:${NODE_PORT}${NC}"
    echo -e "  MySQL Port:      ${GREEN}${DB_PORT}${NC}"
    echo ""
    echo -e "${BLUE}Default Credentials:${NC}"
    echo -e "  Database: ${GREEN}mpwa${NC}"
    echo -e "  Username: ${GREEN}mpwa_user${NC}"
    echo -e "  Password: ${GREEN}mpwa_pass${NC}"
    echo ""
    echo -e "${BLUE}Useful Commands:${NC}"
    echo -e "  View logs:           ${YELLOW}docker-compose logs -f${NC}"
    echo -e "  View app logs:       ${YELLOW}docker-compose logs -f app${NC}"
    echo -e "  Stop containers:     ${YELLOW}docker-compose stop${NC}"
    echo -e "  Start containers:    ${YELLOW}docker-compose start${NC}"
    echo -e "  Restart containers:  ${YELLOW}docker-compose restart${NC}"
    echo -e "  Remove containers:   ${YELLOW}docker-compose down${NC}"
    echo -e "  Remove all data:     ${YELLOW}docker-compose down -v${NC}"
    echo -e "  Enter app container: ${YELLOW}docker-compose exec app sh${NC}"
    echo -e "  Run artisan command: ${YELLOW}docker-compose exec app php artisan <command>${NC}"
    echo ""
    echo -e "${YELLOW}Note: If you see any warnings above, please check the logs.${NC}"
    echo ""
}

cleanup_on_error() {
    print_error "Deployment failed!"
    print_info "Cleaning up..."
    docker-compose down
    exit 1
}

# Main execution
trap cleanup_on_error ERR

main() {
    clear
    print_header "MPWA Docker Deployment"
    echo ""
    
    check_requirements
    setup_environment
    build_containers
    start_containers
    setup_application
    verify_deployment
    show_info
}

# Run main function
main
