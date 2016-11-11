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
			Joomla.submitform(task, document.getElementById('questions-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=copy_questions'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="questions-form" class="form-validate">
   
	<div id="j-main-container" class="span10 form-horizontal">
		<fieldset>
			<legend><?php echo JText::_('COM_JOOMLAQUIZ_COPY_QUESTIONS');?></legend>
			<div style="float:left;margin-right:10px;">
				<strong><?php echo JText::_('COM_JOOMLAQUIZ_COPY_TO_QUESTION');?></strong>
			</div>
			<div style="float:left;margin-right:10px;">
				<label for="categorycopy" class="element-invisible"><?php echo JText::_('COM_JOOMLAQUIZ_FILTER');?></label>
				<?php echo $this->quizzesFields;?>
			</div>
			<div style="float:left;">
				<?php echo JText::_('COM_JOOMLAQUIZ_THIS_WILLCOPYMOVE');?>
			</div>
			<div class="clearfix"> </div>
			
			<table class="table table-striped" id="quizzesList" style="margin-top:20px;">
				<thead>
				<tr>
					<th width="1%" class="nowrap left">
						#
					</th>
					<th class="hidden-phone">
						<?php echo JText::_('COM_JOOMLAQUIZ_QUESTIONS_BEING');?>
					</th>
					<th class="nowrap left">
						<?php echo JText::_('COM_JOOMLAQUIZ_FROM_QUIZ');?>
					</th>
				<tr>
				</thead>
				<tbody>
				<?php foreach ($this->copy_questions as $i => $item) :?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
						<td class="order nowrap">
							<span class="sortable-handler inactive" >
								<i class="icon-menu"><?php echo ($i+1);?></i>
							</span>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
								<?php echo JoomlaquizHelper::jq_substr($item->question_name, 0, 150);?>
							</div>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
								<?php echo $item->quiz_name;?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</div>
</form>
