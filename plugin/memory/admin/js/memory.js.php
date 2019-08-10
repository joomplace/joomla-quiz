<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

?>
<script language="javascript" type="text/javascript">

var quest_type = <?php echo $q_om_type; ?>;

function jq_uploadMemoryImage()
{
	var filename = document.getElementById('Filedata').value;
	if(filename == ''){
		alert('Select file to upload, please!');
		return false;
	}
	
	var form = document.adminForm;
	form.setAttribute('target', 'brkFrame');
	form.task.value = 'question.apply';
	form.plgtask.value = 'upload_resize_crop_img';
	form.submit();
	form.setAttribute('target', '');
	form.plgtask.value = '';
	return true;
}
		
function Delete_tbl_row(element) {
	var del_index = element.parentNode.parentNode.sectionRowIndex;
	var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
	element.parentNode.parentNode.parentNode.deleteRow(del_index);
	
}

function Add_new_tbl_field(elem_field, elem_field2, elem_field3, tbl_id, field_name, field_name2, field_name3) {
	
	var new_element_txt = document.getElementById(elem_field).options[document.getElementById(elem_field).selectedIndex].value;
	var new_points = document.getElementById(elem_field2).value;
	var new_pairs = document.getElementById(elem_field3).value;
	document.getElementById(elem_field).options[0].selected = true;
	document.getElementById('imagelib').src = '<?php echo JURI::root();?>images/joomlaquiz/images/memory/tnnophoto.jpg';
	document.getElementById(elem_field2).value = '';
	if (new_element_txt == '') {
		alert("Please select image form the left list");return;
	}
	
	var tbl_elem = document.getElementById(tbl_id);
	var row = tbl_elem.insertRow(tbl_elem.rows.length);
	var cell1 = document.createElement("td");
	var cell2 = document.createElement("td");
	var cell3 = document.createElement("td");
	var cell4 = document.createElement("td");
	var cell5 = document.createElement("td");
	
	var input_hidden = document.createElement("input");
	input_hidden.type = "hidden";
	input_hidden.name = field_name;
	input_hidden.value = new_element_txt;
	
	var input_hidden_id = document.createElement("input");
	input_hidden_id.type = "hidden";
	input_hidden_id.name = "jq_hid_fields_ids[]";
	input_hidden_id.value = "0";
	cell1.align = 'center';
	cell1.innerHTML = '';
	cell2.innerHTML = "<img src = '<?php echo JURI::root();?>images/joomlaquiz/images/memory/" + new_element_txt + "'/>";
	cell2.appendChild(input_hidden);
	cell2.appendChild(input_hidden_id);
	cell3.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
	cell4.innerHTML = '';
	var input_hidden_p = document.createElement("input");
	input_hidden_p.type = "text";
	input_hidden_p.size = "5";
	input_hidden_p.name = field_name2;
	input_hidden_p.value = new_points;
	input_hidden_p.setAttribute("value", new_points);
	cell4.appendChild(input_hidden_p);
	
	cell5.innerHTML = '';
	var input_hidden_d = document.createElement("input");
	input_hidden_d.type = "hidden";
	input_hidden_d.size = "5";
	input_hidden_d.name = field_name3;
	input_hidden_d.value = new_pairs;
	input_hidden_d.setAttribute("value", new_pairs);
	cell5.appendChild(input_hidden_d);
	
	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell3);
	row.appendChild(cell4);
	row.appendChild(cell5);
	
}

</script>