# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start down logs sh composer vendor sf cc

## 👷 Makefile
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## 🐳 Docker
setup: ## Sets up dependencies for environment
	sh docker/setup.sh

build: ## Builds container(s)
	@$(DOCKER_COMP) build --pull --no-cache $(c)

up: ## Start container(s)
	@$(DOCKER_COMP) up $(c)

up-d: ## Start container(s) in detached mode (no logs)
	@$(DOCKER_COMP) up --detach $(c)

start: setup build up-d ## Set up, build and start the containers

stop: ## Stop container(s)
	@$(DOCKER_COMP) stop $(c)

down: ## Stop and remove container(s)
	@$(DOCKER_COMP) down $(c) --remove-orphans

logs: ## Show logs
	@$(DOCKER_COMP) logs $(c)

logs-f: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow $(c)

ps: ## Show containers' statuses
	@$(DOCKER_COMP) ps

sh: ## Connect to a container via SH
	$(DOCKER_COMP) exec $(c) sh

bash: ## Connect to a container via BASH
	$(DOCKER_COMP) exec $(c) bash

php-sh: ## Connect to the PHP FPM container via SH
	@$(PHP_CONT)

php-bash: ## Connect to the PHP FPM container via BASH
	@$(PHP_CONT) bash

## ✅ Code Quality
phpcs: ## Run PHP Code Sniffer
	@$(PHP_CONT) ./vendor/bin/phpcs

phpcs-fix: ## Run PHP Code Sniffer (fix)
	@$(PHP_CONT) ./vendor/bin/phpcbf

phpstan: ## Run PHPStan
	@$(PHP_CONT) ./vendor/bin/phpstan

lint: phpcs phpstan ## Run PHP Code Sniffer and PHPStan

test: ## Run tests, pass the parameter "args=" to run the command with arguments or options
	@$(PHP) bin/phpunit $(args)

test-cov: ## Run tests and generate coverage report
	@$(DOCKER_COMP) exec -e XDEBUG_MODE=coverage php vendor/bin/simple-phpunit --coverage-clover coverage/clover/clover.xml --coverage-html coverage/html

## 🧙 Composer
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

## 🎶 Symfony
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

migrations-diff: c=doctrine:migrations:diff ## Generate diff of migrations based on entities
migrations-diff: sf

migrations-migrate: c=doctrine:migrations:migrate ## Execute all migrations
migrations-migrate: sf
