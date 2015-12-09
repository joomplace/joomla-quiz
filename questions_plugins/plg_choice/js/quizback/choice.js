case '1':
	var rad_name = 'quest_choice';
	var form_name = 'quest_form'+questions[n].cur_quest_id;
	var tItem = eval('document.'+form_name);
	if (tItem) {
		var selItem = eval('document.'+form_name+'.'+rad_name);
		if (selItem) {
			if (selItem.length) {
				var i;
				for (i = 0; i<selItem.length; i++) {
					selItem[i].disabled = '';
				}
			} else {
				selItem.disabled = '';
			}
		}
	}
break;