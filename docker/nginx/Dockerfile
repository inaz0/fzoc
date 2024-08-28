FROM nginx:stable-alpine

# Copie des fichiers de configuration de nginx
COPY ./host.conf.template /etc/nginx/templates/
# Copie du script d'initialisation (en remplacement de celui fourni par nginx)
COPY ./entrypoint.sh /docker-entrypoint.sh

RUN chmod 744 /docker-entrypoint.sh

# On défini nos volumes
VOLUME /usr/local/fzoc/www

# On change le dossier par défautl
WORKDIR /usr/local/fzoc

# On expose uniquement le port 80
EXPOSE 80