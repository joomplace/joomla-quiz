DELETE FROM `#__quiz_t_qtypes` WHERE c_id = '14';
INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (14, 'Dalliclick', 'dalliclick');

CREATE TABLE IF NOT EXISTS `#__quiz_t_dalliclick` (
			  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `c_choice` text NOT NULL,
			  `c_right` char(1) NOT NULL,
			  `c_question_id` int(10) NOT NULL,
			  `ordering` int(11) NOT NULL,
			  `c_incorrect_feed` text NOT NULL,
			  `a_point` float NOT NULL,
			  `c_quiz_id` int(11) NOT NULL,
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;
			
CREATE TABLE IF NOT EXISTS `#__quiz_r_student_dalliclick` (
			  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `c_sq_id` int(10) NOT NULL,
			  `c_choice_id` int(10) NOT NULL,
			  `c_elapsed_time` int(10) NOT NULL,
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;

			UPDATE `#__extensions` SET `enabled` = 1 WHERE `element` = 'dalliclick';
		
