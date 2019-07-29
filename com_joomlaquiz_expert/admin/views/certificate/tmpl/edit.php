<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

if ($this->item->cert_file && is_file(JPATH_SITE.'/images/joomlaquiz/images/'.$this->item->cert_file))
{
	$img_cert = JURI::root().'images/joomlaquiz/images/'.$this->item->cert_file;
	$cert_size = @getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$this->item->cert_file);
	$cert_hg = $cert_size[1];
	$cert_wd = $cert_size[0];
	$accept_js_func = 1;
}
else
{
	$img_cert = JURI::root().'images/blank.png';
	$cert_hg = 0;
	$cert_wd = 0;
	$accept_js_func = 0;
}
?>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=edit&id='.(int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="certificate-form" class="form-validate">
	<div id="j-main-container" class="span10 form-horizontal">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_JOOMLAQUIZ_NEW_CERTIFICATE') : JText::sprintf('COM_JOOMLAQUIZ_EDIT_CERTIFICATE', $this->item->id); ?></legend>
			<div style="margin:15px;font-size:18px;">
				To see how to create a certificate with all the necessary data included, see our online manual <a href="http://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe/certificates.htm" target="_blank">here..</a>
			</div>
			<div class="control-group form-inline">
				<?php echo $this->form->getLabel('cert_name'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('cert_name'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
				<label class="required control-label" for="jform_cert_file" id="jform_cert_file-lbl" aria-invalid="false"><?php echo JText::_('COM_JOOMLAQUIZ_IMAGE')?><span class="star"></span></label>
				<div class="controls">
					<?php 
						$directory = 'joomlaquiz';
						$app = JFactory::getApplication();
						$cur_template = $app->getTemplate();
					?>
					<table cellpadding="0" cellspacing="0" border="0"><tr><td>
					<?php echo $this->lists['images']?></td><td style="padding-left:10px;"><a href="#" onclick="window.open('index.php?tmpl=component&option=com_joomlaquiz&amp;task=hotspot.uploadimage&amp;directory=<?php echo $directory; ?>&amp;t=<?php echo $cur_template; ?>','win1','width=450,height=200');" class="btn"><?php echo JText::_('COM_JOOMLAQUIZ_UPLOAD_NEWIMAGE')?></a>
					</td></tr></table>
				</div>
				
			</div>
			<div class="control-group form-inline">
				<table class="adminform" style="margin-top:10px; margin-bottom:10px; ">
					<tr><td>
						<small>
							<?php echo JText::_('COM_JOOMLAQUIZ_AFTER_YOU_HAVE_UPLOADED');?>
						</small><br>
						<small>
							<?php echo JText::_('COM_JOOMLAQUIZ_IF_YOU_WANT_TOUPLOAD');?>
						</small>
					</td></tr>
				</table>
			</div>
			<div class="control-group form-inline">
				<table width="100%" class="adminform">
					<tr>
						<td colspan="2">
							<img src="<?php echo $img_cert?>" alt="" name="imagelib" />
						</td>
					</tr>
				</table>
			</div>
			<div class="control-group form-inline">
				<?php echo $this->form->getLabel('crtf_text'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('crtf_text'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
				<?php echo JText::_('COM_JOOMLAQUIZ_YOU_CAN_USE_THEFOLLOWING');?>
			</div>
			<div class="control-group form-inline">
				<?php echo $this->form->getLabel('crtf_align'); ?>
				<div class="controls" style="display: inline-block; margin-left: 0px!important;">
					<?php echo $this->form->getInput('crtf_align'); ?>
					<div class="alert">
						<?php echo JText::_('COM_JOOMLAQUIZ_MAKE_SURE_TO_SET_COORDINATES');?>
					</div>
				</div>
			</div>
			<div class="control-group form-inline">
				<?php echo $this->form->getLabel('crtf_shadow'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('crtf_shadow'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
				<?php echo $this->form->getLabel('text_x'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('text_x'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
				<?php echo $this->form->getLabel('text_y'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('text_y'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
				<?php echo $this->form->getLabel('text_size'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('text_size'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
				<label class=" required control-label" for="jform_text_font" id="jform_text_font-lbl" aria-invalid="false"><?php echo JText::_('COM_JOOMLAQUIZ_FONT')?><span class="star"></span></label>
				<div class="controls">
					<?php echo JHTML::_('select.genericlist', $this->lists['fonts'], 'text_font', 'class="text_area" style="max-width: 300px;" style="max-width: 300px;" size="1" ', 'value', 'text', ((isset($this->item->text_font))?$this->item->text_font:null) );?>
				</div>
			</div>
			<div class="control-group form-inline">
				<?php echo $this->form->getLabel('cert_offset'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('cert_offset'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
				<table class="table table-striped" id="qfld_tbl">
					<tr>
						<th class="center" style="width: 250px"><?php echo JText::_('COM_JOOMLAQUIZ_TEXT_FIELD');?></th>
						<th class="center" style="width: 50px">&nbsp;&nbsp;</th>
						<th class="center" style="width: 100px"><?php echo JText::_('COM_JOOMLAQUIZ_SHADOW');?></th>
						<th class="center" style="width: 100px"><?php echo JText::_('COM_JOOMLAQUIZ_TEXT_X_ALIGN_CENTER');?></th>
						<th class="center" style="width: 50px"><?php echo JText::_('COM_JOOMLAQUIZ_TEXT_X');?></th>
						<th class="center" style="width: 50px"><?php echo JText::_('COM_JOOMLAQUIZ_TEXT_Y');?></th>
						<th class="center" style="width: 50px"><?php echo JText::_('COM_JOOMLAQUIZ_FONT_SIZE');?></th>
						<th class="center" style="width: 250px"><?php echo JText::_('COM_JOOMLAQUIZ_FONT');?></th>
						<th class="center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
					</tr>
					<?php
					$k = 0; $ii = 1;
					foreach ($this->lists['fields'] as $field) { ?>
					<tr class="<?php echo "row$k"; ?>">
						<td class="center" style="width: 250px">
							<input size="30" type="text" name="jq_hid_fields[]" value="<?php echo htmlspecialchars(stripslashes($field->f_text))?>" />
							<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $field->c_id?>" />
						</td>
						<td class="center" style="width: 50px">
							<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>
						</td>
						<td class="center" style="width: 100px">
							<input type="checkbox" name="dummy[]"  <?php echo ($field->shadow?'checked="checked"':'')?> value="1" onchange="javascript: if(this.checked){getObj('jq_fields_shadow_<?php echo $field->c_id?>').value=1} else{getObj('jq_fields_shadow_<?php echo $field->c_id?>').value=0}" />
							<input type="hidden" id="jq_fields_shadow_<?php echo $field->c_id?>" name="jq_fields_shadow[]" value="<?php echo $field->shadow?>"  />
						</td>
                        <td class="center" style="width: 100px">
                            <input type="checkbox" name="x_center[]" style="width: 50px" <?php echo ($field->text_x_center?'checked="checked"':'')?>
                                                       value="1" onchange="javascript: if(this.checked){getObj
                                    ('jq_fields_text_x_center_<?php echo $field->c_id?>').value=1} else{getObj('jq_fields_text_x_center_<?php echo $field->c_id?>').value=0}" />
                            <input type="hidden" id="jq_fields_text_x_center_<?php echo $field->c_id?>" name="jq_fields_x_center[]" value="<?php echo $field->text_x_center?>"  />
                        </td>
						<td class="center" style="width: 50px">
                            <input type="text" name="jq_hid_field_x[]" style="width: 50px" value="<?php echo
                            $field->text_x?>" size="4" />
                        </td>
						<td class="center" style="width: 50px"><input type="text" name="jq_hid_field_y[]"
                                                                      style="width: 50px"
                                                   value="<?php echo
                            $field->text_y?>" size="4" /></td>
						<td class="center" style="width: 50px"><input type="text" name="jq_hid_field_h[]"
                                                                      style="width: 50px"
                                                   value="<?php echo
                            $field->text_h?>" size="4" /></td>
						<td class="center" style="width: 250px"><?php echo JHTML::_('select.genericlist', $this->lists['fonts'], 'jq_hid_field_font[]', 'class="text_area" style="max-width: 300px;" size="1" ', 'value', 'text', $field->font );?></td>
						<td></td>
					</tr>
					<?php
					$k = 1 - $k; $ii ++;
					} ?>
				</table>
					<br />
				<table class="table table-striped">
					<tr>
						<td class="center" style="width: 250px">
							<input size="30" id="new_text" class="text_area" type="text" name="new_text" />
						</td>
                        <td class="center" style="width: 50px"></td>
						<td class="center"  style="width: 100px">
                                <input type="checkbox" name="new_shadow" id="new_shadow"   value="1" />
						</td>
                        <td class="center" style="width: 100px">
                            <input type="checkbox" name="new_x_center" id="new_x_center" style="width: 50px"
                                   value="" onClick="javascript:TextX_switch_input(this);"/>
						</td>
							<td class="center" style="width: 50px"><input style="width: 50px" type="text" name="new_x" id="new_x"
                                                       value="" size="4"
                                /></td>
							<td class="center" style="width: 50px"><input style="width: 50px" type="text" name="new_y" id="new_y"
                                                       value="" size="4"
                                /></td>
							<td class="center" style="width: 50px"><input style="width: 50px"type="text" name="new_h"
                                                                          id="new_h"
                                                       value="" size="4" /></td>
							<td class="center" style="width: 250px"><?php echo JHTML::_('select.genericlist',
                                    $this->lists['fonts'],
                                    'new_font', 'class="text_area" style="max-width: 300px;" size="1" ', 'value', 'text', null );?></td>
							<td>
							<input class="btn" type="button" name="add_new_field" style="width:70px " value="Add" onClick="javascript:Add_new_tbl_field();" />
						</td>
					</tr>
				</table>
			</div>
		</fieldset>
	    
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>	
	</div>
</form>
<script type="text/javascript">

Joomla.submitbutton = function(task)
	{
		var form = document.adminForm;
		// do field validation
		if ((task != 'certificate.cancel') && (getObj('jform_cert_name').value == "")){
			alert( "<?php echo JText::_('COM_JOOMLAQUIZ_CERTIFICATE_MUST');?>" );
		} else if ((task != 'certificate.cancel') && (getObj('jformcert_file').value == "")){
			alert( "<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_A_FILE_DOT');?>" );
		} else {
			Joomla.submitform(task, document.getElementById('certificate-form'));
		}
	}
	
	function getObj(name) {
		if (document.getElementById)  {  return document.getElementById(name);  }
		else if (document.all)  {  return document.all[name];  }
		else if (document.layers)  {  return document.layers[name];  }
	} 
		
	function Delete_tbl_row(element) {
		var del_index = element.parentNode.parentNode.sectionRowIndex;
		var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
		element.parentNode.parentNode.parentNode.deleteRow(del_index);
	}
		
	function copyOptions(from,to){
		var options = new Object();			
		for(var i=0;i<from.options.length;i++){
			var o = from.options[i];			
			var index=to.options.length;
			to.options[index] = new Option( o.text, o.value, false, false);
		}
		to.selectedIndex = from.selectedIndex;
	}


	var number = 9999;
	function Add_new_tbl_field() {
		var new_text = getObj('new_text').value;
		var new_shadow = (getObj('new_shadow').checked? 1: 0);
		var new_x_center = (getObj('new_x_center').checked? 1: 0);
		var new_x = getObj('new_x').value;
		var new_y = getObj('new_y').value;
		var new_h = getObj('new_h').value;
		var new_font = getObj('new_font').value;

		if (new_text == '') {
			alert("<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_ENTER_TEXT');?>");
			return;
		}
			
		getObj('new_text').value = '';
		getObj('new_shadow').checked = false;
		getObj('new_x_center').checked = false;
		getObj('new_x').value = '';
		getObj('new_y').value = '';
		getObj('new_h').value = '';
			
		var tbl_elem = getObj('qfld_tbl');
		var row = tbl_elem.insertRow(tbl_elem.rows.length);
		var cell1 = document.createElement("td");
		var cell2 = document.createElement("td");
		var cell3 = document.createElement("td");
		var cell4 = document.createElement("td");
		var cell5 = document.createElement("td");
		var cell6 = document.createElement("td");
		var cell7 = document.createElement("td");
		var cell8 = document.createElement("td");
			
		cell1.setAttribute('class', 'center');
		cell1.setAttribute('style', 'width:250px');

		cell2.setAttribute('class', 'center');
		cell2.setAttribute('style', 'width:50px');

		cell3.setAttribute('class', 'center');
		cell3.setAttribute('style', 'width:100px');

		cell4.setAttribute('class', 'center');
		cell4.setAttribute('style', 'width:100px');

		cell5.setAttribute('class', 'center');
		cell5.setAttribute('style', 'width:50px');

        cell6.setAttribute('class', 'center');
        cell6.setAttribute('style', 'width:50px');

        cell7.setAttribute('class', 'center');
        cell7.setAttribute('style', 'width:50px');

        cell8.setAttribute('class', 'center');
        cell8.setAttribute('style', 'width:250px');

		var input_text = document.createElement("input");
		input_text.type = "text";
		input_text.name = 'jq_hid_fields[]';
		input_text.value = new_text;
		input_text.setAttribute('size', 30);
						
		var input_hidden_id = document.createElement("input");
		input_hidden_id.type = "hidden";
		input_hidden_id.setAttribute('name','jq_hid_fields_ids[]');
			
		input_hidden_id.value = "0";
			
		cell1.appendChild(input_text);
		cell1.appendChild(input_hidden_id);
			
		cell2.innerHTML = '<'+'a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
			
		var input_check = document.createElement("input");
			
		input_check.type = "checkbox";
		input_check.setAttribute('name','dummy[]');
			
		input_check.checked = (new_shadow==1);
		input_check.id = 'jq_fields_shadow_' + number;
		input_check.setAttribute('onchange','javascript: if(this.checked){getObj("jq_fields_shadow_'+number+'").value=1} else{getObj("jq_fields_shadow_'+number+'").value=0}');
			
		var input_hidden_check = document.createElement("input");
		input_hidden_check.type = "hidden";
		input_hidden_check.setAttribute('name','jq_fields_shadow[]');
		input_hidden_check.value = new_shadow;
			
		cell3.appendChild(input_check);
		cell3.appendChild(input_hidden_check);
			
        var input_check_center = document.createElement("input");

        input_check_center.type = "checkbox";
        input_check_center.setAttribute('name','x_center[]');

        input_check_center.checked = (new_x_center==1);
        input_check_center.id = 'jq_fields_x_center_' + number;
        input_check_center.setAttribute('onchange','javascript: if(this.checked){getObj("jq_fields_x_center_'+number+'").value=1} else{getObj("jq_fields_x_center_'+number+'").value=0}');

        var input_hidden_check_center = document.createElement("input");
        input_hidden_check_center.type = "hidden";
        input_hidden_check_center.setAttribute('name','jq_fields_x_center[]');
        input_hidden_check_center.value = new_x_center;

        cell4.appendChild(input_check_center);
        cell4.appendChild(input_hidden_check_center);

		var inp_text_x = document.createElement("input");
		inp_text_x.type = "text";
		inp_text_x.name = "jq_hid_field_x[]";
		inp_text_x.value = new_x;
		inp_text_x.setAttribute('size', 4);
		inp_text_x.setAttribute('style', 'width:50px');
		if(input_check_center.checked){
            inp_text_x.setAttribute('disabled', 'disabled');
        }
		cell5.appendChild(inp_text_x);
			
		var inp_text_y = document.createElement("input");
		inp_text_y.type = "text";
		inp_text_y.name = "jq_hid_field_y[]";
		inp_text_y.value = new_y;
		inp_text_y.setAttribute('size', 4);
		inp_text_y.setAttribute('style', 'width:50px');
		cell6.appendChild(inp_text_y);
			
		var inp_text_h = document.createElement("input");
		inp_text_h.type = "text";
		inp_text_h.name = "jq_hid_field_h[]";
		inp_text_h.value = new_h;
		inp_text_h.setAttribute('size', 4);
		inp_text_h.setAttribute('style', 'width:50px');
		cell7.appendChild(inp_text_h);
			
		var input_select = document.createElement("select");
		input_select.name = 'jq_hid_field_font[]';
		input_select.className = 'inputbox';
		copyOptions(getObj('new_font'), input_select);
		
		cell8.appendChild(input_select);
			
		row.appendChild(cell1);
		row.appendChild(cell2);
		row.appendChild(cell3);
		row.appendChild(cell4);
		row.appendChild(cell5);
		row.appendChild(cell6);
		row.appendChild(cell7);
		row.appendChild(cell8);

        var new_x_obj = getObj('new_x');
        new_x_obj.removeAttribute('disabled');
	}

	function TextX_switch_input(obj) {
        var new_x = getObj('new_x');

        if(obj.checked){
            new_x.setAttribute('disabled','disabled');
        }
        else{
            new_x.removeAttribute('disabled');
        }
        // console.log(new_x);

    }

jQuery(function ($) {

    $('#qfld_tbl').on('change', '[name^=x_center]', function(){
        var next_input = $(this).parent().next().children();
        if ($(this).is(':checked')) {
            next_input.attr('disabled','disabled');
	}
        else{
            next_input.removeAttr('disabled');
        }
    });


    var list_x_center = $('[name^=x_center]');

    list_x_center.each(function () {
        if(this.checked){
            var next_input = $(this).parent().next().children();
            next_input.attr('disabled','disabled');
            console.log(this);
        }
    });

    // Для передачи данных задисэйбленных элементов
    $('form').submit(function(e) {
        $(':disabled').each(function(e) {
            $(this).removeAttr('disabled');
        })
    });
});
</script>
