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
class JoomlaquizViewCreateDalliclick
{
	public static function getQuestionContent($qdata, $data){
		
		$jq_tmpl_html = "<input type='hidden' value='0' name='c_qform'  /> \n ";
		$jq_tmpl_html .= "<div class='dc_buttons_container'><table align='left' class='jq_mchoice'> \n";
		foreach ($qdata as $qone) {
			if (!isset($qone->text)) continue;
					
			$jq_tmpl_html .= "<tr><td valign='top' class='jq_input_pos' ><button class='dc_button' id='quest_choice_".$qone->value."' name='quest_choice' onclick='clearSquaresStop(this)'>".$qone->text."</button></td>" . "\n";				
			$jq_tmpl_html .= "</tr>" . "\n";
		}			
		$jq_tmpl_html .= "</table></div>" . "\n";
		
		return $jq_tmpl_html;
		
	}
}

?>