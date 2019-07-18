CREATE TABLE IF NOT EXISTS `#__quiz_certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cert_name` varchar(50) NOT NULL,
  `cert_file` varchar(50) NOT NULL,
  `crtf_align` varchar(100) NOT NULL DEFAULT '0',
  `crtf_shadow` tinyint(4) NOT NULL DEFAULT '0',
  `text_x` int(11) NOT NULL DEFAULT '0',
  `text_y` int(11) NOT NULL DEFAULT '0',
  `text_size` tinyint(4) NOT NULL DEFAULT '10',
  `crtf_text` text NOT NULL,
  `text_font` varchar(255) NOT NULL,
  `cert_offset` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Структура таблицы `#__quiz_cert_fields`
--

CREATE TABLE IF NOT EXISTS `#__quiz_cert_fields` (
  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cert_id` int(11) NOT NULL DEFAULT '0',
  `f_text` text,
  `text_x` int(11) NOT NULL DEFAULT '0',
  `text_y` int(11) NOT NULL DEFAULT '0',
  `text_h` int(11) NOT NULL DEFAULT '0',
  `shadow` tinyint(4) NOT NULL DEFAULT '0',
  `font` varchar(255) DEFAULT 'arial.ttf',
  `text_x_center` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;


REPLACE INTO `#__quiz_cert_fields` (`c_id`, `cert_id`, `f_text`, `text_x`, `text_y`, `text_h`, `shadow`, `font`) VALUES
(1, 2, 'For the successful completion of quiz:', 170, 520, 20, 0, 'arial.ttf'),
(2, 2, '#reg_answer#', 170, 680, 20, 0, 'arial.ttf'),
(3, 2, 'dated from #date(d F Y)#', 170, 630, 20, 0, 'arial.ttf'),
(4, 2, '#course#', 170, 570, 20, 1, 'arial.ttf'),
(5, 2, '#name#', 350, 450, 20, 1, 'arial.ttf'),
(6, 1, 'For the successful completion of quiz:', 180, 620, 20, 0, 'arial.ttf'),
(7, 1, '#reg_answer#', 180, 780, 20, 0, 'arial.ttf'),
(8, 1, 'dated from #date(d F Y)#', 180, 730, 20, 0, 'arial.ttf'),
(9, 1, '#course#', 180, 670, 20, 1, 'arial.ttf'),
(10, 1, '#name#', 350, 580, 20, 1, 'arial.ttf'),
(11, 3, 'For the successful completion of quiz:', 320, 410, 16, 0, 'arial.ttf'),
(12, 3, '#reg_answer#', 520, 480, 16, 0, 'arial.ttf'),
(13, 3, 'dated from #date(d F Y)#', 690, 620, 16, 0, 'arial.ttf'),
(14, 3, '#course#', 690, 410, 16, 1, 'arial.ttf'),
(15, 3, '#name#', 540, 360, 16, 1, 'arial.ttf');

CREATE TABLE IF NOT EXISTS `#__quiz_constants` (
  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key_value` varchar(255) NOT NULL DEFAULT '',
  `default_value` text NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__quiz_dashboard_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;


REPLACE INTO `#__quiz_dashboard_items` (`id`, `title`, `url`, `icon`, `published`) VALUES
(1, 'Manage Quizzes', 'index.php?option=com_joomlaquiz&view=quizzes', '/administrator/components/com_joomlaquiz/assets/images/quizzes48.png', 1),
(2, 'Manage Questions', 'index.php?option=com_joomlaquiz&view=questions', '/administrator/components/com_joomlaquiz/assets/images/questions48.png', 1),
(3, 'Help', 'https://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe-3.0/index.html', '/administrator/components/com_joomlaquiz/assets/images/help48.png', 1);



CREATE TABLE IF NOT EXISTS `#__quiz_export` (
  `eid` int(11) NOT NULL AUTO_INCREMENT,
  `e_filename` varchar(100) NOT NULL DEFAULT '',
  `e_date` date NOT NULL DEFAULT '0000-00-00',
  `e_quizes` text NOT NULL,
  PRIMARY KEY (`eid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `#__quiz_feed_option` (
  `quiz_id` int(11) NOT NULL,
  `from_percent` char(3) NOT NULL,
  `to_percent` char(3) NOT NULL,
  `fmessage` text NOT NULL,
  PRIMARY KEY (`quiz_id`,`from_percent`,`to_percent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE IF NOT EXISTS `#__quiz_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_file` varchar(50) DEFAULT NULL,
  `is_default` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `#__quiz_lpath` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `paid_check` tinyint(1) NOT NULL DEFAULT '1',
  `short_descr` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `published` tinyint(4) DEFAULT NULL,
  `asset_id` int(18) NOT NULL,
  `lp_access_message` text NOT NULL,
  `category` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__quiz_lpath_quiz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lid` int(11) NOT NULL,
  `type` char(1) NOT NULL,
  `qid` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_lpath_stage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `oid` int(11) NOT NULL DEFAULT '0',
  `rel_id` int(11) NOT NULL DEFAULT '0',
  `lpid` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '0',
  `qid` int(11) NOT NULL DEFAULT '0',
  `stage` int(11) NOT NULL DEFAULT '0',
  `attempts` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `#__quiz_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `processor` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `amount` decimal(12,5) DEFAULT NULL,
  `cur_code` char(3) DEFAULT NULL,
  `date` datetime DEFAULT '0000-00-00 00:00:00',
  `pid` varchar(32) NOT NULL,
  `user_id` int(11) DEFAULT '0',
  `checked_out` int(11) DEFAULT '0',
  `checked_out_time` datetime DEFAULT '0000-00-00 00:00:00',
  `confirmed_time` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `#__quiz_pool` (
  `q_id` int(11) NOT NULL DEFAULT '0',
  `q_cat` int(11) NOT NULL DEFAULT '0',
  `q_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`q_id`,`q_cat`,`q_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__quiz_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` varchar(32) DEFAULT NULL,
  `type` char(1) DEFAULT NULL,
  `rel_id` int(11) NOT NULL,
  `xdays` int(5) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `attempts` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_products_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `oid` int(11) DEFAULT '0',
  `qp_id` int(11) NOT NULL DEFAULT '0',
  `xdays_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period_start` date NOT NULL DEFAULT '0000-00-00',
  `period_end` date NOT NULL DEFAULT '0000-00-00',
  `attempts` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_product_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `quiz_sku` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_q_cat` (
  `qc_id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_category` varchar(255) NOT NULL DEFAULT '',
  `qc_instruction` text NOT NULL,
  `qc_tag` varchar(255) NOT NULL,
  PRIMARY KEY (`qc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_q_chain` (
  `quiz_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `q_chain` text NOT NULL,
  `s_unique_id` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`s_unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_blank` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_sq_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_answer` varchar(255) NOT NULL DEFAULT '',
  `is_correct` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_sq_id` (`c_sq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_r_student_choice` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_sq_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_choice_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_sq_id` (`c_sq_id`),
  KEY `c_choice_id` (`c_choice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_dalliclick` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_sq_id` int(10) NOT NULL,
  `c_choice_id` int(10) NOT NULL,
  `c_elapsed_time` int(10) NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__quiz_r_student_hotspot` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_sq_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_select_x` int(10) unsigned NOT NULL DEFAULT '0',
  `c_select_y` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_sq_id` (`c_sq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `#__quiz_r_student_matching` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_sq_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_sel_text` text NOT NULL,
  `c_matching_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_sq_id` (`c_sq_id`),
  KEY `c_matching_id` (`c_matching_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `#__quiz_r_student_memory` (
  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_sq_id` int(11) NOT NULL,
  `c_mid` int(11) NOT NULL,
  `c_elapsed_time` int(11) NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_puzzle` (
  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_sq_id` int(11) unsigned NOT NULL,
  `c_piece` int(10) NOT NULL,
  `c_elapsed_time` int(10) NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_r_student_question` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_stu_quiz_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_question_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_score` float DEFAULT '0',
  `c_attempts` int(11) NOT NULL DEFAULT '0',
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `remark` text NOT NULL,
  `reviewed` tinyint(1) NOT NULL DEFAULT '0',
  `c_elapsed_time` int(10) NOT NULL,
  `c_flag_question` tinyint(2) NOT NULL,
  `respond_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`c_id`),
  KEY `c_stu_quiz_id` (`c_stu_quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_r_student_quiz` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_quiz_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_student_id` int(11) unsigned NOT NULL DEFAULT '0',
  `c_total_score` float NOT NULL DEFAULT '0',
  `c_total_time` int(10) unsigned NOT NULL DEFAULT '0',
  `c_date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `c_passed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `unique_id` varchar(32) NOT NULL DEFAULT '',
  `allow_review` int(11) NOT NULL DEFAULT '0',
  `c_order_id` int(11) DEFAULT '0',
  `c_rel_id` int(11) DEFAULT '0',
  `c_lid` int(11) NOT NULL DEFAULT '0',
  `unique_pass_id` varchar(32) NOT NULL DEFAULT '',
  `c_finished` tinyint(4) DEFAULT '1',
  `user_email` varchar(255) NOT NULL DEFAULT '',
  `c_passing_score` decimal(12,2) NOT NULL DEFAULT '0.00',
  `c_max_score` decimal(12,2) NOT NULL DEFAULT '0.00',
  `user_name` varchar(50) NOT NULL,
  `user_surname` varchar(100) NOT NULL,
  `params` varchar(1024) NOT NULL DEFAULT '{}',
  PRIMARY KEY (`c_id`),
  KEY `c_student_id` (`c_student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_share` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `c_quiz_id` int(12) unsigned NOT NULL,
  `c_stu_quiz_id` int(12) unsigned NOT NULL,
  `c_user_id` int(12) unsigned NOT NULL,
  `c_share_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_r_student_survey` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_sq_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_answer` text NOT NULL,
  PRIMARY KEY (`c_id`),
  KEY `c_sq_id` (`c_sq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__quiz_setup` (
  `c_par_name` varchar(20) NOT NULL DEFAULT '',
  `c_par_value` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `c_par_name` (`c_par_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__quiz_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;


REPLACE INTO `#__quiz_templates` (`id`, `template_name`) VALUES
(1, 'joomlaquiz_standard'),
(2, 'joomlaquiz_t3_bs3'),
(3, 'joomlaquiz_blue'),
(4, 'joomlaquiz_simple'),
(5, 'joomlaquiz_pretty_green'),
(6, 'joomlaquiz_pretty_blue');



CREATE TABLE IF NOT EXISTS `#__quiz_t_blank` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_question_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `points` float NOT NULL DEFAULT '0',
  `css_class` varchar(255) NOT NULL,
  `c_quiz_id` int(11) NOT NULL,
  `gtype` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_question_id` (`c_question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__quiz_t_category` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_category` varchar(255) NOT NULL DEFAULT '',
  `c_instruction` text NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__quiz_t_choice` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_choice` text NOT NULL,
  `c_right` char(1) NOT NULL DEFAULT '0',
  `c_question_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `c_incorrect_feed` text NOT NULL,
  `a_point` float NOT NULL DEFAULT '0',
  `c_quiz_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_question_id` (`c_question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__quiz_t_ext_hotspot` (
  `c_id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `c_question_id` int(12) NOT NULL,
  `c_paths` text NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__quiz_t_faketext` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_quest_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_text` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`c_id`),
  KEY `c_quest_id` (`c_quest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_t_hotspot` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_question_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_start_x` int(10) unsigned NOT NULL DEFAULT '0',
  `c_start_y` int(10) unsigned NOT NULL DEFAULT '0',
  `c_width` int(10) unsigned NOT NULL DEFAULT '0',
  `c_height` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_question_id` (`c_question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_t_matching` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_question_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_left_text` text NOT NULL,
  `c_right_text` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `c_quiz_id` int(11) NOT NULL DEFAULT '0',
  `a_points` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_question_id` (`c_question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_t_memory` (
  `m_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_question_id` int(11) NOT NULL,
  `a_points` float NOT NULL,
  `c_img` varchar(50) NOT NULL,
  `a_pairs` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_t_pbreaks` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_quiz_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_question_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_quiz_id` (`c_quiz_id`),
  KEY `c_question_id` (`c_question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_t_puzzle` (
  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_question_id` int(11) NOT NULL,
  `c_pieces` int(11) NOT NULL DEFAULT '4',
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__quiz_t_qtypes` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_qtype` varchar(50) NOT NULL,
  `c_type` varchar(50) NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;


REPLACE INTO `#__quiz_t_qtypes` (`c_id`, `c_qtype`, `c_type`) VALUES
(1, 'Multiple Choice', 'choice'),
(2, 'Multiple Response', 'mresponse'),
(3, 'True/False', 'truefalse'),
(4, 'Matching Drag&Drop', 'dragdrop'),
(5, 'Matching Drop-Down', 'dropdown'),
(6, 'Fill in the blank', 'blank'),
(7, 'Hotspot', 'hotspot'),
(8, 'Surveys', 'surveys'),
(9, 'Boilerplate', 'boilerplate'),
(10, 'Multiple question', 'mquestion'),
(11, 'Jigsaw Puzzle', 'puzzle'),
(12, 'Image Match', 'imgmatch'),
(13, 'Memory', 'memory'),
(14, 'Dalliclick', 'dalliclick');



CREATE TABLE IF NOT EXISTS `#__quiz_t_question` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_quiz_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_point` float NOT NULL DEFAULT '0',
  `c_attempts` int(11) unsigned DEFAULT '1',
  `c_question` text NOT NULL,
  `c_image` varchar(255) NOT NULL DEFAULT '',
  `c_type` tinyint(4) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `c_right_message` text,
  `c_wrong_message` text,
  `c_feedback` int(11) NOT NULL DEFAULT '0',
  `cq_id` int(11) NOT NULL DEFAULT '0',
  `c_ques_cat` int(11) NOT NULL DEFAULT '0',
  `c_random` char(1) NOT NULL DEFAULT '0',
  `c_partial` tinyint(4) NOT NULL DEFAULT '0',
  `c_partially_message` text NOT NULL,
  `published` tinyint(4) DEFAULT '1',
  `c_title_true` varchar(255) DEFAULT NULL,
  `c_title_false` varchar(255) DEFAULT NULL,
  `c_qform` tinyint(4) NOT NULL DEFAULT '0',
  `report_name` varchar(255) NOT NULL,
  `c_layout` tinyint(4) NOT NULL DEFAULT '0',
  `c_separator` tinyint(4) NOT NULL DEFAULT '1',
  `c_manual` tinyint(1) NOT NULL DEFAULT '0',
  `c_penalty` tinyint(4) NOT NULL DEFAULT '0',
  `c_immediate` tinyint(1) NOT NULL DEFAULT '0',
  `sq_delayed` int(5) NOT NULL DEFAULT '1',
  `c_width` int(10) NOT NULL DEFAULT '150',
  `c_timer` int(10) NOT NULL,
  `c_height` int(10) NOT NULL DEFAULT '150',
  `c_column` int(11) NOT NULL DEFAULT '1',
  `c_img_cover` varchar(50) NOT NULL DEFAULT 'tnnophoto.jpg',
  `c_sec_penalty` int(11) NOT NULL DEFAULT '0',
  `c_detailed_feedback` text NOT NULL,
  `c_time_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `c_show_timer` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_quiz_id` (`c_quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__quiz_t_quiz` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `c_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `c_author` varchar(255) NOT NULL DEFAULT '',
  `c_full_score` int(10) unsigned NOT NULL DEFAULT '0',
  `c_title` varchar(255) NOT NULL DEFAULT '',
  `c_description` text NOT NULL,
  `c_short_description` text,
  `c_image` varchar(255) NOT NULL DEFAULT '',
  `c_time_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `c_min_after` int(10) unsigned NOT NULL DEFAULT '0',
  `c_passing_score` float NOT NULL DEFAULT '0',
  `c_created_time` date NOT NULL DEFAULT '0000-00-00',
  `c_published` char(1) NOT NULL DEFAULT '0',
  `c_right_message` text NOT NULL,
  `c_wrong_message` text NOT NULL,
  `c_pass_message` text NOT NULL,
  `c_unpass_message` text NOT NULL,
  `c_enable_review` char(1) NOT NULL DEFAULT '',
  `c_email_to` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `c_email_chk` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `c_enable_print` char(1) NOT NULL DEFAULT '',
  `c_enable_sertif` char(1) NOT NULL DEFAULT '',
  `c_skin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `c_random` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `published` int(11) NOT NULL DEFAULT '0',
  `c_slide` tinyint(4) NOT NULL DEFAULT '1',
  `c_language` int(11) NOT NULL DEFAULT '0',
  `c_certificate` int(11) NOT NULL DEFAULT '0',
  `c_feedback` int(11) NOT NULL DEFAULT '0',
  `c_feedback_pdf` int(2) NOT NULL DEFAULT '0',
  `c_pool` int(11) NOT NULL DEFAULT '0',
  `c_resbycat` char(1) NOT NULL DEFAULT '0',
  `c_feed_option` char(1) NOT NULL DEFAULT '0',
  `c_show_quest_pos` tinyint(4) NOT NULL DEFAULT '1',
  `c_show_quest_points` tinyint(4) NOT NULL DEFAULT '1',
  `c_show_author` tinyint(1) NOT NULL,
  `c_show_timer` tinyint(4) DEFAULT '1',
  `c_once_per_day` tinyint(4) DEFAULT '0',
  `c_emails` text NOT NULL,
  `c_timer_style` tinyint(4) NOT NULL DEFAULT '0',
  `c_statistic` tinyint(4) NOT NULL DEFAULT '0',
  `c_metadescr` text NOT NULL,
  `c_keywords` text NOT NULL,
  `c_metatitle` text NOT NULL,
  `c_ismetadescr` tinyint(4) NOT NULL DEFAULT '0',
  `c_iskeywords` tinyint(4) NOT NULL DEFAULT '0',
  `c_ismetatitle` tinyint(4) NOT NULL DEFAULT '0',
  `c_number_times` int(11) NOT NULL DEFAULT '1',
  `c_pagination` tinyint(4) NOT NULL DEFAULT '0',
  `c_enable_prevnext` tinyint(4) NOT NULL DEFAULT '0',
  `paid_check` tinyint(1) DEFAULT '1',
  `paid_check_descr` text NOT NULL,
  `c_allow_continue` tinyint(1) NOT NULL DEFAULT '1',
  `c_autostart` tinyint(4) DEFAULT '0',
  `c_redirect_after` tinyint(4) NOT NULL DEFAULT '0',
  `c_redirect_delay` tinyint(4) NOT NULL DEFAULT '0',
  `c_redirect_linktype` tinyint(4) NOT NULL DEFAULT '0',
  `c_redirect_link` text NOT NULL,
  `c_grading` tinyint(1) NOT NULL DEFAULT '0',
  `c_ifmanual` tinyint(1) NOT NULL DEFAULT '0',
  `c_enable_skip` tinyint(3) NOT NULL,
  `c_show_result` tinyint(3) NOT NULL DEFAULT '1',
  `c_show_qfeedback` tinyint(3) NOT NULL,
  `c_flag` tinyint(3) NOT NULL,
  `c_hide_feedback` tinyint(3) NOT NULL,
  `c_share_buttons` tinyint(3) NOT NULL,
  `c_auto_breaks` tinyint(5) NOT NULL,
  `asset_id` int(18) NOT NULL,
  `c_quiz_access_message` text NOT NULL,
  `c_quiz_certificate_access_message` text NOT NULL,
  `one_time` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_user_id` (`c_user_id`),
  KEY `c_author` (`c_author`),
  KEY `c_category_id` (`c_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `#__quiz_t_text` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_blank_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_text` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `c_quiz_id` int(11) NOT NULL DEFAULT '0',
  `regexp` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_blank_id` (`c_blank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
