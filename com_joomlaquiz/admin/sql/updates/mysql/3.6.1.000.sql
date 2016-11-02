ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `time_left` INT(11) NOT NULL DEFAULT '0' AFTER `params`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `time_past` INT(11) NOT NULL DEFAULT '0' AFTER `params`;
ALTER TABLE `#__quiz_r_student_quiz` ADD COLUMN `respond_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `params`;