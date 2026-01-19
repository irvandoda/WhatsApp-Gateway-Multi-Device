#!/bin/bash

# MPWA Deployment Verification Script
# This script verifies that all components are working correctly

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

APP_PORT=${APP_PORT:-8000}
NODE_PORT=${NODE_PORT:-3100}
DB_PORT=${DB_PORT:-3306}

PASSED=0
FAILED=0

print_test() {
    echo -e "${BLUE}[TEST]${NC} $1"
}

print_pass() {
    echo -e "${GREEN}[PASS]${NC} $1"
    PASSED=$((PASSED + 1))
}

print_fail() {
    echo -e "${RED}[FAIL]${NC} $1"
    FAILED=$((FAILED + 1))
}

print_header() {
    echo ""
    echo -e "${BLUE}================================================${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}================================================${NC}"
    echo ""
}

# Test 1: Check if containers are running
test_containers_running() {
    print_test "Checking if containers are running..."
    
    if docker-compose ps | grep -q "mpwa-app.*Up"; then
        print_pass "App container is running"
    else
        print_fail "App container is not running"
        return 1
    fi
    
    if docker-compose ps | grep -q "mpwa-mysql.*Up"; then
        print_pass "MySQL container is running"
    else
        print_fail "MySQL container is not running"
        return 1
    fi
}

# Test 2: Check MySQL connectivity
test_mysql_connection() {
    print_test "Checking MySQL connection..."
    
    if docker-compose exec -T mysql mysqladmin ping -h localhost --silent 2>/dev/null; then
        print_pass "MySQL is responding"
    else
        print_fail "MySQL is not responding"
        return 1
    fi
    
    # Test database exists
    if docker-compose exec -T mysql mysql -u mpwa_user -pmpwa_pass -e "USE mpwa;" 2>/dev/null; then
        print_pass "Database 'mpwa' exists and is accessible"
    else
        print_fail "Cannot access database 'mpwa'"
        return 1
    fi
}

# Test 3: Check web server
test_web_server() {
    print_test "Checking web server..."
    
    if curl -f -s -o /dev/null -w "%{http_code}" http://localhost:${APP_PORT} | grep -q "200\|302"; then
        print_pass "Web server is responding"
    else
        print_fail "Web server is not responding on port ${APP_PORT}"
        return 1
    fi
}

# Test 4: Check Node.js server
test_nodejs_server() {
    print_test "Checking Node.js server..."
    
    if docker-compose logs app 2>&1 | grep -q "Server running and listening on port"; then
        print_pass "Node.js server is running"
    else
        print_fail "Node.js server is not running"
        return 1
    fi
    
    # Check if port is listening
    if docker-compose exec -T app netstat -tuln 2>/dev/null | grep -q ":${NODE_PORT}"; then
        print_pass "Node.js server is listening on port ${NODE_PORT}"
    else
        print_fail "Node.js server is not listening on port ${NODE_PORT}"
        return 1
    fi
}

# Test 5: Check PHP-FPM
test_php_fpm() {
    print_test "Checking PHP-FPM..."
    
    if docker-compose exec -T app pgrep php-fpm >/dev/null 2>&1; then
        print_pass "PHP-FPM is running"
    else
        print_fail "PHP-FPM is not running"
        return 1
    fi
}

# Test 6: Check Nginx
test_nginx() {
    print_test "Checking Nginx..."
    
    if docker-compose exec -T app pgrep nginx >/dev/null 2>&1; then
        print_pass "Nginx is running"
    else
        print_fail "Nginx is not running"
        return 1
    fi
}

# Test 7: Check Laravel installation
test_laravel() {
    print_test "Checking Laravel installation..."
    
    if docker-compose exec -T app php artisan --version 2>/dev/null | grep -q "Laravel"; then
        print_pass "Laravel is installed"
    else
        print_fail "Laravel is not properly installed"
        return 1
    fi
    
    # Check if APP_KEY is set
    if docker-compose exec -T app php artisan tinker --execute="echo config('app.key');" 2>/dev/null | grep -q "base64:"; then
        print_pass "APP_KEY is configured"
    else
        print_fail "APP_KEY is not configured"
        return 1
    fi
}

# Test 8: Check database migrations
test_migrations() {
    print_test "Checking database migrations..."
    
    if docker-compose exec -T app php artisan migrate:status 2>/dev/null | grep -q "Ran"; then
        print_pass "Database migrations have been run"
    else
        print_fail "Database migrations have not been run"
        return 1
    fi
}

# Test 9: Check storage permissions
test_permissions() {
    print_test "Checking storage permissions..."
    
    if docker-compose exec -T app test -w /var/www/html/storage 2>/dev/null; then
        print_pass "Storage directory is writable"
    else
        print_fail "Storage directory is not writable"
        return 1
    fi
    
    if docker-compose exec -T app test -w /var/www/html/bootstrap/cache 2>/dev/null; then
        print_pass "Bootstrap cache directory is writable"
    else
        print_fail "Bootstrap cache directory is not writable"
        return 1
    fi
}

# Test 10: Check Node.js dependencies
test_node_dependencies() {
    print_test "Checking Node.js dependencies..."
    
    if docker-compose exec -T app test -d /var/www/html/node_modules 2>/dev/null; then
        print_pass "Node.js dependencies are installed"
    else
        print_fail "Node.js dependencies are not installed"
        return 1
    fi
}

# Test 11: Check Composer dependencies
test_composer_dependencies() {
    print_test "Checking Composer dependencies..."
    
    if docker-compose exec -T app test -d /var/www/html/vendor 2>/dev/null; then
        print_pass "Composer dependencies are installed"
    else
        print_fail "Composer dependencies are not installed"
        return 1
    fi
}

# Test 12: Check credentials directory
test_credentials_directory() {
    print_test "Checking credentials directory..."
    
    if docker-compose exec -T app test -d /var/www/html/credentials 2>/dev/null; then
        print_pass "Credentials directory exists"
    else
        print_fail "Credentials directory does not exist"
        return 1
    fi
    
    if docker-compose exec -T app test -w /var/www/html/credentials 2>/dev/null; then
        print_pass "Credentials directory is writable"
    else
        print_fail "Credentials directory is not writable"
        return 1
    fi
}

# Test 13: Check environment configuration
test_environment() {
    print_test "Checking environment configuration..."
    
    if docker-compose exec -T app test -f /var/www/html/.env 2>/dev/null; then
        print_pass ".env file exists"
    else
        print_fail ".env file does not exist"
        return 1
    fi
    
    # Check critical env variables
    if docker-compose exec -T app grep -q "DB_HOST=mysql" /var/www/html/.env 2>/dev/null; then
        print_pass "Database host is configured correctly"
    else
        print_fail "Database host is not configured correctly"
        return 1
    fi
}

# Test 14: Check disk space
test_disk_space() {
    print_test "Checking disk space..."
    
    DISK_USAGE=$(docker-compose exec -T app df -h /var/www/html 2>/dev/null | awk 'NR==2 {print $5}' | sed 's/%//')
    
    if [ "$DISK_USAGE" -lt 90 ]; then
        print_pass "Disk space is adequate (${DISK_USAGE}% used)"
    else
        print_fail "Disk space is running low (${DISK_USAGE}% used)"
        return 1
    fi
}

# Test 15: Check memory usage
test_memory() {
    print_test "Checking memory usage..."
    
    MEMORY_USAGE=$(docker stats --no-stream --format "{{.MemPerc}}" mpwa-app 2>/dev/null | sed 's/%//')
    
    if [ ! -z "$MEMORY_USAGE" ]; then
        if (( $(echo "$MEMORY_USAGE < 90" | bc -l) )); then
            print_pass "Memory usage is normal (${MEMORY_USAGE}%)"
        else
            print_fail "Memory usage is high (${MEMORY_USAGE}%)"
            return 1
        fi
    else
        print_fail "Cannot determine memory usage"
        return 1
    fi
}

# Main execution
main() {
    clear
    print_header "MPWA Deployment Verification"
    
    echo "Starting comprehensive verification tests..."
    echo ""
    
    test_containers_running || true
    test_mysql_connection || true
    test_web_server || true
    test_nodejs_server || true
    test_php_fpm || true
    test_nginx || true
    test_laravel || true
    test_migrations || true
    test_permissions || true
    test_node_dependencies || true
    test_composer_dependencies || true
    test_credentials_directory || true
    test_environment || true
    test_disk_space || true
    test_memory || true
    
    echo ""
    print_header "Verification Summary"
    
    TOTAL=$((PASSED + FAILED))
    echo -e "Total Tests: ${BLUE}${TOTAL}${NC}"
    echo -e "Passed: ${GREEN}${PASSED}${NC}"
    echo -e "Failed: ${RED}${FAILED}${NC}"
    echo ""
    
    if [ $FAILED -eq 0 ]; then
        echo -e "${GREEN}✓ All tests passed! Deployment is successful.${NC}"
        echo ""
        echo -e "${BLUE}Access your application at:${NC}"
        echo -e "  Web: ${GREEN}http://localhost:${APP_PORT}${NC}"
        echo -e "  API: ${GREEN}http://localhost:${NODE_PORT}${NC}"
        echo ""
        exit 0
    else
        echo -e "${RED}✗ Some tests failed. Please check the logs and fix the issues.${NC}"
        echo ""
        echo -e "${YELLOW}Troubleshooting commands:${NC}"
        echo -e "  View logs: ${BLUE}docker-compose logs -f${NC}"
        echo -e "  Check status: ${BLUE}docker-compose ps${NC}"
        echo -e "  Restart services: ${BLUE}docker-compose restart${NC}"
        echo ""
        exit 1
    fi
}

# Run main function
main
