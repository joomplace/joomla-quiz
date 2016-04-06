case '5':
	var item_name = 'quest_match';
	var form_name = 'quest_form'+questions[n].cur_quest_id;
	var selItem = eval('document.'+form_name+'.'+item_name);
	if (selItem) {
		if (selItem.length) { var i;
			for (i = 0; i<selItem.length; i++) {
				selItem[i].disabled = 'disabled';
			}
		} else {
			selItem.disabled = 'disabled';
		}
	}
	return;
break;