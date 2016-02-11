DELETE FROM `#__quiz_t_qtypes` WHERE c_id = '11';
INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (11, 'Jigsaw Puzzle', 'puzzle');

CREATE TABLE IF NOT EXISTS `#__quiz_t_puzzle` (
			  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `c_question_id` int(11) NOT NULL,
			  `c_pieces` int(11) NOT NULL DEFAULT '4',
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_puzzle` (
			  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `c_sq_id` int(11) unsigned NOT NULL,
			  `c_piece` int(10) NOT NULL,
			  `c_elapsed_time` int(10) NOT NULL,
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;

		UPDATE `#__extensions` SET `enabled` = 1 WHERE `element` = 'puzzle';