.PHONY: help deploy start stop restart logs status shell backup restore verify clean update

# Default target
help:
	@echo "MPWA Docker Management Commands"
	@echo "================================"
	@echo ""
	@echo "Setup & Deployment:"
	@echo "  make deploy          - Full deployment (build, start, setup)"
	@echo "  make start           - Start containers"
	@echo "  make stop            - Stop containers"
	@echo "  make restart         - Restart containers"
	@echo ""
	@echo "Monitoring:"
	@echo "  make logs            - View all logs"
	@echo "  make logs-app        - View app logs"
	@echo "  make logs-mysql      - View MySQL logs"
	@echo "  make status          - Show container status"
	@echo "  make verify          - Run deployment verification"
	@echo ""
	@echo "Maintenance:"
	@echo "  make shell           - Enter app container shell"
	@echo "  make shell-mysql     - Enter MySQL shell"
	@echo "  make backup          - Create backup"
	@echo "  make clean           - Remove containers (keep data)"
	@echo "  make clean-all       - Remove containers and volumes"
	@echo "  make update          - Update application"
	@echo ""
	@echo "Laravel Commands:"
	@echo "  make artisan CMD=... - Run artisan command"
	@echo "  make migrate         - Run migrations"
	@echo "  make cache-clear     - Clear all caches"
	@echo "  make optimize        - Optimize application"
	@echo ""

# Deployment
deploy:
	@chmod +x deploy.sh
	@./deploy.sh

# Container management
start:
	@docker-compose up -d
	@echo "Containers started"

stop:
	@docker-compose stop
	@echo "Containers stopped"

restart:
	@docker-compose restart
	@echo "Containers restarted"

# Logs
logs:
	@docker-compose logs -f

logs-app:
	@docker-compose logs -f app

logs-mysql:
	@docker-compose logs -f mysql

# Status
status:
	@docker-compose ps

# Shell access
shell:
	@docker-compose exec app sh

shell-mysql:
	@docker-compose exec mysql mysql -u mpwa_user -pmpwa_pass mpwa

# Backup & Restore
backup:
	@chmod +x backup.sh
	@./backup.sh

restore:
	@chmod +x restore.sh
	@./restore.sh $(FILE)

# Verification
verify:
	@chmod +x verify-deployment.sh
	@./verify-deployment.sh

# Cleanup
clean:
	@docker-compose down
	@echo "Containers removed (data preserved)"

clean-all:
	@docker-compose down -v
	@echo "Containers and volumes removed"

# Update
update:
	@echo "Pulling latest changes..."
	@git pull
	@echo "Rebuilding containers..."
	@docker-compose build --no-cache
	@echo "Restarting services..."
	@docker-compose down
	@docker-compose up -d
	@echo "Running migrations..."
	@docker-compose exec -T app php artisan migrate --force
	@echo "Clearing caches..."
	@docker-compose exec -T app php artisan config:cache
	@docker-compose exec -T app php artisan route:cache
	@docker-compose exec -T app php artisan view:cache
	@echo "Update completed"

# Laravel commands
artisan:
	@docker-compose exec app php artisan $(CMD)

migrate:
	@docker-compose exec app php artisan migrate --force

cache-clear:
	@docker-compose exec app php artisan config:clear
	@docker-compose exec app php artisan cache:clear
	@docker-compose exec app php artisan route:clear
	@docker-compose exec app php artisan view:clear
	@echo "All caches cleared"

optimize:
	@docker-compose exec app php artisan config:cache
	@docker-compose exec app php artisan route:cache
	@docker-compose exec app php artisan view:cache
	@docker-compose exec app composer dump-autoload --optimize
	@echo "Application optimized"

# Build
build:
	@docker-compose build --no-cache

rebuild:
	@docker-compose down
	@docker-compose build --no-cache
	@docker-compose up -d
	@echo "Containers rebuilt"
