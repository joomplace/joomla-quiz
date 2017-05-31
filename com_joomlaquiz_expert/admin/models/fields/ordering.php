<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldOrdering extends JFormField
{
    protected $type = 'ordering';

    public function getInput()
    {
		$app = JFactory::getApplication();
		$filter_quiz_id = $app->getUserStateFromRequest('questions.filter.quiz_id', 'filter_quiz_id', '');
		
		$db    = JFactory::getDbo();
		if(!$filter_quiz_id){
			$c_id = $app->input->get('c_id', 0);
			if($c_id){
				$db->setQuery("SELECT `c_quiz_id` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
				$c_quiz_id = $db->loadResult();
			} else {
				$c_quiz_id = 0;
			}
			
			$filter_quiz_id = $c_quiz_id;
		}
		
        $query="SELECT `ordering` as value, CONCAT(`ordering`, '. ', `c_question`) as text FROM #__quiz_t_question WHERE `c_quiz_id` = '".$filter_quiz_id."' ORDER BY `ordering`";
        $db->setQuery($query);
        $ordering_list = $db->loadObjectlist();

        $query="SELECT DISTINCT(`c_id`) FROM `#__quiz_t_quiz`";
        $db->setQuery($query);
        $quizes = $db->loadObjectList();

        $list = '<select name="'.$this->name.'" id="'.$this->id.'" style="width:220px;">';
        foreach($ordering_list as $question) {
			$selected = ($question->value == $this->value) ? "selected='selected'" : "";
			$list .= '<option value="'.$question->value.'" '.$selected.'>'.strip_tags($question->text).'</option>';
        }
        
        $list .= '</select>';

        return $list;
    }
}