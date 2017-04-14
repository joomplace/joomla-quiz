ALTER TABLE `#__quiz_t_question` ADD COLUMN `parent_id` INT(11) NOT NULL AFTER `c_id`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `params` text COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `#__quiz_t_question` ADD INDEX (`parent_id`)