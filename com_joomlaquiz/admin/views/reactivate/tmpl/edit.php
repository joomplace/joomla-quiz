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
		<?php
								
		$count = count($this->lists['all']);			
		$product_id = '0';
		$k =0;
		for($i=0; $i<$count; $i++) { 
			$row = $this->lists['all'][$i];
			if(strval($row->pid) != strval($product_id)) {
				$product_id = $row->pid;
			?>
		<div class="col100">
			<fieldset class="adminform"><legend><?php echo $row->product_title; ?></legend>			
			<table class="table table-striped" cellpadding="10" cellspacing="10">
					<thead>
						<tr>
							<th width="20"></th>
							<th class="title" width="50%"><?php echo JText::_('COM_JOOMLAQUIZ_NAME');?></th>
							<th class="title" width="50px"><?php echo JText::_('COM_JOOMLAQUIZ_TYPE');?></th>
							<th class="title"><?php echo JText::_('COM_JOOMLAQUIZ_RESTRICTIONS');?></th>
							<th width="100px"><?php echo JText::_('COM_JOOMLAQUIZ_RESET');?></th>
						</tr>
					</thead>					
			<?php
			}
				$reactivated = (array_key_exists($row->id, $this->lists['products_stat']) ? $this->lists['products_stat'][$row->id] : null);

				$display = 0;
				$row->suffix = '';
				if($row->xdays > 0) {
					$display = 1;
							
					$day_start = 0;
					if(empty($reactivated->xdays_start) || !$reactivated->xdays_start || $reactivated->xdays_start == '0000-00-00 00:00:00') {
						$day_start = ($row->date_added && $row->date_added != '0000-00-00 00:00:00' ? $row->date_added : 0);
					} else {
						$day_start = $reactivated->xdays_start;
					}
					$days_left_ts = strtotime(JFactory::getDate()) - ($day_start ? strtotime($day_start) : 0);
					$days_left = floor($days_left_ts/(60*60*24));
							
					$color = ($days_left < $row->xdays ? 'green' : 'red');
					$row->suffix .= '<span style="color:' . $color . '">';
					$row->suffix .= sprintf(($row->type == 'q' ? JText::_('COM_JOOMLAQUIZ_QUIZ_XDAYS') : JText::_('COM_JOOMLAQUIZ_LPATH_XDAYS')), $row->xdays);
					$row->suffix .= '&nbsp;' . $days_left . JText::_('COM_JOOMLAQUIZ_DAYS_PASSED').'</span>';

				} else if(($row->period_start && $row->period_start != '0000-00-00') || ($row->period_end && $row->period_end != '0000-00-00')) {
					$display = 2;
							
					$period = array();							
					$period_start_ts = 0;
					if(isset($reactivated->period_start) && $reactivated->period_start && $reactivated->period_start != '0000-00-00') {
						$period_start_ts = strtotime($reactivated->period_start);
						$period[] = sprintf(JText::_('COM_JOOMLAQUIZ_LPATH_PERIOD_FROM'), date(JText::_('COM_JOOMLAQUIZ_LPATH_PERIOD_FORMAT'), $period_start_ts));
								
					} else if($row->period_start && $row->period_start != '0000-00-00') {
						$period_start_ts = strtotime($row->period_start);
						$period[] = sprintf(JText::_('COM_JOOMLAQUIZ_LPATH_PERIOD_FROM'), date(JText::_('COM_JOOMLAQUIZ_LPATH_PERIOD_FORMAT'), $period_start_ts));
					}
							
					$period_end_ts = 0;
					if(isset($reactivated->period_end) && $reactivated->period_end && $reactivated->period_end != '0000-00-00') {
						$period_end_ts = strtotime($reactivated->period_end);
						$period[] = sprintf(JText::_('COM_JOOMLAQUIZ_LPATH_PERIOD_TO'), date(JText::_('COM_JOOMLAQUIZ_LPATH_PERIOD_FORMAT'), $period_end_ts));
								
					} elseif($row->period_end && $row->period_end != '0000-00-00') {
						$period_end_ts = strtotime($row->period_end);
						$period[] = sprintf(JText::_('COM_JOOMLAQUIZ_LPATH_PERIOD_TO'), date(JText::_('COM_JOOMLAQUIZ_LPATH_PERIOD_FORMAT'), $period_end_ts));
					}
							
					$color = (((strtotime(JFactory::getDate()) < $period_start_ts) || ($period_end_ts && strtotime(JFactory::getDate()) > $period_end_ts))  ? 'red' : 'green');
					$row->suffix .= '<span style="color:' . $color . '">';
					$row->suffix .= sprintf(($row->type == 'q' ? JText::_('COM_JOOMLAQUIZ_QUIZ_PERIOD') : JText::_('COM_JOOMLAQUIZ_LPATH_PERIOD')), implode(' ', $period));
					$row->suffix .= '</span>';
				}
						
				if($row->attempts > 0) {
					$r_a = isset($reactivated->attempts)? intval($reactivated->attempts): 0;
					$color = ( ($row->attempts * $this->lists['product_quantity'] ) > $r_a ? 'green' : 'red');
					$row->suffix .= '<span style="color:' . $color . '">';
					$row->suffix .= ($row->suffix ? '<br />' : '') . sprintf(JText::_('COM_JOOMLAQUIZ_QUIZ_ATTEMPTS'), $row->attempts * $this->lists['product_quantity']);
					$row->suffix .= 'The user has made ' . $r_a . ' attempt(s)';
					$row->suffix .= '</span>';
				}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td class="has-context"><input type="checkbox" name="cid[]" value="<?php echo $row->id; ?>" /></td>
				<td align="left" nowrap class="has-context">
					<?php echo $row->rel_title; ?>
				</td>
				<td align="center" nowrap class="has-context">
					<?php echo $row->rel_type_full; ?>
				</td>
				<td align="left" class="has-context">
					<?php echo $row->suffix; ?>
				</td>
				<td nowrap class="has-context"><?php
					if($display == 0 || $display == 1){
					?>
						<input type="checkbox" name="xdays[]" value="<?php echo $row->id; ?>" checked />&nbsp;&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_XDAYS');?>&nbsp;&nbsp;
					<?php	}
					if($display == 0 || $display == 2){?>
						<input type="checkbox" name="period[]" value="<?php echo $row->id; ?>" checked />&nbsp;&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_PERIOD');?>&nbsp;&nbsp;<?php
					}?>
					<br /><input type="checkbox" name="attempts[]" value="<?php echo $row->id; ?>" checked />&nbsp;&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_ATTEMPTS_NO');?>
				</td>
			</tr>
			<?php									
			if(@$this->lists['all'][$i+1]->pid != $row->pid) {
			?>
		</table>
		</fieldset></div><div class="clr"></div>		
		<?php
			}
			$k = (!$k) ? 1 : 0;
		}
		?>	    
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="oid" value="<?php echo $this->lists['oid']; ?>" />
	<?php echo JHtml::_('form.token'); ?>	
	</div>
</form>
<script type="text/javascript">

Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('payment-form'));
	}

</script>
