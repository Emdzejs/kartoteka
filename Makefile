setup:
	docker compose build
	docker compose up -d --wait database
	docker compose run --rm --no-deps backend composer install
	#docker compose run --rm --no-deps backend php bin/console doctrine:migrations:migrate --no-interaction
	docker compose up -d --wait

up:
	docker compose up -d

down:
	docker compose down