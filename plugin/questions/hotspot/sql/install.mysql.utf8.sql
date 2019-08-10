DELETE FROM `#__quiz_t_qtypes` WHERE c_id = '7';
INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (7, 'Hotspot', 'hotspot');

CREATE TABLE IF NOT EXISTS `#__quiz_t_hotspot` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_question_id` int(10) unsigned NOT NULL default '0',
		`c_start_x` int(10) unsigned NOT NULL default '0',
		`c_start_y` int(10) unsigned NOT NULL default '0',
		`c_width` int(10) unsigned NOT NULL default '0',
		`c_height` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_question_id` (`c_question_id`) ) DEFAULT CHARSET=utf8;
		
CREATE TABLE IF NOT EXISTS `#__quiz_r_student_hotspot` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_sq_id` int(10) unsigned NOT NULL default '0',
		`c_select_x` int(10) unsigned NOT NULL default '0',
		`c_select_y` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_sq_id` (`c_sq_id`) ) DEFAULT CHARSET=utf8;
		
	UPDATE `#__extensions` SET `enabled` = 1 WHERE `element` = 'hotspot';