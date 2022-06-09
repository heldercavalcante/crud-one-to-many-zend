#!/bin/bash

if [ -f .env ]
then
  export $(cat .env | sed 's/#.*//g' | xargs)
fi


if [ -z "$PROJECT_NAME" ]; then
    echo "Você deve criar um arquivo .env e informar a variavel PROJECT_NAME"
    exit 1
fi


docker-compose down

cat <<EOF > docker-php-ext-xdebug.ini
zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20190902/xdebug.so 
xdebug.mode=debug
xdebug.client_host=$(hostname -I | awk '{print $1}')
xdebug.client_port=9000
xdebug.idekey="docker-xdebug"
EOF

docker-compose up -d --build

printf "\n\nACESSE A APLICACAO: http://localhost/\n"

printf "\n\nACESSE O PHPMYADMIN: http://localhost:3000\n"
printf "\nServidor: mysql"
printf "\nUtilizador: root"
printf "\nPalavra-passe: 12345\n\n"