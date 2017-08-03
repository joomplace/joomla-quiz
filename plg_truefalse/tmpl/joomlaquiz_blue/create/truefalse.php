<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaquiz Deluxe class
 */
class JoomlaquizViewCreateTruefalse
{
	public static function getQuestionContent($qdata, $data){
		
		$jq_tmpl_html = '';
		if (!$data['q_data']->c_qform) { // radiobuttons
			if ($data['q_data']->c_layout == 0) {
				$jq_tmpl_html = "<input type='hidden' value='0' name='c_qform'  /> \n ";
				$jq_tmpl_html .= "<table align='left' class='jq_mchoice'> \n";
				foreach ($qdata as $qone) {
					if (!isset($qone->value)) continue;

					$class = $qone->c_right ? 'ques_top' : 'ques_down';

					$qone->c_right = 0;
					
					$jq_tmpl_html .= "<tr><td valign='top' class='jq_input_pos' ><input id='quest_choice_".$qone->value."' name='quest_choice' value='".$qone->value."' type='radio'  ".($qone->c_right == 1? ' checked="checked" ':"")."></td><td align='left' class='quest_pos'><label class='$class' for='quest_choice_".$qone->value."'>".stripslashes($qone->text)."</label></td>" . "\n";				
					$jq_tmpl_html .= "</tr>" . "\n";
				}			
				$jq_tmpl_html .= "</table>" . "\n";
			}
		}
		
		return $jq_tmpl_html;
	}
}

?>