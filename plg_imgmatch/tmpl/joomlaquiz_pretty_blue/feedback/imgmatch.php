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
class JoomlaquizViewFeedbackImgmatch
{
	public static function getFeedbackContent($qdata, $data){
		
		$jq_tmpl_html = "<div class='jq_feedback_question_content'>";
		$jq_tmpl_html .= '<table class="jq_feedback_question_content_inner" style="text-align:center">
							<tbody>
							<tr class="jq_feedback_question_content_header">
								<td>'.JText::_('COM_JQ_POSSIBLE_ANSWERS').'</td>
								<td>'.JText::_('COM_QUIZ_CORRECT').'</td>
								<td>'.JText::_('COM_JQ_YOUR_CHOICE').'</td>';
		$k = 2;
		if(is_array($qdata))
		foreach($qdata as $t) {
			$correct = ($t['c_correct']) ? '<img src="'.JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_pretty_blue/images/result_true_green.png"/>' : '<img src="'.JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_pretty_blue/images/result_false.png"/>';
			$jq_tmpl_html .= '<tr class="jq_feedback_question_content_container">'.
								'<td><img src="'. JURI::root().'images/joomlaquiz/images/resize/'.($t['c_left_text']). '" style="height:100px;" /></td>'.
								'<td><img src="'. JURI::root().'images/joomlaquiz/images/resize/'.($t['c_right_text']). '" style="height:100px;" /></td>'.
								'<td><img src="'. JURI::root().'images/joomlaquiz/images/resize/'.($t['c_sel_text']). '" style="height:100px;" style="vertical-align:middle;"/>&nbsp;'.$correct.'</td>'.
								'</tr>';
			$k = 3 - $k;
		}
		
		$jq_tmpl_html .= "<tr><td colspan='4' valign='top' align='left'><strong>".JText::_('COM_QUIZ_RES_MES_SCORE')." ".$qdata[0]['score']."</strong></td>" . "\n";
		$jq_tmpl_html .= "</tr></tbody></table>" . "\n";
		$jq_tmpl_html.='</div>' . "\n";
		
		return $jq_tmpl_html;
	}
}

?>