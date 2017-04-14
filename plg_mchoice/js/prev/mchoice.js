case '15':
	var qform = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.c_qform.value'));

	if (qform == 1) {
		answer = jq_Check_MChoices_slist(questions[n].cur_quest_id, 'quest_choice');
	} else {
		answer = jq_Check_MChoices_radio(questions[n].cur_quest_id, 'quest_choice');
	}

	if (answer !== false) {
		null;
	} else {
		no_answer = true;
	}
break;