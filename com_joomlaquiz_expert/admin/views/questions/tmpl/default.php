<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
 
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $ordering = $listOrder == 'ordering';
$user		= JFactory::getUser();
$userId		= $user->get('id');
$extension  = 'com_joomlaquiz';
$app = JFactory::getApplication();

$saveOrder	= $listOrder == 'ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joomlaquiz&task=questions.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'questionsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<link rel="stylesheet" href="<?php echo JURI::root();?>administrator/components/com_joomlaquiz/assets/css/thickbox/thickbox.css" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JURI::root();?>administrator/components/com_joomlaquiz/assets/js/thickbox/thickbox.js" ></script>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
	
	function deleteInvalidQuestion(q_id){
		    jQuery.ajax({
				type: "POST",
				url: "index.php?option=com_joomlaquiz&task=questions.delete_invalid",
				data: { q_id: q_id }
				}).done(function( msg ) {
					if(msg == 'success'){
						jQuery('#quest_row_' + q_id).fadeOut('slow');
						setTimeout("removeRow(" + q_id + ")", 1000);
					}
				});
	}
	
	function removeRow(q_id){
		jQuery('#quest_row_' + q_id).remove();
	}
</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=questions'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>
        <div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_JOOMLAQUIZ_FILTER_SEARCH_DESC'); ?></label>
				<input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_JOOMLAQUIZ_FILTER_SEARCH_DESC'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_JOOMLAQUIZ_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
            <div class="btn-group pull-right hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
            </div>
			
            <div class="btn-group pull-right hidden-phone">
                    <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                    <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                            <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                            <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                            <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                    </select>
            </div>
            <div class="btn-group pull-right">
                    <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                    <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                            <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                            <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                    </select>
            </div>
        </div>
        <div class="clearfix"> </div>
        <table class="table table-striped" id="questionsList">
            	<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
					</th>
					<th width="1%" class="hidden-phone nowrap center">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="30%">
						<?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_TEXT', 'c_question', $listDirn, $listOrder); ?> 
					</th>
					<th width="5%">
						<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'published', $listDirn, $listOrder); ?>
					</th>
					<th width="15%">
						<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_TYPE', 'c_type', $listDirn, $listOrder); ?> 
					</th>
					<th width="30%">
						<?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_QUIZ2', 'c_title', $listDirn, $listOrder); ?> 
					</th>
					<th width="1%">
						<?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_ID', 'c_id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="10">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php if(sizeof($this->items)) { foreach ($this->items as $i => $item) :
				$ordering  = ($listOrder == 'ordering');
				$canEdit	= $user->authorise('core.edit', $extension.'.questions.'.$item->c_id);
                $canCheckin	= $user->authorise('core.admin', 'com_checkin');
                $canChange	= $user->authorise('core.edit.state', $extension.'.questions.'.$item->c_id) && $canCheckin;
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1" id="quest_row_<?php echo $item->c_id;?>">
					<td class="order nowrap center">
						<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel	  = '';
						if (!$saveOrder) :
							$disabledLabel    = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						endif; ?>
						<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
							<i class="icon-menu"></i>
						</span>
						
					<?php else : ?>
						<span class="sortable-handler inactive" >
							<i class="icon-menu"></i>
						</span>
					<?php endif; ?>
					    <input type="text" style="display:none" name="order[]" size="5"
							value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
					</td>
					<td class="muted">
						<?php echo $i+JFactory::getApplication()->input->get('limitstart',0)+1; ?>
					</td>
					<td>
						<?php if($item->enabled){?>
							<?php echo JHtml::_('grid.id', $i, $item->c_id); ?>
						<?php } else {?>
							<i class="icon-unpublish " onclick="deleteInvalidQuestion(<?php echo $item->c_id;?>)" style="cursor:pointer;"></i>
						<?php }?>
					</td>
					<td class="nowrap has-context">
                        <div>
                        <?php
							$question = $item->c_question;
                            $length =100;
                            $dots="...";
                            $spacebar= ' ';
                            if(strlen($question)>$length)
                            {
                                $part = JoomlaquizHelper::jq_substr($question, 0 , $length);                               
                                $question = $part.$dots;
                            }
                            if ($canEdit && $item->enabled) : ?>
                            	<a title="<?php echo $this->escape($item->c_question); ?>" href="<?php echo JRoute::_('index.php?option=com_joomlaquiz&task=question.edit&c_id='.$item->c_id);?>"><?php echo $this->escape($question); ?></a>
                        	<?php else : ?>
                            <?php echo $this->escape($question); ?>
                        <?php endif; ?>
                        </div>
					</td>
					<td class="has-context">
						<?php if($item->enabled):?>
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'questions.', $canChange);?>
						<?php endif;?>
					</td>
					<td class="has-context">
						<?php echo $item->category; ?>
					</td>
					<td class="has-context">
                        <div>
                            <?php echo $item->qtype_full; ?>
                        </div>
					</td>
					<td class="has-context">
                        <div>
                            <?php echo $item->quiz_name; ?>
                        </div>
					</td>
					<td class="has-context">
                        <div>
                            <?php echo $item->c_id; ?>
                        </div>
					</td>
					<td class="has-context">
						<?php if($item->enabled):?>
							<?php 
							
							if ($app->getUserStateFromRequest('quizzes.filter.quiz_id', 'filter_quiz_id') > 0 && !(in_array($item->c_id, $this->pbreaks))) { ?>
								<a class="btn" style="color:#009900" title="Add page break" href="index.php?option=com_joomlaquiz&task=questions.add_pbreak&quiz_id=<?php echo $item->c_quiz_id;?>&quest_id=<?php echo $item->c_id;?>"><?php echo JText::_('COM_JOOMLAQUIZ_ADD_PAGE_BREAK');?></a>
							<?php }?>
						<?php endif;?>
					</td>
				</tr>
				<?php
				if ($app->getUserStateFromRequest('quizzes.filter.quiz_id', 'filter_quiz_id') > 0 && in_array($item->c_id, $this->pbreaks)) {
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td colspan="8" style="padding:0px 30px 0px 30px;">
					<strong><?php echo JText::_('COM_JOOMLAQUIZ_PAGE_BREAK');?></strong>								
					</td>
					<td><a class="btn" style="color:#990000" title="Remove page break" href="index.php?option=com_joomlaquiz&task=questions.delete_pbreak&pid=<?php echo $item->c_id;?>"><?php echo JText::_('COM_JOOMLAQUIZ_REMOVE_PAGE_BREAK');?></a>
					</td>
				</tr>
				<?php
				}
				?>
				<?php endforeach; ?>
            <?php } else { ?>
                <tr>
                    <td colspan="10" align="center" >
                        <?php echo JText::sprintf('COM_JOOMLAQUIZ_NOQUESTCREATED'); ?>
                        <a onclick="javascript: tb_start(this);return false;" href="index.php?option=com_joomlaquiz&amp;tmpl=component&amp;task=questions.new_question_type&amp;KeepThis=true&amp;TB_iframe=true&amp;height=350&amp;width=700" href="#">
                            <?php echo JText::_('COM_JOOMLAQUIZ_CREATEANEWONE'); ?>
                        </a>
                    </td>
                </tr>
            <?php }?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
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