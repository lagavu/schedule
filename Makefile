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

build-production:
	docker build --pull --file=shedule/docker/production/nginx.docker --tag ${REGISTRY_ADDRESS}/shedule-nginx:${IMAGE_TAG} shedule
	docker build --pull --file=shedule/docker/production/php-fpm.docker --tag ${REGISTRY_ADDRESS}/shedule-php-fpm:${IMAGE_TAG} shedule
	docker build --pull --file=shedule/docker/production/php-cli.docker --tag ${REGISTRY_ADDRESS}/shedule-php-cli:${IMAGE_TAG} shedule
	docker build --pull --file=shedule/docker/production/postgres.docker --tag ${REGISTRY_ADDRESS}/shedule-postgres:${IMAGE_TAG} shedule
	docker build --pull --file=shedule/docker/production/redis.docker --tag ${REGISTRY_ADDRESS}/shedule-redis:${IMAGE_TAG} shedule
	docker build --pull --file=centrifugo/docker/production/centrifugo.docker --tag ${REGISTRY_ADDRESS}/centrifugo:${IMAGE_TAG} centrifugo

push-production:
	docker push ${REGISTRY_ADDRESS}/shedule-nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/shedule-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/shedule-php-cli:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/shedule-postgres:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/shedule-redis:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/centrifugo:${IMAGE_TAG}

deploy-production:
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o StrictHostKeyChecking=no -P ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "shedule_APP_SECRET=${shedule_APP_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "shedule_DB_PASSWORD=${shedule_DB_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "shedule_REDIS_PASSWORD=${shedule_REDIS_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "shedule_MAILER_URL=${shedule_MAILER_URL}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "shedule_OAUTH_FACEBOOK_SECRET=${shedule_OAUTH_FACEBOOK_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_BASE_URL=${STORAGE_BASE_URL}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_HOST=${STORAGE_FTP_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_USERNAME=${STORAGE_FTP_USERNAME}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_PASSWORD=${STORAGE_FTP_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_WS_HOST=${CENTRIFUGO_WS_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_API_KEY=${CENTRIFUGO_API_KEY}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_SECRET=${CENTRIFUGO_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose up --build -d'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'until docker-compose exec -T shedule-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose run --rm shedule-php-cli php bin/console doctrine:migrations:migrate --no-interaction'

