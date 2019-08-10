case '10':
	var return_arr = new Array;
	var inputs_arr = document.forms["quest_form"+questions[n].cur_quest_id].elements;
	for(var i=0; i<inputs_arr.length; i++) {
		inputs_arr[i].disabled = 'disabled';
	}
	return;
break;