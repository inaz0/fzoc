FROM php:8.3-fpm AS build
#FROM php:8.3-fpm-alpine AS build

#RUN \
#    apk update && apk add curl mysql-client python3 py3-pip git python3-dev scons-pyc
RUN \
    apt update && apt install -y curl mariadb-client python3 python3-venv python3-pip git python3-dev

RUN mkdir -p /usr/local/fzoc/ufbt && cd /usr/local/fzoc/ && python3 -m venv ufbt && cd ufbt && . bin/activate && python3 -m pip install ufbt

#&& pip3 install --upgrade pip  && pip3 install -U setuptools && pip3 install SCons
RUN chmod 777 -R /usr/local/fzoc/ufbt
RUN docker-php-ext-install pdo pdo_mysql

FROM build

#USER www-data

# On défini nos volumes
VOLUME /usr/local/fzoc/www

# On change le dossier par défaut
WORKDIR /usr/local/fzoc

# On expose uniquement le port 9000
EXPOSE 9000
