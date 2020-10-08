up:
	docker-compose up -d

down:
	docker-compose down

bash:
	docker-compose exec php-fpm bash

migrate:
	docker-compose exec php-fpm php artisan migrate --seed

build-prod:
	docker build --file=./docker/prod/nginx/Dockerfile --tag ${DOCKER_REGISTRY}/nginx:${IMAGE_TAG} .
	docker build --file=./docker/prod/php/Dockerfile --tag ${DOCKER_REGISTRY}/php-fpm:${IMAGE_TAG} .

push-prod:
	docker push ${DOCKER_REGISTRY}/nginx:${IMAGE_TAG}
	docker push ${DOCKER_REGISTRY}/php-fpm:${IMAGE_TAG}
