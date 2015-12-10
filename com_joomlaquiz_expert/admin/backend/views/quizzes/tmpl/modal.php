<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
 		
JHTML::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$quizzes = array();
foreach($this->items as $it){
	$quizzes[$it->c_id] = $it->c_title;
}

?>
<script>
	function onQuizInsertClick(sender, event)
	{
		event.preventDefault();

		window.parent.onQuizInsertClick(jQuery('form #quiz_id').val());
	}

	function onQuizCancelClick(sender, event)
	{
		window.parent.SqueezeBox.close();
	}
</script>
<form id="quiz-selector-modal" name="adminForm" action="index.php" method="post" autocomplete="off" style="margin:0px;">
	<div id="j-main-container" class="span12 form-horizontal html5fb_insert_publication_tag">
		<h3 class="_title" style="text-align:center;">
			<?php echo $this->escape(JText::_('COM_JOOMLAQUIZ_INSERT_QUIZ_TAG_TITLE')); ?>
		</h3>
		<hr>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->escape(JText::_('COM_JOOMLAQUIZ_INSERT_QUIZ_LABEL')); ?>
			</div>
			<div class="controls">
				<select name="quiz_id" id="quiz_id">
					<?php foreach($quizzes as $k => $v){
						echo "<option value=\"$k\">$v</option>";
					} ?>
				</select>
			</div>
		</div>
		<hr>
		<div class="control-group buttons_group text-right">
			<button class="btn btn-primary" onclick="onQuizInsertClick(this, event);">
				<?php echo $this->escape(JText::_('COM_JOOMLAQUIZ_INSERT_QUIZ_BTN_INSERT')); ?>
			</button>
			<button class="btn" onclick="onQuizCancelClick(this, event);">
				<?php echo $this->escape(JText::_('COM_JOOMLAQUIZ_INSERT_QUIZ_BTN_CANCEL')); ?>
			</button>
		</div>
	</div>
</form>