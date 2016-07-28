ALTER TABLE `#__quiz_certificates` ADD COLUMN `cert_offset` INT(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `text_font`;

UPDATE `#__quiz_certificates` SET `cert_offset` = 100 WHERE `#__quiz_certificates`.`id` = 1;
UPDATE `#__quiz_certificates` SET `cert_offset` = 150 WHERE `#__quiz_certificates`.`id` = 2;
UPDATE `#__quiz_certificates` SET `cert_offset` = 200 WHERE `#__quiz_certificates`.`id` = 3;
