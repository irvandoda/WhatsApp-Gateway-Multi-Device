# MPWA Restore Script for Windows PowerShell
# Restores backup created by backup.ps1

param(
    [Parameter(Mandatory=$false)]
    [string]$BackupFile
)

$ErrorActionPreference = "Stop"

function Write-Info {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor Cyan
}

function Write-Success {
    param([string]$Message)
    Write-Host "[SUCCESS] $Message" -ForegroundColor Green
}

function Write-Error-Custom {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

function Write-Warning-Custom {
    param([string]$Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

# Check if backup file is provided
if (-not $BackupFile) {
    Write-Error-Custom "Please provide backup file path"
    Write-Host "Usage: .\restore.ps1 -BackupFile <backup_file.zip>"
    Write-Host ""
    Write-Host "Available backups:"
    if (Test-Path "backups") {
        Get-ChildItem -Path "backups" -Filter "*.zip" | Format-Table Name, Length, LastWriteTime
    }
    else {
        Write-Host "No backups found"
    }
    exit 1
}

# Check if backup file exists
if (-not (Test-Path $BackupFile)) {
    Write-Error-Custom "Backup file not found: $BackupFile"
    exit 1
}

Write-Info "Starting restore process..."
Write-Info "Backup file: $BackupFile"

# Confirm restore
Write-Host ""
Write-Warning-Custom "This will overwrite existing data!"
$confirmation = Read-Host "Are you sure you want to continue? (yes/no)"
Write-Host ""
if ($confirmation -ne "yes") {
    Write-Info "Restore cancelled"
    exit 0
}

# Create temporary directory
$TempDir = Join-Path $env:TEMP "mpwa_restore_$(Get-Date -Format 'yyyyMMddHHmmss')"
New-Item -ItemType Directory -Path $TempDir | Out-Null

Write-Info "Extracting backup to temporary directory..."
Expand-Archive -Path $BackupFile -DestinationPath $TempDir -Force

# Find the backup directory
$BackupDir = Get-ChildItem -Path $TempDir -Directory -Filter "mpwa_backup_*" | Select-Object -First 1

if (-not $BackupDir) {
    Write-Error-Custom "Invalid backup file structure"
    Remove-Item -Recurse -Force $TempDir
    exit 1
}

$BackupPath = $BackupDir.FullName
Write-Success "Backup extracted"

# Stop containers
Write-Info "Stopping containers..."
docker-compose stop

# Restore database
$dbBackup = Join-Path $BackupPath "database.sql"
if (Test-Path $dbBackup) {
    Write-Info "Restoring database..."
    docker-compose start mysql
    Start-Sleep -Seconds 5
    
    # Wait for MySQL
    $maxTries = 30
    $counter = 0
    $mysqlReady = $false
    
    while ($counter -lt $maxTries) {
        try {
            docker-compose exec -T mysql mysqladmin ping -h localhost --silent 2>&1 | Out-Null
            if ($LASTEXITCODE -eq 0) {
                $mysqlReady = $true
                break
            }
        }
        catch {
            # Continue waiting
        }
        Write-Host "." -NoNewline
        Start-Sleep -Seconds 2
        $counter++
    }
    Write-Host ""
    
    if (-not $mysqlReady) {
        Write-Error-Custom "MySQL failed to start"
        Remove-Item -Recurse -Force $TempDir
        exit 1
    }
    
    Get-Content $dbBackup | docker-compose exec -T mysql mysql -u mpwa_user -pmpwa_pass mpwa
    Write-Success "Database restored"
}
else {
    Write-Warning-Custom "No database backup found"
}

# Restore .env file
$envBackup = Join-Path $BackupPath ".env"
if (Test-Path $envBackup) {
    Write-Info "Restoring .env file..."
    Copy-Item $envBackup .env -Force
    Write-Success ".env file restored"
}
else {
    Write-Warning-Custom "No .env backup found"
}

# Restore credentials
$credBackup = Join-Path $BackupPath "credentials"
if (Test-Path $credBackup) {
    Write-Info "Restoring credentials..."
    if (Test-Path credentials) {
        Remove-Item -Recurse -Force credentials
    }
    Copy-Item -Recurse $credBackup . -Force
    Write-Success "Credentials restored"
}
else {
    Write-Warning-Custom "No credentials backup found"
}

# Restore storage
$storageBackup = Join-Path $BackupPath "storage.zip"
if (Test-Path $storageBackup) {
    Write-Info "Restoring storage..."
    if (Test-Path storage) {
        Remove-Item -Recurse -Force storage
    }
    Expand-Archive -Path $storageBackup -DestinationPath . -Force
    Write-Success "Storage restored"
}
else {
    Write-Warning-Custom "No storage backup found"
}

# Clean up temporary directory
Remove-Item -Recurse -Force $TempDir

# Start all containers
Write-Info "Starting all containers..."
docker-compose up -d

# Wait for services to be ready
Write-Info "Waiting for services to be ready..."
Start-Sleep -Seconds 10

# Set permissions
Write-Info "Setting permissions..."
docker-compose exec -T app chown -R www-data:www-data /var/www/html/storage
docker-compose exec -T app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec -T app chown -R www-data:www-data /var/www/html/credentials
docker-compose exec -T app chmod -R 775 /var/www/html/storage
docker-compose exec -T app chmod -R 775 /var/www/html/bootstrap/cache
docker-compose exec -T app chmod -R 775 /var/www/html/credentials

# Clear caches
Write-Info "Clearing caches..."
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan view:clear

Write-Success "Restore completed successfully!"
Write-Host ""
Write-Host "Your application has been restored from backup" -ForegroundColor Green
Write-Host "Access your application at: " -NoNewline
Write-Host "http://localhost:8000" -ForegroundColor Blue
Write-Host ""
