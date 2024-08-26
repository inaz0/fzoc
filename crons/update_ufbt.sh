#!/bin/bash
#
#

# Bash to execute update for ufbt for each firmware

find ../tasks_update -type f -exec bash {} \;

# on nettoie à la fin : 
find ../tasks_update -type f -exec rm {} \;
