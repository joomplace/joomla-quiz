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
//JHtml::_('formbehavior.chosen', 'select');

?>
<a style="float: right; margin-top: 5px;" class="btn btn-small" href="#" onclick="Joomla.submitbutton('configuration.apply');"><?php echo JText::_('JTOOLBAR_APPLY');?></a>
<form action="<?php echo JRoute::_('index.php'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="configuration-form">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLAQUIZ_OPTIONS'); ?></legend>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('wysiwyg_options'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('wysiwyg_options'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('lp_content_catid'); ?>
				</div>
                <div class="controls">
					<?php echo $this->form->getInput('lp_content_catid'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('include_articles_from_subcats'); ?>
				</div>
                <div class="controls">
					<?php echo $this->form->getInput('include_articles_from_subcats'); ?>
				</div>
			</div>
		</fieldset> 
	<input type="hidden" name="task" value="configuration.save" />
    <input type="hidden" name="option" value="com_joomlaquiz" />
    <input type="hidden" name="popup" value="false" />

	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">

Joomla.submitbutton = function(task)
{
	var form = document.adminForm;
	var TB_window = window.parent.document.getElementById('TB_window');
	
	if(TB_window != null){
		form.popup.value = true;
	}

	Joomla.submitform(task, document.getElementById('configuration-form'));
}

</script>