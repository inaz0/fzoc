# Prérequis
## Docker install

- docker
- docker compose or (docker-compose)
- apache2 or nginx

## No docker install 

- mariadb or mysql
- php8.x-fpm
- curl
- python3
- python3-venv 
- python3-pip
- python3-dev
- git
- apache2 or nginx

# Docker install

[ENG]

Copy the dotenv.example to .env and complete it, just need normaly database information and cloudflare if need.

Add this following line in cron: (depend of your docker version the name was fzoc_fpm_1 or fzoc-fpm-1)

```
# Every minutes check for compilatio task
* * * * * docker exec -it fzoc_fpm_1 php /usr/local/fzoc/www/crons/task_runner.php

# Every hours check for firmware update
0 * * * * docker exec -it fzoc_fpm_1 sh /usr/local/fzoc/www/crons/update_ufbt.sh

# Every hours check for outdated application
0 * * * * docker exec -it fzoc_fpm_1 php /usr/local/fzoc/www/crons/task_delete.php
```

To finish run the command to up the application (depend on your docker version maybe "docker compose" or "docker-compose")

```
docker-compose up -d
```

[FR]

Copier le fichier dotenv.example vers .env et modifiez les informations nécessaire, le plus souvent la base de données et les informations de cloudflaire

Ajouter les crons suivante : (en fonction de votre version de docker le nom sera soit fzoc_fpm_1 soit fzoc-fpm-1)

```
# Every minutes check for compilatio task
* * * * * docker exec -it fzoc_fpm_1 php /usr/local/fzoc/www/crons/task_runner.php

# Every hours check for firmware update
0 * * * * docker exec -it fzoc_fpm_1 sh /usr/local/fzoc/www/crons/update_ufbt.sh

# Every hours check for outdated application
0 * * * * docker exec -it fzoc_fpm_1 php /usr/local/fzoc/www/crons/task_delete.php
```
Pour terminer, exécutez la commande pour lancer l'application (selon votre version de Docker, peut-être « docker compose » ou « docker-compose »)

``` 
docker-compose up -d 
```

# No docker install

[ENG]

You need to create a mariadb/mysql database with the create_db.sql script (be careful it does not create the database).

Copy the file located in www/config_example.php to www/config.php and fill in the updated information for your configuration

Add your vhost on apache2 or nginx (example file on git), and restart the service.

Add the following crons (adjust the path to the files)


```
# Every minutes check for compilatio task
* * * * * cd /var/www/fzoc/www/crons && php task_runner.php

# Every hours check for firmware update
0 * * * * cd /var/www/fzoc/www/crons && sh update_ufbt.sh

# Every hours check for outdated application
0 * * * * cd /var/www/fzoc/www/crons && php task_delete.php
```

[FR]

Après avoir cloné le dépôt sur votre serveur à l'emplacement désiré

Vous devez créer une base de données mariadb/mysql avec le script create_db.sql (attention il ne créé pas la base).

Copier le fichier qui se trouve dans www/config_example.php vers www/config.php et complétez les informations à jour pour votre configuration

Ajouter votre vhost sur apache2 ou nginx (fichier exemple sur le git), et relancer le service.

Ajouter les crons suivante (ajuster le chemin vers les fichiers)

```
# Every minutes check for compilatio task
* * * * * cd /var/www/fzoc/www/crons && php task_runner.php

# Every hours check for firmware update
0 * * * * cd /var/www/fzoc/www/crons && sh update_ufbt.sh

# Every hours check for outdated application
0 * * * * cd /var/www/fzoc/www/crons && php task_delete.php
```

# Update ?

[ENG]

Feel free to pull request the install if needed!

[FR]

N'hésitez pas à pull request l'install si besoin !