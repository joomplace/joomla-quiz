<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');

require_once( JPATH_ROOT .'/components/com_joomlaquiz/libraries/apps.php' );
$appsLib = JqAppPlugins::getInstance();
$plugins = $appsLib->loadApplications();

$header = $this->statistics[0];
$quizzes = $this->statistics[1];
$summary = (isset($this->statistics[2])) ? $this->statistics[2] : null;
$questions = (isset($this->statistics[3])) ? $this->statistics[3] : null;

$document 	= JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/assets/css/joomlaquiz.css');

if(isset($quizzes[0]) && isset($quizzes[0]->available) && $quizzes[0]->available == 'no stats'){
	echo '<div class="moduletable joomlaquiz_container contentpane joomlaquiz"><h1 class="componentheading">'.JText::_('COM_STATISTICS_TITLE').'</h1><br /><br /><strong>'.JText::_('COM_STATISTICS_NO_STATS').'</strong></div>';

	echo JoomlaquizHelper::poweredByHTML();
} else {
?>
<div class="moduletable joomlaquiz_container contentpane joomlaquiz">
	<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=statistics'); ?>" method="post" name="adminForm">
		<h1 class="componentheading"><?php echo $header; ?></h1>
		<br/>
		<?php if ($quizzes) {?>
		<strong><?php echo JText::_('COM_STATISTICS_SEL_QUIZ');?>:</strong> <?php echo $quizzes;?><br />
		<?php }?>
		<br />
		<table class="jq_statistics_container_table" width="100%">
			<thead>
			<tr>
				<td class="sectiontableheader" colspan="2"><?php echo JText::_('COM_STATISTICS_QUIZ_SUMMARY');?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td width="250"><?php echo JText::_('COM_STATISTICS_COMPLETED');?>:</td>
				<td align="left"><?php echo $summary['total_finished'];?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_STATISTICS_PASSED');?>:</td>
				<td align="left"><?php echo $summary['total_passed'];?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_STATISTICS_MAX_SCORE');?>:</td>
				<td align="left"><?php echo $summary['quiz_max_score'];?> <?php echo JText::_('COM_STATISTICS_POINTS');?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_STATISTICS_PASSING_SCORE');?>:</td>
				<td align="left"><?php echo sprintf(JText::_('COM_STATISTICS_PASSING_SCORE_VALUE'), $summary['quiz_pass_score'], round($summary['quiz_max_score']*$summary['quiz_pass_score']/100, 1));?></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_STATISTICS_USER_MAX_SCORE');?>:</td>
				<td align="left"><?php echo $summary['user_max_score'];?> <?php echo JText::_('COM_STATISTICS_POINTS');?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_STATISTICS_USER_MIN_SCORE');?>:</td>
				<td align="left"><?php echo $summary['user_min_score'];?> <?php echo JText::_('COM_STATISTICS_POINTS');?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_STATISTICS_USER_AVG_SCORE');?>:</td>
				<td align="left"><?php echo round($summary['user_avg_score'], 1);?> <?php echo JText::_('COM_STATISTICS_POINTS');?></td>
			</tr>
			</tbody>
		</table>
		<br />
		<br />
		<table class="jq_statistics_container_table" width="100%">
			<thead>
			<tr>
				<td class="sectiontableheader" colspan="2"><?php echo JText::_('COM_STATISTICS_QUESTS');?></td>
			</tr>
			</thead>
			<tbody>
			<?php
				$k = 1;
				foreach($questions as $i=>$question) { ?>
					<tr class="sectiontableentry<?php echo $k; ?>">
						<td width="20" style="padding: 3px;"><strong><?php echo ($i+1);?></strong></td>
						<td style="padding-left:0px;"><?php echo ($question->c_question);?></td>
					</tr>
					<tr>
						<td colspan="2">
							<table>
							<tr>
								<td width="400"><strong><?php echo JText::_('COM_STATISTICS_OPTIONS');?></strong></td>
                                <td width="50" align="center"><strong><?php if($question->c_type != 8)echo JText::_('COM_STATISTICS_COUNT');?></strong></td>
								<td width="100">&nbsp;</td>
								<td width="300">&nbsp;</td>
							</tr>
							<?php
								$type = JoomlaquizHelper::getQuestionType($question->c_type);
								$data = array();
								$data['quest_type'] = $type;
								$data['question'] = $question;
								
								$appsLib->triggerEvent( 'onStatisticContent' , $data );
															
							?>
							</table>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
			?>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_joomlaquiz" />
		<input type="hidden" name="task" value="statistics" />
	</form>
</div>
<?php
echo JoomlaquizHelper::poweredByHTML();
}
?>