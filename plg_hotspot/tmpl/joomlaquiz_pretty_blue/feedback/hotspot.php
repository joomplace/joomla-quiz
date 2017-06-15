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
class JoomlaquizViewFeedbackHotspot
{
	public static function getFeedbackContent($feedback_data, $data){
		
		$isMobile = JFactory::getApplication()->client->mobile;

		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$path_str = '';
		if(count($feedback_data['qdata']['c_paths'])){
			foreach($feedback_data['qdata']['c_paths'] as $path){
				$path_str .= '<path style="fill-opacity: 0.5;" fill="#147edb" stroke="#ffffff" d="'.$path.'" stroke-width="3" fill-opacity="0.5"/>'."\n";
			}
		}
		
		if($isMobile){

			$jq_tmpl_html = '
				<center>
				<div id="foo_'.$feedback_data['qdata']['quest_id'].'" style="margin-top:15px;" class="hotspot" data-qid="'.$feedback_data['qdata']['quest_id'].'">
					<svg version="1.1" xmlns="http://www.w3.org/2000/svg" id="canvas_'.$feedback_data['qdata']['quest_id'].'">
					<image x="0" y="0" preserveAspectRatio="none" xlink:href="'.JURI::root().'images/joomlaquiz/images/'.$feedback_data['qdata']['c_image'].'" id="img_'.$feedback_data['qdata']['quest_id'].'"/>
					
					<circle cx="'.$feedback_data['qdata']['c_select_x'].'" cy="'.$feedback_data['qdata']['c_select_y'].'" r="5" fill="#ffa500" stroke="#ff0000" style=""/>
					</svg>
				</div>';
		} else {

			$jq_tmpl_html = '
				<center>
				<div id="foo_'.$feedback_data['qdata']['quest_id'].'" style="margin-top:15px;">
					<svg height="'.$feedback_data['qdata']['height'].'" version="1.1" width="'.$feedback_data['qdata']['width'].'" xmlns="http://www.w3.org/2000/svg">
					<image x="0" y="0" width="'.$feedback_data['qdata']['width'].'" height="'.$feedback_data['qdata']['height'].'" preserveAspectRatio="none" xlink:href="'.JURI::root().'images/joomlaquiz/images/'.$feedback_data['qdata']['c_image'].'"/>
					'.$path_str.'
					<circle cx="'.$feedback_data['qdata']['c_select_x'].'" cy="'.$feedback_data['qdata']['c_select_y'].'" r="5" fill="#ffa500" stroke="#ff0000" style=""/>
					</svg>
				</div>';
		}
				
		$jq_tmpl_html .= '
				<table>
				<tr>
					<td class="review_statistic">'.JText::_('COM_QUIZ_RST_PPAST').' '.$feedback_data['qdata']['past_this'].' '.JText::_('COM_QUIZ_RST_PPAST_TIMES').', '.$feedback_data['qdata']['rht_proc'].'% '.JText::_('COM_QUIZ_RST_ARIGHT').'</td>
				</tr>
				<tr>
					<td valign="top"><br/><strong>'.JText::_('COM_QUIZ_RES_MES_SCORE').' '.$feedback_data['qdata']['score'].'</strong><br /></td>
				</tr>
				</table>
			</center>';

		return $jq_tmpl_html;
	}
}

?>