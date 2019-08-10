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
class JoomlaquizViewCaptionBlank
{
	public static function getCaption($q_data, $stu_quiz_id){
		
		$database = JFactory::getDBO();
		if (!$q_data->c_qform){
			$q_data->c_question = str_replace('{answers}', '', $q_data->c_question);
		} elseif ($q_data->c_qform) {
			if (strpos($q_data->c_question, '{answers}') === false) {
				$q_data->c_question .= '{answers}';
			}
				$q_data->c_question = JoomlaquizHelper::Blnk_replace_answers($q_data);
		}
					
		$query = "SELECT * FROM #__quiz_t_blank WHERE c_question_id=".$q_data->c_id;
		$database->setQuery($query);
		$blnk = $database->loadObjectList();
					
		$ret_add = '<form onsubmit="javascript: return false;" name="quest_form'.$q_data->c_id.'"> <div> '.JoomlaquizHelper::Blnk_replace_quest($q_data->c_id, $q_data->c_question, $stu_quiz_id, $q_data->c_qform).' <input type="hidden" name="blnk_cnt"  value="'.count($blnk).'"/> </div> </form>';
				
		return $ret_add;
	}
}

?>
    
		 
		 