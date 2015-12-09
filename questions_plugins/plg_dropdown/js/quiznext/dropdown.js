case '5':
	answer = jq_Check_valueItem('quest_match', 'quest_form'+questions[n].cur_quest_id);
	answer = URLencode(answer);
	if (answer != '') {
		null;
	} else {
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		setTimeout("jq_releaseBlock()", 1000);
		return false;
	}
break;