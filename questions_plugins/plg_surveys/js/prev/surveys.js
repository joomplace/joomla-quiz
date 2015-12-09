case '8':
	var answer = URLencode(TRIM_str(eval('document.quest_form'+questions[n].cur_quest_id+'.survey_box.value')));
	if (answer != '') {
		null;
	} else {
		no_answer = true;
	}
break;