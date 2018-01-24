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

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        if (!(int)$q_data->c_qform){
            $q_data->c_question = preg_replace('/{answers\d+}/i', '', $q_data->c_question);
        } else {
            if (strpos($q_data->c_question, '{answers' ) === false) {

                $query->select('COUNT(*)')
                    ->from($db->qn('#__quiz_t_blank'))
                    ->where($db->qn('c_question_id') .'='. $db->q((int)$q_data->c_id));
                $db->setQuery($query);
                $blanks_count = $db->loadResult();

                if((int)$blanks_count){
                    for($i=0; $i < $blanks_count; $i++){
                        $q_data->c_question .= '{answers'.($i+1).'}';
                    }
                }
            }
            $q_data->c_question = JoomlaquizHelper::Blnk_replace_answers($q_data);
        }

        $query->clear();
        $query->select($db->qn('c_id'))
            ->from($db->qn('#__quiz_t_blank'))
            ->where($db->qn('c_question_id') .'='. $db->q((int)$q_data->c_id));
        $db->setQuery($query);
        $blnk = $db->loadObjectList();

        $ret_add = '<form onsubmit="javascript: return false;" name="quest_form'.$q_data->c_id.'"> <div> '.JoomlaquizHelper::Blnk_replace_quest($q_data->c_id, $q_data->c_question, $stu_quiz_id, $q_data->c_qform).' <input type="hidden" name="blnk_cnt"  value="'.count($blnk).'"/> </div> </form>';

        return $ret_add;
	}
}

?>
    
		 
		 