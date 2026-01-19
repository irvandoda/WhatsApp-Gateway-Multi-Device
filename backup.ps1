# MPWA Backup Script for Windows PowerShell
# Creates a complete backup of database, credentials, and storage

$ErrorActionPreference = "Stop"

# Configuration
$BackupDir = "backups"
$Date = Get-Date -Format "yyyyMMdd_HHmmss"
$BackupName = "mpwa_backup_$Date"
$BackupPath = Join-Path $BackupDir $BackupName

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

# Create backup directory
if (-not (Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir | Out-Null
}

if (-not (Test-Path $BackupPath)) {
    New-Item -ItemType Directory -Path $BackupPath | Out-Null
}

Write-Info "Starting backup process..."
Write-Info "Backup location: $BackupPath"

# Backup database
Write-Info "Backing up database..."
try {
    docker-compose exec -T mysql mysqldump -u mpwa_user -pmpwa_pass mpwa | Out-File -FilePath "$BackupPath\database.sql" -Encoding UTF8
    Write-Success "Database backed up"
}
catch {
    Write-Error-Custom "Failed to backup database: $_"
    exit 1
}

# Backup .env file
Write-Info "Backing up .env file..."
if (Test-Path .env) {
    Copy-Item .env "$BackupPath\.env"
    Write-Success ".env file backed up"
}
else {
    Write-Warning-Custom ".env file not found"
}

# Backup credentials
Write-Info "Backing up credentials..."
if ((Test-Path credentials) -and (Get-ChildItem credentials -Recurse)) {
    Copy-Item -Recurse credentials "$BackupPath\"
    Write-Success "Credentials backed up"
}
else {
    Write-Warning-Custom "No credentials to backup"
}

# Backup storage
Write-Info "Backing up storage..."
if (Test-Path storage) {
    Compress-Archive -Path storage -DestinationPath "$BackupPath\storage.zip" -Force
    Write-Success "Storage backed up"
}
else {
    Write-Warning-Custom "Storage directory not found"
}

# Create backup info file
$backupInfo = @"
MPWA Backup Information
=======================
Backup Date: $(Get-Date)
Backup Name: $BackupName
Database: mpwa
User: mpwa_user

Contents:
- database.sql: MySQL database dump
- .env: Environment configuration
- credentials/: WhatsApp session credentials
- storage.zip: Application storage files

Restore Instructions:
1. Extract this backup to your MPWA directory
2. Run: docker-compose exec -T mysql mysql -u mpwa_user -pmpwa_pass mpwa < database.sql
3. Copy .env file to root directory
4. Extract storage.zip to root directory
5. Copy credentials/ to root directory
6. Restart containers: docker-compose restart
"@

Set-Content -Path "$BackupPath\backup_info.txt" -Value $backupInfo

# Create compressed archive
Write-Info "Creating compressed archive..."
$archivePath = "$BackupDir\$BackupName.zip"
Compress-Archive -Path $BackupPath -DestinationPath $archivePath -Force
Remove-Item -Recurse -Force $BackupPath

$backupSize = (Get-Item $archivePath).Length / 1MB
$backupSizeFormatted = "{0:N2} MB" -f $backupSize

Write-Success "Backup completed successfully!"
Write-Host ""
Write-Host "Backup Details:" -ForegroundColor Green
Write-Host "  Location: " -NoNewline
Write-Host $archivePath -ForegroundColor Blue
Write-Host "  Size: " -NoNewline
Write-Host $backupSizeFormatted -ForegroundColor Blue
Write-Host "  Date: " -NoNewline
Write-Host (Get-Date) -ForegroundColor Blue
Write-Host ""
Write-Host "To restore this backup:" -ForegroundColor Yellow
Write-Host "  1. Extract: " -NoNewline
Write-Host "Expand-Archive -Path $archivePath -DestinationPath ." -ForegroundColor Blue
Write-Host "  2. Follow instructions in backup_info.txt"
Write-Host ""

# Clean old backups (keep last 7 days)
Write-Info "Cleaning old backups (keeping last 7 days)..."
$oldBackups = Get-ChildItem -Path $BackupDir -Filter "mpwa_backup_*.zip" | Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-7) }
foreach ($backup in $oldBackups) {
    Remove-Item $backup.FullName -Force
    Write-Info "Removed old backup: $($backup.Name)"
}
Write-Success "Old backups cleaned"
