<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');

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
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=uploadquestions'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="questions-form" class="form-validate">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_UPLOAD_QUEST')?></legend>
			<table class="adminlist">
				<tr>
					<th align="left" style="text-align:left;">&nbsp;<?php echo JText::_('COM_JOOMALQUIZ_SELECT_FILE_WITH_QUESTIONS');?></th>
				</tr>
					<tr >

						<td align="left" style="text-align:left;">&nbsp;<input type="file" name="importme" size="60" /></td>
					</tr>	
				<tr>
					<th align="left" style="text-align:left;">&nbsp;<?php echo JText::_('COM_JOOMALQUIZ_QUIZ_WHERE_WANT');?></th>
				</tr>
					<tr >

						<td align="left" style="text-align:left;">&nbsp;<?php echo $this->quizzesFields;?></td>
					</tr>		
							
				</table>
				<br />
				<br />
				<?php echo JText::_('COM_JOOMLAQUIZ_CAN_UPLOAD_ONLY');?><em><?php echo JText::_('COM_JOOMLAQUIZ_MULTIPLE_CHOICE');?></em><?php echo JText::_('COM_JOOMLAQUIZ_AND');?><em><?php echo JText::_('COM_JOOMALQUIZ_MULTIPLE_RESPONS');?></em><?php echo JText::_('COM_JOOMLAQUIZ_QUESTIONS');?><br />
				<?php echo JText::_('COM_JOOMLAQUIZ_CSV_FILE');?><br />
				
				<br />

				<table border="1" cellpadding="2" cellspacing="2">
					<tr>
						<td><strong><?php echo JText::_('COM_JOOMLAQUIZ_FIRST_LINE');?></strong></td>
						<td></td>
						<td><?php echo 'question category';?></td>
						<td><?php echo 'question type';?></td>
						<td><?php echo 'is correct';?></td>
						<td><?php echo 'question/answer text';?></td>
						<td><?php echo 'points';?></td>
						<td><?php echo 'attempts';?></td>
						<td><?php echo 'random';?></td>
						<td><?php echo 'is feedback';?></td>
						<td><?php echo 'correct feedback text';?></td>
						<td><?php echo 'incorrect feedback text';?></td>						
					</tr>
					<tr>
						<td><strong><?php echo JText::_('COM_JOOMLAQUIZ_FOLLOWING_LINES');?></strong></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_CATEGORY1');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_MCHOICE');?></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_MULTIPLE_CHOICE_QUESTION');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_10');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_1');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_0');?></td>
						<td><?php echo JText::_('COM_JOOMALQUIZ_TRUE');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_YOU_ARE_RIGHT');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_YOU_ARE_WRONG');?></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_FALSE');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_ANSWER11');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_1');?></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_SPECIFIC_FEEDBACK11');?></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_FALSE');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_ANSWER12');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_1');?></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_SPECIFIC_FEEDBACK12');?></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMALQUIZ_TRUE');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_ANSWER13');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_2');?></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_SPECIFIC_FEEDBACK13');?></td>
						<td></td>
					</tr>
					
					<tr>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_CATEGORY2');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_MRESPONSE');?></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_MULTIPLE_RESPONSE_TEXT2');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_5');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_10');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_0');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_FALSE');?></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMALQUIZ_TRUE');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_ANSWER21');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_2');?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_FALSE');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_ANSWER22');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_1');?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo JText::_('COM_JOOMALQUIZ_TRUE');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_ANSWER23');?></td>
						<td><?php echo JText::_('COM_JOOMLAQUIZ_2');?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
				<br />
				<strong><?php echo JText::_('COM_JOOMLAQUIZ_CLICK');?><a target="_blank" href="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/csv/question_upload_example.csv"><?php echo JText::_('COM_JOOMLAQUIZ_HERE');?></a><?php echo JText::_('COM_JOOMLAQUIZ_TO_DOWNLOAD');?></strong>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="com_joomlaquiz" />
	<input type="hidden" name="task" value="upload_quest" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0">		
</form>
<?php if ($this->messageTrigger) { ?>
    <div id="notification" class="jqd-survey-wrap clearfix" style="clear: both">
        <div class="jqd-survey">
            <span><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES1"); ?><a onclick="jq_dateAjaxRef()" style="cursor: pointer" rel="nofollow" target="_blank"><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES2"); ?></a><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES3"); ?><i id="close-icon" class="icon-remove" onclick="jq_dateAjaxIcon()"></i></span>
        </div>
    </div>
<?php } ?>