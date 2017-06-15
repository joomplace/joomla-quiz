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
class JoomlaquizViewFeedbackDropdown
{
	public static function getFeedbackContent($feedback_data, $data){
		
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$jq_tmpl_html = "<div style='width:100%;'>";
		$jq_tmpl_html .= '<table id="quest_table" width="100%">
							<tr>
								<td class="sectiontableheader" width="50%">'.JText::_('COM_JQ_POSSIBLE_ANSWERS').'</td>
								<td width="15%" class="sectiontableheader">'.JText::_('COM_QUIZ_CORRECT').'</td>
								<td width="15%" class="sectiontableheader">'.JText::_('COM_JQ_YOUR_CHOICE').'</td>
								<td class="sectiontableheader" width="auto">'.JText::_('COM_JQ_PEOPLE_STATISTIC').'</td>';
		$k = 2;
		if(is_array($feedback_data['qdata']))
		foreach($feedback_data['qdata'] as $t) {
			$jq_tmpl_html .= '<tr class="sectiontableentry'.$k.'">'.
								'<td>'. stripslashes($t['c_left_text']). '</td>'.
								'<td>'. stripslashes($t['c_right_text']). '</td>'.
								'<td>'. stripslashes($t['c_sel_text']). '</td>'.
								'<td></td>
							</tr>';
			$k = 3 - $k;
		}
	
		if(isset($feedback_data['qdata'][0]['past_this'])) {							
			$jq_tmpl_html .= '<tr><td colspan="4" class="review_statistic">'.JText::_('COM_QUIZ_RST_PPAST').' '.$feedback_data['qdata'][0]['past_this'].' '.JText::_('COM_QUIZ_RST_PPAST_TIMES').', '.$feedback_data['qdata'][0]['rht_proc'].'% '.JText::_('COM_QUIZ_RST_ARIGHT').'</td></tr>';
			$jq_tmpl_html .= "<tr><td colspan='4' valign='top'><br /><strong>".JText::_('COM_QUIZ_RES_MES_SCORE')." ".$feedback_data['qdata'][0]['score']."</strong><br /></td>" . "\n";
			$jq_tmpl_html .= "</tr>
							</table>" . "\n";
			$jq_tmpl_html.='</div>' . "\n";
		}
		
		return $jq_tmpl_html;
	}
}

?>