#!/bin/sh


mariadb -u root -p"$MARIADB_ROOT_PASSWORD"<<-EOSQL

	use $BDD_NAME;

	CREATE TABLE IF NOT EXISTS fzco_firmware (
		firmware_id int AUTO_INCREMENT NOT NULL UNIQUE,
		firmware_name varchar(255) NOT NULL,
		firmware_url_update text NOT NULL,
		firmware_url_git text NOT NULL,
		firmware_is_active int NOT NULL,
		firmware_ufbt_path varchar(255) NOT NULL,
		PRIMARY KEY (firmware_id)
	);

	CREATE TABLE IF NOT EXISTS fzco_firmware_version (
		firmware_version_id int AUTO_INCREMENT NOT NULL UNIQUE,
		firmware_version_update_date datetime NOT NULL,
		firmware_version_type ENUM('release','dev') NOT NULL,
		firmware_version_name varchar(255) NOT NULL,
		firmware_version_is_active int NOT NULL,
		PRIMARY KEY (firmware_version_id)
	);

	CREATE TABLE IF NOT EXISTS fzco_depend (
		depend_firmware_id int NOT NULL,
		depend_firmware_version_id int NOT NULL,
		PRIMARY KEY (depend_firmware_id, depend_firmware_version_id)
	);

	CREATE TABLE IF NOT EXISTS fzco_application (
		application_id int AUTO_INCREMENT NOT NULL UNIQUE,
		application_name varchar(255) NOT NULL,
		application_appid varchar(255) NOT NULL,
		application_url_git text NOT NULL,
		PRIMARY KEY (application_id)
	);

	CREATE TABLE IF NOT EXISTS fzco_compiled (
		compiled_firmware_version_id int NOT NULL,
		compiled_application_id int NOT NULL,
		compiled_date datetime NOT NULL,
		compiled_path_fap varchar(255) NOT NULL,
		compiled_status  ENUM('pending','error', 'success','deleted', 'impossible')  NOT NULL,
		PRIMARY KEY (compiled_firmware_version_id, compiled_application_id, compiled_date )
	);

	ALTER TABLE fzco_depend ADD CONSTRAINT fzco_depend_fk0 FOREIGN KEY (depend_firmware_id) REFERENCES fzco_firmware(firmware_id);
	ALTER TABLE fzco_depend ADD CONSTRAINT fzco_depend_fk1 FOREIGN KEY (depend_firmware_version_id) REFERENCES fzco_firmware_version(firmware_version_id);
	ALTER TABLE fzco_compiled ADD CONSTRAINT fzco_compiled_fk0 FOREIGN KEY (compiled_firmware_version_id) REFERENCES fzco_firmware_version(firmware_version_id);
	ALTER TABLE fzco_compiled ADD CONSTRAINT fzco_compiled_fk1 FOREIGN KEY (compiled_application_id) REFERENCES fzco_application(application_id);


	# File for update the database, on the first version is init File

	INSERT INTO fzco_firmware VALUES 
	(1,
	'Official',
	'https://update.flipperzero.one/firmware/directory.json',
	'https://github.com/flipperdevices/flipperzero-firmware',
	1,
	'official');

	INSERT INTO fzco_firmware VALUES 
	(2,
	'Momentum',
	'https://up.momentum-fw.dev/firmware/directory.json',
	'https://github.com/Next-Flip/Momentum-Firmware',
	1,
	'momentum');

	INSERT INTO fzco_firmware_version VALUES (
		1,
		'2024-07-24 09:59:00',
		'release',
		'0.104.0',
		1
	);

	INSERT INTO fzco_firmware_version VALUES (
		2,
		'2024-07-24 09:59:00',
		'dev',
		'dev-ofw',
		1
	);

	INSERT INTO fzco_firmware_version VALUES (
		3,
		'2024-07-29 02:59:00',
		'release',
		'mntm-005',
		1
	);

	INSERT INTO fzco_firmware_version VALUES (
		4,
		'2024-07-29 02:59:00',
		'dev',
		'mntm-dev',
		1
	);

	INSERT INTO fzco_depend VALUES (1,1);
	INSERT INTO fzco_depend VALUES (1,2);
	INSERT INTO fzco_depend VALUES (2,3);
	INSERT INTO fzco_depend VALUES (2,4);


	-- ajout du firmware unleashed le 21-08-2024
	INSERT INTO fzco_firmware VALUES 
	(3,
	'Unleashed',
	'https://up.unleashedflip.com/directory.json',
	'https://github.com/DarkFlippers/unleashed-firmware/',
	1,
	'unleashed');

	INSERT INTO fzco_firmware_version VALUES (
		6,
		'2024-08-18 12:59:00',
		'release',
		'unlshd-077',
		1
	);


	INSERT INTO fzco_firmware_version VALUES (
		7,
		'2024-08-18 12:59:00',
		'dev',
		'unlshd-077',
		1
	);


	INSERT INTO fzco_depend VALUES (3,6);
	INSERT INTO fzco_depend VALUES (3,7);
EOSQL
