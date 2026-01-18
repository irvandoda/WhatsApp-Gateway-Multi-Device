.PHONY: help build up down restart logs shell composer npm migrate seed queue node clean

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build all Docker images
	docker-compose build

up: ## Start all containers
	docker-compose up -d

down: ## Stop all containers
	docker-compose down

restart: ## Restart all containers
	docker-compose restart

logs: ## Show logs from all containers
	docker-compose logs -f

logs-app: ## Show logs from Laravel app
	docker-compose logs -f app

logs-node: ## Show logs from Node.js worker
	docker-compose logs -f node

logs-queue: ## Show logs from queue worker
	docker-compose logs -f queue

shell: ## Open shell in Laravel container
	docker-compose exec app bash

shell-node: ## Open shell in Node.js container
	docker-compose exec node sh

composer: ## Run composer install
	docker-compose exec app composer install

npm: ## Run npm install
	docker-compose exec node npm install

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migration with seeding
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

queue: ## Start queue worker manually
	docker-compose exec app php artisan queue:work

cache-clear: ## Clear all caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

cache-optimize: ## Optimize caches
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache

node: ## Start Node.js worker manually
	docker-compose exec node node server.js

clean: ## Remove all containers, volumes, and images
	docker-compose down -v --rmi all

ps: ## Show running containers
	docker-compose ps
