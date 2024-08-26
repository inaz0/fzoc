FROM php:8.3-fpm-alpine AS build

RUN \
    docker-php-ext-install pdo pdo_mysql && \
    \
    docker-php-ext-install curl mysql-client && \

FROM build

# Variables liées à la base de données
ENV DB_HOST 'db'
ENV DB_PORT '3306'
ENV DB_USERNAME 'fzco'
ENV DB_PASSWORD ''
ENV DB_NAME 'fzco'

# On défini nos volumes
VOLUME /usr/local/fzoc/www

# On change le dossier par défaut
WORKDIR /usr/local/fzoc

CMD [ "fpm-server" ]

# On expose uniquement le port 9000
EXPOSE 9000
