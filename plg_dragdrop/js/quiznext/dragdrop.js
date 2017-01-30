case '4':
	var i_id;
	var i_value;
	answer = '';
	var complete = true;
	var mas_ans = new Array(questions[n].kol_drag_elems);
	for (i=0; i<questions[n].kol_drag_elems; i++) {
		mas_ans[i] = 0;
		if ( (questions[n].ids_in_cont[i] > 0) && (questions[n].ids_in_cont[i] <= questions[n].kol_drag_elems) ) {
			if (questions[n].cont_for_ids[questions[n].ids_in_cont[i] - 1] == i+1) {
				mas_ans[i] = questions[n].ids_in_cont[i];
				answer = answer + questions[n].answ_ids[questions[n].ids_in_cont[i]] + '```';
			} else { complete = false; }
		} else { complete = false; }
	}
	if (!complete) {
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		setTimeout("jq_releaseBlock()", 1000);
		return false;
	} else {
		answer = answer.substring(0, answer.length - 3);
		null;
	}
break;