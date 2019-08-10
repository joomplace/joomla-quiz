function jq_Check_MQuestions_radio(quest_id, rad_name) {
	var return_arr = new Array;
	var inputs_arr = document.forms["quest_form"+quest_id].elements;
	for(var i=0; i<inputs_arr.length; i++) {
		if(inputs_arr[i].type.toLowerCase() == 'radio') {
			if(inputs_arr[i].name == (rad_name+'_'+inputs_arr[i].value)) {

				if(!(document.getElementById(rad_name+'_1_'+inputs_arr[i].value).checked)
					&& !(document.getElementById(rad_name+'_0_'+inputs_arr[i].value).checked))
				{
					return false;
				}
				if((inputs_arr[i].id == (rad_name+'_1_'+inputs_arr[i].value)) && inputs_arr[i].checked) {
					return_arr.push(inputs_arr[i].value);
				}
			}
		}
	}

	var return_str = return_arr.join();

	return (return_str ? return_str : '');
}

function jq_Check_MQuestions_slist(quest_id, rad_name) {
	var return_arr = new Array;
	var inputs_arr = document.forms["quest_form"+quest_id].elements;
	for(var i=0; i<inputs_arr.length; i++) {
		if(inputs_arr[i].tagName.toLowerCase() == 'select') {
			if(inputs_arr[i].name.substring(0, rad_name.length) == rad_name) {
				if (inputs_arr[i].value == '0') {
					return false;
				}
				if(inputs_arr[i].value == '2') {
					return_arr.push(inputs_arr[i].name.substring(rad_name.length+1));
				}
			}
		}
	}

	var return_str = return_arr.join();
	return (return_str ? return_str : '');
}