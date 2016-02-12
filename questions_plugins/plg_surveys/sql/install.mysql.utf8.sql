DELETE FROM `#__quiz_t_qtypes` WHERE c_id = '8';
INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (8, 'Surveys', 'surveys');

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_survey` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_sq_id` int(10) unsigned NOT NULL default '0',
		`c_answer` text NOT NULL,
		PRIMARY KEY  (`c_id`),
		KEY `c_sq_id` (`c_sq_id`) ) DEFAULT CHARSET=utf8;
		
		UPDATE `#__extensions` SET `enabled` = 1 WHERE `element` = 'surveys';
