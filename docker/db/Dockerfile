FROM mariadb:11.5

ENV MARIADB_ROOT_HOST 'localhost'
ENV MARIADB_ALLOW_EMPTY_ROOT_PASSWORD false
ENV MARIADB_RANDOM_ROOT_PASSWORD true
ENV MARIADB_AUTO_UPGRADE true

COPY ./001_create_db_and_user.sh /docker-entrypoint-initdb.d
COPY ./002_create_db.sh /docker-entrypoint-initdb.d

# Copie du script d'initialisation
COPY ./entrypoint.sh /entrypoint.sh

RUN chmod 755 /entrypoint.sh


ENTRYPOINT [ "/entrypoint.sh" ]
CMD [ "mariadbd" ]

# On expose uniquement le port 3306
EXPOSE 3306
