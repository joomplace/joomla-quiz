function jq_Check_selectRadio(rad_name, form_name) {
	var tItem = eval('document.'+form_name);
	if (tItem) {
		var selItem = eval('document.'+form_name+'.'+rad_name);
		if (selItem) {
			if (selItem.length) { var i;
				for (i = 0; i<selItem.length; i++) {
					if (selItem[i].checked) {
						if (selItem[i].value > 0) { return selItem[i].value; } } }
			} else if (selItem.checked) { return selItem.value; } }
		return false; }
	return false;
}