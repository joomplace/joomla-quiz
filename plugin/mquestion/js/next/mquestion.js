case '10': //Multi Questions
	var qform = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.c_qform.value'));

	if (qform == 1) {
		var res = jq_Check_MQuestions_slist(questions[n].cur_quest_id, 'quest_choice');
	} else {
		var res = jq_Check_MQuestions_radio(questions[n].cur_quest_id, 'quest_choice');
	}
	if (res === false) {
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		return false;
	}
break;