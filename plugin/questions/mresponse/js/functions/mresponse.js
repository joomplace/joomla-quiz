function jq_Check_selectCheckbox(check_name, form_name) {
	selItem = eval('document.'+form_name+'.'+check_name);
	var rrr = '';
	if (selItem) {
		if (selItem.length) { var i;
			for (i = 0; i<selItem.length; i++) {
				if (selItem[i].checked) {
					if (selItem[i].value > 0) { rrr = rrr + selItem[i].value + ', '; }
				}}
			rrr = rrr.substring(0, rrr.length - 2);
		} else if (selItem.checked) { rrr = rrr + selItem.value; }}
	return rrr;
}