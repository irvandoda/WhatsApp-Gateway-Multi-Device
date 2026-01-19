#!/bin/bash

# MPWA Monitoring Script
# Real-time monitoring of MPWA containers

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_header() {
    clear
    echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║           MPWA Docker Monitoring Dashboard                ║${NC}"
    echo -e "${BLUE}║                 $(date '+%Y-%m-%d %H:%M:%S')                      ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

check_container_status() {
    echo -e "${BLUE}Container Status:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
    # Check app container
    if docker-compose ps | grep -q "mpwa-app.*Up"; then
        echo -e "  App Container:   ${GREEN}✓ Running${NC}"
    else
        echo -e "  App Container:   ${RED}✗ Stopped${NC}"
    fi
    
    # Check MySQL container
    if docker-compose ps | grep -q "mpwa-mysql.*Up"; then
        echo -e "  MySQL Container: ${GREEN}✓ Running${NC}"
    else
        echo -e "  MySQL Container: ${RED}✗ Stopped${NC}"
    fi
    echo ""
}

check_services() {
    echo -e "${BLUE}Service Status:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
    # Check PHP-FPM
    if docker-compose exec -T app pgrep php-fpm >/dev/null 2>&1; then
        echo -e "  PHP-FPM:         ${GREEN}✓ Running${NC}"
    else
        echo -e "  PHP-FPM:         ${RED}✗ Stopped${NC}"
    fi
    
    # Check Nginx
    if docker-compose exec -T app pgrep nginx >/dev/null 2>&1; then
        echo -e "  Nginx:           ${GREEN}✓ Running${NC}"
    else
        echo -e "  Nginx:           ${RED}✗ Stopped${NC}"
    fi
    
    # Check Node.js
    if docker-compose exec -T app pgrep node >/dev/null 2>&1; then
        echo -e "  Node.js:         ${GREEN}✓ Running${NC}"
    else
        echo -e "  Node.js:         ${RED}✗ Stopped${NC}"
    fi
    
    # Check MySQL
    if docker-compose exec -T mysql mysqladmin ping -h localhost --silent 2>/dev/null; then
        echo -e "  MySQL:           ${GREEN}✓ Running${NC}"
    else
        echo -e "  MySQL:           ${RED}✗ Stopped${NC}"
    fi
    echo ""
}

check_connectivity() {
    echo -e "${BLUE}Connectivity:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
    # Check web server
    if curl -f -s -o /dev/null http://localhost:8000; then
        echo -e "  Web Server:      ${GREEN}✓ Responding${NC}"
    else
        echo -e "  Web Server:      ${RED}✗ Not Responding${NC}"
    fi
    
    # Check Node.js server
    if docker-compose exec -T app netstat -tuln 2>/dev/null | grep -q ":3100"; then
        echo -e "  Node.js API:     ${GREEN}✓ Listening${NC}"
    else
        echo -e "  Node.js API:     ${RED}✗ Not Listening${NC}"
    fi
    
    # Check database
    if docker-compose exec -T app php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null | grep -q "PDO"; then
        echo -e "  Database:        ${GREEN}✓ Connected${NC}"
    else
        echo -e "  Database:        ${RED}✗ Not Connected${NC}"
    fi
    echo ""
}

show_resource_usage() {
    echo -e "${BLUE}Resource Usage:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
    # Get container stats
    docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}" | grep mpwa
    echo ""
}

show_disk_usage() {
    echo -e "${BLUE}Disk Usage:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
    # Docker disk usage
    docker system df | tail -n +2
    echo ""
    
    # Volume usage
    echo -e "${BLUE}Volume Usage:${NC}"
    docker volume ls --format "table {{.Name}}\t{{.Driver}}" | grep mpwa
    echo ""
}

show_recent_logs() {
    echo -e "${BLUE}Recent Logs (Last 10 lines):${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    docker-compose logs --tail=10 app 2>&1 | tail -n 10
    echo ""
}

show_database_info() {
    echo -e "${BLUE}Database Information:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
    # Get database size
    DB_SIZE=$(docker-compose exec -T mysql mysql -u mpwa_user -pmpwa_pass -e "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.TABLES WHERE table_schema = 'mpwa';" 2>/dev/null | tail -n 1)
    echo -e "  Database Size:   ${GREEN}${DB_SIZE} MB${NC}"
    
    # Get table count
    TABLE_COUNT=$(docker-compose exec -T mysql mysql -u mpwa_user -pmpwa_pass -e "SELECT COUNT(*) FROM information_schema.TABLES WHERE table_schema = 'mpwa';" 2>/dev/null | tail -n 1)
    echo -e "  Tables:          ${GREEN}${TABLE_COUNT}${NC}"
    
    # Get connection count
    CONN_COUNT=$(docker-compose exec -T mysql mysql -u mpwa_user -pmpwa_pass -e "SHOW STATUS LIKE 'Threads_connected';" 2>/dev/null | tail -n 1 | awk '{print $2}')
    echo -e "  Connections:     ${GREEN}${CONN_COUNT}${NC}"
    echo ""
}

show_menu() {
    echo -e "${YELLOW}Commands:${NC}"
    echo "  [r] Refresh  [l] View Logs  [s] Shell  [q] Quit"
    echo ""
}

# Main monitoring loop
main() {
    while true; do
        print_header
        check_container_status
        check_services
        check_connectivity
        show_resource_usage
        show_disk_usage
        show_database_info
        show_recent_logs
        show_menu
        
        # Wait for user input with timeout
        read -t 5 -n 1 key
        
        case $key in
            r|R)
                continue
                ;;
            l|L)
                docker-compose logs -f
                ;;
            s|S)
                docker-compose exec app sh
                ;;
            q|Q)
                echo "Exiting monitor..."
                exit 0
                ;;
        esac
    done
}

# Run main function
main
