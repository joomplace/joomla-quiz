<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$start = $this->state->get('list.start');

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task){
	    if (task == "userquiz.notify"){
		    alert("In developing.");
		}
		
		if (task != "" && document.formvalidator.isValid(document.getElementById("adminForm"))){
			Joomla.submitform(task, document.getElementById("adminForm"));
		} else {
		    alert("'.$this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')).'");
		}
	};
	jQuery(function($){
	    $("#jform_userquiz_quiz_id").on("change", function(){
	        $("#adminForm").submit();
	    });
	});
');

echo $this->loadTemplate('menu');

?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=userquiz'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

    <?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>

	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_JOOMLAQUIZ_USERQUIZ_TITLE'); ?></legend>

        <div class="span10">
            <div class="span4" style="margin-top:8px;"><?php echo $this->listQuizzes; ?></div>
            <h2 class="span8">
                <?php echo JText::_('COM_JOOMLAQUIZ_USERQUIZ_VIEW_NAME_QUIZ'); ?>
                <?php echo $this->current_quizName; ?>
            </h2>
        </div>

        <div class="span10" style="margin-top:16px;">
            <div class="span4">
                <div><?php echo JText::_('COM_JOOMLAQUIZ_USERQUIZ_VIEW_SELECT_USERS'); ?></div>
                <div><?php echo $this->listUsers; ?></div>
            </div>

            <div class="span8">
                <?php if(empty($this->assignedUsers)): ?>
                    <div class="alert alert-no-items">
                        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else: ?>

                <div class="pull-right hidden-phone"><?php echo $this->pagination->getLimitBox(); ?></div>

                <table class="table table-striped" id="assignedUsersList">
                    <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo '#'; ?>
                        </th>
                        <th width="1%" class="center">
                            <?php echo JHtml::_('grid.checkall'); ?>
                        </th>
                        <th style="min-width:50px" class="nowrap">
                            <?php echo JText::_('COM_JOOMLAQUIZ_USERQUIZ_VIEW_THEAD_USERID'); ?>
                        </th>
                        <th style="min-width:100px" class="nowrap">
                            <?php echo JText::_('COM_JOOMLAQUIZ_USERQUIZ_VIEW_THEAD_USERNAME'); ?>
                        </th>
                        <th width="10%" class="nowrap hidden-phone">
                            <?php echo JText::_('COM_JOOMLAQUIZ_USERQUIZ_VIEW_THEAD_NOTIFIED'); ?>
                        </th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td colspan="5">
                        </td>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php foreach ($this->assignedUsers as $i => $item) : ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="order nowrap center hidden-phone">
                            <?php echo $i+1+$start ?>
                        </td>
                        <td class="center">
                            <?php echo JHtml::_('grid.id', $i, $item->user_id); ?>
                        </td>
                        <td class="has-context">
                            <?php echo $item->user_id; ?>
                        </td>
                        <td class="has-context">
                            <?php echo $item->name; ?>
                        </td>
                        <td class="small hidden-phone">
                            <?php echo $item->notified ? '<span class="icon-checkmark-2"> </span>' : '<span class="icon-unpublish"> </span>'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->pagination->getListFooter(); ?>

                <?php endif;?>
            </div>
        </div>

	</fieldset>

	<input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>	
	</div>
</form>