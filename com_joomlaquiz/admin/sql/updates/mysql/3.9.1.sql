DELIMITER $$
DROP PROCEDURE IF EXISTS upgrade_database_390_to_391 $$
CREATE PROCEDURE upgrade_database_390_to_391()
  BEGIN
    IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
                                                                   AND COLUMN_NAME='c_prob_total_q' AND TABLE_NAME='#__quiz_t_quiz') ) THEN
      ALTER TABLE `#__quiz_t_quiz` ADD COLUMN `c_prob_total_q` INT(11) NOT NULL DEFAULT '0' AFTER `c_auto_breaks`;
    END IF;
  END $$
CALL upgrade_database_390_to_391() $$
DELIMITER ;