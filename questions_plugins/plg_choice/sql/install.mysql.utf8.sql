DELETE FROM `#__quiz_t_qtypes` WHERE c_id = '1';
INSERT INTO `#__quiz_t_qtypes` (c_id, c_qtype, c_type) VALUES (1, 'Multiple Choice', 'choice');

CREATE TABLE IF NOT EXISTS `#__quiz_t_choice` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_choice` text NOT NULL,
		`c_right` char(1) NOT NULL default '0',
		`c_question_id` int(10) unsigned NOT NULL default '0',
		`ordering` int(11) NOT NULL default '0',
		`c_incorrect_feed` text NOT NULL default '',
		`a_point` FLOAT( 11 ) NOT NULL default '0',
		`c_quiz_id` int(11) NOT NULL default '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_question_id` (`c_question_id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_choice` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_sq_id` int(10) unsigned NOT NULL default '0',
		`c_choice_id` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_sq_id` (`c_sq_id`),
		KEY `c_choice_id` (`c_choice_id`) ) DEFAULT CHARSET=utf8;
		
UPDATE `#__extensions` SET `enabled` = 1 WHERE `element` = 'choice';