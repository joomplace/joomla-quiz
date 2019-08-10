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
class JoomlaquizViewFeedbackDalliclick
{
	public static function getFeedbackContent($feedback_data, $data){
		
		$jq_tmpl_html = "<div class='jq_feedback_question_content'><div class='dc_layout'><div class='dc_cover_container'><div class='cover'><img src='".JURI::root()."/images/joomlaquiz/images/".$feedback_data['c_image']."' width='".$feedback_data['w']."' height='".$feedback_data['h']."' /></div></div>" . "\n";
		
		$jq_tmpl_html .= "<div class='dc_buttons_container'><table class='jq_feedback_question_content_inner'>";
		
		if(!empty($feedback_data['choice_data'])){
			foreach($feedback_data['choice_data'] as $ii => $qone){
				$bck = ($qone->value == $feedback_data['uanswer']['c_choice_id'][0]) ? ($qone->c_right == 1 ? "style='background:#93C162;color:white;'" : "style='background:#FF3333;color:white;'") : "";
				if($feedback_data['choice_data'][$ii]->c_right == 1) $bck = "style='background:#93C162;color:white;'";
				$jq_tmpl_html .= "<tr><td class='jq_input_pos'><button class='dc_button' disabled='disabled' ".$bck.">".$qone->text."</button></td></tr>";
			}
		}
		$jq_tmpl_html .= "</table></div></div>" . "\n";
		
		$jq_tmpl_html .= "<table>" . "\n";
		$jq_tmpl_html .= "<tr><td colspan='2' align='left'><strong>".JText::_('COM_QUIZ_RES_MES_SCORE')."</strong>&nbsp;".$feedback_data['choice_data'][0]->score.";&nbsp;<strong>".JText::_('COM_QUIZ_ELAPSED_TIME')."</strong>&nbsp;".$feedback_data['uanswer']['c_elapsed_time'][0]." sec</td></tr>";
		
		$jq_tmpl_html .= "</table></div>" . "\n";
					
		return $jq_tmpl_html;
	}
}

?>