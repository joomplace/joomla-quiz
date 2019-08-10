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
class JoomlaquizViewCreateDropdown
{
	public static function getQuestionContent($qdata, $data){
		
		$jq_tmpl_html = "<table align='center' class='jq_mdropdown'>" . "\n";
		foreach ($qdata as $qone) {
			if (!isset($qone->c_left_text)) continue;
			$jq_tmpl_html .= "<tr><td align='left' class='left_text'>".stripslashes($qone->c_left_text)."</td><td align='left' class='right_text'>".$qone->c_right_text."</td>" . "\n";
			$jq_tmpl_html .= "</tr>" . "\n";
		}		
		$jq_tmpl_html .= "</table>" . "\n";
		
		return $jq_tmpl_html;
	}
}

?>