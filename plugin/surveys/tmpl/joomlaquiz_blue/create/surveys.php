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
class JoomlaquizViewCreateSurveys
{
	public static function getQuestionContent($answer, $data){
		
		$jq_tmpl_html = "<textarea name='survey_box' class='inputbox jq_survey'>".$answer."</textarea>" . "\n";
		return $jq_tmpl_html;
	}
}

?>