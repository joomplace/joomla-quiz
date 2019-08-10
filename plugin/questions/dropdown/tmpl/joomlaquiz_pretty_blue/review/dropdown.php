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
class JoomlaquizViewReviewDropdown
{
	public static function getReviewContent($review_data, $data){
		
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$jq_tmpl_html = "<table align='center' class='jq_mdropdown'>" . "\n";
		foreach ($review_data as $qone) {
			if (!isset($qone->c_left_text)) continue;
			$jq_tmpl_html .= "<tr><td align='left' class='quest_pos'>".stripslashes($qone->c_left_text)."</td><td align='left' class='quest_pos'>".stripslashes($qone->c_right_text)."</td>" . "\n";
			$jq_tmpl_html .= "</tr>" . "\n";
		}		
		$jq_tmpl_html .= "</table>";
		
		$jq_tmpl_html .= "<table align='left' width='100%'><tr><td class='review_statistic'>".$review_data[0]->overal;
		$jq_tmpl_html .= '<br/>';		
		$jq_tmpl_html .= '<table width="100%" id=\'quest_table\'><tr class="sectiontableheader"><td colspan="3" align="left"><strong>'.JText::_('COM_QUIZ_ANSWER').'</strong></td></tr>';
		if (is_array($review_data[0]->answers))
		foreach($review_data[0]->answers as $answer){
			$jq_tmpl_html .= '<tr class="sectiontableentry1">
								<td style="padding-left: 10px;" align="left">'. stripslashes($answer['c_left_text']) . '</td>
								<td style="padding-left: 10px;" align="left">'. stripslashes($answer['c_sel_text']) . '</td>
								<td width="40%"></td>
							</tr>';	
		}
		$jq_tmpl_html .= '</table>';
		
		$jq_tmpl_html .= "</td></tr></table>" . "\n";
		
		return $jq_tmpl_html;
	}
}

?>