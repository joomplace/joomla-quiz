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
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		setTimeout("jq_releaseBlock()", 1000);
		return false;
	}
break;