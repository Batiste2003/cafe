# Smart Café - Docker Makefile
# ============================
# Usage: make [command]

.PHONY: help build up up-all down restart logs \
        shell shell-front artisan migrate fresh seed \
        test composer npm clean prune

# Default target - show help
help:
	@echo "Smart Café - Docker Commands"
	@echo "============================"
	@echo ""
	@echo "Starting Services:"
	@echo "  make build      - Build Docker images"
	@echo "  make up         - Start API + Frontend"
	@echo "  make up-all     - Start all services (including mobile)"
	@echo "  make down       - Stop all services"
	@echo "  make restart    - Restart all services"
	@echo ""
	@echo "Logs:"
	@echo "  make logs       - View all logs"
	@echo "  make logs-back  - View backend logs"
	@echo "  make logs-front - View frontend logs"
	@echo ""
	@echo "Shell Access:"
	@echo "  make shell      - Open shell in backend container"
	@echo "  make shell-front - Open shell in frontend container"
	@echo ""
	@echo "Laravel Commands:"
	@echo "  make artisan    - Run artisan command (use: make artisan cmd='migrate')"
	@echo "  make migrate    - Run database migrations"
	@echo "  make fresh      - Fresh migration with seeders"
	@echo "  make seed       - Run database seeders"
	@echo "  make test       - Run PHPUnit tests"
	@echo ""
	@echo "Package Management:"
	@echo "  make composer   - Run composer command (use: make composer cmd='install')"
	@echo "  make npm        - Run npm in frontend (use: make npm cmd='install')"
	@echo ""
	@echo "Setup:"
	@echo "  make setup      - Initial setup (build + install + migrate)"
	@echo "  make env        - Copy .env.example files"
	@echo ""
	@echo "Cleanup:"
	@echo "  make clean      - Stop and remove containers"
	@echo "  make prune      - Remove all unused Docker data"

# ===========================================
# Docker Commands
# ===========================================

# Build all Docker images
build:
	@echo "Building Docker images..."
	docker compose build

# Start API + Frontend (default services)
up:
	@echo "Starting Smart Café (API + Frontend)..."
	docker compose up -d
	@echo ""
	@echo "Services started!"
	@echo "  - API:      http://localhost:8000"
	@echo "  - Frontend: http://localhost:5173"

# Start all services including mobile
up-all:
	@echo "Starting Smart Café (all services)..."
	docker compose --profile mobile up -d
	@echo ""
	@echo "Services started!"
	@echo "  - API:      http://localhost:8000"
	@echo "  - Frontend: http://localhost:5173"
	@echo "  - Mobile:   Expo on ports 8081, 19000-19001"

# Stop all services
down:
	@echo "Stopping all services..."
	docker compose --profile mobile down

# Restart all services
restart: down up

# ===========================================
# Logs
# ===========================================

# View all logs
logs:
	docker compose logs -f

# View backend logs
logs-back:
	docker compose logs -f backend nginx

# View frontend logs
logs-front:
	docker compose logs -f frontend

# ===========================================
# Shell Access
# ===========================================

# Open shell in backend container
shell:
	docker compose exec backend sh

# Open shell in frontend container
shell-front:
	docker compose exec frontend sh

# ===========================================
# Laravel Commands
# ===========================================

# Run artisan command
artisan:
	docker compose exec backend php artisan $(cmd)

# Run database migrations
migrate:
	@echo "Running migrations..."
	docker compose exec backend php artisan migrate

# Fresh migration with seeders
fresh:
	@echo "Running fresh migration with seeders..."
	docker compose exec backend php artisan migrate:fresh --seed

# Run database seeders
seed:
	@echo "Running seeders..."
	docker compose exec backend php artisan db:seed

# Run PHPUnit tests
test:
	@echo "Running tests..."
	docker compose exec backend php artisan test

# Generate application key
key:
	docker compose exec backend php artisan key:generate

# Clear all caches
cache-clear:
	docker compose exec backend php artisan cache:clear
	docker compose exec backend php artisan config:clear
	docker compose exec backend php artisan route:clear
	docker compose exec backend php artisan view:clear

# ===========================================
# Package Management
# ===========================================

# Run composer command in backend
composer:
	docker compose exec backend composer $(cmd)

# Run npm command in frontend
npm:
	docker compose exec frontend npm $(cmd)

# ===========================================
# Setup
# ===========================================

# Initial setup
setup: env build
	@echo "Installing backend dependencies..."
	docker compose run --rm backend composer install
	@echo "Generating application key..."
	docker compose run --rm backend php artisan key:generate
	@echo "Running migrations..."
	docker compose run --rm backend php artisan migrate
	@echo ""
	@echo "Setup complete! Run 'make up' to start the services."

# Copy .env.example files
env:
	@echo "Copying .env.example files..."
	@if [ ! -f smart-cafe-back/.env ]; then \
		cp smart-cafe-back/.env.example smart-cafe-back/.env; \
		echo "Created smart-cafe-back/.env"; \
	else \
		echo "smart-cafe-back/.env already exists"; \
	fi
	@if [ ! -f smart-cafe-front/.env ]; then \
		cp smart-cafe-front/.env.example smart-cafe-front/.env; \
		echo "Created smart-cafe-front/.env"; \
	else \
		echo "smart-cafe-front/.env already exists"; \
	fi
	@if [ ! -f smart-cafe-app/.env ]; then \
		cp smart-cafe-app/.env.example smart-cafe-app/.env; \
		echo "Created smart-cafe-app/.env"; \
	else \
		echo "smart-cafe-app/.env already exists"; \
	fi

# ===========================================
# Cleanup
# ===========================================

# Stop and remove containers
clean:
	@echo "Cleaning up..."
	docker compose --profile mobile down -v --remove-orphans

# Remove all unused Docker data
prune:
	@echo "Pruning unused Docker data..."
	docker system prune -af
