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
$model = $this->getModel('question');

?>
<?php echo $this->loadTemplate('menu');?>
<script type="text/javascript">
    
	Joomla.submitbutton = function(task)
	{
		var form = document.adminForm;

		if (task == 'question.preview_quest') {
			if (!form.c_id.value) {
				alert('<?php echo addslashes( JText::_('COM_JOOMLAQUIZ_PLEASE_SAVE_THE'));?>');
				return false;
			} else {
				window.open('index.php?option=com_joomlaquiz&task=question.preview_quest&c_id=<?php echo $this->item->c_id;?>');
				return false;
			}
		}

		if(window.parent && window.parent.tinyMCE){
			if(window.parent.tinyMCE.get('jform_c_question').getContent() == '' && jQuery('#jform_c_question').val() != ''){
				window.parent.tinyMCE.get('jform_c_question').setContent(jQuery('#jform_c_question').val());
			}
			
			if(task != 'question.cancel' && window.parent.tinyMCE.get('jform_c_question').getContent() == ''){
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
				return false;
			}
		}
		
		if (task == 'question.cancel' || document.formvalidator.isValid(document.id('question-form'))) {	
			Joomla.submitform(task, document.getElementById('question-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}

		
	}
	
	function TRIM_str(sStr) {
		sStr = sStr+"";
		return (sStr.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=edit&c_id='.(int) $this->item->c_id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="question-form" class="form-validate">
<div id="j-main-container" class="span12 form-horizontal">
	<ul class="nav nav-tabs" id="questionTabs">
	    <li class="active"><a href="#question-details" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_QUESTION');?></a></li>
	    <?php if($this->is_feedback):?>
		<li><a href="#question-feedback" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_QUESTION_FEEDBACK2');?></a></li>
		<?php endif;?>
		<?php if($this->options != ''):?>
		<li><a href="#question-options" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_QUESTION_OPTIONS');?></a></li>
		<?php endif;?>
		<?php if($this->add_tabs):?>
			<?php foreach($this->add_tabs as $tab):?>
				<?php echo $tab['label'];?>
			<?php endforeach;?>
		<?php endif;?>
	</ul>
	<div class="tab-content">
	    <div class="tab-pane active" id="question-details">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION')?></legend>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_question'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_question'); ?>
				</div>
			</div>
			<?php if($this->is_reportname):?>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('report_name'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('report_name'); ?>
				</div>
			</div>
			<?php endif;?>
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
				    <?php echo $this->form->getLabel('c_separator'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_separator'); ?>
				</div>
			</div>
			<?php 
			if(!empty($this->add_form)){
				foreach($this->add_form as $for => $item){?>
				<div class="control-group">
				<?php if ($for=='c_qform') 
			              echo $item['label'];
				      else {?>
                          <label class="hasPopover control-label" for="<?php echo $for; ?>"
                                 title="<?php echo isset($item['label_title']) ? $item['label_title'] : $item['label']; ?>"
                                 data-content="<?php echo isset($item['label_description']) ? $item['label_description'] : $item['label']; ?>"
                                 id="<?php echo $for; ?>-lbl"
                                 style="width:156px;"><?php echo $item['label'] ?></label>
				<?php }?>
					<div class="controls">
						<?php echo $item['input']?>
					</div>
				</div>
			<?php }
			}
			?>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_DETAILS')?></legend>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_quiz_id'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_quiz_id'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_ques_cat'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_ques_cat'); ?>
				</div>
			</div>
			<?php if($this->is_points):?>
			<div class="control-group">
                <div class="control-class">
				    <?php echo $this->form->getLabel('c_point'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_point'); ?>
				</div>
			</div>
			<?php endif;?>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_attempts'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_attempts'); ?>
				</div>
			</div>
			<?php if($this->is_penalty):?>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_penalty'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_penalty'); ?>
				</div>
			</div>
			<?php endif;?>
		</fieldset>
	    </div>
		<?php if($this->options != ''):?>
		<div class="tab-pane" id="question-options">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_OPTIONS')?></legend>
				<?php echo $this->options;?>
			</fieldset>
		</div>
		<?php endif;?>
		<?php if($this->is_feedback):?>
	    <div class="tab-pane" id="question-feedback">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_FEEDBACK2')?></legend>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_feedback'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_feedback'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_right_message'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_right_message'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_wrong_message'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_wrong_message'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_detailed_feedback'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_detailed_feedback'); ?>
				</div>
			</div>
			<?php if($this->feedback_fields):?>
				<?php foreach($this->feedback_fields as $for => $field):?>
					<div class="control-group">
						<label class=" control-label" for="<?php echo $for;?>" id="<?php echo $for;?>-lbl" style="width:156px;"><?php echo $field['label']?></label>
						<div class="controls">
							<?php echo $field['input']?>
						</div>
					</div>
				<?php endforeach;?>
			<?php endif;?>
		</fieldset>
	    </div>
		<?php endif;?>
		<?php if($this->add_tabs):?>
			<?php foreach($this->add_tabs as $tab):?>
				<?php echo $tab['content'];?>
			<?php endforeach;?>
		<?php endif;?>
	</div>
</div>
<input type="hidden" name="task" value="" />
<input type="hidden" name="jform[c_id]" value="<?php echo $this->item->c_id;?>" />
<input type="hidden" name="c_id" value="<?php echo $this->item->c_id;?>" />
<input type="hidden" name="jform[c_type]" value="<?php echo $this->item->c_type;?>" />
<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
<?php echo JHtml::_('form.token'); ?>
</form>
<?php echo $this->script;?>

