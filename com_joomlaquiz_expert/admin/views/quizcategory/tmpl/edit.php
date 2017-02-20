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
		if (task == 'quizcategory.cancel' || document.formvalidator.isValid(document.id('quizcategory-form'))) {
			<?php echo $this->form->getField('c_instruction')->save(); ?>
			Joomla.submitform(task, document.getElementById('quizcategory-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=edit&c_id='.(int) $this->item->c_id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="quizcategory-form" class="form-validate">
	<div id="j-main-container" class="span10 form-horizontal">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->c_id) ? JText::_('COM_JOOMLAQUIZ_NEW_QUIZ_CATEGORY') : JText::sprintf('COM_JOOMLAQUIZ_EDIT_QUIZ_CATEGORY', $this->item->c_id); ?></legend>
			<div class="control-group form-inline">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_category'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_category'); ?>
				</div>
			</div>
			<br style="clear:both;"/>
			<div class="control-group form-inline">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_instruction'); ?>
                </div>
                <div class="controls">
			        <?php echo $this->form->getInput('c_instruction'); ?>
                </div>
			</div>
		</fieldset>
	    
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>	
	</div>
</form>
