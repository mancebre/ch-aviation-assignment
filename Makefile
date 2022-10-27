.DEFAULT_GOAL := build

USER:=$(shell id -u)
GROUP:=$(shell id -g)

build:
	@docker-compose build
	@docker-compose run --rm --user=$(USER):$(GROUP) php-cli composer -vv install

test:
	@docker-compose run --rm php-cli vendor/bin/phpunit

parse:
	@docker-compose run --rm php-cli bin/app parse

bash:
	@docker-compose run --rm php-cli bash

