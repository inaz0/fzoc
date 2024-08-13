CREATE TABLE IF NOT EXISTS `fzco_firmware` (
	`firmware_id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`firmware_name` varchar(255) NOT NULL,
	`firmware_url_update` text NOT NULL,
	`firmware_url_git` text NOT NULL,
	`firmware_is_active` int NOT NULL,
	`firmware_ufbt_path` varchar(255) NOT NULL,
	PRIMARY KEY (`firmware_id`)
);

CREATE TABLE IF NOT EXISTS `fzco_firmware_version` (
	`firmware_version_id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`firmware_version_update_date` date NOT NULL,
	`firmware_vesion_type` ENUM('release','dev') NOT NULL,
	`firmware_version_name` varchar(255) NOT NULL,
	`firmware_version_is_active` int NOT NULL,
	PRIMARY KEY (`firmware_version_id`)
);


CREATE TABLE IF NOT EXISTS `fzco_depend` (
	`depend_firmware_id` int NOT NULL,
	`depend_firmware_version_id` int NOT NULL,
	PRIMARY KEY (`depend_firmware_id`, `depend_firmware_version_id`)
);

CREATE TABLE IF NOT EXISTS `fzco_application` (
	`application_id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`application_name` varchar(255) NOT NULL,
	`application_appid` varchar(255) NOT NULL,
	`application_url_git` text NOT NULL,
	PRIMARY KEY (`application_id`)
);

CREATE TABLE IF NOT EXISTS `fzco_compiled` (
	`compiled_firmware_version_id` int NOT NULL,
	`compiled_application_id` int NOT NULL,
	`compiled_date` date NOT NULL,
	`compiled_path_fap` varchar(255) NOT NULL,
	`compiled_status`  ENUM('pending','error', 'success','deleted')  NOT NULL,
	PRIMARY KEY (`compiled_firmware_version_id`, `compiled_application_id`)
);





ALTER TABLE `fzco_depend` ADD CONSTRAINT `fzco_depend_fk0` FOREIGN KEY (`depend_firmware_id`) REFERENCES `fzco_firmware`(`firmware_id`);

ALTER TABLE `fzco_depend` ADD CONSTRAINT `fzco_depend_fk1` FOREIGN KEY (`depend_firmware_version_id`) REFERENCES `fzco_firmware_version`(`firmware_version_id`);

ALTER TABLE `fzco_compiled` ADD CONSTRAINT `fzco_compiled_fk0` FOREIGN KEY (`compiled_firmware_version_id`) REFERENCES `fzco_firmware_version`(`firmware_version_id`);

ALTER TABLE `fzco_compiled` ADD CONSTRAINT `fzco_compiled_fk1` FOREIGN KEY (`compiled_application_id`) REFERENCES `fzco_application`(`application_id`);