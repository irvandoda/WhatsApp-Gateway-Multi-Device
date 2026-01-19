$ErrorActionPreference = "Stop"

Write-Host "MPWA Docker Deployment Starting..." -ForegroundColor Blue

# Check Docker
Write-Host "Checking Docker..." -ForegroundColor Cyan
docker --version
if ($LASTEXITCODE -ne 0) { exit 1 }

# Check Docker Compose  
docker-compose --version
if ($LASTEXITCODE -ne 0) { exit 1 }

# Setup .env
Write-Host "Setting up environment..." -ForegroundColor Cyan
if (-not (Test-Path .env)) {
    Copy-Item .env.docker .env
    Write-Host ".env created" -ForegroundColor Green
}

# Generate APP_KEY
$env = Get-Content .env -Raw
if ($env -notmatch "APP_KEY=base64:") {
    $bytes = New-Object byte[] 32
    [Security.Cryptography.RNGCryptoServiceProvider]::Create().GetBytes($bytes)
    $key = [Convert]::ToBase64String($bytes)
    $env = $env -replace "APP_KEY=", "APP_KEY=base64:$key"
    Set-Content .env $env -NoNewline
    Write-Host "APP_KEY generated" -ForegroundColor Green
}

# Create directories
Write-Host "Creating directories..." -ForegroundColor Cyan
New-Item -ItemType Directory -Force -Path storage/app/public | Out-Null
New-Item -ItemType Directory -Force -Path storage/framework/cache/data | Out-Null
New-Item -ItemType Directory -Force -Path storage/framework/sessions | Out-Null
New-Item -ItemType Directory -Force -Path storage/framework/views | Out-Null
New-Item -ItemType Directory -Force -Path storage/logs | Out-Null
New-Item -ItemType Directory -Force -Path bootstrap/cache | Out-Null
New-Item -ItemType Directory -Force -Path credentials | Out-Null

# Build containers
Write-Host "Building Docker containers..." -ForegroundColor Cyan
docker-compose build
if ($LASTEXITCODE -ne 0) { 
    Write-Host "Build failed!" -ForegroundColor Red
    exit 1 
}

# Start containers
Write-Host "Starting containers..." -ForegroundColor Cyan
docker-compose up -d
if ($LASTEXITCODE -ne 0) { 
    Write-Host "Start failed!" -ForegroundColor Red
    exit 1 
}

# Wait for MySQL
Write-Host "Waiting for MySQL..." -ForegroundColor Cyan
Start-Sleep -Seconds 15

# Setup application
Write-Host "Setting up application..." -ForegroundColor Cyan
docker-compose exec -T app composer install --no-dev --optimize-autoloader
docker-compose exec -T app php artisan migrate --force
docker-compose exec -T app php artisan db:seed --force
docker-compose exec -T app php artisan storage:link
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

# Set permissions
Write-Host "Setting permissions..." -ForegroundColor Cyan
docker-compose exec -T app chown -R www-data:www-data /var/www/html/storage
docker-compose exec -T app chmod -R 775 /var/www/html/storage

Write-Host "`nDeployment Complete!" -ForegroundColor Green
Write-Host "Access: http://localhost:8000" -ForegroundColor Yellow
Write-Host "Node.js: http://localhost:3100" -ForegroundColor Yellow
