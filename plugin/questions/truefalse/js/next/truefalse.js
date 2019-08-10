case '3': //true-false
	if (!jq_Check_selectRadio('quest_choice', 'quest_form'+questions[n].cur_quest_id)) {
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		return false;
	}
break;