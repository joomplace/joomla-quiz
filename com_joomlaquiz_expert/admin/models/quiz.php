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
 * Quiz model.
 *
 */
class JoomlaquizModelQuiz extends JModelAdmin
{
	protected $text_prefix = 'COM_JOOMLAQUIZ';
		
	public function getTable($type = 'quiz', $prefix = 'JoomlaquizTable', $config = array())
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
		/* assets auto creation (fix for old data) */
		/*
		$asset = $this->getTable('Asset', 'JTable');
		$asset->loadByName('com_joomlaquiz');
		$parentId = $asset->id;
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('c_id,c_title')
			->from('#__quiz_t_quiz');
		$qzs = $db->setQuery($query)->loadObjectList();
		$table = $this->getTable();
		foreach($qzs as $q){
			$asset = $this->getTable('Asset', 'JTable');
			$asset->loadByName('com_joomlaquiz.quiz.'.$q->c_id);
			if(!$asset->id){
				$asset->name = 'com_joomlaquiz.quiz.'.$q->c_id;
				$asset->rules = '{}';
				$asset->title = $q->c_title;
				$asset->parent_id = $parentId;
				$asset->setLocation($parentId, 'last-child');
				$asset->check();
				$asset->store();
			}
			$query->clear();
			$query->update('`#__quiz_t_quiz`')
				->where('`c_id` = '.$q->c_id)
				->set('`asset_id` = '.$asset->id);
			$db->setQuery($query)->execute();
		}
		*/
		
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomlaquiz.edit.quiz.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('quiz.c_id') == 0) {
				$app = JFactory::getApplication();
				$id = $app->getUserState('com_joomlaquiz.edit.quiz.c_id');
				if ($id) $data->set('c_id', JFactory::getApplication()->input->getInt('c_id', $id));
			}
		}
		
		return $data;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$app	= JFactory::getApplication();
		$form = $this->loadForm('com_joomlaquiz.quiz', 'quiz', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	public function getCategories(){
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM #__quiz_t_category order by c_category";
		$db->setQuery( $query );
		$jq_cats = $db->loadObjectList();
		
		return $jq_cats;
	}
	
	public function getCertificates(){
		$db = JFactory::getDBO();
		
		$jq_cert = array();
		$query = "SELECT id as value, cert_name as text FROM #__quiz_certificates order by cert_file";
		$db->setQuery( $query );
		$jq_cert = $db->loadObjectList();
		
		return $jq_cert;
	}
	
	public function getTemplates(){
		$db = JFactory::getDBO();
		
		$jq_temps = array();
		$query = "SELECT * FROM #__quiz_templates order by id";
		$db->setQuery( $query );
		$jq_temps = $db->loadObjectList();
		
		return $jq_temps;
	}
	
	public function getQuizData($quiz_id)
	{
		$db = JFactory::getDBO();
		$return = array();
		
		$feed_opres = array();
		if($quiz_id){
			$query = "SELECT * FROM #__quiz_feed_option WHERE quiz_id=".$quiz_id;
			$db->setQuery( $query );
			$feed_opres = $db->loadObjectList();
		}
		$return['feed_opres'] = $feed_opres;
		
		$return['if_pool'] = array();
		if($quiz_id){
			$query = "SELECT * FROM #__quiz_pool WHERE q_id=".$quiz_id;
			$db->setQuery($query);
			$return['if_pool'] =  $db->loadObjectList();
		}
		
		$query = 'SELECT id AS value, title AS text'
		. ' FROM #__categories'
		. ' WHERE `parent_id` = "1" AND `extension` = "com_joomlaquiz.questions" AND `published` = "1"'
		. ' ORDER BY lft'
		;
		$db->setQuery( $query );
		$qc_tag[] = JHTML::_('select.option', '', JText::_('COM_JOOMLAQUIZ_CHOOSE_HEAD_CATEGORY'));
		$qc_tag = array_merge( $qc_tag, $db->loadObjectList() );
		foreach($qc_tag as $i=>$tag) {
			if($tag->value) {
				$qc_tag[$i]->value = 'head_category_'.$tag->value;
			}
		}
		$return['head_cat_arr'] = $qc_tag;
				
		$jq_qcat = array();
		$query = "SELECT id as value, title as text, parent_id AS head_category FROM #__categories WHERE `extension` = 'com_joomlaquiz.questions' AND `published` = '1' ORDER BY parent_id, lft";
		$db->setQuery( $query );
		$jq_qcat =  $db->loadObjectList();
		$return['jq_pool_cat'] = $jq_qcat;
		
		$return['q_count'] = '';
		if($quiz_id){
			$db->setQuery("SELECT `q_count` FROM #__quiz_pool WHERE `q_id` = '".$quiz_id."'");
			$return['q_count'] = $db->loadResult();
		}
		
		return $return;
	}
}