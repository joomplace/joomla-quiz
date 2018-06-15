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

		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = document.getElementById(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 1 - start_index%2;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;
					Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
					if (i > 1) { 
						tbl_elem.rows[i].cells[3].innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
					} else { tbl_elem.rows[i].cells[3].innerHTML = ''; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[4].innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="Move Down"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="Move Down"></a>';;
					} else { tbl_elem.rows[i].cells[4].innerHTML = ''; }
					tbl_elem.rows[i].className = 'row'+row_k;
					count++;
					row_k = 1 - row_k;
				}
			}
		}
			
		function Redeclare_element_inputs(object) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						var inp_value = children[i].value;
						object.removeChild(object.childNodes[i]);
						var input_hidden = document.createElement("input");
						input_hidden.type = "hidden";
						input_hidden.name = inp_name;
						input_hidden.value = inp_value;
						object.appendChild(input_hidden);
					}
				}
			}
		}
		
		function Delete_tbl_row(element) {
			var del_index = element.parentNode.parentNode.sectionRowIndex;
			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
			element.parentNode.parentNode.parentNode.deleteRow(del_index);
			ReAnalize_tbl_Rows(del_index - 1, tbl_id);
		}

		function Up_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex > 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var row = table.insertRow(sec_indx - 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.align = 'left';
				cell2.innerHTML = cell2_tmp;
				cell3.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
				cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(document.createElement("td"));
				row.appendChild(document.createElement("td"));
				ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
			}
		}

		function Down_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var row = table.insertRow(sec_indx + 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.align = 'left';
				cell2.innerHTML = cell2_tmp;
				cell3.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
				cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(document.createElement("td"));
				row.appendChild(document.createElement("td"));
				ReAnalize_tbl_Rows(sec_indx, tbl_id);
			}
		}

		function jq_changeCheckbox(elem, id) {
			if(document.getElementById('jq_hid_regexp_'+id)) {
				document.getElementById('jq_hid_regexp_'+id).value = elem.checked? 1: 0;
			}
		}
		
		function jq_changeCheckbox2(elem, id) {
			if(document.getElementById('jq_hid_gtype_'+id)) {
				document.getElementById('jq_hid_gtype_'+id).value = elem.checked? 1: 0;
			}
		}

		
		var jq_num = 99999;
		function Add_new_tbl_field(elem_field, elem_field2,  tbl_id, field_name, row_tbl_cn) {
			var new_element_txt = document.getElementById(elem_field).value;
			document.getElementById(elem_field).value = '';
			jq_num++;
			if (TRIM_str(new_element_txt) == '') {
				alert("<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_ENTER_TEXT');?>");return;
			}
			
			var new_element_regexp = document.getElementById(elem_field2).checked;
			document.getElementById(elem_field).checked = false;
			
			var tbl_elem = document.getElementById(tbl_id);
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = field_name;
			input_hidden.value = new_element_txt;
			
			var input_hidden_id = document.createElement("input");
			input_hidden_id.type = "hidden";
			input_hidden_id.name = "jq_hid_fields_ids_"+row_tbl_cn+"[]";
			input_hidden_id.value = "0";
			
			var label = document.createElement("label");
			label.innerHTML = '&nbsp;Regular expression';
			label.style.display = "inline";
			
			var input_hidden_check = document.createElement("input");
			input_hidden_check.type = "hidden";
			input_hidden_check.name = "jq_hid_regexp_"+row_tbl_cn+"[]";
			input_hidden_check.id = "jq_hid_regexp_"+jq_num;
			input_hidden_check.value = new_element_regexp? '1': '0';
			
			var input_check = document.createElement("input");
			input_check.type = "checkbox";
			input_check.name = "jq_hid_regexp_chk_"+row_tbl_cn+"[]";
			input_check.value = "1";
			input_check.checked = new_element_regexp;
            input_check.onchange =  new Function('jq_changeCheckbox(this, '+jq_num+')');
			jq_num++;
			
			cell1.align = 'center';
			cell1.innerHTML = 0;
			cell2.innerHTML = new_element_txt;
			cell2.appendChild(input_hidden);
			cell2.appendChild(input_hidden_id);
			
			
			cell3.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
			cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
			cell5.innerHTML = '';
			cell6.appendChild(input_check);
			cell6.appendChild(input_hidden_check);
			cell6.appendChild(label);
			
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
		}
		
		var tbl_count = parseInt('<?php echo count($blank_data);?>');
		function new_blnk_tbl() {
			tbl_count = tbl_count + 1;
			jq_num++;
		
			var rem_tbl = document.createElement("table");
			rem_tbl.setAttribute("cellpadding", "10");
			rem_tbl.setAttribute("cellspacing", "10");
			rem_tbl.style.marginTop = "15px";
			var rem_row = rem_tbl.insertRow(0);
			var rem_th1 = document.createElement("td");
			var rem_th2 = document.createElement("td");
            rem_th1.innerHTML = '<strong>Blank <span id="blnk_num_'+(tbl_count-1)+'">' + tbl_count + '</span></strong>, code for question text: {blank'+tbl_count+'}<br />\'All answers of the block (Acceptable & ' + 'Distractors)\' - code for question text: {answers'+tbl_count+'}';
            rem_th2.innerHTML ='<'+'a href="javascript: void(0);" onclick="javascript:Delete_blnk_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png" border="0" alt="Delete"></a>';
            rem_row.appendChild(rem_th1);
			rem_row.appendChild(rem_th2);

            var points_label = document.createElement("span");
            points_label.innerHTML = '&nbsp;&nbsp;Points:&nbsp;';
            var input_hidden_p = document.createElement("input");
            input_hidden_p.type = "text";
            input_hidden_p.name = "jq_hid_points_"+(tbl_count-1);
            input_hidden_p.id = "new_points_"+(tbl_count-1);
            input_hidden_p.className = "text_area";
            input_hidden_p.size = "5";

            var css_label = document.createElement("span");
            css_label.innerHTML = '&nbsp;&nbsp;Custom CSS class:&nbsp;';
            var input_hidden_css = document.createElement("input");
            input_hidden_css.type = "text";
            input_hidden_css.name = "jq_hid_css_"+(tbl_count-1);
            input_hidden_css.id = "new_css_"+(tbl_count-1);
            input_hidden_css.className = "text_area";
            input_hidden_css.size = "20";

            var input_check2 = document.createElement("input");
            input_check2.type = "checkbox";
            input_check2.name = "jq_hid_gtype_chk_"+(tbl_count-1)+"[]";
            input_check2.value = "1";
            input_check2.checked = false;
            input_check2.onchange =  new Function('jq_changeCheckbox2(this, '+jq_num+')');;
            jq_num++;
            input_check2.style.marginLeft = "10px";

            var label2 = document.createElement("label");
            label2.style.display = "inline";
            label2.innerHTML = '&nbsp;Points for each answer';

            var input_hidden_check2 = document.createElement("input");
            input_hidden_check2.type = "hidden";
            input_hidden_check2.name = "jq_hid_gtype_"+(tbl_count-1)+"[]";
            input_hidden_check2.id = "jq_hid_gtype_"+jq_num;
            input_hidden_check2.value = "0";

            var tbl_new = document.createElement("table");
			tbl_new.className = 'adminlist';
            tbl_new.id = 'qfld_tbl_' + (tbl_count-1);
            tbl_new.className = "table table-striped";
            tbl_new.setAttribute("cellpadding", "10");
			tbl_new.setAttribute("cellspacing", "10");
			tbl_new.style.marginTop = "10px";

			var row = tbl_new.insertRow(tbl_new.rows.length);
			var th1 = document.createElement("th");
            th1.innerHTML = '#';
            th1.setAttribute("width","20px");
            th1.setAttribute("align","center");
            var th2 = document.createElement("th");
            th2.innerHTML = 'Acceptable answers';
            th2.setAttribute("width","auto");
            th2.setAttribute("align","center");
            th2.className = "title";
            var th3 = document.createElement("th");
            th3.innerHTML = 'Delete';
            th3.setAttribute("width","20px");
            th3.setAttribute("align","center");
            th3.className = "title";
            var th4 = document.createElement("th");
            th4.innerHTML = 'Move up';
            th4.setAttribute("width","20px");
            th4.setAttribute("align","center");
            th4.className = "title";
            var th5 = document.createElement("th");
            th5.innerHTML = 'Move down';
            th5.setAttribute("width","20px");
            th5.setAttribute("align","center");
            th5.className = "title";
            var th6 = document.createElement("th");
            th6.innerHTML = 'Is regular expression';
            th6.setAttribute("width","200px");
            th6.setAttribute("align","center");
            th6.className = "title";
            row.appendChild(th1);
			row.appendChild(th2);
			row.appendChild(th3);
			row.appendChild(th4);
			row.appendChild(th5);
			row.appendChild(th6);
			
			var tbl_add = document.createElement("table");
			tbl_add.className = 'adminlist';
			var row_add = tbl_add.insertRow(0);
			var td1 = document.createElement("td");
			var div_add = document.createElement("div");
		
			var input_hidden_ad = document.createElement("input");
			input_hidden_ad.type = "text";
            input_hidden_ad.name = "new_field_"+(tbl_count-1);
            input_hidden_ad.id = "new_field_"+(tbl_count-1);
            input_hidden_ad.className = "text_area";
            input_hidden_ad.style.width = "205px";
            input_hidden_ad.style.marginLeft = "50px";

			var label = document.createElement("label");
			label.style.display = "inline";
			label.innerHTML = '&nbsp;Regular expression';
			var br = document.createElement("br");
			
			var input_check = document.createElement("input");
			input_check.type = "checkbox";
            input_check.name = "new_regexp_"+(tbl_count-1);
            input_check.id = "new_regexp_"+(tbl_count-1);
            input_check.value = "1";
			input_check.checked = false;
			input_check.style.marginLeft = "10px";
			
			var input_hidden_btn = document.createElement("input");
			input_hidden_btn.type = "button";
			input_hidden_btn.name = "add_new_field[]";
			input_hidden_btn.className = "modal-button btn";
			input_hidden_btn.value = "Add";
			input_hidden_btn.style.width = "70px";
            input_hidden_btn.onclick = new Function('Add_new_tbl_field("new_field_'+(tbl_count-1)+'", "new_regexp_'+(tbl_count-1)+'", "qfld_tbl_'+(tbl_count-1)+'", "jq_hid_fields_'+(tbl_count-1)+'[]", '+(tbl_count-1)+')');

			var btn_label = document.createElement("span");
			btn_label.innerHTML = '&nbsp;&nbsp;&nbsp;&nbsp;';
			
			var input_h_count = document.createElement("input");
			input_h_count.type = "hidden";
			input_h_count.name = "blnk_arr[]";
            input_h_count.value = (tbl_count-1);
			
			var input_h_ids = document.createElement("input");
			input_h_ids.type = "hidden";
			input_h_ids.name = "blnk_arr_id[]";
			input_h_ids.value = 0;
			
			div_add.appendChild(input_h_ids);
			div_add.appendChild(input_h_count);
			div_add.appendChild(input_hidden_ad);	

			div_add.appendChild(input_check);
			div_add.appendChild(label);
			
			div_add.appendChild(btn_label);			
			div_add.appendChild(input_hidden_btn);
			
			row_add.appendChild(td1);
			td1.appendChild(div_add);

            var distractors_add = document.createElement("div");
            distractors_add.className = "adminform";
            distractors_add.style.marginTop = "30px";

            var distractors_add_div1 = document.createElement("div");
            distractors_add_div1.style.marginLeft = "10px";
            var distractors_add_div1_span = document.createElement("span");
            distractors_add_div1_span.className = "distractors-title";
            distractors_add_div1_span.style.fontWeight = "bold";
            var distractors_add_div1_span_span = document.createElement("span");
            distractors_add_div1_span_span.innerHTML = tbl_count;
            distractors_add_div1_span.innerHTML = "Distractors ";
            distractors_add_div1_span.appendChild(distractors_add_div1_span_span);
            distractors_add_div1.appendChild(distractors_add_div1_span);

            var distractors_add_table1 = document.createElement("table");
            distractors_add_table1.className = 'adminlist';
            distractors_add_table1.id = 'qfld_tbl_fake' + tbl_count;
            distractors_add_table1.setAttribute("cellpadding", "7");
            var d_row = distractors_add_table1.insertRow(distractors_add_table1.rows.length);
            var d_th1 = document.createElement("th");
            d_th1.innerHTML = '#';
            d_th1.setAttribute("width","20px");
            d_th1.setAttribute("align","center");
            var d_th2 = document.createElement("th");
            d_th2.innerHTML = 'Distractor';
            d_th2.setAttribute("width","200px");
            d_th2.setAttribute("align","center");
            d_th2.className = "title";
            var d_th3 = document.createElement("th");
            d_th3.innerHTML = '';
            d_th3.setAttribute("width","20px");
            d_th3.setAttribute("align","center");
            var d_th4 = document.createElement("th");
            d_th4.innerHTML = '';
            d_th4.setAttribute("width","20px");
            d_th4.setAttribute("align","left");
            d_row.appendChild(d_th1);
            d_row.appendChild(d_th2);
            d_row.appendChild(d_th3);
            d_row.appendChild(d_th4);

            var distractors_add_table2 = document.createElement("table");
            distractors_add_table2.className = 'adminlist';
            distractors_add_table2.style.width = "100%";
            var distractors_add_table2_row1 = distractors_add_table2.insertRow(0);
            var distractors_add_table2_row1_th = document.createElement("th");
            distractors_add_table2_row1_th.style.textAlign = "left";
            distractors_add_table2_row1_th.className = "title";
            distractors_add_table2_row1_th.innerHTML = "Add new distractor";
            distractors_add_table2_row1.appendChild(distractors_add_table2_row1_th);
            distractors_add_table2.appendChild(distractors_add_table2_row1);

            var distractors_add_table2_row2 = distractors_add_table2.insertRow(1);
            var distractors_add_table2_row2_td = document.createElement("td");
            var distractors_add_table2_row2_td_div = document.createElement("div");
            var distractors_add_table2_row2_td_div_input1 = document.createElement("input");
            distractors_add_table2_row2_td_div_input1.type = "text";
            distractors_add_table2_row2_td_div_input1.name = "new_field_fake";
            distractors_add_table2_row2_td_div_input1.id = "new_field_fake_" + (tbl_count-1);
            distractors_add_table2_row2_td_div_input1.className = "text_area";
            distractors_add_table2_row2_td_div_input1.style.width = "205px";
            var distractors_add_table2_row2_td_div_input2 = document.createElement("input");
            distractors_add_table2_row2_td_div_input2.type = "button";
            distractors_add_table2_row2_td_div_input2.name = "add_new_field";
            distractors_add_table2_row2_td_div_input2.className = "modal-button btn";
            distractors_add_table2_row2_td_div_input2.value = "Add";
            distractors_add_table2_row2_td_div_input2.onclick = new Function('Add_new_tbl_field2('+(tbl_count-1)+')');
            distractors_add_table2_row2_td_div.appendChild(distractors_add_table2_row2_td_div_input1);
            distractors_add_table2_row2_td_div.appendChild(distractors_add_table2_row2_td_div_input2);
            distractors_add_table2_row2_td.appendChild(distractors_add_table2_row2_td_div);
            distractors_add_table2_row2.appendChild(distractors_add_table2_row2_td);

            distractors_add.appendChild(distractors_add_div1);
            distractors_add.appendChild(distractors_add_table1);
            distractors_add.appendChild(distractors_add_table2);

            var hr_add = document.createElement("hr");
            var wrapper = document.createElement("div");
            wrapper.className = "option-row";

            var id_before = document.getElementById('id_before');
			var td_insert = id_before.parentNode;
            wrapper.appendChild(rem_tbl);
            wrapper.appendChild(points_label);
            wrapper.appendChild(input_hidden_p);
            wrapper.appendChild(css_label);
            wrapper.appendChild(input_hidden_css);
            wrapper.appendChild(input_check2);
            wrapper.appendChild(label2);
            wrapper.appendChild(input_hidden_check2);
            wrapper.appendChild(tbl_new);
            wrapper.appendChild(tbl_add);
            wrapper.appendChild(distractors_add);
            wrapper.appendChild(hr_add);
            td_insert.insertBefore(wrapper, id_before);
            re_number_blnk();
		}

    function Add_new_tbl_field2(n) {
        var new_element_txt = document.getElementById('new_field_fake_' + n).value;

        if (TRIM_str(new_element_txt) == '') {
				alert("<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_ENTER_TEXT');?>");
                document.getElementById('new_field_fake_' + n).focus();
				return;
			}
            document.getElementById('new_field_fake_' + n).value = '';

            var tbl_elem = document.getElementById('qfld_tbl_fake' + (n+1));
			
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");

			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
            input_hidden.name = 'jq_hid_fake_'+ n +'[]';
			input_hidden.value = new_element_txt;
						
			cell1.align = 'center';
			cell1.innerHTML = '&nbsp;';
			cell2.innerHTML = new_element_txt;
			cell2.appendChild(input_hidden);
            cell3.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row2(this); return false;" title="Delete"><img src="<?php echo JURI::root();?>administrator/components/com_joomlaquiz/assets/images/publish_x.png" border="0" alt="Delete"></a>';
    		cell4.innerHTML = '&nbsp;';
			
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);			
		}
		
		function Delete_tbl_row2(element) {
			var del_index = element.parentNode.parentNode.sectionRowIndex;
			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
			element.parentNode.parentNode.parentNode.deleteRow(del_index);
			
		}
		
		function Delete_blnk_row(element){
            jQuery(element).closest('.option-row').remove();
			re_number_blnk();
		}
		
		function re_number_blnk(){
			var blnk_arr = eval("document.adminForm['blnk_arr\[\]']");
			if(blnk_arr.length){
				for(var i=0; i<blnk_arr.length; i++){
					var blnk_num = document.getElementById("blnk_num_"+blnk_arr[i].value);
                    jQuery(blnk_num).closest('.option-row').find('.distractors-title span').html((i+1));
                    blnk_num.innerHTML = i+1;
                    var textNode = jQuery(blnk_num).closest('td').html();
                    textNode = textNode.replace(/\{blank[0-9]*\}/, '{blank' + (i+1) + '}');
                    textNode = textNode.replace(/\{answers[0-9]*\}/, '{answers' + (i+1) + '}');
                    jQuery(blnk_num).closest('td').html(textNode);
                }
			}else{
				if(blnk_arr.value){
                    var blnk_num = document.getElementById("blnk_num_"+blnk_arr.value);
                    jQuery(blnk_num).closest('.option-row').find('.distractors-title span').html('1');
                    blnk_num.innerHTML = 1;
                    var textNode = jQuery(blnk_num).closest('td').html();
                    textNode = textNode.replace(/\{blank[0-9]*\}/, '{blank1}');
                    textNode = textNode.replace(/\{answers[0-9]*\}/, '{answers1}');
                    jQuery(blnk_num).closest('td').html(textNode);
                }
			}
		}
		
		function TRIM_str(sStr) {
			sStr = sStr+"";
			return (sStr.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
		}
</script>