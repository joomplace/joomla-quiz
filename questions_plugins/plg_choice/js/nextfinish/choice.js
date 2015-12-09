case '1':
case '3':
	var qform = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.c_qform.value'));
	if (qform == 1) {
		answer = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.quest_choice.value'));

	} else {
		answer = jq_Check_selectRadio('quest_choice', 'quest_form'+questions[n].cur_quest_id);
		if (answer) {
			null;
		}
	}
break;