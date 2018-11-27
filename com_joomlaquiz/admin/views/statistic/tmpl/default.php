<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

$appsLib = JqAppPlugins::getInstance();
$appsLib->loadApplications();

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$extension = 'com_joomlaquiz';
?>
<style type="text/css">
	.jq_color_1{ background-color:  #009933; border: 1px solid  #006633; }

	.jq_color_2{ background-color: #99CC00; border: 1px solid #999900; }
		
	.jq_color_3{ background-color: #9966FF; border: 1px solid  #9933FF; }
		
	.jq_color_4{ background-color: #FFCC00; border: 1px solid #FF9900; }
		
	.jq_color_5{ background-color: #FF3366; border: 1px solid #FF3333; }
		
	.jq_color_6{ background-color: #3399FF; border: 1px solid #3333FF; }
		
	.jq_color_7{ background-color: #66FF99; border: 1px solid #66CC66; }
</style>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=statistic'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div style="margin-top: 10px;" id="j-main-container" class="span10">
	<?php else : ?>
	<div style="margin-top: 10px;" id="j-main-container">
	<?php endif;?>
		<strong><?php echo JText::_('COM_JOOMLAQUIZ_SELECT_QUIZ');?></strong> <?php echo $this->statistic['quizzes'];?>
		<br />
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title" colspan="2"><?php echo JText::_('COM_JOOMLAQUIZ_QUIZ_SUMMARY');?></th>	
				</tr>
			</thead>
			<tbody>
			<tr>
				<td width="200"><?php echo JText::_('COM_JOOMLAQUIZ_PEOPLE_COMPLETED_THEQUIZ');?></td>
				<td align="left"><?php echo $this->statistic['summary']['total_finished'];?></td>
			</tr>
			<tr>
				<td width="200"><?php echo JText::_('COM_JOOMLAQUIZ_PEOPLE_SUCCESSFULLY');?></td>
				<td align="left"><?php echo $this->statistic['summary']['total_passed'];?></td>
			</tr>
			<tr>
				<td width="200"><?php echo JText::_('COM_JOOMLAQUIZ_CURRENT_MAX_SCORE');?></td>
				<td align="left"><?php echo $this->statistic['summary']['quiz_max_score'];?> <?php echo JText::_('COM_JOOMLAQUIZ_MIN_POINTS');?></td>
			</tr>
			<tr>
				<td width="200"><?php echo JText::_('COM_JOOMLAQUIZ_CURRENT_PASSING');?></td>
				<td align="left"><?php echo $this->statistic['summary']['quiz_pass_score'];?><?php echo JText::_('COM_JOOMLAQUIZ_PICENT_OR');?> <?php echo round($this->statistic['summary']['quiz_max_score']*$this->statistic['summary']['quiz_pass_score']/100, 1);?> <?php echo JText::_('COM_JOOMLAQUIZ_MIN_POINTS');?></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td width="200"><?php echo JText::_('COM_JOOMLAQUIZ_USERS_MAX_SCORE');?></td>
				<td align="left"><?php echo $this->statistic['summary']['user_max_score'];?> <?php echo JText::_('COM_JOOMLAQUIZ_MIN_POINTS');?></td>
			</tr>
			<tr>
				<td width="200"><?php echo JText::_('COM_JOOMLAQUIZ_USERS_MIN_SCORE');?></td>
				<td align="left"><?php echo $this->statistic['summary']['user_min_score'];?> <?php echo JText::_('COM_JOOMLAQUIZ_MIN_POINTS');?></td>
			</tr>
			<tr>
				<td width="200"><?php echo JText::_('COM_JOOMLAQUIZ_USERS_AVERAGE_SCRORE');?></td>
				<td align="left"><?php echo round($this->statistic['summary']['user_avg_score'], 1);?> <?php echo JText::_('COM_JOOMLAQUIZ_MIN_POINTS');?></td>
			</tr>
			</tbody>
		</table>
		<br />
		<table class="adminlist">
			<thead>
            <?php if(!empty($this->statistic['questions'])){?>
				<tr>
					<th class="title" colspan="2"><?php echo JText::_('COM_JOOMLAQUIZ_QUESTIONS');?></th>		
				</tr>
    <?php }?>
			</thead>
			<tbody>
			<?php
			if(!empty($this->statistic['questions'])){
				$k = 0;
				foreach($this->statistic['questions'] as $i => $question){
					if($question != ''){
					?>
					<tr class="row<?php echo $k;?>">
						<td width="20"><?php echo ($i+1);?></td>
						<td><?php echo ($question->c_question);?></td>
					</tr>
					<tr>
						<td colspan="2">
							<table>
								<tr>
									<td width="400"><strong><?php echo JText::_('COM_JOOMLAQUIZ_OPTIONS');?></strong></td>
                                    <td width="50"><strong><?php if($question->c_type != 8)echo JText::_('COM_JOOMLAQUIZ_COUNT');?></strong></td>
									<td width="100">&nbsp;</td>
									<td width="300">&nbsp;</td>
								</tr>
								<?php
									$content = '';
									$data = array();
									$type		= JoomlaquizHelper::getQuestionType($question->c_type);
									$data['quest_type'] = $type;
									$data['question'] = $question;
									
									$statistic = $appsLib->triggerEvent( 'onGetAdminStatistic' , $data );
									echo $statistic[0];
								?>
							</table>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
					}
				}
			}
			?>
			</tbody>
		</table>
	<input type="hidden" name="task" value="" />
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