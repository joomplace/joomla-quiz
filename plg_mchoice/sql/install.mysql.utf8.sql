DELETE FROM `#__quiz_t_qtypes` WHERE c_id = '15';
INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (15, 'Choice (4th generation)', 'mchoice');

CREATE TABLE IF NOT EXISTS `#__quiz_r_choice` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_sq_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_choice_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_sq_id` (`c_sq_id`),
  KEY `c_choice_id` (`c_choice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__quiz_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` int(11) NOT NULL,
  `text` varchar(256) CHARACTER SET utf8mb4 NOT NULL,
  `right` tinyint(1) NOT NULL DEFAULT '0',
  `points` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='JoomlaQuiz question`s options';

UPDATE `#__extensions` SET `enabled` = 1 WHERE `element` = 'mchoice';
