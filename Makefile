.PHONY: test stan cs fix help test-phpunit test-coverage all build up down bash debug setup

COMPOSE := docker-compose

help:
	@echo "Available targets:"
	@echo "  test          - Run PHPUnit tests"
	@echo "  test-coverage - Run tests with coverage"
	@echo "  stan          - Run PHPStan static analysis"
	@echo "  cs            - Run PHP Code Sniffer"
	@echo "  fix           - Run PHP Code Beautifier and Fixer"
	@echo "  all           - Run all checks (test, stan, cs)"
	@echo "  build         - Build Docker container"
	@echo "  setup         - Build and install dependencies"
	@echo "  up            - Start Docker container"
	@echo "  down          - Stop Docker container"
	@echo "  bash          - Enter container shell"
	@echo "  debug         - Run debug command"

build:
	${COMPOSE} build --no-cache

setup: build up
	${COMPOSE} run --rm app composer install

up:
	${COMPOSE} up -d

down:
	${COMPOSE} down

bash:
	${COMPOSE} exec app bash

debug:
	${COMPOSE} exec -e XDEBUG_MODE=debug app php -dxdebug.start_with_request=yes bin/run-basket

test: test-phpunit

test-phpunit:
	${COMPOSE} run --rm app vendor/bin/phpunit --coverage-text

test-coverage:
	${COMPOSE} exec -e XDEBUG_MODE=coverage app composer test-coverage
	
stan:
	${COMPOSE} run --rm app vendor/bin/phpstan analyse

cs:
	${COMPOSE} run --rm app vendor/bin/phpcs

fix:
	${COMPOSE} run --rm app vendor/bin/phpcbf

all: test stan cs
