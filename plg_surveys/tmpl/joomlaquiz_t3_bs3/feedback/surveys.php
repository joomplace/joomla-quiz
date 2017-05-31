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
class JoomlaquizViewFeedbackSurveys
{
	public static function getFeedbackContent($feedback_data, $data){
		
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$jq_tmpl_html = "<strong>".JText::_('COM_JQ_YOUR_ANSWER').":</strong><br />".$feedback_data['answer']."<br /><br /> <strong>".JText::_('COM_QUIZ_RES_MES_SCORE')." ".$data['score']."</strong><br />" . "\n";
		return $jq_tmpl_html;
	}
}

?>