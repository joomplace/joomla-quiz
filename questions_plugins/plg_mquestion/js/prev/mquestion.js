case '10':
	var qform = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.c_qform.value'));

	if (qform == 1) {
		answer = jq_Check_MQuestions_slist(questions[n].cur_quest_id, 'quest_choice');
	} else {
		answer = jq_Check_MQuestions_radio(questions[n].cur_quest_id, 'quest_choice');
	}

	if (answer !== false) {
		null;
	} else {
		no_answer = true;
	}
break;