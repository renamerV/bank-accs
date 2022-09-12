init: down pull up

up:
	docker-compose up -d
	docker-compose exec app composer install
	docker-compose exec app php artisan migrate

down:
	docker-compose down --remove-orphans

pull:
	docker-compose pull

build:
	docker-compose build

ps:
	docker-compose ps

test:
	docker-compose exec app php artisan test

coverage:
	docker-compose exec app php artisan test --coverage

phpcs:
	docker-compose exec app php vendor/bin/phpcs

