case '8': //survey question
	var answer = eval('document.quest_form'+questions[n].cur_quest_id+'.survey_box.value');
	if (TRIM_str(answer) == '') {
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		return false;
	}
break;