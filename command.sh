docker build -t ilhamrhmtkbr/talent-hub .

docker-compose up --build

docker container create --name talent-hub-app-php --network net-talent-hub --volume "$(pwd):/var/www/html" --publish 8080:80 ilhamrhmtkbr/talent-hub

docker compose -f prod-docker-compose.yaml build

./vendor/bin/phpunit -c phpunit.xml

docker compose -f docker-compose-dev.yaml