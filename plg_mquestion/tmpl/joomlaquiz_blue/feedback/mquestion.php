<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaquiz Deluxe class
 */
class JoomlaquizViewFeedbackMquestion
{
	public static function getFeedbackContent($feedback_data, $data){
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$jq_tmpl_html = "<table align='left' class='jq_mquestions_fdb' width='100%'>
							<tr>
								<td class='sectiontableheader' width='auto'>".JText::_('COM_JQ_POSSIBLE_ANSWERS')."</td> \n
								<td width='15%' align='center' class='sectiontableheader' valign='top'>".JText::_('COM_QUIZ_CORRECT')."</td> \n
								<td valign='top' align='center' width='15%' class='sectiontableheader'>".JText::_('COM_JQ_YOUR_CHOICE')."</td> \n
								<td valign='top'  width='auto' class='sectiontableheader'>".JText::_('COM_JQ_PEOPLE_STATISTIC')."</td> \n
							</tr> \n";
		$k = 2;
		foreach ($feedback_data['choice_data'] as $qone) {
			$jq_tmpl_html .= "
					<tr class='sectiontableentry".$k."'>
						<td align='left' class='quest_pos'>".stripslashes($qone->text)."</td> \n
						<td valign='top' align='center'>".(($qone->c_right == 1)?$feedback_data['choice_data'][0]->c_title_true: $feedback_data['choice_data'][0]->c_title_false)." </td> \n
						<td valign='top' align='center'>".(in_array($qone->value, $feedback_data['user_answer'])? $feedback_data['choice_data'][0]->c_title_true: $feedback_data['choice_data'][0]->c_title_false)."</td> \n";
			if(isset($qone->statistic)) {
				$jq_tmpl_html .= "<td class='choice_static'  valign='top'>".$qone->statistic."</td> \n";
			}
	
			$jq_tmpl_html .= "</tr>" . "\n";
			$k = 3 - $k;
		}
		
		if(isset($feedback_data['qdata'][0]->past_this)) {
			$jq_tmpl_html .= "<tr>
								<td colspan='4' class='review_statistic'>".JText::_('COM_QUIZ_RST_PANSW')." ".$feedback_data['qdata'][0]->past_this." ".JText::_('COM_QUIZ_RST_PANSW_TIMES')."</td>" . "\n";
			$jq_tmpl_html .= "</tr>" . "\n";
		}

		$jq_tmpl_html .= "<tr>
								<td colspan='4' valign='top'><br /><strong>".JText::_('COM_QUIZ_RES_MES_SCORE')." ".$feedback_data['qdata'][0]->score."</strong><br /></td>" . "\n";
		$jq_tmpl_html .= "</tr>" . "\n";
		$jq_tmpl_html .= "</table>" . "\n";
				
		return $jq_tmpl_html;
	}
}

?>