case '2': //Multi Response
	var res = jq_Check_selectCheckbox('quest_choice', 'quest_form'+questions[n].cur_quest_id);
	if (res == '') {
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		return false;
	}
break;