ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `time_left` INT(11) NOT NULL DEFAULT '0' AFTER `params`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `past_time` INT(11) NOT NULL DEFAULT '0' AFTER `time_left`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `respond_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `past_time`;

