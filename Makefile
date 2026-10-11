setup:
	docker compose build
	docker compose up -d --wait database
	docker compose run --rm --no-deps backend composer install
	docker compose up -d --wait
	docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec backend php bin/console --env=test doctrine:database:create --if-not-exists
	docker compose exec backend php bin/console --env=test doctrine:migrations:migrate --no-interaction

up:
	docker compose up -d

down:
	docker compose down

enter-backend-container:
	docker compose exec backend sh

test:
	docker compose exec -e APP_ENV=test backend php bin/phpunit