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
$user		= JFactory::getUser();
$userId		= $user->get('id');
$extension = 'com_joomlaquiz';
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		//table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		//order = table.options[table.selectedIndex].value;
        order = '';
		//if (order != '<?php echo $listOrder; ?>') {
		//	dirn = 'asc';
		//} else {
			dirn = direction.options[direction.selectedIndex].value;
		//}
		Joomla.tableOrdering(order, dirn, '');
	}

	Joomla.submitbutton = function( task ) {
		Joomla.submitform( task );
		document.getElementsByName("task")[0].value = "";
	}
</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=results'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div style="margin-top: 10px;" id="j-main-container" class="span10">
	<?php else : ?>
	<div style="margin-top: 10px;" id="j-main-container">
	<?php endif;?>
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
                    <label for="category_id" class="element-invisible"><?php echo JText::_('COM_JOOMLAQUIZ_FILTER');?></label>
                    <?php echo $this->lists['quiz'];?>
            </div>
			<div class="btn-group pull-right">
                    <label for="category_id" class="element-invisible"><?php echo JText::_('COM_JOOMLAQUIZ_FILTER');?></label>
                    <?php echo $this->lists['user'];?>
            </div>
			<div class="btn-group pull-right">
                    <label for="category_id" class="element-invisible"><?php echo JText::_('COM_JOOMLAQUIZ_FILTER');?></label>
                    <?php echo $this->lists['passed'];?>
            </div>
            <?php //custom586 start ?>
            <div class="btn-group pull-right">
                <label for="category_id" class="element-invisible"><?php echo JText::_('COM_JOOMLAQUIZ_FILTER');?></label>
                <?php echo $this->lists['categories'];?>
            </div>
            <?php //custom586 end ?>
        <div class="clearfix"> </div>
        <table class="table table-striped" id="resultsList">
            <thead>
				<tr>
					<th width="1%" class="nowrap center">
						#
					</th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_CATEGORY_NAME', 'category_title', $listDirn, $listOrder); ?>
                    </th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_USER', 'username', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_QUIZ2', 'c_title', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_TOTAL_SCORE', 'c_full_score', $listDirn, $listOrder); ?>
					</th>
					<?php echo JHtml::_('content.prepare','',$this->items,'admin.results.table.head'); ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo 12+(int)JHtml::_('content.prepare','',$this->items,'admin.results.table.count'); ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :

                $item->username = $item->user_name;
				if (!$item->user_id){
					if($item->user_name != '' && $item->user_email == ''){
						$item->username = $item->user_name;
					} elseif($item->user_name != '' && $item->user_email != ''){
						$item->username = $item->user_name.' ('.$item->user_email.')';
					} elseif($item->user_email != ''){
						$item->username = $item->user_email;
					} else {
						$item->username = "Anonymous";
					}
				}
				
				if (!$item->username) $item->username = "User not found";
				if (!$item->c_title) $item->c_title = "Quiz not found";
				
				$canEdit	= $user->authorise('core.edit',	$extension.'.results.'.$item->quiz_id);
                $canCheckin	= $user->authorise('core.admin', 'com_checkin');
                $canChange	= $user->authorise('core.edit.state', $extension.'.results.'.$item->quiz_id) && $canCheckin;
			?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
					<td class="order nowrap center">
						<?php echo ($i+1);?>
					</td>
                    <td class="has-context">
                        <div class="pull-left">
                            <?php echo $item->category_title; ?>
                        </div>
                    </td>
					<td class="has-context">
                        <div class="pull-left">
                            <?php echo $item->username; ?>
                        </div>
					</td>
					<td class="has-context">
                        <div class="pull-left">
                            <?php echo $item->c_title; ?>
                        </div>
					</td>
					<td class="has-context">
                        <div class="pull-left">
                            <?php echo $item->c_full_score; ?>
                        </div>
					</td>
					<?php echo JHtml::_('content.prepare','',$item,'admin.results.table.row'); ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
        
    </div>
</form>