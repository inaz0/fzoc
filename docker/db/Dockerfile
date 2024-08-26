FROM mariadb:11.5

# Variables d'environnement
ENV DB_USERNAME 'dnc'
ENV DB_PASSWORD ''
ENV DB_NAME 'dnc'

ENV MARIADB_ROOT_HOST 'localhost'
ENV MARIADB_ALLOW_EMPTY_ROOT_PASSWORD false
ENV MARIADB_RANDOM_ROOT_PASSWORD true
ENV MARIADB_AUTO_UPGRADE true


# Commande de démarrage personnalisée
ENTRYPOINT [ "/entrypoint.sh" ]
CMD [ "mariadbd" ]

# On expose uniquement le port 3306
EXPOSE 3306
