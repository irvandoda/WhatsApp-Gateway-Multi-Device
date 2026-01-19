#!/bin/bash

# Script to verify all deployment files are present

echo "Checking MPWA Docker Deployment Files..."
echo "========================================"
echo ""

MISSING=0
PRESENT=0

check_file() {
    if [ -f "$1" ]; then
        echo "✓ $1"
        PRESENT=$((PRESENT + 1))
    else
        echo "✗ MISSING: $1"
        MISSING=$((MISSING + 1))
    fi
}

check_dir() {
    if [ -d "$1" ]; then
        echo "✓ $1/"
        PRESENT=$((PRESENT + 1))
    else
        echo "✗ MISSING: $1/"
        MISSING=$((MISSING + 1))
    fi
}

echo "Documentation Files:"
check_file "START-HERE.md"
check_file "QUICKSTART.md"
check_file "DOCKER-DEPLOYMENT.md"
check_file "README.Docker.md"
check_file "DEPLOYMENT-CHECKLIST.md"
check_file "DEPLOYMENT-SUMMARY.md"

echo ""
echo "Deployment Scripts:"
check_file "deploy.sh"
check_file "deploy.ps1"
check_file "deploy-production.sh"
check_file "setup-ssl.sh"

echo ""
echo "Verification Scripts:"
check_file "verify-deployment.sh"
check_file "verify-deployment.ps1"

echo ""
echo "Backup & Restore Scripts:"
check_file "backup.sh"
check_file "backup.ps1"
check_file "restore.sh"
check_file "restore.ps1"

echo ""
echo "Maintenance Scripts:"
check_file "update.sh"
check_file "monitor.sh"
check_file "Makefile"

echo ""
echo "Docker Configuration:"
check_file "Dockerfile"
check_file "docker-compose.yml"
check_file "docker-compose.prod.yml"
check_file ".dockerignore"
check_file ".env.docker"
check_file ".env.example"
check_file ".gitignore.docker"

echo ""
echo "Docker Config Directories:"
check_dir "docker"
check_dir "docker/nginx"
check_dir "docker/nginx-proxy"
check_dir "docker/nginx-proxy/conf.d"
check_dir "docker/php"
check_dir "docker/mysql"
check_dir "docker/supervisor"
check_dir "docker/ssl"

echo ""
echo "Docker Config Files:"
check_file "docker/nginx/nginx.conf"
check_file "docker/nginx/default.conf"
check_file "docker/nginx-proxy/nginx.conf"
check_file "docker/nginx-proxy/conf.d/mpwa.conf"
check_file "docker/php/php-fpm.conf"
check_file "docker/php/php.ini"
check_file "docker/mysql/my.cnf"
check_file "docker/supervisor/supervisord.conf"
check_file "docker/healthcheck.sh"

echo ""
echo "========================================"
echo "Summary:"
echo "  Present: $PRESENT files/directories"
echo "  Missing: $MISSING files/directories"
echo ""

if [ $MISSING -eq 0 ]; then
    echo "✓ All deployment files are present!"
    echo "✓ Ready to deploy!"
    exit 0
else
    echo "✗ Some files are missing!"
    echo "✗ Please check the missing files above"
    exit 1
fi
