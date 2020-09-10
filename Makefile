up:
	docker-compose up -d

down:
	docker-compose down

bash:
	docker-compose exec php-fpm bash

migrate:
	docker-compose exec php-fpm php artisan migrate --seed
