ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_set_default`	tinyint(1) DEFAULT NULL AFTER `params`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_default_points`	float DEFAULT NULL AFTER `c_set_default`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_default_attempts`	int(11) DEFAULT NULL AFTER `c_default_points`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_default_enable_questions_feedback`	tinyint(1) DEFAULT NULL AFTER `c_default_attempts`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_default_f_right_message`	text DEFAULT NULL AFTER `c_default_enable_questions_feedback`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_default_f_wrong_message`	text DEFAULT NULL AFTER `c_default_f_right_message`;
ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_default_f_detailed_wrong_message`	text DEFAULT NULL AFTER `c_default_f_wrong_message`;