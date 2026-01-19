# Script to verify all deployment files are present

Write-Host "Checking MPWA Docker Deployment Files..." -ForegroundColor Blue
Write-Host "========================================" -ForegroundColor Blue
Write-Host ""

$Missing = 0
$Present = 0

function Check-File {
    param([string]$Path)
    
    if (Test-Path $Path) {
        Write-Host "✓ $Path" -ForegroundColor Green
        $script:Present++
    }
    else {
        Write-Host "✗ MISSING: $Path" -ForegroundColor Red
        $script:Missing++
    }
}

function Check-Dir {
    param([string]$Path)
    
    if (Test-Path $Path -PathType Container) {
        Write-Host "✓ $Path/" -ForegroundColor Green
        $script:Present++
    }
    else {
        Write-Host "✗ MISSING: $Path/" -ForegroundColor Red
        $script:Missing++
    }
}

Write-Host "Documentation Files:" -ForegroundColor Cyan
Check-File "START-HERE.md"
Check-File "QUICKSTART.md"
Check-File "DOCKER-DEPLOYMENT.md"
Check-File "README.Docker.md"
Check-File "DEPLOYMENT-CHECKLIST.md"
Check-File "DEPLOYMENT-SUMMARY.md"

Write-Host ""
Write-Host "Deployment Scripts:" -ForegroundColor Cyan
Check-File "deploy.sh"
Check-File "deploy.ps1"
Check-File "deploy-production.sh"
Check-File "setup-ssl.sh"

Write-Host ""
Write-Host "Verification Scripts:" -ForegroundColor Cyan
Check-File "verify-deployment.sh"
Check-File "verify-deployment.ps1"

Write-Host ""
Write-Host "Backup & Restore Scripts:" -ForegroundColor Cyan
Check-File "backup.sh"
Check-File "backup.ps1"
Check-File "restore.sh"
Check-File "restore.ps1"

Write-Host ""
Write-Host "Maintenance Scripts:" -ForegroundColor Cyan
Check-File "update.sh"
Check-File "monitor.sh"
Check-File "Makefile"

Write-Host ""
Write-Host "Docker Configuration:" -ForegroundColor Cyan
Check-File "Dockerfile"
Check-File "docker-compose.yml"
Check-File "docker-compose.prod.yml"
Check-File ".dockerignore"
Check-File ".env.docker"
Check-File ".env.example"
Check-File ".gitignore.docker"

Write-Host ""
Write-Host "Docker Config Directories:" -ForegroundColor Cyan
Check-Dir "docker"
Check-Dir "docker\nginx"
Check-Dir "docker\nginx-proxy"
Check-Dir "docker\nginx-proxy\conf.d"
Check-Dir "docker\php"
Check-Dir "docker\mysql"
Check-Dir "docker\supervisor"
Check-Dir "docker\ssl"

Write-Host ""
Write-Host "Docker Config Files:" -ForegroundColor Cyan
Check-File "docker\nginx\nginx.conf"
Check-File "docker\nginx\default.conf"
Check-File "docker\nginx-proxy\nginx.conf"
Check-File "docker\nginx-proxy\conf.d\mpwa.conf"
Check-File "docker\php\php-fpm.conf"
Check-File "docker\php\php.ini"
Check-File "docker\mysql\my.cnf"
Check-File "docker\supervisor\supervisord.conf"
Check-File "docker\healthcheck.sh"

Write-Host ""
Write-Host "========================================" -ForegroundColor Blue
Write-Host "Summary:" -ForegroundColor Blue
Write-Host "  Present: $Present files/directories" -ForegroundColor Green
Write-Host "  Missing: $Missing files/directories" -ForegroundColor $(if ($Missing -eq 0) { "Green" } else { "Red" })
Write-Host ""

if ($Missing -eq 0) {
    Write-Host "✓ All deployment files are present!" -ForegroundColor Green
    Write-Host "✓ Ready to deploy!" -ForegroundColor Green
    exit 0
}
else {
    Write-Host "✗ Some files are missing!" -ForegroundColor Red
    Write-Host "✗ Please check the missing files above" -ForegroundColor Red
    exit 1
}
