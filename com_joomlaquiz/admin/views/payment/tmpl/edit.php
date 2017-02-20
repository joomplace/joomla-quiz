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
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=edit&id='.(int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="payment-form" class="form-validate">
	<div id="j-main-container" class="span10 form-horizontal">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_JOOMLAQUIZ_EDIT_PAYMENT_DETAILS') : JText::sprintf('COM_JOOMLAQUIZ_EDIT_PAYMENT_DETAILS', $this->item->id); ?></legend>
			<div class="control-group form-inline">
                <div class="control-label">
				    <?php echo $this->form->getLabel('pid'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('pid'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
                <div class="control-label">
				    <?php echo $this->form->getLabel('user_id'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('user_id'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
                <div class="control-label">
				    <?php echo $this->form->getLabel('amount'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('amount'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
                <div class="control-label">
				    <?php echo $this->form->getLabel('cur_code'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('cur_code'); ?>
				</div>
			</div>
			<div class="control-group form-inline">
                <div class="control-label">
				    <?php echo $this->form->getLabel('status'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('status'); ?>
				</div>
			</div>
		</fieldset>
	    
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>	
	</div>
</form>
<script type="text/javascript">

Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('payment-form'));
	}

</script>
