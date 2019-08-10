DELETE FROM `#__quiz_t_qtypes` WHERE c_id = '9';
INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (9, 'Boilerplate', 'boilerplate');

UPDATE `#__extensions` SET `enabled` = 1 WHERE `element` = 'boilerplate';