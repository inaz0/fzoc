# File for update the database, on the first version is init File

INSERT INTO `fzco_firmware` VALUES 
(1,
'Official',
'https://update.flipperzero.one/firmware/directory.json',
'https://github.com/flipperdevices/flipperzero-firmware',
1,
'official');

INSERT INTO `fzco_firmware` VALUES 
(2,
'Momentum',
'https://up.momentum-fw.dev/firmware/directory.json',
'https://github.com/Next-Flip/Momentum-Firmware',
1,
'momentum');

INSERT INTO `fzco_firmware_version` VALUES (
    1,
	'2024-07-24 09:59:00',
	'release',
	'0.104.0',
	1
);

INSERT INTO `fzco_firmware_version` VALUES (
    2,
	'2024-07-24 09:59:00',
	'dev',
	'dev-ofw',
	1
);

INSERT INTO `fzco_firmware_version` VALUES (
    3,
	'2024-07-29 02:59:00',
	'release',
	'mntm-005',
	1
);

INSERT INTO `fzco_firmware_version` VALUES (
    4,
	'2024-07-29 02:59:00',
	'dev',
	'mntm-dev',
	1
);

INSERT INTO `fzco_depend` VALUES (1,1);
INSERT INTO `fzco_depend` VALUES (1,2);
INSERT INTO `fzco_depend` VALUES (2,3);
INSERT INTO `fzco_depend` VALUES (2,4);

INSERT INTO `fzco_firmware_version` VALUES (
    5,
	'2024-08-15 03:59:00',
	'release',
	'0.105.0',
	1
);

INSERT INTO `fzco_depend` VALUES (1,5);
UPDATE fzco_firmware_version SET firmware_version_is_active = 0 WHERE firmware_version_id=1;