#!/bin/bash
#
#

# Bash to execute update for ufbt for each firmware

#lister les fichier
#pour chaque lancer le activate avant
#a la fin de chaque delete le fichier

find ../tasks_update -type f -name *.sh -exec bash {} \;