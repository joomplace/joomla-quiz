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
class JoomlaquizViewCaptionHotspot
{
	public static function getCaption($q_data, $stu_quiz_id){
		
		$ret_add = '<div style="clear:both;">'.$q_data->c_question.'</div><form onsubmit=\'javascript: return false;\' name=\'quest_form'.$q_data->c_id.'\'><input type=\'hidden\' name=\'hotspot_x\' value=\'0\' /><input type=\'hidden\' name=\'hotspot_y\' value=\'0\' /></form><div id=\'quiz_'.$q_data->c_id.'hs_container_add\'>&nbsp;</div><div  id="div_qoption'.$q_data->c_id.'">&nbsp;</div>';
				
		return $ret_add;
	}
}

?>
    
		 
		 