# MPWA Deployment Verification Script for Windows PowerShell
# This script verifies that all components are working correctly

param(
    [int]$AppPort = 8000,
    [int]$NodePort = 3100,
    [int]$DbPort = 3306
)

$ErrorActionPreference = "Continue"
$Passed = 0
$Failed = 0

function Write-TestHeader {
    param([string]$Message)
    Write-Host "`n[TEST] $Message" -ForegroundColor Cyan
}

function Write-Pass {
    param([string]$Message)
    Write-Host "[PASS] $Message" -ForegroundColor Green
    $script:Passed++
}

function Write-Fail {
    param([string]$Message)
    Write-Host "[FAIL] $Message" -ForegroundColor Red
    $script:Failed++
}

function Write-SectionHeader {
    param([string]$Message)
    Write-Host "`n================================================" -ForegroundColor Blue
    Write-Host "  $Message" -ForegroundColor Blue
    Write-Host "================================================`n" -ForegroundColor Blue
}

function Test-ContainersRunning {
    Write-TestHeader "Checking if containers are running..."
    
    $containers = docker-compose ps 2>&1 | Out-String
    
    if ($containers -match "mpwa-app.*Up") {
        Write-Pass "App container is running"
    }
    else {
        Write-Fail "App container is not running"
    }
    
    if ($containers -match "mpwa-mysql.*Up") {
        Write-Pass "MySQL container is running"
    }
    else {
        Write-Fail "MySQL container is not running"
    }
}

function Test-MySQLConnection {
    Write-TestHeader "Checking MySQL connection..."
    
    try {
        docker-compose exec -T mysql mysqladmin ping -h localhost --silent 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "MySQL is responding"
        }
        else {
            Write-Fail "MySQL is not responding"
        }
    }
    catch {
        Write-Fail "MySQL is not responding"
    }
    
    try {
        docker-compose exec -T mysql mysql -u mpwa_user -pmpwa_pass -e "USE mpwa;" 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "Database 'mpwa' exists and is accessible"
        }
        else {
            Write-Fail "Cannot access database 'mpwa'"
        }
    }
    catch {
        Write-Fail "Cannot access database 'mpwa'"
    }
}

function Test-WebServer {
    Write-TestHeader "Checking web server..."
    
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:$AppPort" -UseBasicParsing -TimeoutSec 10 -ErrorAction Stop
        if ($response.StatusCode -eq 200 -or $response.StatusCode -eq 302) {
            Write-Pass "Web server is responding"
        }
        else {
            Write-Fail "Web server returned status code: $($response.StatusCode)"
        }
    }
    catch {
        Write-Fail "Web server is not responding on port $AppPort"
    }
}

function Test-NodeJSServer {
    Write-TestHeader "Checking Node.js server..."
    
    $logs = docker-compose logs app 2>&1 | Out-String
    
    if ($logs -match "Server running and listening on port") {
        Write-Pass "Node.js server is running"
    }
    else {
        Write-Fail "Node.js server is not running"
    }
    
    $netstat = docker-compose exec -T app netstat -tuln 2>&1 | Out-String
    if ($netstat -match ":$NodePort") {
        Write-Pass "Node.js server is listening on port $NodePort"
    }
    else {
        Write-Fail "Node.js server is not listening on port $NodePort"
    }
}

function Test-PHPFPM {
    Write-TestHeader "Checking PHP-FPM..."
    
    try {
        docker-compose exec -T app pgrep php-fpm 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "PHP-FPM is running"
        }
        else {
            Write-Fail "PHP-FPM is not running"
        }
    }
    catch {
        Write-Fail "PHP-FPM is not running"
    }
}

function Test-Nginx {
    Write-TestHeader "Checking Nginx..."
    
    try {
        docker-compose exec -T app pgrep nginx 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "Nginx is running"
        }
        else {
            Write-Fail "Nginx is not running"
        }
    }
    catch {
        Write-Fail "Nginx is not running"
    }
}

function Test-Laravel {
    Write-TestHeader "Checking Laravel installation..."
    
    $version = docker-compose exec -T app php artisan --version 2>&1 | Out-String
    
    if ($version -match "Laravel") {
        Write-Pass "Laravel is installed"
    }
    else {
        Write-Fail "Laravel is not properly installed"
    }
    
    $appKey = docker-compose exec -T app php artisan tinker --execute="echo config('app.key');" 2>&1 | Out-String
    if ($appKey -match "base64:") {
        Write-Pass "APP_KEY is configured"
    }
    else {
        Write-Fail "APP_KEY is not configured"
    }
}

function Test-Migrations {
    Write-TestHeader "Checking database migrations..."
    
    $migrations = docker-compose exec -T app php artisan migrate:status 2>&1 | Out-String
    
    if ($migrations -match "Ran") {
        Write-Pass "Database migrations have been run"
    }
    else {
        Write-Fail "Database migrations have not been run"
    }
}

function Test-Permissions {
    Write-TestHeader "Checking storage permissions..."
    
    try {
        docker-compose exec -T app test -w /var/www/html/storage 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "Storage directory is writable"
        }
        else {
            Write-Fail "Storage directory is not writable"
        }
    }
    catch {
        Write-Fail "Storage directory is not writable"
    }
    
    try {
        docker-compose exec -T app test -w /var/www/html/bootstrap/cache 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "Bootstrap cache directory is writable"
        }
        else {
            Write-Fail "Bootstrap cache directory is not writable"
        }
    }
    catch {
        Write-Fail "Bootstrap cache directory is not writable"
    }
}

function Test-NodeDependencies {
    Write-TestHeader "Checking Node.js dependencies..."
    
    try {
        docker-compose exec -T app test -d /var/www/html/node_modules 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "Node.js dependencies are installed"
        }
        else {
            Write-Fail "Node.js dependencies are not installed"
        }
    }
    catch {
        Write-Fail "Node.js dependencies are not installed"
    }
}

function Test-ComposerDependencies {
    Write-TestHeader "Checking Composer dependencies..."
    
    try {
        docker-compose exec -T app test -d /var/www/html/vendor 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "Composer dependencies are installed"
        }
        else {
            Write-Fail "Composer dependencies are not installed"
        }
    }
    catch {
        Write-Fail "Composer dependencies are not installed"
    }
}

function Test-CredentialsDirectory {
    Write-TestHeader "Checking credentials directory..."
    
    try {
        docker-compose exec -T app test -d /var/www/html/credentials 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "Credentials directory exists"
        }
        else {
            Write-Fail "Credentials directory does not exist"
        }
    }
    catch {
        Write-Fail "Credentials directory does not exist"
    }
    
    try {
        docker-compose exec -T app test -w /var/www/html/credentials 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass "Credentials directory is writable"
        }
        else {
            Write-Fail "Credentials directory is not writable"
        }
    }
    catch {
        Write-Fail "Credentials directory is not writable"
    }
}

function Test-Environment {
    Write-TestHeader "Checking environment configuration..."
    
    try {
        docker-compose exec -T app test -f /var/www/html/.env 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Pass ".env file exists"
        }
        else {
            Write-Fail ".env file does not exist"
        }
    }
    catch {
        Write-Fail ".env file does not exist"
    }
    
    $envContent = docker-compose exec -T app cat /var/www/html/.env 2>&1 | Out-String
    if ($envContent -match "DB_HOST=mysql") {
        Write-Pass "Database host is configured correctly"
    }
    else {
        Write-Fail "Database host is not configured correctly"
    }
}

function Test-DiskSpace {
    Write-TestHeader "Checking disk space..."
    
    $diskInfo = docker-compose exec -T app df -h /var/www/html 2>&1 | Out-String
    if ($diskInfo -match "(\d+)%") {
        $usage = [int]$matches[1]
        if ($usage -lt 90) {
            Write-Pass "Disk space is adequate ($usage% used)"
        }
        else {
            Write-Fail "Disk space is running low ($usage% used)"
        }
    }
    else {
        Write-Fail "Cannot determine disk space usage"
    }
}

function Test-Memory {
    Write-TestHeader "Checking memory usage..."
    
    try {
        $memStats = docker stats --no-stream --format "{{.MemPerc}}" mpwa-app 2>&1 | Out-String
        if ($memStats -match "(\d+\.?\d*)%") {
            $usage = [decimal]$matches[1]
            if ($usage -lt 90) {
                Write-Pass "Memory usage is normal ($usage%)"
            }
            else {
                Write-Fail "Memory usage is high ($usage%)"
            }
        }
        else {
            Write-Fail "Cannot determine memory usage"
        }
    }
    catch {
        Write-Fail "Cannot determine memory usage"
    }
}

function Main {
    Clear-Host
    Write-SectionHeader "MPWA Deployment Verification"
    
    Write-Host "Starting comprehensive verification tests...`n"
    
    Test-ContainersRunning
    Test-MySQLConnection
    Test-WebServer
    Test-NodeJSServer
    Test-PHPFPM
    Test-Nginx
    Test-Laravel
    Test-Migrations
    Test-Permissions
    Test-NodeDependencies
    Test-ComposerDependencies
    Test-CredentialsDirectory
    Test-Environment
    Test-DiskSpace
    Test-Memory
    
    Write-SectionHeader "Verification Summary"
    
    $Total = $Passed + $Failed
    Write-Host "Total Tests: " -NoNewline
    Write-Host $Total -ForegroundColor Blue
    Write-Host "Passed: " -NoNewline
    Write-Host $Passed -ForegroundColor Green
    Write-Host "Failed: " -NoNewline
    Write-Host $Failed -ForegroundColor Red
    Write-Host ""
    
    if ($Failed -eq 0) {
        Write-Host "✓ All tests passed! Deployment is successful." -ForegroundColor Green
        Write-Host ""
        Write-Host "Access your application at:" -ForegroundColor Blue
        Write-Host "  Web: " -NoNewline
        Write-Host "http://localhost:$AppPort" -ForegroundColor Green
        Write-Host "  API: " -NoNewline
        Write-Host "http://localhost:$NodePort" -ForegroundColor Green
        Write-Host ""
        exit 0
    }
    else {
        Write-Host "✗ Some tests failed. Please check the logs and fix the issues." -ForegroundColor Red
        Write-Host ""
        Write-Host "Troubleshooting commands:" -ForegroundColor Yellow
        Write-Host "  View logs: " -NoNewline
        Write-Host "docker-compose logs -f" -ForegroundColor Blue
        Write-Host "  Check status: " -NoNewline
        Write-Host "docker-compose ps" -ForegroundColor Blue
        Write-Host "  Restart services: " -NoNewline
        Write-Host "docker-compose restart" -ForegroundColor Blue
        Write-Host ""
        exit 1
    }
}

# Run main function
Main
