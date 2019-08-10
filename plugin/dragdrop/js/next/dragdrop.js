case '4': // Drag'AND'Drop
	var i_id;
	var i_value;
	var complete = true;
	for (i=0; i<questions[n].kol_drag_elems; i++) {
		if ( (questions[n].ids_in_cont[i] > 0) && (questions[n].ids_in_cont[i] <= questions[n].kol_drag_elems) ) {

			if (questions[n].cont_for_ids[questions[n].ids_in_cont[i] - 1] == i+1) {
				null;
			} else {
				complete = false;
			}

		} else {
			complete = false;
		}
	}
	if (!complete) {
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		return false;}
break;