FROM nginx:stable-alpine

# Argument permettant de définir la version cible de la codebase.
# Permet notamment de s'assurer que l'image fpm est cohérente avec l'image http.
ARG CODEBASE_REVISION_HASH

# Copie de l'argument CODEBASE_REVISION_HASH dans une variable d'environnement.
# Afin de pouvoir l'exploiter à l'intérieur des conteneurs.
ENV CODEBASE_REVISION_HASH=${CODEBASE_REVISION_HASH}

# Variables liées à l'application
ENV APP_DOMAIN=
ENV API_DOMAIN=

# Variables liées aux websockets
ENV WS_HOST=ws
ENV WS_HTTP_PORT=8000

# Variables liées au serveur FPM
ENV FPM_HOST=fpm
ENV FPM_PORT=9000

# Variables liées à la base de données
ENV DB_HOST=db
ENV DB_PORT=3306

# Variables liées à Redis
ENV REDIS_HOST=cache
ENV REDIS_PORT=6379

# Variables liées à l'éditeur de diagrammes
ENV FLOWCHART_EDITOR_HOST=flowchart-editor
ENV FLOWCHART_EDITOR_PORT=8080

# Variables liées à l'upload de fichiers
ENV DMS_STORAGE_PATH=/usr/local/dnc/files/

# Variables liées au comportement du conteneur
ENV CONSIDER_SPECIFIC_CODE_AS_EXTERNAL=true

# Variables liées au SSO
ENV SSO_AUTH_ENABLED=false
ENV SSO_AUTH_SKIP_LOGIN_PAGE=false
ENV SSO_AUTH_ENABLE_SLO=false

# Variables liées à l'observabilité
ENV OBSERVABILITY_ENABLED=false
ENV OBSERVABILITY_SENTRY_DSN=

# Gestion des secrets via Gitlab
ENV SECRETS_MANAGER_GITLAB_ENABLED=false
ENV SECRETS_MANAGER_GITLAB_API_URL=
ENV SECRETS_MANAGER_GITLAB_PROJECT_ID=
ENV SECRETS_MANAGER_GITLAB_ACCESS_TOKEN=

# Copie des fichiers de configuration de nginx
COPY ./files/nginx/conf.d/ /etc/nginx/conf.d/

# Copie des fichiers de template
COPY ./files/templates/ /usr/src/dnc/templates/

# Copie des scripts lancés au démarrage du conteneur
# Ces scripts sont exécutés à l'aide de run-parts
COPY ./files/entrypoint.d/ /docker-entrypoint.d/

# Copie du script d'initialisation (en remplacement de celui fourni par nginx)
COPY ./files/entrypoint.sh /docker-entrypoint.sh

# Copie du script de healthcheck
COPY ./files/healthcheck.sh /healthcheck.sh

# Copie de la codebase de Datalizr No Code
COPY ./temporary/codebase/ /usr/src/dnc/codebase/

# Copie des fichiers binaires supplémentaires du PATH
COPY ./files/bin/backup /usr/local/bin/
COPY ./files/bin/restore /usr/local/bin/

# Exécution des tâches suivantes (en une seule couche) :
# - mise à jour du conteneur
# - installation de netcat (pour la vérification de l'état démarré des serveurs), curl et jq (pour l'entrypoint)
# - nettoyage des fichiers et dossiers inutiles
# - suppression de l'utilisateur nginx
# - création de l'utilisateur chargé de l'exécution de nginx
# - mise à jour du fichier de configuration de nginx pour utiliser le bon utilisateur
# - création du fichier /etc/environment
# - mise à jour des droits sur les fichiers copiés
# - création du dossier des fichiers
# - mise à jour des droits sur le dossier des fichiers
RUN apk update && \
    apk upgrade && \
    apk add --no-cache netcat-openbsd curl jq && \
    rm -r /var/cache/apk/* && \
    rm -r /usr/share/nginx/html && \
    rm /etc/nginx/conf.d/default.conf && \
    deluser nginx && \
    addgroup -S -g 191 dnc && \
    adduser -S -D -h /usr/local/dnc -k /dev/null -s /bin/false -G dnc -u 191 dnc && \
    sed -i 's/^user.*/user dnc;/' /etc/nginx/nginx.conf && \
    touch /etc/environment && \
    chown dnc:dnc /etc/environment && \
    chown root:root /docker-entrypoint.sh /docker-entrypoint.d/* /etc/nginx/conf.d/* /usr/src/dnc/templates/* /healthcheck.sh && \
    chmod 644 /docker-entrypoint.d/* /etc/nginx/conf.d/* /usr/src/dnc/templates/* && \
    chmod 744 /docker-entrypoint.sh /docker-entrypoint.d/*.sh /healthcheck.sh && \
    chown root:root /usr/local/bin/backup /usr/local/bin/restore && \
    chmod 744 /usr/local/bin/backup /usr/local/bin/restore && \
    chown dnc:dnc -R /usr/src/dnc/codebase/ && \
    find /usr/src/dnc/codebase/ -type d -exec chmod 755 {} \; && \
    find /usr/src/dnc/codebase/ -type f -exec chmod 644 {} \; && \
    find /usr/src/dnc/codebase/ -type f -iname "*.sh" -exec chmod 744 {} \; && \
    mkdir -p /usr/local/dnc/files/ && \
    chmod 755 /usr/local/dnc/files/ && \
    chown dnc:dnc /usr/local/dnc/files/

# On défini nos volumes
VOLUME /usr/local/dnc/www
VOLUME /usr/local/dnc/files

# On change le dossier par défaut
WORKDIR /usr/local/dnc

# On expose uniquement le port 80
EXPOSE 80