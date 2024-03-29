CI ?= false
COMPOSER_HOME ?= ${HOME}/.config/composer
COMPOSER_CACHE_DIR ?= ${HOME}/.cache/composer

define run-php
	if [ "$(CI)" = true ]; then \
		$(2); \
	else \
		docker run --rm -it \
			--user $(shell id -u):$(shell id -g) \
			--env COMPOSER_HOME \
			--env COMPOSER_CACHE_DIR \
			--volume ${COMPOSER_HOME}:${COMPOSER_HOME} \
			--volume ${COMPOSER_CACHE_DIR}:${COMPOSER_CACHE_DIR} \
			--volume ./:/app \
			--volume ./tools/composer-bin:/usr/bin/composer \
			--workdir /app \
			php:$(strip $(1))-cli-alpine $(2); \
	fi
endef

define run-composer
	$(call run-php, $(1), composer $(2))
endef

define run-behat
	$(call docker-run-php, $(1), vendor/bin/behat)
endef

##  -----
##@ Utils
##  -----

composer: tools/composer-bin ## Wrapper for composer commands with docker
	$(call run-composer, 8.2, ${arg})
.PHONY: .composer

##  --
##@ QA
##  --

php-cs-fixer: tools/php-cs-fixer/vendor/bin/php-cs-fixer ## Run php-cs-fixer
	$(call run-php, 8.2, tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config tools/php-cs-fixer/.php-cs-fixer.dist.php -v)
.PHONY: php-cs-fixer

php-cs-fixer-check: tools/php-cs-fixer/vendor/bin/php-cs-fixer
	$(call run-php, 8.2, tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config tools/php-cs-fixer/.php-cs-fixer.dist.php -v --dry-run)
.PHONY: php-cs-fixer-check

tools/php-cs-fixer/vendor/bin/php-cs-fixer:
	$(call run-composer, 8.2, install --working-dir=tools/php-cs-fixer)

phpstan: tools/phpstan/vendor/bin/phpstan vendor ## Run phpstan
	$(call run-php, 8.2, tools/phpstan/vendor/bin/phpstan analyse -c tools/phpstan/phpstan.neon)
.PHONY: phpstan

tools/phpstan/vendor/bin/phpstan:
	$(call run-composer, 8.2, install --working-dir=tools/phpstan)

##  -----------------
##@ Composer installs
##  -----------------

composer-install-7.4-lowest: tools/composer-bin ## Install vendors in PHP 7.4 with lowest dependencies versions
	$(call run-composer, 7.4, update --prefer-lowest)
.PHONY: composer-install-7.4-lowest

composer-install-7.4: tools/composer-bin ## Install vendors in PHP 7.4
	$(call run-composer, 7.4, update)
.PHONY: composer-install-7.4

composer-install-8.0: tools/composer-bin ## Install vendors in PHP 8.0
	$(call run-composer, 8.0, update)
.PHONY: composer-install-8.0

composer-install-8.1: tools/composer-bin ## Install vendors in PHP 8.1
	$(call run-composer, 8.1, update)
.PHONY: composer-install-8.1

composer-install-8.2: tools/composer-bin ## Install vendors in PHP 8.2
	$(call run-composer, 8.2, update)
.PHONY: composer-install-8.2

vendor:
	$(call run-composer, 8.2, update)

##  -----
##@ Tests
##  -----

test-all: ## All tests
	$(MAKE) test-7.4-lowest
	$(MAKE) test-7.4
	$(MAKE) test-8.0
	$(MAKE) test-8.1
	$(MAKE) test-8.2
.PHONY: test-all

test-7.4-lowest: composer-install-7.4-lowest ## Test in PHP 7.4 with lowest dependencies versions
	$(call run-behat, 7.4)
.PHONY: test-7.4-lowest

test-7.4: composer-install-7.4 ## Test in PHP 7.4
	$(call run-behat, 7.4)
.PHONY: test-7.4

test-8.0: composer-install-8.0 ## Test in PHP 8.0
	$(call run-behat, 8.0)
.PHONY: test-8.0

test-8.1: composer-install-8.1 ## Test in PHP 8.1
	$(call run-behat, 8.1)
.PHONY: test-8.1

test-8.2: composer-install-8.2 ## Test in PHP 8.2
	$(call run-behat, 8.2)
.PHONY: test-8.2

tools/composer-bin:
	docker run --name behat-gather-context-extension-composer composer:latest --version
	docker cp behat-gather-context-extension-composer:/usr/bin/composer tools/composer-bin
	docker rm behat-gather-context-extension-composer

##  ----
##@ Misc
##  ----

.DEFAULT_GOAL := help
.PHONY: help
# See https://www.thapaliya.com/en/writings/well-documented-makefiles/
help: ## Display this help
	@awk 'BEGIN {FS = ":.* ##"; printf "\n\033[32;1m  Behat Gather Context Extension\n  ------------------------------\033[0m\n"} /^[%a-zA-Z0-9._-]+:.* ## / { printf "  \033[33m%-25s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@
