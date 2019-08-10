case '2':
case '3':
	var check_name = 'quest_choice';
	var form_name =  'quest_form'+questions[n].cur_quest_id;
	var selItem = eval('document.'+form_name+'.'+check_name);
	if (selItem) {
		if (selItem.length) { var i;
			for (i = 0; i<selItem.length; i++) {
				selItem[i].disabled = '';
			}
		} else {
			selItem.disabled = '';
		}
	}
break;