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
class JoomlaquizViewReviewSurveys
{
	public static function getReviewContent($review_data, $data){
		$jq_tmpl_html = "<textarea name='survey_box' class='inputbox jq_survey".($data['css_class']?' '.$data['css_class']:'')."' disabled='disabled'>".$review_data."</textarea>" . "\n";
		return $jq_tmpl_html;
	}
}

?>