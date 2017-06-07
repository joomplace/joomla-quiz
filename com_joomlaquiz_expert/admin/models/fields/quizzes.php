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

class JFormFieldQuizzes extends JFormField
{
    protected $type = 'quizzes';

    public function getInput()
    {
		$app = JFactory::getApplication();
		$filter_quiz_id = $app->getUserStateFromRequest('questions.filter.quiz_id', 'filter_quiz_id', '');
		
		$this->value = ($this->value) ? $this->value : $filter_quiz_id;
		
        $db    = JFactory::getDbo();

        $query="SELECT DISTINCT(`c_id`) AS `value`, `c_title` AS `text` FROM `#__quiz_t_quiz`";
        $db->setQuery($query);
        $quizzes = $db->loadObjectList();
        $selected = ($this->value == 0) ? "selected='selected'" : "";
		
        $list = '<select name="'.$this->name.'" id="'.$this->id.'" style="width:220px;" onchange="changeDynaList( \'jform[ordering]\', questions, $(\'jform_c_quiz_id\').getSelected().get(\'value\'), 0, 0);">';
        $list .= '<option value="0" '.$selected.'>'.JTEXT::_('COM_JOOMLAQUIZ_SUBMENU_QUESTIONS_POOL').'</option>';
        foreach($quizzes as $quiz) {
			$selected = ($this->value == $quiz->value) ? "selected='selected'" : "";
            $list .= '<option value="'.$quiz->value.'" '.$selected.'>'.$quiz->text.'</option>';
        }
        $list .= '</select>';

        return $list;
    }
}