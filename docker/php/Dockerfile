FROM php:8.3-fpm AS build
RUN \
    apt update && apt install -y curl mariadb-client python3 python3-venv python3-pip git python3-dev


RUN docker-php-ext-install pdo pdo_mysql

FROM build

COPY ./update_ufbt.sh /update_ufbt.sh

RUN chmod 755 /update_ufbt.sh

# On défini nos volumes
VOLUME /usr/local/fzoc/www

# On change le dossier par défaut
WORKDIR /usr/local/fzoc
RUN /bin/sh /update_ufbt.sh

# On expose uniquement le port 9000
EXPOSE 9000
