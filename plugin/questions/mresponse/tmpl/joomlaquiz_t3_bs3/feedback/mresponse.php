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
class JoomlaquizViewFeedbackMresponse
{
	public static function getFeedbackContent($feedback_data, $data){
		
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$jq_tmpl_html = "<div class='jq_feedback_question_content'><table class='jq_feedback_question_content_inner'>
							<tr class='jq_feedback_question_content_header'>
								<td class='jq_feedback_question_content_col_wide'>".JText::_('COM_JQ_POSSIBLE_ANSWERS')."</td>
								<td class='jq_feedback_question_content_col_narrow'>".JText::_('COM_QUIZ_CORRECT')."</td>
								<td class='jq_feedback_question_content_col_narrow'>".JText::_('COM_JQ_YOUR_CHOICE')."</td>
								<td class='jq_feedback_question_content_col_narrow'>".JText::_('COM_JQ_PEOPLE_STATISTIC')."</td>
							</tr>" . "\n";
		$k = 2;
		foreach ($feedback_data['choice_data'] as $qone) {				
			$jq_tmpl_html .= "<tr class='jq_feedback_question_content_container'>
								<td class='jq_feedback_question_content_col_wide'>".stripslashes($qone->text)."</td>
								<td class='jq_feedback_question_content_col_narrow'>".($qone->c_right == 1?"<img src='".JURI::root()."components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_pretty_green/images/result_true_green.png' border=0>":"&nbsp;")."</td>
								<td class='jq_feedback_question_content_col_narrow'>".
									(in_array($qone->value, $feedback_data['uanswer'])? "<img src='".JURI::root()."components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_pretty_green/images/".($qone->c_right==1?'result_true_green.png': 'result_false.png')."' border=0>":"&nbsp;").
								"</td>" . "\n";
			if(isset($qone->statistic))	{
				$jq_tmpl_html .= "<td class='jq_feedback_question_content_col_narrow'>".$qone->statistic."</td>" . "\n";
			}
			$jq_tmpl_html .= "</tr>" . "\n";
			$k = 3 - $k;
		}
		if(isset($feedback_data['choice_data'][0]->past_this)){
			$jq_tmpl_html .= "<tr>
								<td colspan='4' class='review_statistic'  valign='top'>".JText::_('COM_QUIZ_RST_PANSW')." ".$feedback_data['choice_data'][0]->past_this." ".JText::_('COM_QUIZ_RST_PANSW_TIMES')."</td>
							  </tr>" . "\n";
		}		
		$jq_tmpl_html .= "<tr>
							<td colspan='4' valign='top'><br /><strong>".JText::_('COM_QUIZ_RES_MES_SCORE')." ".$feedback_data['choice_data'][0]->score."</strong><br /></td>" . "\n";
		$jq_tmpl_html .= "</tr>
						</table></div>" . "\n";
				
		return $jq_tmpl_html;
	}
}

?>