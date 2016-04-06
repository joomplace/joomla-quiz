DELETE FROM `#__quiz_t_qtypes` WHERE c_id = '13';
INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (13, 'Memory', 'memory');

CREATE TABLE IF NOT EXISTS `#__quiz_t_memory` (
			  `m_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `c_question_id` int(11) NOT NULL,
			  `a_points` float NOT NULL,
			  `c_img` varchar(50) NOT NULL,
			  `a_pairs` int(10) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`m_id`)
			) DEFAULT CHARSET=utf8;
			
CREATE TABLE IF NOT EXISTS `#__quiz_r_student_memory` (
			  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `c_sq_id` int(11) NOT NULL,
			  `c_mid` int(11) NOT NULL,
			  `c_elapsed_time` int( 11 ) NOT NULL,
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;
			
			UPDATE `#__extensions` SET `enabled` = 1 WHERE `element` = 'memory';
