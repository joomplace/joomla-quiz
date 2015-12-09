case '7': //hotspot question
	if (circle == null) {
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id+'_hs'));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		return false;
	}
break;