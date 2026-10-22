COMPOSE := docker compose
ARTISAN := $(COMPOSE) exec -u app php php artisan

.PHONY: coverage up down restart build ps logs sh composer migrate fresh seed test test-db lint watch dev

up:
	$(COMPOSE) up -d --build

down:
	$(COMPOSE) down

restart:
	$(COMPOSE) restart php nginx

build:
	$(COMPOSE) build

sh:
	$(COMPOSE) exec -u app php sh

composer:
	$(COMPOSE) exec -u app php composer $(ARGS)

migrate:
	$(ARTISAN) migrate $(ARGS)

fresh:
	$(ARTISAN) migrate:fresh

seed:
	$(ARTISAN) db:seed $(ARGS)

test-db:
	@$(COMPOSE) exec -T postgres psql -U antenna -d antenna -tAc "SELECT 1 FROM pg_database WHERE datname='antenna_monitoring'" | grep -q 1 \
		|| $(COMPOSE) exec -T postgres createdb -U antenna antenna_monitoring

test: test-db
	$(COMPOSE) exec -u app php php artisan test

coverage: test-db
	$(COMPOSE) exec -u app -e XDEBUG_MODE=coverage php php artisan test --coverage

lint:
	$(COMPOSE) exec -u app php vendor/bin/pint

watch:
	$(COMPOSE) run --rm node pnpm run build --watch

dev:
	$(COMPOSE) --profile dev run --rm --service-ports vite
