.PHONY: help install start stop restart logs clean test migrate fixtures jwt-keys

help: ## Show this help message
	@echo 'CounselRank SEO Audit Service'
	@echo '============================='
	@echo ''
	@echo 'Available commands:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install dependencies
	composer install
	@echo "Dependencies installed successfully!"

start: ## Start all services
	docker-compose up -d
	@echo "Services started! Access at http://localhost:8080"

stop: ## Stop all services
	docker-compose down
	@echo "Services stopped"

restart: stop start ## Restart all services

logs: ## Show service logs
	docker-compose logs -f

clean: ## Clean up containers and volumes
	docker-compose down -v --remove-orphans
	docker system prune -f
	@echo "Cleanup completed"

test: ## Run tests
	docker-compose exec php vendor/bin/phpunit

test-unit: ## Run unit tests only
	docker-compose exec php vendor/bin/phpunit --testsuite=unit

test-integration: ## Run integration tests only
	docker-compose exec php vendor/bin/phpunit --testsuite=integration

migrate: ## Run database migrations
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction

fixtures: ## Load test data
	docker-compose exec php bin/console doctrine:fixtures:load --no-interaction

jwt-keys: ## Generate JWT keys
	docker-compose exec php bin/console lexik:jwt:generate-keypair

setup: install start ## Complete initial setup
	@echo "Waiting for services to be ready..."
	@sleep 10
	docker-compose exec php bin/console doctrine:database:create --if-not-exists
	$(MAKE) migrate
	$(MAKE) fixtures
	$(MAKE) jwt-keys
	@echo "Setup completed! Access at http://localhost:8080"

worker: ## Start worker processes
	docker-compose exec php bin/console messenger:consume crawl lighthouse analyze aggregate report notify

worker-crawl: ## Start crawl worker only
	docker-compose exec php bin/console messenger:consume crawl

worker-lighthouse: ## Start lighthouse worker only
	docker-compose exec php bin/console messenger:consume lighthouse

worker-analyze: ## Start analyze worker only
	docker-compose exec php bin/console messenger:consume analyze

audit-run: ## Run an audit (usage: make audit-run ID=audit_id)
	docker-compose exec php bin/console audit:run $(ID)

audit-status: ## Check audit status (usage: make audit-status ID=audit_id)
	docker-compose exec php bin/console audit:status $(ID)

health: ## Check service health
	@echo "Checking service health..."
	@curl -s http://localhost:8080/api/health || echo "API not responding"
	@docker-compose exec -T php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1 && echo "Database: OK" || echo "Database: ERROR"
	@docker-compose exec -T redis redis-cli ping > /dev/null 2>&1 && echo "Redis: OK" || echo "Redis: ERROR"

monitoring: ## Open monitoring interfaces
	@echo "Opening monitoring interfaces..."
	@open http://localhost:16686  # Jaeger
	@open http://localhost:9090   # Prometheus
	@open http://localhost:9001   # MinIO Console

api-docs: ## Open API documentation
	@open http://localhost:8080/api/docs

dev-tools: ## Install development tools
	composer require --dev symfony/maker-bundle symfony/debug-bundle symfony/web-profiler-bundle
	@echo "Development tools installed!"

production: ## Production deployment preparation
	composer install --no-dev --optimize-autoloader
	@echo "Production build ready!"
