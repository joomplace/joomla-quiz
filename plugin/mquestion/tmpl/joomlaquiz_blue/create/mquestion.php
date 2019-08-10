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
class JoomlaquizViewCreateMquestion
{
	public static function getQuestionContent($qdata, $data){
		
		$jq_tmpl_html = "<input type='hidden' value='{$data['q_data']->c_qform}' name='c_qform'  /> \n ";
		$jq_tmpl_html .= "<table align='left' class='jq_mquestions'>" . "\n";			
		foreach ($qdata as $qone) {
			if (!isset($qone->value)) continue;

			$jq_tmpl_html .= "<tr>"
				. "<td align='left' class='quest_pos'>". stripslashes($qone->text) ."</td>"
				. "<td align='left' class='jq_input_pos'>"
				.	($data['q_data']->c_qform?
					//selectllist
						'<select name="quest_choice_'.$qone->value.'" class="inputbox jq_mchoice_select">'. "\n"
						."<option value='0' ".($qone->c_right == -1? ' selected="selected" ': "").">".JText::_('COM_JQ_PLEASE_SELECT')."</option>". "\n"
						."<option value='2' ".($qone->c_right == 1? ' selected="selected" ': "").">".strip_tags(stripslashes($qone->title_true))."</option>". "\n"
						."<option value='1' ".($qone->c_right == 0? ' selected="selected" ': "").">".strip_tags(stripslashes($qone->title_false))."</option>". "\n"
						.'</select>'. "\n"
					://radiobuttons
						 "<input id='quest_choice_1_".$qone->value."' name='quest_choice_".$qone->value."' value='".$qone->value."' type='radio' ".($qone->c_right == 1? ' checked="checked" ': "").">"
						. "<label for='quest_choice_1_".$qone->value."'>".stripslashes($qone->title_true)."</label>"
						. "<input id='quest_choice_0_".$qone->value."' name='quest_choice_".$qone->value."' value='".$qone->value."' type='radio' ".($qone->c_right == 0? ' checked="checked" ': "").">"
						. "<label for='quest_choice_0_".$qone->value."'>".stripslashes($qone->title_false)."</label>"
					)
				. "</td>"
				. "\n";
			$jq_tmpl_html .= "</tr>" . "\n";
		}
		$jq_tmpl_html .= "</table>" . "\n";
		
		return $jq_tmpl_html;
	}
}

?>