case '1': //Multi choice
	var qform = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.c_qform.value'));
	if (qform == 1) {
		if (!parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.quest_choice.value'))) {
			try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
			ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
			return false;
		}
	} else {
		if (!jq_Check_selectRadio('quest_choice', 'quest_form'+questions[n].cur_quest_id)) {
			try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
			ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
			return false;
		}
	}
break;