UPDATE `#__quiz_dashboard_items` SET `icon` = '/administrator/components/com_joomlaquiz/assets/images/quizzes48.png' WHERE `#__quiz_dashboard_items`.`id` = 1;
UPDATE `#__quiz_dashboard_items` SET `icon` = '/administrator/components/com_joomlaquiz/assets/images/questions48.png' WHERE `#__quiz_dashboard_items`.`id` = 2;
UPDATE `#__quiz_dashboard_items` SET `icon` = '/administrator/components/com_joomlaquiz/assets/images/help48.png' WHERE `#__quiz_dashboard_items`.`id` = 3;

ALTER TABLE `#__quiz_certificates` ADD COLUMN `cert_offset` INT(4) NOT NULL AFTER `text_font`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `one_time` INT(1) NOT NULL DEFAULT '0';
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_feedback_pdf` INT(2)  NOT NULL AFTER `c_feedback`;

DELETE FROM `#__quiz_t_quiz` WHERE `c_title` = 'Questions Pool';