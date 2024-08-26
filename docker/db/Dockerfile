FROM mariadb:11.5

# Variables d'environnement
ENV DB_USERNAME 'dnc'
ENV DB_PASSWORD ''
ENV DB_NAME 'dnc'

# Chiffrement des données stockées sur le disque (limité aux tables de données)
ENV DB_DATA_AT_REST_ENCRYPTION_KEY ''

# Mise à jour de paramètres par défaut du conteneur parent
ENV MARIADB_ROOT_HOST 'localhost'
ENV MARIADB_ALLOW_EMPTY_ROOT_PASSWORD false
ENV MARIADB_RANDOM_ROOT_PASSWORD true
ENV MARIADB_AUTO_UPGRADE true

# Variables liées à l'application
ENV APP_DOMAIN ''

# Gestion des secrets via Gitlab
ENV SECRETS_MANAGER_GITLAB_ENABLED 'false'
ENV SECRETS_MANAGER_GITLAB_API_URL ''
ENV SECRETS_MANAGER_GITLAB_PROJECT_ID ''
ENV SECRETS_MANAGER_GITLAB_ACCESS_TOKEN ''

# Gestion des accents
ENV LANG C.UTF-8

# Copie des scripts lancés au premier démarrage du conteneur (à l'initialisation de la base de données)
# Ces scripts sont exécutés par l'entrypoint par défaut du conteneur mariadb
COPY ./files/docker-entrypoint-initdb.d /docker-entrypoint-initdb.d

# Copie des scripts d'entrypoint lancés à chaque démarrage du conteneur
COPY ./files/entrypoint.d/ /entrypoint.d

# Copie du script d'initialisation
COPY ./files/entrypoint.sh /entrypoint.sh

# Copie du script de healthcheck
COPY ./files/healthcheck.sh /healthcheck.sh

# Copie des fichiers binaires supplémentaires du PATH
COPY ./files/bin/* /usr/local/bin/

# Copie des fichiers de template
COPY ./files/templates/ /usr/src/dnc/templates

# Exécution des tâches suivantes (en une seule couche) : 
# - mise à jour du conteneur
# - installation de gettext-base (qui contient la commande envsubst), de dialog (pour la commande clone-db),
#   curl et jq (pour l'entrypoint)
# - nettoyage des fichiers et dossiers inutiles
# - mise à jour des droits sur les fichiers copiés
RUN apt update && \
    apt upgrade -y && \
    apt install gettext-base dialog curl jq -y && \
    rm -r /var/cache/apt/* && \
    chown root:root /healthcheck.sh && \
    chown mysql:mysql /docker-entrypoint-initdb.d/* && \
    chmod 644 /docker-entrypoint-initdb.d/* /entrypoint.d/* && \
    chmod 744 /docker-entrypoint-initdb.d/*.sh /entrypoint.d/*.sh /entrypoint.sh /healthcheck.sh && \
    chown root:root /usr/local/bin/backup /usr/local/bin/restore && \
    chmod 744 /usr/local/bin/backup /usr/local/bin/restore && \
    (chown root:root /usr/local/bin/clone-db || true) && \
    (chmod 744 /usr/local/bin/clone-db || true)

# Commande de démarrage personnalisée
ENTRYPOINT [ "/entrypoint.sh" ]
CMD [ "mariadbd" ]

# On expose uniquement le port 3306
EXPOSE 3306
