CREATE TABLE IF NOT EXISTS `#__quiz_r_student_dalliclick` (
      `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `c_sq_id` int(10) NOT NULL,
      `c_choice_id` int(10) NOT NULL,
      `c_elapsed_time` int(10) NOT NULL,
      PRIMARY KEY (`c_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_puzzle` (
      `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `c_sq_id` int(11) unsigned NOT NULL,
      `c_piece` int(10) NOT NULL,
      `c_elapsed_time` int(10) NOT NULL,
      PRIMARY KEY (`c_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__quiz_r_student_memory` (
      `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `c_sq_id` int(11) NOT NULL,
      `c_mid` int(11) NOT NULL,
      `c_elapsed_time` int(11) NOT NULL,
      PRIMARY KEY (`c_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__quiz_certificates` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_cert_fields` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_constants` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_dashboard_items` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_export` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_feed_option` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_languages` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_lpath` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_lpath_quiz` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_lpath_stage` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_payments` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_pool` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_products` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_products_stat` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_product_info` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_q_cat` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_q_chain` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_blank` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_choice` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_dalliclick` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_hotspot` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_matching` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_memory` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_puzzle` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_question` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_quiz` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_share` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_r_student_survey` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_setup` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_templates` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_blank` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_category` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_choice` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_dalliclick` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_ext_hotspot` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_faketext` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_hotspot` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_matching` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_memory` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_pbreaks` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_puzzle` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_qtypes` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_question` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_quiz` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `#__quiz_t_text` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;