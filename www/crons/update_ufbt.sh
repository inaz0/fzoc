#!/bin/sh
#
#

# Bash to execute update for ufbt for each firmware

php /usr/local/fzoc/www/crons/auto_update_firmware_version.php

find /usr/local/fzoc/www/tasks_update -type f -exec sh {} \;

# on nettoie à la fin : 
find /usr/local/fzoc/www/tasks_update -type f -exec rm {} \;
