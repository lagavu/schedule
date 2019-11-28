up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-pull docker-build docker-up schedule-init
test-all: schedule-test
test-schedule: schedule-test-schedule
test-schedule-form: schedule-test-schedule-form
fixtures: schedule-fixtures

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

schedule-init: schedule-composer-install schedule-assets-install schedule-migrations schedule-ready

schedule-clear:
	docker run --rm -v ${PWD}/schedule:/app --workdir=/app alpine rm -f .ready

schedule-composer-install:
	docker-compose run --rm schedule-php-cli composer install

schedule-assets-install:
	docker-compose run --rm schedule-node yarn install
	docker-compose run --rm schedule-node npm rebuild node-sass

schedule-migrations:
	docker-compose run --rm schedule-php-cli php bin/console doctrine:migrations:migrate --no-interaction

schedule-fixtures:
	docker-compose run --rm schedule-php-cli php bin/console doctrine:fixtures:load --no-interaction

schedule-ready:
	docker run --rm -v ${PWD}/schedule:/app --workdir=/app alpine touch .ready

schedule-assets-dev:
	docker-compose rhedulen --rm schedule-node npm run dev

schedule-test-schedule:
	sudo docker-compose run --rm schedule-php-cli php bin/phpunit tests/Functional/ScheduleControllerTest.php

schedule-test-schedule-form:
	sudo docker-compose run --rm schedule-php-cli php bin/phpunit tests/Functional/ScheduleControllerValidationTest.php

schedule-test:
	sudo docker-compose run --rm schedule-php-cli php bin/phpunit tests/Functional