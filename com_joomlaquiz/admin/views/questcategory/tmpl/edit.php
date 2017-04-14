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

?>
<script type="text/javascript">

Joomla.submitbutton = function(task)
	{
		if (task == 'questcategory.cancel' || document.formvalidator.isValid(document.id('questcategory-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('questcategory-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=edit&qc_id='.(int) $this->item->qc_id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="questcategory-form" class="form-validate">
	<div id="j-main-container" class="span10 form-horizontal">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->qc_id) ? JText::_('COM_JOOMLAQUIZ_NEW_QUESTION_CATEGORY') : JText::sprintf('COM_JOOMLAQUIZ_EDIT_QUESTION_CATEGORY', $this->item->qc_id); ?></legend>
			<div class="control-group form-inline">
				<?php echo $this->form->getLabel('qc_category'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('qc_category'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
                <div class="control-label">
                    <?php echo $this->form->getLabel('description'); ?>
                </div>
                <div class="controls">
			        <?php echo $this->form->getInput('description'); ?>
                </div>
			</div>
			<div class="control-group form-inline">
                <div class="control-label">
				    <?php echo $this->form->getLabel('qc_tag_t'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('qc_tag_t'); ?>
					<?php echo $this->qc_tag_dd; ?>
				</div>
			</div>
		</fieldset>
	    
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>	
	</div>
</form>
