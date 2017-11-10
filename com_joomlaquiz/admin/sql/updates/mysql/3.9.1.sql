SELECT count(*)
INTO @exist
FROM information_schema.columns
WHERE table_schema = database()
      and COLUMN_NAME = 'c_prob_total_q'
      AND table_name = '#__quiz_t_quiz';
set @query = IF(@exist <= 0, 'alter table intent add column c_prob_total_q INT(11) NOT NULL DEFAULT 0 AFTER c_auto_breaks',
                'select \'Column Exists\' status');
prepare stmt from @query;
EXECUTE stmt;