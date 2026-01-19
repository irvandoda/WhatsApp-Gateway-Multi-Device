#!/bin/bash

# MPWA Backup Script
# Creates a complete backup of database, credentials, and storage

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Configuration
BACKUP_DIR="backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="mpwa_backup_${DATE}"
BACKUP_PATH="${BACKUP_DIR}/${BACKUP_NAME}"

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

# Create backup directory
mkdir -p "${BACKUP_PATH}"

print_info "Starting backup process..."
print_info "Backup location: ${BACKUP_PATH}"

# Backup database
print_info "Backing up database..."
docker-compose exec -T mysql mysqldump -u mpwa_user -pmpwa_pass mpwa > "${BACKUP_PATH}/database.sql"
print_success "Database backed up"

# Backup .env file
print_info "Backing up .env file..."
if [ -f .env ]; then
    cp .env "${BACKUP_PATH}/.env"
    print_success ".env file backed up"
else
    print_warning ".env file not found"
fi

# Backup credentials
print_info "Backing up credentials..."
if [ -d credentials ] && [ "$(ls -A credentials)" ]; then
    cp -r credentials "${BACKUP_PATH}/"
    print_success "Credentials backed up"
else
    print_warning "No credentials to backup"
fi

# Backup storage
print_info "Backing up storage..."
if [ -d storage ]; then
    tar -czf "${BACKUP_PATH}/storage.tar.gz" storage/
    print_success "Storage backed up"
else
    print_warning "Storage directory not found"
fi

# Create backup info file
cat > "${BACKUP_PATH}/backup_info.txt" << EOF
MPWA Backup Information
=======================
Backup Date: $(date)
Backup Name: ${BACKUP_NAME}
Database: mpwa
User: mpwa_user

Contents:
- database.sql: MySQL database dump
- .env: Environment configuration
- credentials/: WhatsApp session credentials
- storage.tar.gz: Application storage files

Restore Instructions:
1. Extract this backup to your MPWA directory
2. Run: docker-compose exec -T mysql mysql -u mpwa_user -pmpwa_pass mpwa < database.sql
3. Copy .env file to root directory
4. Extract storage.tar.gz: tar -xzf storage.tar.gz
5. Copy credentials/ to root directory
6. Restart containers: docker-compose restart
EOF

# Create compressed archive
print_info "Creating compressed archive..."
cd "${BACKUP_DIR}"
tar -czf "${BACKUP_NAME}.tar.gz" "${BACKUP_NAME}/"
rm -rf "${BACKUP_NAME}/"
cd ..

BACKUP_SIZE=$(du -h "${BACKUP_DIR}/${BACKUP_NAME}.tar.gz" | cut -f1)

print_success "Backup completed successfully!"
echo ""
echo -e "${GREEN}Backup Details:${NC}"
echo -e "  Location: ${BLUE}${BACKUP_DIR}/${BACKUP_NAME}.tar.gz${NC}"
echo -e "  Size: ${BLUE}${BACKUP_SIZE}${NC}"
echo -e "  Date: ${BLUE}$(date)${NC}"
echo ""
echo -e "${YELLOW}To restore this backup:${NC}"
echo -e "  1. Extract: ${BLUE}tar -xzf ${BACKUP_DIR}/${BACKUP_NAME}.tar.gz${NC}"
echo -e "  2. Follow instructions in backup_info.txt"
echo ""

# Clean old backups (keep last 7 days)
print_info "Cleaning old backups (keeping last 7 days)..."
find "${BACKUP_DIR}" -name "mpwa_backup_*.tar.gz" -type f -mtime +7 -delete
print_success "Old backups cleaned"
