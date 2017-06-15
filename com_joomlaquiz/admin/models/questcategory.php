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
 * Question category model.
 *
 */
class JoomlaquizModelQuestcategory extends JModelAdmin
{
	protected $text_prefix = 'COM_JOOMLAQUIZ';
		
	public function getTable($type = 'Questcategory', $prefix = 'JoomlaquizTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getItem($pk = null)
	{
		$result = parent::getItem($pk);
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
		$data = JFactory::getApplication()->getUserState('com_joomlaquiz.edit.questcategory.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('questcategory.qc_id') == 0) {
				$app = JFactory::getApplication();
				$id = $app->getUserState('com_joomlaquiz.edit.questcategory.qc_id');
				if ($id) $data->set('qc_id', JFactory::getApplication()->input->getInt('qc_id', $id));
			}
		}
		
		return $data;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$app	= JFactory::getApplication();
		$form = $this->loadForm('com_joomlaquiz.questcategory', 'questcategory', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	public function getTags(){
		$db = JFactory::getDBO();
		
		$query = 'SELECT DISTINCT(qc_tag) AS value, qc_tag AS text'
		. ' FROM #__quiz_q_cat'
		. ' WHERE TRIM(qc_tag) <> \'\''
		. ' ORDER BY qc_tag'
		;
		$db->setQuery( $query );
		$qc_tag[] = JHTML::_('select.option', '', JText::_('COM_JOOMLAQUIZ_SELECT_MAIN_CATEGORY') );
		$qc_tag = array_merge( $qc_tag, $db->loadObjectList() );
		
		return $qc_tag;
	}
	
}