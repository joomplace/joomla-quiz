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
JHTML::_('behavior.calendar');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

?>
<script type="text/javascript">
function sel_all_fields() {
	sel = document.forms.adminForm.available_fields;
	for (x=0; x<sel.length; x++) {
		sel.options[x].selected = true;
	}
}
		
function select_field() {
	qw = 0;
	available_fields = document.forms.adminForm.available_fields;
	selected_field = document.forms.adminForm.selected_field;
	for (x=0; x<available_fields.length; x++) {
		if (available_fields.options[x].selected) qw=1;
	}
	if (!qw){
		alert ('<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_FIELDS');?>');	
		return;
	}
			
	for (x=0, m=available_fields.length; x<m; x++) {
		if (available_fields.options[x].selected){

			var new_o = document.createElement('option');
			new_o.text = available_fields.options[x].text;
			new_o.value = available_fields.options[x].value;
			try {
				selected_field.add(new_o,null);
			} catch(e) {
				selected_field.add(new_o);
			}
			available_fields.remove(x);		
			x--;
			m--;
		}
	}	
}
		
function unselect_field() {
	qw = 0;
	selected_field = document.forms.adminForm.selected_field;
	available_fields = document.forms.adminForm.available_fields;
	for (x=0; x<selected_field.length; x++) {
		if (selected_field.options[x].selected) qw=1;
	}
	if (!qw){
		alert ('<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_FIELDS');?>');	
		return;
	}
			
	for (x=0, m=selected_field.length; x<m; x++) {
		if (selected_field.options[x].selected){

			var new_o = document.createElement('option');
			new_o.text = selected_field.options[x].text;
			new_o.value = selected_field.options[x].value;
			try {
				available_fields.add(new_o,null);
			} catch(e) {
				available_fields.add(new_o);
			}
					
			selected_field.remove(x);
			x--;
			m--;					
		}
	}	
}
		
function copy_all() {
	selected_field = document.forms.adminForm.selected_field;
	available_fields = document.forms.adminForm.available_fields;
	for (x=0, m=available_fields.length; x<m; x++) {
		var new_o = document.createElement('option');
		new_o.text = available_fields.options[x].text;
		new_o.value = available_fields.options[x].value;
		try {
			selected_field.add(new_o,null);
		} catch(e) {
			selected_field.add(new_o);
		}
				
		available_fields.remove(x);	
		x--;
		m--;
	}
}
		
Joomla.submitbutton = function(task)
	{
		sel = document.forms.adminForm.selected_field;
		for (x=0; x<sel.length; x++) {
			sel.options[x].selected = true;
		}
		Joomla.submitform(task, document.getElementById('dynamic-form'));
		
	}
</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=dynamic'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="dynamic-form" class="form-validate">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div style="margin-top: 10px;" id="j-main-container" class="span10">
	<?php else : ?>
	<div style="margin-top: 10px;" id="j-main-container">
	<?php endif;?>
	<table>
		<tr>
			<td valign="top" colspan="3">	
				<strong><?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT');?></strong>
				<?php echo $this->lists['quizzes'];?>
			</td>
		</tr>
		<tr>
			<td valign="top">							
				<strong><?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_THE_FIELDS');?></strong>
						
				<table border="0" align="center" valign="center">
					<tr><td><?php echo JText::_('COM_JOOMLAQUIZ_AVAILABE_FIELDS');?></td><td></td><td><?php echo JText::_('COM_JOOMLAQUIZ_SELECTED_TO_INCLUDE_IN');?></td></tr>
					<tr>
						<td valign="top">
							<?php
								echo JHTML::_('select.genericlist', $this->lists['fields'], 'available_fields[]', 'class="text_area" size="10" multiple="multiple" style="width:250px" ', 'value', 'text', null);
							?>
							<br /><br />						
							<input type="button" class="btn" value="<?php echo JText::_('COM_JOOMLAQUIZ_SELECT_ALL');?>" onclick="javascript: sel_all_fields();" />
						</td>
						<td align="center" valign="middle">
							<input type="button" class="btn" value=">>" onclick="javascript: select_field();"/><br />
							<input type="button" class="btn" value="<<" onclick="javascript: unselect_field();"/><br />
							<br />
							<input type="button" class="btn" value="<?php echo JText::_('COM_JOOMLAQUIZ_COPY_ALL');?>" onclick="javascript: copy_all();"/>
						</td>
						<td valign="top">
							<select name="selected_field[]" id="selected_field" multiple="multiple" size="10" style="width:250px">
							</select>	
						</td>
					</tr>
					</table>
			</td>
			<td width="50">&nbsp;</td>
			<td valign="top">	
				<strong><?php echo JText::_('COM_JOOMLAQUIZ_SELECT_DATE_RANGE');?></strong> <br />
				<?php echo JText::_('COM_JOOMLAQUIZ_START_DATE');?> 
						
				<?php echo JHTML::_('calendar', '', 'startdate', 'startdate', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19'));?>
				<?php echo JText::_('COM_JOOMLAQUIZ_END_DATE');?> 
				<?php echo JHTML::_('calendar', '', 'enddate', 'enddate', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19'));?>
				<br />
				<small><?php echo JText::_('COM_JOOMLAQUIZ_TO_GET_ALL');?> </small>	
			</td>
		</tr>
	</table>		
	<br />
	<strong><small><?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_THE');?></small></strong><br />
	<br />
	<?php echo JText::_('COM_JOOMLAQUIZ_CONDITION_1');?><br />
	<?php
		array_unshift($this->lists['fields'], JHTML::_('select.option','0', JText::_('COM_JOOMLAQUIZ_SELECT_THE_FIELD')));
		echo JHTML::_('select.genericlist', $this->lists['fields'], 'value11', 'class="text_area" size="1" style="max-width:200px;" ', 'value', 'text', 0);
	?>
	&nbsp;&nbsp;	
	<select name="condition1">
		<option value="0"><?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_ONE');?></option>
		<option value="1"><?php echo JText::_('COM_JOOMLAQUIZ_IS_LESS_THEN');?></option>
		<option value="2"><?php echo JText::_('COM_JOOMLAQUIZ_IS_GREATER_TO');?></option>
		<option value="3"><?php echo JText::_('COM_JOOMLAQUIZ_IS_GREATER_THAN');?></option>
		<option value="4"><?php echo JText::_('COM_JOOMLAQUIZ_IS_NOT_EQUAL_TO');?></option>
	</select>
	&nbsp;&nbsp;
	<input name="value12" size="40" class="input-xlarge" value="" type="text"/>
	<br />
	<br />
					
	<?php echo JText::_('COM_JOOMLAQUIZ_OPERATOR');?>
	<select name="operation">
		<option value="0"><?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_ONE');?></option>
		<option value="AND"><?php echo JText::_('COM_JOOMLAQUIZ_AND');?></option>
		<option value="OR"><?php echo JText::_('COM_JOOMLAQUIZ_OR');?></option>
	</select>
	<br />
	<br />
					
	<?php echo JText::_('COM_JOOMLAQUIZ_CONDITION_2');?><br />
	<?php
		echo JHTML::_('select.genericlist', $this->lists['fields'], 'value21', 'class="text_area" size="1" style="max-width:200px;" ', 'value', 'text', 0);
	?>
	&nbsp;&nbsp;
	<select name="condition2">
		<option value="0"><?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_ONE');?></option>
		<option value="1"><?php echo JText::_('COM_JOOMLAQUIZ_IS_LESS_THEN');?></option>
		<option value="2"><?php echo JText::_('COM_JOOMLAQUIZ_IS_GREATER_TO');?></option>
		<option value="3"><?php echo JText::_('COM_JOOMLAQUIZ_IS_GREATER_THAN');?></option>
		<option value="4"><?php echo JText::_('COM_JOOMLAQUIZ_IS_NOT_EQUAL_TO');?></option>
	</select>
	&nbsp;&nbsp;
	<input name="value22" size="40" class="input-xlarge" value="" type="text"/>
	<br />
	<br />	    
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>	
	</div>
</form>

