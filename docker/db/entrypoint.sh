#!/bin/sh

set -e
exec 3>&1

if [ "$1" = "mariadbd" ]
then

    if /usr/bin/find "/entrypoint.d/" -mindepth 1 -maxdepth 1 -type f -print -quit 2>/dev/null | read v; then
        echo >&3 "$0: /entrypoint.d/ is not empty, will attempt to perform configuration"

        echo >&3 "$0: Looking for shell scripts in /entrypoint.d/"
        find "/entrypoint.d/" -follow -type f -print | sort -V | while read -r f; do
            case "$f" in
                *.sh)
                    if [ -x "$f" ]; then
                        echo >&3 "$0: Launching $f";
                        "$f"
                    else
                        # warn on shell scripts without exec bit
                        echo >&3 "$0: Ignoring $f, not executable";
                    fi
                    ;;
                *) echo >&3 "$0: Ignoring $f";;
            esac

            # Déclenchement du sourcing des éventuels secrets exportés
            set -a
            . /etc/environment
            set +a
        done

        echo >&3 "$0: Configuration complete; ready for start up"
    else
        echo >&3 "$0: No files found in /entrypoint.d/, skipping configuration"
    fi
fi

# Sourcing des variables d'environnement éventuelles
if [ -f /etc/environment ]
then
    set -a
    . /etc/environment
    set +a
fi

# Appelle la commande du conteneur via l'entrypoint d'origine de l'image MariaDB, 
# chargé notamment de l'exécution de l'entrypoint d'initialisation de la base de données
exec docker-entrypoint.sh "$@"
