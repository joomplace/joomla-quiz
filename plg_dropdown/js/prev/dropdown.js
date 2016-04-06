case '5':
	answer = jq_Check_valueItem('quest_match', 'quest_form'+questions[n].cur_quest_id);
	answer = URLencode(answer);
	if (answer != '') {
		null;
	} else {
		no_answer = true;
	}
break;