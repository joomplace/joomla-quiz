<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$app = JFactory::getApplication();
$input = $app->input;

?>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=edit&id='.(int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="lpath-form" class="form-validate"><div id="j-main-container" class="span10 form-horizontal">
        <ul class="nav nav-tabs" id="quizTabs">
            <li class="active"><a href="#basic" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_LP_BASIC_SETTINGS');?></a></li>
            <li><a href="#permissions" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_LP_PERMISSIONS_SETTINGS');?></a></li>
        </ul>
<div class="tab-content">
    <div class="tab-pane active" id="basic">
            <fieldset class="adminform">
                <legend><?php echo JText::_('COM_JOOMLAQUIZ_LP_DETAILS')?></legend>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('title'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('title'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('paid_check'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('paid_check'); ?>
                    </div>
                </div>
                <div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('category'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('category'); ?>
					</div>
				</div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('published'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('published'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('short_descr'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('short_descr'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('descr'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('descr'); ?>
                    </div>
                </div>
            </fieldset>
            <fieldset class="adminform">
                <legend><?php echo JText::_('COM_JOOMLAQUIZ_LP_QUIZZES_AND_ARTICLES')?></legend>
                <table class="adminlist" id="qfld_tbl" cellpadding="10" cellspacing="10">
                    <tr>
                        <th width="20px" align="center"><?php echo JText::_('COM_JOOMLAQUIZ_SHARP');?></th>
                        <th class="title" width="400px"><?php echo JText::_('COM_JOOMLAQUIZ_QUIZ2');?></th>
                        <th width="50px" align="center"><?php echo JText::_('COM_JOOMLAQUIZ_TYPE');?></th>
                        <th width="20px" align="center" class="title"></th>
                        <th width="20px" align="center" class="title"></th>
                        <th width="20px" align="center" class="title"></th>
                        <th width="auto"></th>
                    </tr>
                    <?php
                    $k = 0; $ii = 1; $ind_last = count($this->lpaths_data);
                    foreach ($this->lpaths_data as $frow) { ?>
                        <tr class="<?php echo "row$k"; ?>">
                            <td align="center"><?php echo $ii?></td>
                            <td align="left">
                                <?php echo htmlspecialchars(stripslashes($frow->title))?>
                                <input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->id; ?>" />
                                <input type="hidden" name="jq_hid_fields_types[]" value="<?php echo $frow->type; ?>" />
                            </td>
                            <td align="center">
                                <?php echo ($frow->type == 'q' ? 'Quiz' : 'Article'); ?>
                            </td>
                            <td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a></td>
                            <td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a><?php } ?></td>
                            <td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="Move Down"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="Move Down"></a><?php } ?></td>
                            <td></td>
                        </tr>
                    <?php
                    $k = 1 - $k; $ii ++;
                    } ?>
                </table>
                <br />
                <table class="adminlist">
                <tr>
                    <td>
                        <div style="text-align:left; padding-left:30px ">
                            <?php echo $this->quizzes_data['quizzes_list']; ?>
                            <input class="modal-button btn" type="button" name="add_new_field" style="width:100px " value="<?php echo JText::_('COM_JOOMLAQUIZ_ADD_QUIZ');?>" onclick="javascript:Add_new_tbl_field('quiz_id', 'qfld_tbl', 'jq_hid_fields_ids[]', 'jq_hid_fields_types[]');" />
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php echo $this->quizzes_data['articles_list']; ?>
                            <input class="modal-button btn" type="button" name="add_new_field" style="width:100px " value="<?php echo JText::_('COM_JOOMLAQUIZ_ADD_ARTICLE');?>" onclick="javascript:Add_new_tbl_field('article_id', 'qfld_tbl', 'jq_hid_fields_ids[]', 'jq_hid_fields_types[]');" />
                        </div>
                    </td>
                </tr>
                </table>
            </fieldset>
    </div>

    <div class="tab-pane" id="permissions">

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('rules'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('rules'); ?>
                </div>
            </div>

        <div class="control-group">
            <div class="control-label">
                <?php echo $this->form->getLabel('lp_access_message'); ?>
            </div>
            <div class="controls">
                <?php echo $this->form->getInput('lp_access_message'); ?>
            </div>
        </div>

    </div>


</div>


<input type="hidden" name="task" value="" />
<input type="hidden" name="jform[id]" value="<?php echo $this->item->id;?>" />
<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
<?php echo JHtml::_('form.token'); ?>

</form>
<script type="text/javascript">
	<!--
		var quizzes_all = new Array();
		<?php foreach($this->quizzes_data['all_quizzes'] as $q) { ?>
		quizzes_all[<?php echo $q->value; ?>] = '<?php echo str_replace("'", "\\'", $q->text); ?>';
		<?php } ?>

		var articles_all = new Array();
		<?php foreach($this->articles_data['articles'] as $a) { ?>
		articles_all[<?php echo $a->value; ?>] = '<?php echo str_replace("'", "\\'", $a->text); ?>';
		<?php } ?>

		var elem_type_names = new Array;
		elem_type_names['q'] = 'Quiz';
		elem_type_names['a'] = 'Article';
		
		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = document.getElementById(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 1 - start_index%2;//0;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;
					Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
					if (i > 1) { 
						tbl_elem.rows[i].cells[4].innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
					} else { tbl_elem.rows[i].cells[4].innerHTML = ''; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[5].innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="Move Down"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="Move Down"></a>';;
					} else { tbl_elem.rows[i].cells[5].innerHTML = ''; }
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
						//input_hidden_id.id = "hid";
						input_hidden.type = "hidden";
						input_hidden.name = inp_name;
						input_hidden.value = inp_value;
						object.appendChild(input_hidden);
					}
				}
			}
		}
		
		function addOption (oListbox, text, value, isDefaultSelected, isSelected)
		{
			var oOption = document.createElement("option");
			oOption.appendChild(document.createTextNode(text));
			oOption.setAttribute("value", value);

			if (isDefaultSelected) oOption.defaultSelected = true;
			else if (isSelected) oOption.selected = true;

			oListbox.appendChild(oOption);
		}
		
		function Delete_tbl_row(element) {
			var del_index = element.parentNode.parentNode.sectionRowIndex;
			
			var ids = document.getElementsByName('jq_hid_fields_ids[]');
			var types = document.getElementsByName('jq_hid_fields_types[]');
			if (ids.length > new Number(del_index-1)) {
				var type = new String(types[del_index-1].value);
				var id = new Number(ids[del_index-1].value);
				var optn = document.createElement("OPTION");
				if(type == 'q') {
					var oListbox = document.getElementById('quiz_id');
					optn.text = quizzes_all[id];
					optn.value = id;
					addOption(oListbox, optn.text, optn.value, false, true);
				} else {
					var oListbox = document.getElementById('article_id');
					optn.text = articles_all[id];
					optn.value = id;
					addOption(oListbox, optn.text, optn.value, false, true);
				}
			}
			
			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
			element.parentNode.parentNode.parentNode.deleteRow(del_index);
			ReAnalize_tbl_Rows(del_index - 1, tbl_id);

            jQuery('#quiz_id').trigger('liszt:updated');
            jQuery('#article_id').trigger('liszt:updated');
		}

		function Up_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex > 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				var row = table.insertRow(sec_indx - 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				var cell5 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.innerHTML = element.parentNode.parentNode.cells[1].innerHTML;
				cell3.align = 'center';
				cell3.innerHTML = element.parentNode.parentNode.cells[2].innerHTML;
				cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
				cell5.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(cell5);
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
				var inner_cell2 =  element.parentNode.parentNode.cells[1].innerHTML;
				var inner_cell3 = element.parentNode.parentNode.cells[2].innerHTML;
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var row = table.insertRow(sec_indx + 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				var cell5 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.innerHTML = inner_cell2;
				cell3.align = 'center';
				cell3.innerHTML = inner_cell3;
				cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
				cell5.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(cell5);
				row.appendChild(document.createElement("td"));
				row.appendChild(document.createElement("td"));
				ReAnalize_tbl_Rows(sec_indx, tbl_id);
			}
		}

		function Add_new_tbl_field(elem_field, tbl_id, field_name, field_type) {			
			var select = jQuery('#'+elem_field);
			var option = jQuery('option:selected', select);
			var new_element_txt = option.html();
			var new_element_id = option.val();
			
			if(new_element_id == -1) {
				return;
			}
			
			option.remove();
			select.trigger('liszt:updated');
						
			var elem_type = (elem_field == 'quiz_id' ? 'q' : 'a');
			
			if (TRIM_str(new_element_id) < 1) {
				alert("<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_A');?> " + elem_type_names[elem_type] + ".");return;
			}
			

			var tbl_elem = document.getElementById(tbl_id);
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var cell7 = document.createElement("td");

			var input_hidden_id = document.createElement("input");
			//input_hidden_id.id = "hid";
			input_hidden_id.type= "hidden";
			input_hidden_id.name = field_name;
			input_hidden_id.value = new_element_id;

			var input_hidden_type = document.createElement("input");
			input_hidden_type.type = "hidden";
			input_hidden_type.name = field_type;
			input_hidden_type.value = elem_type;
			
			cell1.align = 'center';
			cell1.innerHTML = 0;
			cell2.innerHTML = new_element_txt;
			cell2.appendChild(input_hidden_id);
			cell2.appendChild(input_hidden_type);
			cell3.align = 'center';
			cell3.innerHTML = elem_type_names[elem_type];
			cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
			cell5.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
			cell6.innerHTML = '';
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);
			row.appendChild(cell7);
			

			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
		}
		
		function TRIM_str(sStr) {
			sStr = sStr+"";
			return (sStr.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
		}
		
		Joomla.submitbutton = function(task)
		{
			var ids = document.getElementsByName('jq_hid_fields_ids[]');
			if (task == 'lpath.cancel') {
				<?php echo $this->form->getField('short_descr')->save(); ?>
				<?php echo $this->form->getField('descr')->save(); ?>
				Joomla.submitform(task, document.getElementById('lpath-form'));
				return;
			}
            if ((task == 'lpath.apply' || task == 'lpath.save' || task == 'lpath.save2new' || task == 'lpath.save2copy')
                 && document.formvalidator.isValid(document.id('lpath-form')))
            {
                if(ids.length==0){
                    alert('<?php echo JText::_('COM_JOOMLAQUIZ_LPATH_NOQUIZSELECTED',true);?>');
                    return false;}
                Joomla.submitform(task, document.getElementById('lpath-form'));
            }
            else {
				alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED',true);?>');
			}
		}
	//-->
	
	/* dumb code here as we`ve not renamed our columns still */
	var initialgetUrlParam = getUrlParam;
	getUrlParam = function(){
		if(arguments[0] == 'view'){
			return 'lp';
		}else{
			return initialgetUrlParam.apply(document, arguments);
		}
	}

    jQuery(function($) {
        $('#quiz_id_chzn, #article_id_chzn').on('click', function () {
            window.scrollTo(0,document.body.scrollHeight);
        });
    });

</script>
