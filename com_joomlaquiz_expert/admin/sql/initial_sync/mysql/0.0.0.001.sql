CREATE TABLE IF NOT EXISTS `#__quiz_certificates` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_certificates` ADD COLUMN `cert_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `id`;
ALTER TABLE `#__quiz_certificates` ADD COLUMN `cert_file` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `cert_name`;
ALTER TABLE `#__quiz_certificates` ADD COLUMN `crtf_align` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `cert_file`;
ALTER TABLE `#__quiz_certificates` ADD COLUMN `crtf_shadow` TINYINT(4) NOT NULL DEFAULT '0' AFTER `crtf_align`;
ALTER TABLE `#__quiz_certificates` ADD COLUMN `text_x` INT(11) NOT NULL DEFAULT '0' AFTER `crtf_shadow`;
ALTER TABLE `#__quiz_certificates` ADD COLUMN `text_y` INT(11) NOT NULL DEFAULT '0' AFTER `text_x`;
ALTER TABLE `#__quiz_certificates` ADD COLUMN `text_size` TINYINT(4) NULL DEFAULT '10' AFTER `text_y`;
ALTER TABLE `#__quiz_certificates` ADD COLUMN `crtf_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `text_size`;
ALTER TABLE `#__quiz_certificates` ADD COLUMN `text_font` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'arial.ttf' AFTER `crtf_text`;

CREATE TABLE IF NOT EXISTS `#__quiz_cert_fields` (
`c_id` INT(11)  UNSIGNED NOT NULL AUTO_INCREMENT,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_cert_fields` ADD COLUMN `cert_id` INT(11) NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_cert_fields` ADD COLUMN `f_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `cert_id`;
ALTER TABLE `#__quiz_cert_fields` ADD COLUMN `text_x` INT(11) NOT NULL DEFAULT '0' AFTER `f_text`;
ALTER TABLE `#__quiz_cert_fields` ADD COLUMN `text_y` INT(11) NOT NULL DEFAULT '0' AFTER `text_x`;
ALTER TABLE `#__quiz_cert_fields` ADD COLUMN `text_h` INT(11) NOT NULL DEFAULT '0' AFTER `text_y`;
ALTER TABLE `#__quiz_cert_fields` ADD COLUMN `shadow` TINYINT(4) NOT NULL DEFAULT '0' AFTER `text_h`;
ALTER TABLE `#__quiz_cert_fields` ADD COLUMN `font` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'arial.ttf' AFTER `shadow`;

CREATE TABLE IF NOT EXISTS `#__quiz_constants` (
 `c_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
 PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_constants` ADD COLUMN `key_value` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_id`;
ALTER TABLE `#__quiz_constants` ADD COLUMN `default_value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `key_value`;

CREATE TABLE IF NOT EXISTS `#__quiz_dashboard_items` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_dashboard_items` ADD COLUMN `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `id`;
ALTER TABLE `#__quiz_dashboard_items` ADD COLUMN `url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `title`;
ALTER TABLE `#__quiz_dashboard_items` ADD COLUMN `icon` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `url`;
ALTER TABLE `#__quiz_dashboard_items` ADD COLUMN `published` TINYINT(1) NOT NULL AFTER `icon`;

CREATE TABLE IF NOT EXISTS `#__quiz_export` (
`eid` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`eid`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_export` ADD COLUMN `e_filename` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `eid`;
ALTER TABLE `#__quiz_export` ADD COLUMN `e_date` DATE NOT NULL DEFAULT '0000-00-00' AFTER `e_filename`;
ALTER TABLE `#__quiz_export` ADD COLUMN `e_quizes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `e_date`;

CREATE TABLE IF NOT EXISTS `#__quiz_feed_option` (
`quiz_id` INT(11) NOT NULL
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_feed_option` ADD COLUMN `from_percent` CHAR(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `quiz_id`;
ALTER TABLE `#__quiz_feed_option` ADD COLUMN `to_percent` CHAR(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `from_percent`;
ALTER TABLE `#__quiz_feed_option` ADD COLUMN `fmessage` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `to_percent`;

ALTER TABLE `#__quiz_feed_option`
  ADD PRIMARY KEY (`quiz_id`,`from_percent`,`to_percent`);

CREATE TABLE IF NOT EXISTS `#__quiz_languages` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_languages` ADD COLUMN `lang_file` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `#__quiz_languages` ADD COLUMN `is_default` INT(11) NOT NULL DEFAULT '0' AFTER `lang_file`;

CREATE TABLE IF NOT EXISTS `#__quiz_lpath` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_lpath` ADD COLUMN `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `#__quiz_lpath` ADD COLUMN `paid_check` TINYINT(1) NOT NULL DEFAULT '1' AFTER `title`;
ALTER TABLE `#__quiz_lpath` ADD COLUMN `short_descr` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `paid_check`;
ALTER TABLE `#__quiz_lpath` ADD COLUMN `descr` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `short_descr`;
ALTER TABLE `#__quiz_lpath` ADD COLUMN `published` TINYINT(4) NULL DEFAULT NULL AFTER `descr`;
ALTER TABLE `#__quiz_lpath` ADD COLUMN `asset_id` INT(19) NOT NULL AFTER `published`;
ALTER TABLE `#__quiz_lpath` ADD COLUMN `lp_access_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `asset_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_lpath_quiz` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_lpath_quiz` ADD COLUMN `lid` INT(11) NOT NULL AFTER `id`;
ALTER TABLE `#__quiz_lpath_quiz` ADD COLUMN `type` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `lid`;
ALTER TABLE `#__quiz_lpath_quiz` ADD COLUMN `qid` INT(11) NOT NULL AFTER `type`;
ALTER TABLE `#__quiz_lpath_quiz` ADD COLUMN `order` INT(11) NOT NULL AFTER `qid`;

CREATE TABLE IF NOT EXISTS `#__quiz_lpath_stage` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_lpath_stage` ADD COLUMN `uid` INT(11) NOT NULL DEFAULT '0' AFTER `id`;
ALTER TABLE `#__quiz_lpath_stage` ADD COLUMN `oid` INT(11) NOT NULL DEFAULT '0' AFTER `uid`;
ALTER TABLE `#__quiz_lpath_stage` ADD COLUMN `rel_id` INT(11) NOT NULL DEFAULT '0' AFTER `oid`;
ALTER TABLE `#__quiz_lpath_stage` ADD COLUMN `lpid` INT(11) NOT NULL DEFAULT '0' AFTER `rel_id`;
ALTER TABLE `#__quiz_lpath_stage` ADD COLUMN `type` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `lpid`;
ALTER TABLE `#__quiz_lpath_stage` ADD COLUMN `qid` INT(11) NOT NULL DEFAULT '0' AFTER `type`;
ALTER TABLE `#__quiz_lpath_stage` ADD COLUMN `stage` INT(11) NOT NULL DEFAULT '0' AFTER `qid`;
ALTER TABLE `#__quiz_lpath_stage` ADD COLUMN `attempts` INT(5) NOT NULL DEFAULT '0' AFTER `stage`;

CREATE TABLE IF NOT EXISTS `#__quiz_payments` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_payments` ADD COLUMN `processor` VARCHAR(255) NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `#__quiz_payments` ADD COLUMN `status` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `processor`;
ALTER TABLE `#__quiz_payments` ADD COLUMN `amount` DECIMAL(12,5) NULL DEFAULT NULL AFTER `status`;
ALTER TABLE `#__quiz_payments` ADD COLUMN `cur_code` CHAR(3) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `amount`;
ALTER TABLE `#__quiz_payments` ADD COLUMN `date` DATETIME NULL DEFAULT '0000-00-00 00:00:00' AFTER `cur_code`;
ALTER TABLE `#__quiz_payments` ADD COLUMN `pid` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `date`;
ALTER TABLE `#__quiz_payments` ADD COLUMN `user_id` INT(11) NULL DEFAULT '0' AFTER `pid`;
ALTER TABLE `#__quiz_payments` ADD COLUMN `checked_out` INT(11) NULL DEFAULT '0' AFTER `user_id`;
ALTER TABLE `#__quiz_payments` ADD COLUMN `checked_out_time` DATETIME NULL DEFAULT '0000-00-00 00:00:00' AFTER `checked_out`;
ALTER TABLE `#__quiz_payments` ADD COLUMN `confirmed_time` DATETIME NULL DEFAULT '0000-00-00 00:00:00' AFTER `checked_out_time`;

CREATE TABLE IF NOT EXISTS `#__quiz_pool` (
`q_id` INT(11) NOT NULL DEFAULT '0'
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `#__quiz_pool` ADD COLUMN `q_cat` INT(11) NOT NULL DEFAULT '0' AFTER `q_id`;
ALTER TABLE `#__quiz_pool` ADD COLUMN `q_count` INT(11) NOT NULL DEFAULT '0' AFTER `q_cat`;

ALTER TABLE `#__quiz_pool`
  ADD PRIMARY KEY (`q_id`,`q_cat`,`q_count`);

CREATE TABLE IF NOT EXISTS `#__quiz_products` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_products` ADD COLUMN `pid` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `#__quiz_products` ADD COLUMN `type` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `pid`;
ALTER TABLE `#__quiz_products` ADD COLUMN `rel_id` INT(11) NOT NULL AFTER `type`;
ALTER TABLE `#__quiz_products` ADD COLUMN `xdays` INT(5) NOT NULL AFTER `rel_id`;
ALTER TABLE `#__quiz_products` ADD COLUMN `period_start` DATE NOT NULL AFTER `xdays`;
ALTER TABLE `#__quiz_products` ADD COLUMN `period_end` DATE NOT NULL AFTER `period_start`;
ALTER TABLE `#__quiz_products` ADD COLUMN `attempts` INT(5) NOT NULL AFTER `period_end`;

CREATE TABLE IF NOT EXISTS `#__quiz_products_stat` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_products_stat` ADD COLUMN `uid` INT(11) NOT NULL DEFAULT '0' AFTER `id`;
ALTER TABLE `#__quiz_products_stat` ADD COLUMN `oid` INT(11) NULL DEFAULT '0' AFTER `uid`;
ALTER TABLE `#__quiz_products_stat` ADD COLUMN `qp_id` INT(11) NOT NULL DEFAULT '0' AFTER `oid`;
ALTER TABLE `#__quiz_products_stat` ADD COLUMN `xdays_start` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `qp_id`;
ALTER TABLE `#__quiz_products_stat` ADD COLUMN `period_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `xdays_start`;
ALTER TABLE `#__quiz_products_stat` ADD COLUMN `period_end` DATE NOT NULL DEFAULT '0000-00-00' AFTER `period_start`;
ALTER TABLE `#__quiz_products_stat` ADD COLUMN `attempts` INT(3) NOT NULL DEFAULT '0' AFTER `period_end`;

CREATE TABLE IF NOT EXISTS `#__quiz_product_info` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_product_info` ADD COLUMN `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `id`;
ALTER TABLE `#__quiz_product_info` ADD COLUMN `quiz_sku` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `name`;

CREATE TABLE IF NOT EXISTS `#__quiz_q_cat` (
`qc_id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`qc_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_q_cat` ADD COLUMN `qc_category` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `qc_id`;
ALTER TABLE `#__quiz_q_cat` ADD COLUMN `qc_instruction` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `qc_category`;
ALTER TABLE `#__quiz_q_cat` ADD COLUMN `qc_tag` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `qc_instruction`;

CREATE TABLE IF NOT EXISTS `#__quiz_q_chain` (
`quiz_id` INT(11) NOT NULL DEFAULT '0'
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_q_chain` ADD COLUMN `user_id` INT(11) NOT NULL DEFAULT '0' AFTER `quiz_id`;
ALTER TABLE `#__quiz_q_chain` ADD COLUMN `q_chain` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `user_id`;
ALTER TABLE `#__quiz_q_chain` ADD COLUMN `s_unique_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `q_chain`, ADD PRIMARY KEY (`s_unique_id`);

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_blank` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_blank` ADD COLUMN `c_sq_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_blank` ADD COLUMN `c_answer` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_sq_id`;
ALTER TABLE `#__quiz_r_student_blank` ADD COLUMN `is_correct` TINYINT(2) NOT NULL DEFAULT '0' AFTER `c_answer`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_choice` (
`c_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_choice` ADD COLUMN `c_sq_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_choice` ADD COLUMN `c_choice_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_sq_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_dalliclick` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_dalliclick` ADD COLUMN `c_sq_id` INT(10) NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_dalliclick` ADD COLUMN `c_choice_id` INT(10) NOT NULL DEFAULT '0' AFTER `c_sq_id`;
ALTER TABLE `#__quiz_r_student_dalliclick` ADD COLUMN `c_elapsed_time` INT(10) NOT NULL DEFAULT '0' AFTER `c_choice_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_hotspot` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_hotspot` ADD COLUMN `c_sq_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_hotspot` ADD COLUMN `c_select_x` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_sq_id`;
ALTER TABLE `#__quiz_r_student_hotspot` ADD COLUMN `c_select_y` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_select_x`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_matching` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_matching` ADD COLUMN `c_sq_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_matching` ADD COLUMN `c_sel_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_sq_id`;
ALTER TABLE `#__quiz_r_student_matching` ADD COLUMN `c_matching_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_sel_text`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_memory` (
`c_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_memory` ADD COLUMN `c_sq_id` INT(11) NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_memory` ADD COLUMN `c_mid` INT(11) NOT NULL DEFAULT '0' AFTER `c_sq_id`;
ALTER TABLE `#__quiz_r_student_memory` ADD COLUMN `c_elapsed_time` INT(11) NOT NULL DEFAULT '0' AFTER `c_mid`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_puzzle` (
`c_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_puzzle` ADD COLUMN `c_sq_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_puzzle` ADD COLUMN `c_piece` INT(10) NOT NULL DEFAULT '0' AFTER `c_sq_id`;
ALTER TABLE `#__quiz_r_student_puzzle` ADD COLUMN `c_elapsed_time` INT(10) NULL DEFAULT '0' AFTER `c_piece`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_question` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_question` ADD COLUMN `c_stu_quiz_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_question` ADD COLUMN `c_question_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_stu_quiz_id`;
ALTER TABLE `#__quiz_r_student_question` ADD COLUMN `c_score` FLOAT NULL DEFAULT '0' AFTER `c_question_id`;
ALTER TABLE `#__quiz_r_student_question` ADD COLUMN `c_attempts` INT(11) NOT NULL DEFAULT '0' AFTER `c_score`;
ALTER TABLE `#__quiz_r_student_question` ADD COLUMN `is_correct` TINYINT(1) NOT NULL DEFAULT '0' AFTER `c_attempts`;
ALTER TABLE `#__quiz_r_student_question` ADD COLUMN `remark` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `is_correct`;
ALTER TABLE `#__quiz_r_student_question` ADD COLUMN `reviewed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `remark`;
ALTER TABLE `#__quiz_r_student_question` ADD COLUMN `c_elapsed_time` INT(10) NOT NULL AFTER `reviewed`;
ALTER TABLE `#__quiz_r_student_question` ADD COLUMN `c_flag_question` TINYINT(2) NOT NULL AFTER `c_elapsed_time`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_quiz` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_quiz_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_student_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_quiz_id`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_total_score` FLOAT NOT NULL DEFAULT '0' AFTER `c_student_id`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_total_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_total_score`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_date_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `c_total_time`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_passed` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_date_time`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `unique_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_passed`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `allow_review` INT(11) NOT NULL DEFAULT '0' AFTER `unique_id`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_order_id` INT(11) NULL DEFAULT '0' AFTER `allow_review`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_rel_id` INT(11) NULL DEFAULT '0' AFTER `c_order_id`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_lid` INT(11) NOT NULL DEFAULT '0' AFTER `c_rel_id`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `unique_pass_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_lid`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_finished` TINYINT(4) NULL DEFAULT '1' AFTER `unique_pass_id`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `user_email` VARCHAR(255) NOT NULL AFTER `c_finished`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_passing_score` DECIMAL(12,2) NOT NULL DEFAULT '0.00' AFTER `user_email`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `c_max_score` DECIMAL(12,2) NOT NULL DEFAULT '0.00' AFTER `c_passing_score`;

ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `user_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_max_score`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `user_surname` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `user_name`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `params` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '{}' AFTER `user_surname`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_share` (
`id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_share` ADD COLUMN `c_quiz_id` INT(12) UNSIGNED NOT NULL AFTER `id`;
ALTER TABLE `#__quiz_r_student_share` ADD COLUMN `c_stu_quiz_id` INT(12) UNSIGNED NOT NULL AFTER `c_quiz_id`;
ALTER TABLE `#__quiz_r_student_share` ADD COLUMN `c_user_id` INT(12) UNSIGNED NOT NULL AFTER `c_stu_quiz_id`;
ALTER TABLE `#__quiz_r_student_share` ADD COLUMN `c_share_id` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_user_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_survey` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_r_student_survey` ADD COLUMN `c_sq_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_r_student_survey` ADD COLUMN `c_answer` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_sq_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_setup` (
`c_par_name` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_setup` ADD COLUMN `c_par_value` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_par_name`;

CREATE TABLE IF NOT EXISTS `#__quiz_templates` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_templates` ADD COLUMN `template_name` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `id`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_blank` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_blank` ADD COLUMN `c_question_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_blank` ADD COLUMN `ordering` INT(11) NOT NULL DEFAULT '0' AFTER `c_question_id`;
ALTER TABLE `#__quiz_t_blank` ADD COLUMN `points` FLOAT NOT NULL DEFAULT '0' AFTER `ordering`;
ALTER TABLE `#__quiz_t_blank` ADD COLUMN `css_class` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `points`;
ALTER TABLE `#__quiz_t_blank` ADD COLUMN `c_quiz_id` INT(11) NOT NULL AFTER `css_class`;
ALTER TABLE `#__quiz_t_blank` ADD COLUMN `gtype` TINYINT(1) NOT NULL DEFAULT '0' AFTER `c_quiz_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_category` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_category` ADD COLUMN `c_category` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_id`;
ALTER TABLE `#__quiz_t_category` ADD COLUMN `c_instruction` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_category`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_choice` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_choice` ADD COLUMN `c_choice` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_id`;
ALTER TABLE `#__quiz_t_choice` ADD COLUMN `c_right` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `c_choice`;
ALTER TABLE `#__quiz_t_choice` ADD COLUMN `c_question_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_right`;
ALTER TABLE `#__quiz_t_choice` ADD COLUMN `ordering` INT(11) NOT NULL DEFAULT '0' AFTER `c_question_id`;
ALTER TABLE `#__quiz_t_choice` ADD COLUMN `c_incorrect_feed` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `ordering`;
ALTER TABLE `#__quiz_t_choice` ADD COLUMN `a_point` FLOAT NOT NULL DEFAULT '0' AFTER `c_incorrect_feed`;
ALTER TABLE `#__quiz_t_choice` ADD COLUMN `c_quiz_id` INT(11) NOT NULL DEFAULT '0' AFTER `a_point`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_dalliclick` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_dalliclick` ADD COLUMN `c_choice` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_id`;
ALTER TABLE `#__quiz_t_dalliclick` ADD COLUMN `c_right` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_choice`;
ALTER TABLE `#__quiz_t_dalliclick` ADD COLUMN `c_question_id` INT(10) NOT NULL DEFAULT '0' AFTER `c_right`;
ALTER TABLE `#__quiz_t_dalliclick` ADD COLUMN `ordering` INT(11) NOT NULL DEFAULT '0' AFTER `c_question_id`;
ALTER TABLE `#__quiz_t_dalliclick` ADD COLUMN `c_incorrect_feed` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `ordering`;
ALTER TABLE `#__quiz_t_dalliclick` ADD COLUMN `a_point` FLOAT NOT NULL DEFAULT '0' AFTER `c_incorrect_feed`;
ALTER TABLE `#__quiz_t_dalliclick` ADD COLUMN `c_quiz_id` INT(11) NOT NULL DEFAULT '0' AFTER `a_point`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_ext_hotspot` (
`c_id` int(12) unsigned NOT NULL AUTO_INCREMENT,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_ext_hotspot` ADD COLUMN `c_question_id` INT(12) NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_ext_hotspot` ADD COLUMN `c_paths` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_question_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_memory` (
`m_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`m_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_memory` ADD COLUMN `c_question_id` INT(11) NOT NULL DEFAULT '0' AFTER `m_id`;
ALTER TABLE `#__quiz_t_memory` ADD COLUMN `a_points` FLOAT NOT NULL DEFAULT '0' AFTER `c_question_id`;
ALTER TABLE `#__quiz_t_memory` ADD COLUMN `c_img` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `a_points`;
ALTER TABLE `#__quiz_t_memory` ADD COLUMN `a_pairs` INT(10) NOT NULL DEFAULT '1' AFTER `c_img`;


CREATE TABLE IF NOT EXISTS `#__quiz_t_faketext` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_faketext` ADD COLUMN `c_quest_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_faketext` ADD COLUMN `c_text` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_quest_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_hotspot` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_hotspot` ADD COLUMN `c_question_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_hotspot` ADD COLUMN `c_start_x` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `c_question_id`;
ALTER TABLE `#__quiz_t_hotspot` ADD COLUMN `c_start_y` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_start_x`;
ALTER TABLE `#__quiz_t_hotspot` ADD COLUMN `c_width` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_start_y`;
ALTER TABLE `#__quiz_t_hotspot` ADD COLUMN `c_height` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_width`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_matching` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_matching` ADD COLUMN `c_question_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_matching` ADD COLUMN `c_left_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_question_id`;
ALTER TABLE `#__quiz_t_matching` ADD COLUMN `c_right_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_left_text`;
ALTER TABLE `#__quiz_t_matching` ADD COLUMN `ordering` INT(11) NOT NULL DEFAULT '0' AFTER `c_right_text`;
ALTER TABLE `#__quiz_t_matching` ADD COLUMN `c_quiz_id` INT(11) NOT NULL DEFAULT '0' AFTER `ordering`;
ALTER TABLE `#__quiz_t_matching` ADD COLUMN `a_points` FLOAT NOT NULL DEFAULT '0' AFTER `c_quiz_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_pbreaks` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_pbreaks` ADD COLUMN `c_quiz_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_pbreaks` ADD COLUMN `c_question_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_quiz_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_puzzle` (
`c_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_puzzle` ADD COLUMN `c_question_id` INT(11) NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_puzzle` ADD COLUMN `c_pieces` INT(11) NOT NULL DEFAULT '4' AFTER `c_question_id`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_qtypes` (
`c_id` INT(11) NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=15 ;

ALTER TABLE `#__quiz_t_qtypes` ADD COLUMN `c_qtype` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_id`;
ALTER TABLE `#__quiz_t_qtypes` ADD COLUMN `c_type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_qtype`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_question` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_quiz_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_point` FLOAT NOT NULL DEFAULT '0' AFTER `c_quiz_id`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_attempts` INT(11) UNSIGNED NULL DEFAULT '1' AFTER `c_point`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_question` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_attempts`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_image` VARCHAR(255) NOT NULL AFTER `c_question`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_type` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_image`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `ordering` INT(11) NULL DEFAULT '0' AFTER `c_type`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_right_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `ordering`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_wrong_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `c_right_message`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_feedback` INT(11) NOT NULL DEFAULT '0' AFTER `c_wrong_message`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `cq_id` INT(11) NOT NULL DEFAULT '0' AFTER `c_feedback`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_ques_cat` INT(11) NOT NULL DEFAULT '0' AFTER `cq_id`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_random` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `c_ques_cat`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_partial` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_random`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_partially_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_partial`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `published` TINYINT(4) NULL DEFAULT '1' AFTER `c_partially_message`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_title_true` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `published`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_title_false` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `c_title_true`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_qform` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_title_false`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `report_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_qform`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_layout` TINYINT(4) NOT NULL DEFAULT '0' AFTER `report_name`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_separator` TINYINT(4) NOT NULL DEFAULT '1' AFTER `c_layout`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_manual` TINYINT(1) NOT NULL DEFAULT '0' AFTER `c_separator`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_penalty` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_manual`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_immediate` TINYINT(1) NOT NULL DEFAULT '0' AFTER `c_penalty`;

ALTER TABLE `#__quiz_t_question` ADD COLUMN `sq_delayed` INT(5) NOT NULL DEFAULT '1' AFTER `c_immediate`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_width` INT(10) NOT NULL DEFAULT '150' AFTER `sq_delayed`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_timer` INT(10) NOT NULL AFTER `c_width`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_height` INT(10) NOT NULL DEFAULT '150' AFTER `c_timer`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_column` INT(11) NOT NULL DEFAULT '1' AFTER `c_height`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_img_cover` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'tnnophoto.jpg' AFTER `c_column`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_sec_penalty` INT(11) NOT NULL DEFAULT '0' AFTER `c_img_cover`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_detailed_feedback` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_sec_penalty`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_time_limit` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_detailed_feedback`;
ALTER TABLE `#__quiz_t_question` ADD COLUMN `c_show_timer` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_time_limit`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_quiz` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_category_id`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_author` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_user_id`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_full_score` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_author`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_full_score`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_title`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_image` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_description`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_time_limit` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_image`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_min_after` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_time_limit`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_passing_score` FLOAT NOT NULL DEFAULT '0' AFTER `c_min_after`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_created_time` DATE NOT NULL DEFAULT '0000-00-00' AFTER `c_passing_score`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_published` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `c_created_time`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_right_message` TEXT CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL AFTER `c_published`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_wrong_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_right_message`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_pass_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_wrong_message`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_unpass_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_pass_message`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_enable_review` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_unpass_message`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_email_to` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_enable_review`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_email_chk` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_email_to`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_enable_print` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_email_chk`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_enable_sertif` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_enable_print`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_skin` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_enable_sertif`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_random` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_skin`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `published` INT(11) NOT NULL DEFAULT '0' AFTER `c_random`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_slide` TINYINT(4) NOT NULL DEFAULT '1' AFTER `published`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_language` INT(11) NOT NULL DEFAULT '0' AFTER `c_slide`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_certificate` INT(11) NOT NULL DEFAULT '0' AFTER `c_language`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_feedback` INT(11) NOT NULL DEFAULT '0' AFTER `c_certificate`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_pool` INT(11) NOT NULL DEFAULT '0' AFTER `c_feedback`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_resbycat` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `c_pool`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_feed_option` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `c_resbycat`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_show_quest_pos` TINYINT(4) NOT NULL DEFAULT '1' AFTER `c_feed_option`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_show_quest_points` TINYINT(4) NOT NULL DEFAULT '1' AFTER `c_show_quest_pos`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_show_author` TINYINT(1) NOT NULL AFTER `c_show_quest_points`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_show_timer` TINYINT(4) NOT NULL DEFAULT '1' AFTER `c_show_author`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_short_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `c_show_timer`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_once_per_day` TINYINT(4) NULL DEFAULT '0' AFTER `c_short_description`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_emails` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_once_per_day`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_timer_style` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_emails`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_statistic` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_timer_style`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_metadescr` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_statistic`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_keywords` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_metadescr`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_metatitle` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_keywords`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_ismetadescr` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_metatitle`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_iskeywords` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_ismetadescr`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_ismetatitle` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_iskeywords`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_number_times` INT(11) NOT NULL DEFAULT '1' AFTER `c_ismetatitle`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_pagination` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_number_times`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_enable_prevnext` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_pagination`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `paid_check` TINYINT(1) NULL DEFAULT '1' AFTER `c_enable_prevnext`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `paid_check_descr` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `paid_check`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_allow_continue` TINYINT(1) NOT NULL DEFAULT '1' AFTER `paid_check_descr`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_autostart` TINYINT(4) NULL DEFAULT '0' AFTER `c_allow_continue`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_redirect_after` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_autostart`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_redirect_delay` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_redirect_after`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_redirect_linktype` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_redirect_delay`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_redirect_link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_redirect_linktype`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_grading` TINYINT(1) NOT NULL DEFAULT '0' AFTER `c_redirect_link`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_ifmanual` TINYINT(1) NOT NULL DEFAULT '0' AFTER `c_grading`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_enable_skip` TINYINT(3) NOT NULL AFTER `c_ifmanual`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_show_result` TINYINT(3) NOT NULL DEFAULT '1' AFTER `c_enable_skip`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_show_qfeedback` TINYINT(3) NOT NULL AFTER `c_show_result`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_flag` TINYINT(3) NOT NULL AFTER `c_show_qfeedback`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_hide_feedback` TINYINT(3) NOT NULL AFTER `c_flag`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_share_buttons` TINYINT(3) NOT NULL AFTER `c_hide_feedback`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_auto_breaks` TINYINT(5) NOT NULL AFTER `c_share_buttons`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `asset_id` INT(18) NOT NULL AFTER `c_auto_breaks`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_quiz_access_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `asset_id`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_quiz_certificate_access_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_quiz_access_message`;

CREATE TABLE IF NOT EXISTS `#__quiz_t_text` (
`c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
PRIMARY KEY (`c_id`)
) ENGINE = InnoDB  CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_t_text` ADD COLUMN `c_blank_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_id`;
ALTER TABLE `#__quiz_t_text` ADD COLUMN `c_text` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `c_blank_id`;
ALTER TABLE `#__quiz_t_text` ADD COLUMN `ordering` INT(11) NOT NULL DEFAULT '0' AFTER `c_text`;
ALTER TABLE `#__quiz_t_text` ADD COLUMN `c_quiz_id` INT(11) NOT NULL DEFAULT '0' AFTER `ordering`;
ALTER TABLE `#__quiz_t_text` ADD COLUMN `regexp` TINYINT(4) NOT NULL DEFAULT '0' AFTER `c_quiz_id`;



