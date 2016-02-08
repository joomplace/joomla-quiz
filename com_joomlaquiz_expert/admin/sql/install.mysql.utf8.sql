CREATE TABLE IF NOT EXISTS `#__quiz_dashboard_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_certificates` (
		`id` int(11) NOT NULL auto_increment,
		`cert_name` varchar(50) NOT NULL,
		`cert_file` varchar(50) NOT NULL,
		`crtf_align` varchar(100) NOT NULL default '0',
		`crtf_shadow` tinyint(4) NOT NULL default '0',
		`text_x` int(11) NOT NULL default '0',
		`text_y` int(11) NOT NULL default '0',
		`text_size` tinyint(4) NOT NULL default '10',
		`crtf_text` text NOT NULL default '',
		PRIMARY KEY  (`id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_cert_fields` (
			`c_id` int(11) unsigned NOT NULL auto_increment,
			`cert_id` int(11) NOT NULL default '0',
  			`f_text` text default '',
 			`text_x` int(11) NOT NULL default '0',
			`text_y` int(11) NOT NULL default '0',
			`text_h` int(11) NOT NULL default '0',
			`shadow` tinyint(4) NOT NULL default '0',
			`font` varchar(255) DEFAULT 'arial.ttf',
			PRIMARY KEY  (`c_id`)
			) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_configuration` (
  			`config_var` varchar(50) NOT NULL default '',
 			`config_value` text NOT NULL
			);

CREATE TABLE IF NOT EXISTS `#__quiz_constants` (
			`c_id` int(11) unsigned NOT NULL auto_increment,
  			`key_value` varchar(255) NOT NULL default '',
 			`default_value` text NOT NULL,
			PRIMARY KEY  (`c_id`)
			) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_languages` (
		`id` int(11) NOT NULL auto_increment,
		`lang_file` varchar(50) default NULL,
		`is_default` int(11) NOT NULL default '0',
		PRIMARY KEY  (`id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_question` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_stu_quiz_id` int(10) unsigned NOT NULL default '0',
		`c_question_id` int(10) unsigned NOT NULL default '0',
		`c_score` INT( 11 ) DEFAULT '0',
		`c_attempts` int(11) NOT NULL default '0',
		`is_correct` TINYINT( 1 ) NOT NULL DEFAULT '0',
		`remark` text NOT NULL,
		`reviewed` TINYINT( 1 ) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_stu_quiz_id` (`c_stu_quiz_id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_quiz` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_quiz_id` int(10) unsigned NOT NULL default '0',
		`c_student_id` int(11) unsigned NOT NULL default '0',
		`c_total_score` float unsigned NOT NULL default '0',
		`c_total_time` int(10) unsigned NOT NULL default '0',
		`c_date_time` datetime NOT NULL default '0000-00-00 00:00:00',
		`c_passed` tinyint(1) unsigned NOT NULL default '0',
		`unique_id` varchar(32) NOT NULL default '',
		`allow_review` int(11) NOT NULL default '0',
		`c_order_id` int(11) default '0',
		`c_rel_id` int(11) default '0',
		`c_lid` int(11) NOT NULL default '0',
		`unique_pass_id` varchar(32) NOT NULL default '',
		`c_finished` tinyint(4) default '1',
		`user_email` varchar(255) NOT NULL DEFAULT '',
		`c_passing_score` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0',
		`c_max_score` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_student_id` (`c_student_id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_t_pbreaks` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_quiz_id` int(10) unsigned NOT NULL default '0',
		`c_question_id` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_quiz_id` (`c_quiz_id`),
		KEY `c_question_id` (`c_question_id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_t_category` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_category` varchar(255) NOT NULL default '',
		`c_instruction` text NOT NULL,
		PRIMARY KEY  (`c_id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_t_question` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_quiz_id` int(10) unsigned NOT NULL default '0',
		`c_point` tinyint(3) unsigned NOT NULL default '0',
		`c_attempts` int( 11 ) unsigned DEFAULT '1',
		`c_question` text NOT NULL,
		`c_image` varchar(255) NOT NULL default '',
		`c_type` tinyint(4) NOT NULL default '0',
		`ordering` int(11) default '0',
		`c_right_message` text,
  		`c_wrong_message` text,
		`c_feedback` int(11) NOT NULL default '0',
		`cq_id` int(11) NOT NULL default '0',
		`c_ques_cat` int(11) NOT NULL default '0',
		`c_random` char(1) NOT NULL default '0',
		`c_partial` tinyint(4) NOT NULL DEFAULT '0',
		`c_partially_message` text NOT NULL,
		`published` tinyint(4) DEFAULT '1',
		`c_title_true` varchar(255) DEFAULT NULL,
		`c_title_false` varchar(255) DEFAULT NULL,
		`c_qform` tinyint(4) NOT NULL DEFAULT '0',
		`report_name` varchar( 255 ) NOT NULL,
		`c_layout` tinyint(4) NOT NULL DEFAULT '0',
		`c_separator` tinyint(4) NOT NULL DEFAULT '1',
		`c_manual` tinyint( 1 ) NOT NULL DEFAULT '0',
		`c_penalty` tinyint( 4 ) NOT NULL DEFAULT '0',
		`c_immediate` tinyint( 1 ) NOT NULL DEFAULT '0',
		PRIMARY KEY  (`c_id`),
		KEY `c_quiz_id` (`c_quiz_id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_t_quiz` (
		`c_id` int(10) unsigned NOT NULL auto_increment,
		`c_category_id` int(11) unsigned NOT NULL default '0',
		`c_user_id` int(11) unsigned NOT NULL default '0',
		`c_author` varchar(255) NOT NULL default '',
		`c_full_score` int(10) unsigned NOT NULL default '0',
		`c_title` varchar(255) NOT NULL default '',
		`c_description` text NOT NULL,
		`c_short_description` text,
		`c_image` varchar(255) NOT NULL default '',
		`c_time_limit` int(10) unsigned NOT NULL default '0',
		`c_min_after` int(10) unsigned NOT NULL default '0',
		`c_passing_score` float unsigned NOT NULL default '0',
		`c_created_time` date NOT NULL default '0000-00-00',
		`c_published` char(1) NOT NULL default '0',
		`c_right_message` text NOT NULL,
		`c_wrong_message` text NOT NULL,
		`c_pass_message` text NOT NULL,
		`c_unpass_message` text NOT NULL,
		`c_enable_review` char(1) NOT NULL default '',
		`c_email_to` tinyint(1) unsigned NOT NULL default '0',
		`c_email_chk` tinyint(1) unsigned NOT NULL default '0',
		`c_enable_print` char(1) NOT NULL default '',
		`c_enable_sertif` char(1) NOT NULL default '',
		`c_skin` tinyint(3) unsigned NOT NULL default '0',
		`c_random` tinyint(1) unsigned NOT NULL default '0',
		`c_guest` tinyint(1) unsigned NOT NULL default '0',
		`published` int(11) NOT NULL default '0',
		`c_slide` tinyint(4) NOT NULL default '1',
		`c_language` int(11) NOT NULL default '0',
		`c_certificate` int(11) NOT NULL default '0',
		`c_feedback` int(11) NOT NULL default '0',
		`c_pool` int(11) NOT NULL default '0',
		`c_resbycat` char(1) NOT NULL default '0',
		`c_feed_option` char(1) NOT NULL default '0',
		`c_show_quest_pos` tinyint(4) NOT NULL default '1',
		`c_show_quest_points` tinyint(4) NOT NULL default '1',
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
		`c_grading` tinyint( 1 ) NOT NULL DEFAULT '0',
		`c_ifmanual` tinyint( 1 ) NOT NULL DEFAULT '0',
		`c_enable_skip` tinyint( 3 ) NOT NULL,
		`c_show_result` tinyint( 3 ) NOT NULL DEFAULT '1',
		`c_show_qfeedback` tinyint( 3 ) NOT NULL,
		PRIMARY KEY  (`c_id`),
		KEY `c_user_id` (`c_user_id`),
		KEY `c_author` (`c_author`),
		KEY `c_category_id` (`c_category_id`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_templates` (
		`id` int(11) NOT NULL auto_increment,
		`template_name` varchar(250) NOT NULL,
		PRIMARY KEY  (`id`) ) DEFAULT CHARSET=utf8;
		
INSERT INTO `#__quiz_templates` (`id`, `template_name`) VALUES
(1, 'joomlaquiz_standard'),
(2, 'joomlaquiz_t3_bs3'),
(3, 'joomlaquiz_blue'),
(4, 'joomlaquiz_simple'),
(5, 'joomlaquiz_pretty_green'),
(6, 'joomlaquiz_pretty_blue');

CREATE TABLE IF NOT EXISTS `#__quiz_setup` (
		`c_par_name` varchar(20) NOT NULL default '',
		`c_par_value` varchar(255) NOT NULL default '',
		UNIQUE KEY `c_par_name` (`c_par_name`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_export` (
		`eid` int(11) NOT NULL auto_increment,
		`e_filename` varchar(100) NOT NULL default '',
		`e_date` date NOT NULL default '0000-00-00',
		`e_quizes` text NOT NULL default '',
		PRIMARY KEY  (`eid`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_feed_option` (
		`quiz_id` int(11) NOT NULL,
		`from_percent` char(3) NOT NULL,
		`to_percent` char(3) NOT NULL,
		`fmessage` text NOT NULL default '',
		PRIMARY KEY  (`quiz_id`,`from_percent`,`to_percent`) ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_pool` (
		  `q_id` int(11) NOT NULL default '0',
		  `q_cat` int(11) NOT NULL default '0',
		  `q_count` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`q_id`,`q_cat`,`q_count`)) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_q_cat` (
		  `qc_id` int(11) NOT NULL auto_increment,
		  `qc_category` varchar(255) NOT NULL default '',
		  `qc_instruction` text NOT NULL,
		  `qc_tag` varchar(255) NOT NULL,
		  PRIMARY KEY  (`qc_id`)) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_q_chain` (
		  `quiz_id` int(11) NOT NULL default '0',
		  `user_id` int(11) NOT NULL default '0',
		  `q_chain` text NOT NULL,
		  `s_unique_id` varchar(32) NOT NULL default '',
		  PRIMARY KEY  (`s_unique_id`)) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_lpath` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(255) default NULL,
		  `paid_check` tinyint(1) NOT NULL default '1',
		  `short_descr` varchar(255) NOT NULL,
		  `descr` text NOT NULL,
		  `published` tinyint(4) default NULL,
		  PRIMARY KEY  (`id`)) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_lpath_quiz` (
		  `id` int(11) NOT NULL auto_increment,
		  `lid` int(11) NOT NULL,
		  `type` char(1) NOT NULL,
		  `qid` int(11) NOT NULL,
		  `order` int(11) NOT NULL,
		  PRIMARY KEY  (`id`)) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_lpath_stage` (
		  `id` int(11) NOT NULL auto_increment,
		  `uid` int(11) NOT NULL default '0',
		  `oid` int(11) NOT NULL default '0',
		  `rel_id` int(11) NOT NULL default '0',
		  `lpid` int(11) NOT NULL default '0',
		  `type` char(1) NOT NULL default '0',
		  `qid` int(11) NOT NULL default '0',
		  `stage` int(11) NOT NULL default '0',
		  `attempts` int(5) NOT NULL default '0',
		  PRIMARY KEY  (`id`)) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_payments` (
		  `id` int(11) NOT NULL auto_increment,
		  `processor` varchar(255) default NULL,
		  `status` varchar(255) default NULL,
		  `amount` decimal(12,5) default NULL,
		  `cur_code` char(3) default NULL,
		  `date` datetime default '0000-00-00 00:00:00',
		  `pid` varchar(32) NOT NULL,
		  `user_id` int(11) default '0',
		  `checked_out` int(11) default '0',
		  `checked_out_time` datetime default '0000-00-00 00:00:00',
		  `confirmed_time` datetime default '0000-00-00 00:00:00',
		  PRIMARY KEY  (`id`));

CREATE TABLE IF NOT EXISTS `#__quiz_product_info` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(255) NOT NULL,
		  `quiz_sku` varchar(32) default NULL,
		  PRIMARY KEY  (`id`)) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_products` (
		  `id` int(11) NOT NULL auto_increment,
		  `pid` varchar(32) default NULL,
		  `type` char(1) default NULL,
		  `rel_id` int(11) NOT NULL,
		  `xdays` int(5) NOT NULL,
		  `period_start` date NOT NULL,
		  `period_end` date NOT NULL,
		  `attempts` int(5) NOT NULL,
		  PRIMARY KEY  (`id`)) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_products_stat` (
		  `id` int(11) NOT NULL auto_increment,
		  `uid` int(11) NOT NULL default '0',
		  `oid` int(11) default '0',
		  `qp_id` int(11) NOT NULL default '0',
		  `xdays_start` datetime NOT NULL default '0000-00-00 00:00:00',
		  `period_start` date NOT NULL default '0000-00-00',
		  `period_end` date NOT NULL default '0000-00-00',
		  `attempts` int(3) NOT NULL default '0',
		  PRIMARY KEY  (`id`)) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__quiz_t_qtypes` (
		`c_id` int(11) NOT NULL auto_increment,
		`c_qtype` varchar(50) NOT NULL,
		`c_type` varchar(50) NOT NULL,
		PRIMARY KEY  (`c_id`) ) DEFAULT CHARSET=utf8;