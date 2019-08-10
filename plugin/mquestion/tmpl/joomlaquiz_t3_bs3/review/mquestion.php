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
class JoomlaquizViewReviewMquestion
{
	public static function getReviewContent($review_data, $data){
		$jq_tmpl_html = "<table align='left' class='jq_mquestions'>" . "\n";
		foreach ($review_data as $qone) {
			$jq_tmpl_html .= "<tr>"
				. "<td align='left' class='quest_pos'>". stripslashes($qone->text) . "</td>"
				. "<td align='left' class='jq_input_pos'>"
				. "<input id='quest_choice_1_".$qone->value."' name='quest_choice_".$qone->value."' value='".$qone->value."' type='radio' ".(($qone->c_right == 1 )? ' checked="checked" ': "")." disabled='disabled' >"
				. "<label for='quest_choice_1_".$qone->value."'>".stripslashes($review_data[0]->title_true)."</label> "
				. "<input id='quest_choice_0_".$qone->value."' name='quest_choice_".$qone->value."' value='".$qone->value."' type='radio' ".(($qone->c_right == 0 )? ' checked="checked" ': "")." disabled='disabled' >"
				. "<label for='quest_choice_0_".(isset($qone->value)?$qone->value:"")."'>".stripslashes($review_data[0]->title_false)."</label> "
				. "</td><td>&nbsp;&nbsp;".(isset($qone->statistic_true)? $qone->statistic_true: '')."<br/>&nbsp;&nbsp;".(isset($qone->statistic_false)? $qone->statistic_false: '')."</td>"
				. "\n";

			$jq_tmpl_html .= "</tr>" . "\n";
		}		
		$jq_tmpl_html .= "</table>";

		$jq_tmpl_html .= "<table align='left' width='100%'><tr><td class='review_statistic'>".$review_data[0]->overal;
		$jq_tmpl_html .= '<br/>';

		$jq_tmpl_html .= '<table width="100%" id=\'quest_table\'><tr class="sectiontableheader"><td align="left"><strong>'.JText::_('COM_QUIZ_ANSWER').'</strong></td></tr>';
		$jq_tmpl_html .= '<tr class="sectiontableentry1"><td style="padding-left: 10px;" align="left">'.$review_data[0]->answer.'</td></tr></table>';
			
		$jq_tmpl_html .= "</td></tr></table>" . "\n";
		return $jq_tmpl_html;
	}
}

?>