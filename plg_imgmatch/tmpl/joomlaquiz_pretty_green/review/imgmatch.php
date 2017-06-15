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
class JoomlaquizViewReviewImgmatch
{
	public static function getReviewContent($qdata, $data){
		$jq_tmpl_html = "<div style='width:100%;'>";
		$jq_tmpl_html .= '<table width="100%" id="quest_table">
							<tr>
								<td class="sectiontableheader">'.JText::_('COM_JQ_POSSIBLE_ANSWERS').'</td>
								<td class="sectiontableheader">'.JText::_('COM_JQ_YOUR_CHOICE').'</td>';
		$k = 2;
		if(is_array($qdata))
		foreach($qdata as $t) {
			$correct = ($t['c_correct']) ? '<img src="'.JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_pretty_green/images/result_true_green.png" style="vertical-align:middle;margin-left:10px;"/>' : '<img src="'.JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_pretty_green/images/result_false.png" style="vertical-align:middle;margin-left:10px;"/>';
			$jq_tmpl_html .= '<tr class="sectiontableentry'.$k.'">'.
								'<td><img src="'. JURI::root().'images/joomlaquiz/images/resize/'.($t['c_left_text']). '" style="height:100px;" /></td>'.
								'<td style="vertical-align:middle;"><img src="'. JURI::root().'images/joomlaquiz/images/resize/'.($t['c_sel_text']). '" style="height:100px;" style="vertical-align:middle;"/>'.$correct.'</td>'.
								'</tr>';
			$k = 3 - $k;
		}
		$jq_tmpl_html .= "</table>" . "\n";
		$jq_tmpl_html.='</div>' . "\n";
		
		return $jq_tmpl_html;
	}
}

?>