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
<script type="text/javascript">

Joomla.submitbutton = function(task)
	{
		if (task != '') {
			Joomla.submitform(task, document.getElementById('quizzes-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

jQuery('#jform_imp_pool0').live("click", function(){
    document.getElementById('lbl_jform_imp_pool1').className='btn';
    document.getElementById('lbl_jform_imp_pool0').className='btn active btn-danger';
});

jQuery('#jform_imp_pool1').live("click", function(){
    document.getElementById('lbl_jform_imp_pool1').className='btn active btn-success';
    document.getElementById('lbl_jform_imp_pool0').className='btn';
});

</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=import_quizzes'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="quizzes-form" class="form-validate">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>
		<fieldset class="adminform" style="margin-top: -15px">
			<legend><?php echo JText::_('COM_JOOMLAQUIZ_IMPORT_QUIZZES')?></legend>
			<div class="control-group form-inline">
				<label class="control-label" for="jform_importme" id="jform_importme-lbl" aria-invalid="false"><?php echo JText::_('COM_JOOMLAQUIZ_LOAD_FILE')?></label>
				<div class="controls">
					<input type="file" size="50" name="importme" class="btn btn-small"/>
				</div>
			</div>
			<div class="control-group form-inline">
				<label class="control-label" for="jform_importme" id="jform_importme-lbl" aria-invalid="false"><?php echo JText::_('COM_JOOMLAQUIZ_INCLUDE_POOL')?></label>

                <div class="controls">
                    <fieldset class="radio btn-group" id="jform_imp_pool">
                        <input type="radio" checked="checked" value="0" name="jform[imp_pool]" id="jform_imp_pool0">
                        <label for="jform_imp_pool0" id="lbl_jform_imp_pool0" class="btn active"><?php echo JText::_('COM_JOOMLAQUIZ_NO')?></label>
                        <input type="radio" value="1" name="jform[imp_pool]" id="jform_imp_pool1">
                        <label for="jform_imp_pool1" id="lbl_jform_imp_pool1" class="btn"><?php echo JText::_('COM_JOOMLAQUIZ_YES')?></label>
                    </fieldset>
                </div>
			</div>

		</fieldset>
	    
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>	
	</div>
</form>
<?php if ($this->messageTrigger) { ?>
    <div id="notification" class="jqd-survey-wrap clearfix" style="clear: both">
        <div class="jqd-survey">
            <span><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES1"); ?><a onclick="jq_dateAjaxRef()" style="cursor: pointer" rel="nofollow" target="_blank"><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES2"); ?></a><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES3"); ?><i id="close-icon" class="icon-remove" onclick="jq_dateAjaxIcon()"></i></span>
        </div>
    </div>
<?php } ?>