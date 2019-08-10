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
class JoomlaquizViewCreateMresponse
{
	public static function getQuestionContent($qdata, $data){
		
		$jq_tmpl_html = "<table class='jq_mresponse'>" . "\n";
		foreach ($qdata as $qone) {
			if (!isset($qone->value)) continue;
			$jq_tmpl_html .= "<tr><td class='jq_input_pos'><input id='quest_choice_".$qone->value."' name='quest_choice' value='".$qone->value."' type='checkbox' ".($qone->c_right == 1? ' checked="checked" ': "")."><label class='quest_pos' for='quest_choice_".$qone->value."'>".stripslashes($qone->text)."</label></td>" . "\n";			
			$jq_tmpl_html .= "</tr>" . "\n";
		}
		$jq_tmpl_html .= "</table>" . "\n";
		
		return $jq_tmpl_html;
	}
}

?>