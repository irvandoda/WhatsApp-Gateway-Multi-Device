# PowerShell script to start WAGW with Docker

Write-Host "🐳 Starting WAGW Docker Setup..." -ForegroundColor Cyan
Write-Host ""

# Check if .env exists
if (-not (Test-Path .env)) {
    Write-Host "📝 Creating .env file from env.docker.example..." -ForegroundColor Yellow
    Copy-Item env.docker.example .env
    Write-Host "✅ .env file created. Please edit it with your configuration." -ForegroundColor Green
    Write-Host ""
}

# Check if APP_KEY is set
$envContent = Get-Content .env -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
    docker-compose run --rm app php artisan key:generate
    Write-Host ""
}

# Build images
Write-Host "🔨 Building Docker images..." -ForegroundColor Cyan
docker-compose build

# Start services
Write-Host "🚀 Starting services..." -ForegroundColor Cyan
docker-compose up -d

# Wait for MySQL to be ready
Write-Host "⏳ Waiting for MySQL to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Run migrations
Write-Host "📊 Running database migrations..." -ForegroundColor Cyan
docker-compose exec -T app php artisan migrate --force

# Run seeders
Write-Host "🌱 Seeding database..." -ForegroundColor Cyan
docker-compose exec -T app php artisan db:seed --force

# Optimize application
Write-Host "⚡ Optimizing application..." -ForegroundColor Cyan
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

Write-Host ""
Write-Host "✅ Setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "📱 Access your application:" -ForegroundColor Cyan
Write-Host "   - Web App: http://localhost"
Write-Host "   - Node.js Worker: http://localhost:3000"
Write-Host "   - phpMyAdmin: http://localhost:8080"
Write-Host ""
Write-Host "📝 Default admin credentials:" -ForegroundColor Cyan
Write-Host "   - Email: admin@admin.com"
Write-Host "   - Password: password"
Write-Host ""
Write-Host "📚 For more information, see DOCKER.md" -ForegroundColor Cyan
