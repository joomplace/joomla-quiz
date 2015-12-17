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
JHtml::_('formbehavior.chosen', 'select');

?>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=results&layout=stu_report&cid='.$this->cid); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>
		 <div class="btn-group pull-right hidden-phone">
            <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
            <?php echo $this->pagination->getLimitBox(); ?>
         </div>
		<table class="table table-striped" id="resultList">
            <thead>
				<tr>
					<th width="1%" class="nowrap center">
						#
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th class="nowrap center">
						<?php echo JText::_('COM_JOOMLAQUIZ_QUESTION'); ?> 
					</th>
					<th>
						<?php echo JText::_('COM_JOOMLAQUIZ_TYPE'); ?> 
					</th>
					<th>
						<?php echo JText::_('COM_JOOMLAQUIZ_POINTS2'); ?> 
					</th>
					<th>
						<?php echo JText::_('COM_JOOMLAQUIZ_USER_SCORE'); ?> 
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
					<td class="order nowrap center">
						<?php echo ($this->pagination->limitstart+$i+1);?>
					</td>
					<td class="center">
						<?php if($item->enabled){?>
							<?php echo JHtml::_('grid.id', $i, $item->c_id); ?>
						<?php }?>
					</td>
					<td class="nowrap has-context">
                        <div class="pull-left">
								<?php if($item->enabled){?>
								<a href="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=result&cid='.$item->c_id);?>">
									<?php echo JoomlaquizHelper::jq_substr($this->escape(strip_tags($item->c_question)), 0, 150); ?>
								</a>
								<?php } else {?>
									<?php echo JoomlaquizHelper::jq_substr($this->escape(strip_tags($item->c_question)), 0, 150); ?>
								<?php }?>
                        </div>
					</td>
					<td class="has-context">
                        <div class="pull-left">
                            <?php echo $item->c_qtype; ?>
                        </div>
					</td>
					<td class="has-context">
                        <div class="pull-left">
                            <?php echo $item->c_point; ?>
                        </div>
					</td>
					<td class="has-context">
                        <div class="pull-left">
                            <?php echo $item->c_score; ?>
                        </div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="c_id" value="<?php echo $this->cid;?>" />
		<?php echo JHtml::_('form.token'); ?>
        
    </div>
</form>