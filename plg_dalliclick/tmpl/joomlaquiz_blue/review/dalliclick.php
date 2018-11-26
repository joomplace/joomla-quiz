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
class JoomlaquizViewReviewDalliclick
{
	public static function getReviewContent($review_data, $data){
		
		$jq_tmpl_html = "<div class='jq_feedback_question_content'><div class='dc_layout'><div class='dc_cover_container'><div class='cover'><img src='".JURI::root()."/images/joomlaquiz/images/".$review_data['c_image']."' width='".$review_data['w']."' height='".$review_data['h']."' /></div></div>" . "\n";
		
		$jq_tmpl_html .= "<div class='dc_buttons_container'><table class='jq_feedback_question_content_inner'>";
		
		if(!empty($review_data['choice_data'])){
			foreach($review_data['choice_data'] as $ii => $qone){
				$bck = ($qone->value == $review_data['uanswer']['c_choice_id'][0]) ? ($qone->c_right == 1 ? "style='background:#93C162;color:white;'" : "style='background:#FF3333;color:white;'") : "";
				if($review_data['choice_data'][$ii]->c_right == 1) $bck = "style='background:#93C162;color:white;'";
				$jq_tmpl_html .= "<tr><td class='jq_input_pos'><button class='dc_button' disabled='disabled' ".$bck.">".$qone->text."</button></td></tr>";
			}
		}
		$jq_tmpl_html .= "</table></div></div>" . "\n";
		
		$jq_tmpl_html .= "<table>";
		$jq_tmpl_html .= "<tr><td colspan='2' align='left'><strong>".JText::_('COM_QUIZ_ELAPSED_TIME')."</strong>&nbsp;".$review_data['uanswer']['c_elapsed_time'][0]." sec</td></tr>";
		
		$jq_tmpl_html .= "</table></div>" . "\n";
					
		return $jq_tmpl_html;
	}
}

?>