<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Question model.
 *
 */
class JoomlaquizModelQuestion extends JModelAdmin
{
	protected $text_prefix = 'COM_JOOMLAQUIZ';
		
	public function getTable($type = 'question', $prefix = 'JoomlaquizTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getItem($pk = null)
	{
		$result = parent::getItem($pk);
        $is_set_default = JComponentHelper::getParams('com_joomlaquiz')->get('is_set_default');

        if (!$result->c_id  && $is_set_default) {
            $session = JFactory::getSession();
            $result->c_point = $session->get('jform_c_point_d');
            $result->c_attempts = $session->get('jform_c_attempts_d');
            $result->c_feedback = $session->get('jform_c_feedback_d');
            $result->c_right_message = $session->get('jform_c_right_message_d');
            $result->c_wrong_message = $session->get('jform_c_wrong_message_d');
            $result->c_detailed_feedback = $session->get('jform_c_detailed_feedback_d');
        }

		return $result;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomlaquiz.edit.question.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('question.c_id') == 0) {
				$app = JFactory::getApplication();
				$id = $app->getUserState('com_joomlaquiz.edit.question.c_id');
				if ($id) $data->set('c_id', JFactory::getApplication()->input->getInt('c_id', $id));
			}
			if (!$data->c_quiz_id){
			$data->set('c_quiz_id', JFactory::getApplication()->getUserState('quizzes.filter.quiz_id'));
			}
		}
		
		return $data;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$new_qtype_id = JFactory::getApplication()->input->get('new_qtype_id');
		$this->setState('question.new_qtype_id', $new_qtype_id);
		
		$form = $this->loadForm('com_joomlaquiz.question', 'question', array('control' => 'jform', 'load_data' => $loadData));
				
		if (empty($form)) {
			return false;
		}
		return $form;
	}
}