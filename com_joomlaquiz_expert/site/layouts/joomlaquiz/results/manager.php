<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
$pagination = $displayData->pagination;
JHtml::_('script', 'system/core.js', true, true);
?>
<div class="jq_results_container">
	<form name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=results'.JoomlaquizHelper::JQ_GetItemId());?>" method="post">
	<table class="jq_results_container_table table-striped" cellpadding="10" cellspacing="10" border="0" width="100%">
	<tr>	
		<td class="sectiontableheader">#</td>
		<td class="sectiontableheader"><?php echo JText::_('COM_QUIZ_MAIL_MESSAGE_USER'); ?></td>
		<td class="sectiontableheader"><?php echo JText::_('COM_JQ_QUIZ'); ?></td>
		<td class="sectiontableheader"><?php echo JText::_('COM_JQ_DATE_TIME'); ?></td>
		<td class="sectiontableheader"><?php echo JText::_('COM_QUIZ_RES_MES_SCORE2'); ?></td>		
		<td class="sectiontableheader"><?php echo JText::_('COM_JQ_PASSED'); ?></td>
		<td class="sectiontableheader"><?php echo JText::_('COM_JQ_SPEND_TIME'); ?></td>
		<td class="sectiontableheader"><?php echo JText::_('COM_JQ_CERTIFICATE'); ?></td>
	</tr>

		<?php
		$k = 1;
		foreach($displayData->items as $i=>$row){
			$link 	= JRoute::_("index.php?option=com_joomlaquiz&task=results.sturesult&id=".$row->id.JoomlaquizHelper::JQ_GetItemId());

			$img_passed	= $row->c_passed ? 'result_panel_true.png' : 'result_panel_false.png';
			$alt_passed = $row->c_passed ? JText::_('COM_JQ_RESULT_PASSED') : JText::_('COM_JQ_RESULT_FAILED');			
			?>
			<tr class="sectiontableentry<?php echo $k; ?>">
				<td align="center"><?php echo ( $pagination->limitstart + $i + 1 ); ?></td>
				<td align="left">
					<?php if ($row->name) { ?>
						<span title="<?php echo JFactory::getUser($row->c_student_id)->username; ?>" alt="<?php echo JFactory::getUser($row->c_student_id)->username; ?>" style="cursor: help;"><?php echo JFactory::getUser($row->c_student_id)->name; ?></span>
					<?php } else {  ?>
						<span title="<?php echo $row->user_surname; ?>" alt="<?php echo $row->user_surname; ?>" style="cursor: help;"><?php echo $row->user_name; ?></span>
					<?php } ?>
				</td>
				<td align="left">
					<a href="<?php echo JRoute::_($link); ?>">
						<?php echo $row->c_title; ?>
					</a>
				</td>
				<td align="left">
					<?php echo $row->c_date_time; ?>
				</td>
				<td align="left">
					<?php if ($row->c_passed == -1)	 { echo JText::_('COM_JQ_SCORE_PENDING'); } else {?>
					<?php echo number_format($row->user_score, 2, '.', ' '); ?>
					<?php }?>
				</td>
				<td align="center">
					<?php if ($row->c_passed == -1)	 { ?><strong>?</strong><?php } else {?>
					<img src="<?php echo JURI::root();?>components/com_joomlaquiz/assets/images/<?php echo $img_passed;?>" border="0" alt="<?php echo $alt_passed; ?>" />
					<?php }?>								
				</td>
				<td align="left">
					<?php
					$tot_min = floor($row->c_total_time / 60);
					$tot_sec = $row->c_total_time - $tot_min*60;
					echo str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT);
					?>
				</td>
				<td align="center">
					<?php if($row->c_certificate && $row->c_passed):?>
					<a onclick="window.open ('<?php echo JRoute::_("index.php?option=com_joomlaquiz&task=printcert.get_certificate&stu_quiz_id=".$row->id.".&user_unique_id=".$row->unique_id); ?>','blank');" href="javascript:void(0)"><?php echo JText::_('COM_JOOMLAQUIZ_DOWNLOAD');?></a>
					<?php endif;?>
				</td>
			</tr>
			<?php
			$k = 3 - $k;
		}?>
		<tfoot>
		<tr>
			<td colspan="9"><?php echo $pagination->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
		<input type="hidden" name="option" value="com_joomlaquiz" />
		<input type="hidden" name="view" value="results" />
		<input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->getInt('Itemid', 0); ?>" />
	</form>
</div>