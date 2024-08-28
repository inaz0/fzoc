#!/bin/sh


cd /usr/local/fzoc/ && python3 -m venv ufbt && cd ufbt && . bin/activate && python3 -m pip install ufbt

# official firmware creation
mkdir -p /usr/local/fzoc/ufbt/fz_official_dev
cd  /usr/local/fzoc/ufbt/fz_official_dev
rm -f .env
ufbt dotenv_create --state-dir /usr/local/fzoc/ufbt/fz_official_dev
ufbt update --channel dev --index-url=https://update.flipperzero.one/firmware/directory.json

mkdir -p /usr/local/fzoc/ufbt/fz_official_release
cd  /usr/local/fzoc/ufbt/fz_official_release
rm -f .env
ufbt dotenv_create --state-dir /usr/local/fzoc/ufbt/fz_official_release
ufbt update --index-url=https://update.flipperzero.one/firmware/directory.json

# momentum firmware creation
mkdir -p /usr/local/fzoc/ufbt/fz_momentum_dev
cd  /usr/local/fzoc/ufbt/fz_momentum_dev
rm -f .env
ufbt dotenv_create --state-dir /usr/local/fzoc/ufbt/fz_momentum_dev
ufbt update --channel dev --index-url=https://up.momentum-fw.dev/firmware/directory.json

mkdir -p /usr/local/fzoc/ufbt/fz_momentum_release
cd  /usr/local/fzoc/ufbt/fz_momentum_release
rm -f .env
ufbt dotenv_create --state-dir /usr/local/fzoc/ufbt/fz_momentum_release
ufbt update --index-url=https://up.momentum-fw.dev/firmware/directory.json

#unleashed firmware creation
mkdir -p /usr/local/fzoc/ufbt/fz_unleashed_dev
cd  /usr/local/fzoc/ufbt/fz_unleashed_dev
rm -f .env
ufbt dotenv_create --state-dir /usr/local/fzoc/ufbt/fz_unleashed_dev
ufbt update --channel dev --index-url=https://up.unleashedflip.com/directory.json

mkdir -p /usr/local/fzoc/ufbt/fz_unleashed_release
cd  /usr/local/fzoc/ufbt/fz_unleashed_release
rm -f .env
ufbt dotenv_create --state-dir /usr/local/fzoc/ufbt/fz_unleashed_release
ufbt update --index-url=https://up.unleashedflip.com/directory.json

deactivate