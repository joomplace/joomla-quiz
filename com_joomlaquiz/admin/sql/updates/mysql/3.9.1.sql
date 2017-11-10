delimiter $$
create procedure foo ()
  begin
    declare continue handler for 1060 begin end;
    alter table `#__quiz_t_quiz` add column `c_prob_total_q` INT(11) NOT NULL DEFAULT '0' AFTER `c_auto_breaks`;
  end;;
call foo()$$
delimiter ;