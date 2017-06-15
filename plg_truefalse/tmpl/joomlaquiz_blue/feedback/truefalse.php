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
class JoomlaquizViewFeedbackTruefalse
{
	public static function getFeedbackContent($feedback_data, $data){
		
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$jq_tmpl_html = "<table align='left' class='jq_mchoice_fdb' width='100%'>
							<tr>
								<td class='sectiontableheader' width='auto'>".JText::_('COM_JQ_POSSIBLE_ANSWERS')."</td>
								<td class='sectiontableheader' align='center' valign='top' width='15%'>".JText::_('COM_QUIZ_CORRECT')."</td>
								<td valign='top' align='center' width='15%' class='sectiontableheader'>".JText::_('COM_JQ_YOUR_CHOICE')."</td>
								<td valign='top' width='auto' class='sectiontableheader'>".JText::_('COM_JQ_PEOPLE_STATISTIC')."</td>
							</tr>" . "\n";
		$k = 2;
		foreach ($feedback_data['choice_data'] as $qone) {				
			$jq_tmpl_html .= "<tr class='sectiontableentry".$k."'>
								<td align='left' class='quest_pos' >".stripslashes($qone->text)."</td>
								<td valign='top' align='center'>".($qone->c_right == 1?"<img src='".JURI::root()."components/com_joomlaquiz/assets/images/tick.png' border=0>":"&nbsp;")."</td>
								<td valign='top' align='center'>".
									(in_array($qone->value, $feedback_data['uanswer'])? "<img src='".JURI::root()."components/com_joomlaquiz/assets/images/".($qone->c_right==1?'tick.png': 'tickr.png')."' border=0>":"&nbsp;").
								"</td>" . "\n";
			if(isset($qone->statistic))	{
				$jq_tmpl_html .= "<td class='choice_static' valign='top'>".$qone->statistic."</td>" . "\n";
			}
			$jq_tmpl_html .= "</tr>" . "\n";
			$k = 3 - $k;
		}
		if(isset($feedback_data['choice_data'][0]->past_this)){
			$jq_tmpl_html .= "<tr>
								<td colspan='4' class='review_statistic' valign='top'>".JText::_('COM_QUIZ_RST_PANSW')." ".$feedback_data['choice_data'][0]->past_this." ".JText::_('COM_QUIZ_RST_PANSW_TIMES')."</td>
							  </tr>" . "\n";
		}
		$jq_tmpl_html .= "<tr>
							<td colspan='4' valign='top'><br /><strong>".JText::_('COM_QUIZ_RES_MES_SCORE')." ".$feedback_data['choice_data'][0]->score."</strong><br /></td>" . "\n";
		$jq_tmpl_html .= "</tr>
						</table>" . "\n";
		
		return $jq_tmpl_html;
	}
}

?>