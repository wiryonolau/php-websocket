# HELP
# This will output the help for each task
.PHONY: help

help: ## This help.
    @awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help

THIS_FILE := $(lastword $(MAKEFILE_LIST))
PHP_VERSION ?= "7.4"

%:
	@echo ""
all:
	@echo ""
build:
	@if [ "$$(docker images -q php:$(PHP_VERSION)-cli-ext 2>/dev/null)" = "" ]; then \
		cd $$(pwd)/docker/php-cli-ext && docker build --build-arg PHP_VERSION=$(PHP_VERSION) -t php:$(PHP_VERSION)-cli-ext .; \
	fi
run:
	$(MAKE) build
	docker run --rm -it \
        -v $$(pwd):/srv/$$(basename "`pwd`") \
		-w /srv/$$(basename "`pwd`") \
		--user "$$(id -u):$$(id -g)" \
        --name $$(basename "`pwd`")_cli \
    php:$(PHP_VERSION)-cli-ext $(filter-out $@,$(MAKECMDGOALS))
unittest:
	$(MAKE) build
	docker stop $$(basename "`pwd`")_ws || true
	docker run --rm -it -d \
        -v $$(pwd):/srv/$$(basename "`pwd`") \
		-w /srv/$$(basename "`pwd`") \
		--user "$$(id -u):$$(id -g)" \
        --name $$(basename "`pwd`")_ws \
		--network host \
    php:$(PHP_VERSION)-cli-ext php tests/bin/test.php
	@while [ "$$(nc -v -z -w 2 127.0.0.1 13370 > /dev/null 2>&1 && echo 1 || echo 0)" -eq "0" ]; do \
        echo "Awaiting port 13370 to be ready" ; \
        sleep 1; \
    done
	echo "Port 13370 ready"
	docker run --rm -it \
        -v $$(pwd):/srv/$$(basename "`pwd`") \
		-w /srv/$$(basename "`pwd`") \
		--user "$$(id -u):$$(id -g)" \
        --name $$(basename "`pwd`")_cli \
		--network host \
    php:$(PHP_VERSION)-cli-ext vendor/bin/phpunit --verbose --debug tests
	docker logs $$(basename "`pwd`")_ws
	docker stop $$(basename "`pwd`")_ws || true
composer-install:
	docker run --rm -it \
        -v $$(pwd):/srv/$$(basename "`pwd`") \
        -w /srv/$$(basename "`pwd`") \
        -e COMPOSER_HOME="/srv/$$(basename "`pwd`")/.composer" \
        --user $$(id -u):$$(id -g) \
    composer composer install --no-plugins --no-scripts --prefer-dist -v
composer-update:
	docker run --rm -it \
        -v $$(pwd):/srv/$$(basename "`pwd`") \
        -w /srv/$$(basename "`pwd`") \
        -e COMPOSER_HOME="/srv/$$(basename "`pwd`")/.composer" \
        --user $$(id -u):$$(id -g) \
    composer composer update --no-plugins --no-scripts --prefer-dist -v
composer:
	docker run --rm -it \
        -v $$(pwd):/srv/$$(basename "`pwd`") \
        -w /srv/$$(basename "`pwd`") \
        -e COMPOSER_HOME="/srv/$$(basename "`pwd`")/.composer" \
        --user $$(id -u):$$(id -g) \
    composer composer $(filter-out $@,$(MAKECMDGOALS))
