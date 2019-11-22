up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up shedule-init


docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

shedule-init: shedule-composer-install shedule-assets-install shedule-migrations shedule-ready

shedule-clear:
	docker run --rm -v ${PWD}/shedule:/app --workdir=/app alpine rm -f .ready

shedule-composer-install:
	docker-compose run --rm shedule-php-cli composer install

shedule-assets-install:
	docker-compose run --rm shedule-node yarn install
	docker-compose run --rm shedule-node npm rebuild node-sass

shedule-oauth-keys:
	docker-compose run --rm shedule-php-cli mkdir -p var/oauth
	docker-compose run --rm shedule-php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm shedule-php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm shedule-php-cli chmod 644 var/oauth/private.key var/oauth/public.key

shedule-wait-db:
	until docker-compose exec -T shedule-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

shedule-migrations:
	docker-compose run --rm shedule-php-cli php bin/console doctrine:migrations:migrate --no-interaction

shedule-fixtures:
	docker-compose run --rm shedule-php-cli php bin/console doctrine:fixtures:load --no-interaction

shedule-ready:
	docker run --rm -v ${PWD}/shedule:/app --workdir=/app alpine touch .ready

shedule-assets-dev:
	docker-compose run --rm shedule-node npm run dev

shedule-test:
	docker-compose run --rm shedule-php-cli php bin/phpunit

shedule-test-coverage:
	docker-compose run --rm shedule-php-cli php bin/phpunit --coverage-clover var/clover.xml --coverage-html var/coverage

shedule-test-unit:
	docker-compose run --rm shedule-php-cli php bin/phpunit --testsuite=unit

shedule-test-unit-coverage:
	docker-compose run --rm shedule-php-cli php bin/phpunit --testsuite=unit --coverage-clover var/clover.xml --coverage-html var/coverage