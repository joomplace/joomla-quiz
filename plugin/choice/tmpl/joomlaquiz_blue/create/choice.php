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
class JoomlaquizViewCreateChoice
{
	public static function getQuestionContent($qdata, $data){
		
		$jq_tmpl_html = '';
		if (!$data['q_data']->c_qform) { // radiobuttons
			if ($data['q_data']->c_layout == 0) {
				$jq_tmpl_html = "<input type='hidden' value='0' name='c_qform'  /> \n ";
				$jq_tmpl_html .= "<table align='left' class='jq_mchoice'> \n";
				foreach ($qdata as $qone) {
					if (!isset($qone->value)) continue;
					
					$jq_tmpl_html .= "<tr><td valign='top' class='jq_input_pos' ><input id='quest_choice_".$qone->value."' name='quest_choice' value='".$qone->value."' type='radio'  ".($qone->c_right == 1? ' checked="checked" ':"")."></td><td align='left' class='quest_pos'><label for='quest_choice_".$qone->value."'>".stripslashes($qone->text)."</label></td>" . "\n";				
					$jq_tmpl_html .= "</tr>" . "\n";
				}			
				$jq_tmpl_html .= "</table>" . "\n";
			} elseif ($data['q_data']->c_layout == 1){	
				$jq_tmpl_html = "<input type='hidden' value='0' name='c_qform'  /> \n ";
				$jq_tmpl_html .= "<div class='jq_mchoice'> \n";
				foreach ($qdata as $qone) {
					if (!isset($qone->value)) continue;
					$jq_tmpl_html .= "<div class='msq_option'>";
					
					$jq_tmpl_html .= "<div class='jq_input_pos' ><input id='quest_choice_".$qone->value."' name='quest_choice' value='".$qone->value."' type='radio'  ".($qone->c_right == 1? ' checked="checked" ':"")."></div><div class='quest_pos'><label for='quest_choice_".$qone->value."'>".stripslashes($qone->text)."</label></div>" . "\n";				
					
					$jq_tmpl_html .= "</div>" . "\n";
				}
				$jq_tmpl_html .= "</div>" . "\n";
			}
		} else { // select list
			$jq_tmpl_html = '<input type="hidden" value="1" name="c_qform"  />'. "\n";
			$jq_tmpl_html .= '<select name="quest_choice" class="inputbox jq_mchoice_select">'. "\n";
			$qoptions = '';
			$qoptions_selected = false;
			foreach ($qdata as $qone) {
				if (!isset($qone->value)) continue;
				if ($qone->c_right == 1) $qoptions_selected = true;
				$qoptions .= "<option value='".$qone->value."' ".($qone->c_right == 1? "selected='selected' ":"").">".strip_tags(stripslashes($qone->text))."</option>". "\n";
			}
			
			if ($qoptions_selected){
				$qoptions = "<option value='0'>".JText::_('COM_JQ_PLEASE_SELECT')."</option>". "\n". $qoptions;
			} else {
				$qoptions = "<option value='0' selected='selected'>".JText::_('COM_JQ_PLEASE_SELECT')."</option>". "\n" . $qoptions;
			}
			
			$jq_tmpl_html .= $qoptions.'</select>'. "\n";
		}
		
		return $jq_tmpl_html;
	}
}

?>