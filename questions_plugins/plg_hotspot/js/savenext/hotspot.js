case '7':
	var hs_x = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.hotspot_x.value'));
	var hs_y = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.hotspot_y.value'));
	if ((hs_x != 0) && (hs_y != 0)) {
		var answer = hs_x + ',' + hs_y;
		null;
	} else {
		
	}
break;