DELETE FROM `#__quiz_t_qtypes` WHERE c_id = '6';
INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (6, 'Fill in the blank', 'blank');

CREATE TABLE IF NOT EXISTS `#__quiz_t_faketext` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_quest_id` int(10) unsigned NOT NULL default '0',
		`c_text` varchar(255) NOT NULL default '',
		PRIMARY KEY  (`c_id`),
		KEY `c_quest_id` (`c_quest_id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_blank` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_sq_id` int(10) unsigned NOT NULL default '0',
		`c_answer` varchar(255) NOT NULL default '',
		`is_correct` tinyint(2) NOT NULL default '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_sq_id` (`c_sq_id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_t_text` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_blank_id` int(10) unsigned NOT NULL default '0',
		`c_text` varchar(255) NOT NULL default '',
		`ordering` int(11) NOT NULL default '0',
		`c_quiz_id` int(11) NOT NULL default '0',
		`regexp` tinyint(4) NOT NULL default '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_blank_id` (`c_blank_id`) ) DEFAULT CHARSET=utf8;
		
CREATE TABLE IF NOT EXISTS `#__quiz_t_blank` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_question_id` int(10) unsigned NOT NULL default '0',
		`ordering` int(11) NOT NULL default '0',
		`points` FLOAT( 11 ) DEFAULT '0' NOT NULL,
		`css_class` VARCHAR( 255 ) NOT NULL,
		`c_quiz_id` INT NOT NULL,
		`gtype` TINYINT( 1 ) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_question_id` (`c_question_id`) ) DEFAULT CHARSET=utf8;
		
UPDATE `#__extensions` SET `enabled` = 1 WHERE `element` = 'blank';