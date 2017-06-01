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
        if (!empty($result->c_id))
        {
            $result->tags = new JHelperTags;
            $result->tags->getTagIds($result->c_id, $this->getTable()->type);
        }
		return $result;
	}
		
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomlaquiz.edit.question.data', array());

		if (empty($data)) {
			$data = $this->getItem();
			
			$ordering = $this->getOrdering();
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

    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        if(JFactory::getUser()->authorise('core.create', 'com_joomlaquiz')){
            $form->setFieldAttribute('c_ques_cat', 'allowAdd', 'true');
        }
        parent::preprocessForm($form, $data, $group);
    }


    public function getForm($data = array(), $loadData = true)
	{
		$new_qtype_id = JFactory::getApplication()->input->get('new_qtype_id');
		$this->setState('question.new_qtype_id', $new_qtype_id);
		$ordering = $this->getOrdering();
		
		$form = $this->loadForm('com_joomlaquiz.question', 'question', array('control' => 'jform', 'load_data' => $loadData));
				
		if (empty($form)) {
			return false;
		}

		$this->preprocessForm($form, $data);

		return $form;
	}
	
	public function getOrdering(){
		$db = JFactory::getDBO();
		$query="SELECT c_id, c_quiz_id, ordering as value, CONCAT(ordering, '. ', c_question) as text FROM #__quiz_t_question";
		$db->setQuery($query);
		$ordering_list = $db->loadObjectlist();
		return $ordering_list;
	}

    public function getQuizzesList(){
        $db    = JFactory::getDbo();
        $query="SELECT DISTINCT(`c_id`) AS `value`, `c_title` AS `text` FROM `#__quiz_t_quiz`";
        $db->setQuery($query);
        $quizzes = $db->loadObjectList();
        return $quizzes;
    }
}