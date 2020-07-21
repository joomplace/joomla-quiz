<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class JoomlaquizViewReviewMemory
{
	public static function getReviewContent($review_data, $data)
    {
		$jq_tmpl_html = "<div style='width:100%;'>";
		$jq_tmpl_html .= '<table width="100%" id="quest_table">
							<tr>
								<td>'.JText::_('COM_JQ_POSSIBLE_ANSWERS').'</td>
								<td>'.JText::_('COM_JQ_YOUR_CHOICE').'</td>
								<td>'.JText::_('COM_JQ_YOUR_MEMORY_POINTS').'</td>
							</tr>';
		
		$k = 2;
		if(!empty($review_data['memory_data'])){
			foreach($review_data['memory_data'] as $mem) {
				$wrong_correct = (in_array($mem->m_id, $review_data['udata'])) ? '<img src="'.JURI::root().'components/com_joomlaquiz/assets/images/tick.png" style="vertical-align:middle;margin-left:10px;"/>' : '<img src="'.JURI::root().'components/com_joomlaquiz/assets/images/tickr.png" style="vertical-align:middle;margin-left:10px;"/>';
				
				$expl = '';
				if(in_array($mem->m_id, $review_data['udata'])){
					$cc = array_search($mem->m_id, $review_data['udata']);
					$take_time = $review_data['c_elapsed_times'][$cc];
					$penalty = ($data['q_data']->c_sec_penalty) ? round($take_time/$data['q_data']->c_sec_penalty) * $data['q_data']->c_penalty : 0;
					$score = $mem->a_points - $penalty;
					$expl = ' ('.JText::_('COM_JOOMLAQUIZ_YOU_GOT').' '.$mem->a_points.' '.JText::_('COM_JOOMLAQUIZ_POINTS_MINUS').' '.$penalty.' '.JText::_('COM_JOOMLAQUIZ_PENALTY_POINTS').')';
					if($score < 0){
						$score = 0;
					}
				} else {
					$score = 0;
				}
								
				$jq_tmpl_html .= '<tr class="sectiontableentry'.$k.'">'.
                                    '<td class="jq_feedback_question_content_col_wide"><img src="'. JURI::root().'images/joomlaquiz/images/memory/'.($mem->c_img). '" />&nbsp;&nbsp;<img src="'. JURI::root().'images/joomlaquiz/images/memory/'.($mem->c_img). '" /></td>'.
                                    '<td style="vertical-align:middle;">'.$wrong_correct.'</td>'.
                                    '<td>'.$score.$expl.'</td>'.
								'</tr>';
				$k = 3 - $k;
			}
		}

        $jq_tmpl_html .= "</table></div>";

		return $jq_tmpl_html;
	}
}
?>