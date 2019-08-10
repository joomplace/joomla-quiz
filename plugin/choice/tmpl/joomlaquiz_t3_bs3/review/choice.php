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
class JoomlaquizViewReviewChoice
{
	public static function getReviewContent($review_data, $data){
		
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$jq_tmpl_html = "<input type='hidden' value='{$data['q_data']->c_qform}' name='c_qform'  /> \n ";
		$jq_tmpl_html .= "<table class='jq_mchoice'> \n";
		foreach ($review_data as $qone) {
			if (!isset($qone->value)) continue;
				
			$jq_tmpl_html .= "<tr><td class='jq_input_pos' ><input disabled='disabled' id='quest_choice_".$qone->value."' name='quest_choice' value='".$qone->value."' type='radio' ".($qone->c_right == 1? ' checked="checked" ':"")."><label class='quest_pos' for='quest_choice_".$qone->value."'>".stripslashes($qone->text)."</label></td>" . "\n";				
			if (isset($qone->statistic)) {
				$jq_tmpl_html .= "<td class='choice_static'>".$qone->statistic."</td>" . "\n";
			}
			$jq_tmpl_html .= "</tr>" . "\n";
		}		
		$jq_tmpl_html .= "</table>";
		
		$jq_tmpl_html .= "<table class='jq_mchoice_overal'><tr><td class='review_statistic'>".$review_data[0]->overal;
	
		$jq_tmpl_html .= '<br/>';		
		$jq_tmpl_html .= '<table width="100%" id="quest_table"><tr class="sectiontableheader"><td align="left"><strong>'.JText::_('COM_QUIZ_ANSWER').'</strong></td></tr>';		
		$jq_tmpl_html .= '<tr class="sectiontableentry1"><td style="padding-left: 10px;" align="left">'.$review_data[0]->answer.'</td></tr></table>';
					
		$jq_tmpl_html .= "</td></tr></table>" . "\n";
		
		return $jq_tmpl_html;
	}
}

?>