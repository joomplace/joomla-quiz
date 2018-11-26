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
class JoomlaquizModelLpath extends JModelAdmin
{
	protected $text_prefix = 'COM_JOOMLAQUIZ';
		
	public function getTable($type = 'lpath', $prefix = 'JoomlaquizTable', $config = array())
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
		$data = JFactory::getApplication()->getUserState('com_joomlaquiz.edit.lpath.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('lpath.id') == 0) {
				$app = JFactory::getApplication();
				$id = $app->getUserState('com_joomlaquiz.edit.lpath.id');
				if ($id) $data->set('id', JFactory::getApplication()->input->getInt('id', $id));
			}
		}
		
		return $data;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$app	= JFactory::getApplication();
		$form = $this->loadForm('com_joomlaquiz.lpath', 'lpath', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	public function getLpathAll($lid){
		$database = JFactory::getDBO();
		if($lid) {
			$query = 'SELECT l_q.qid AS id, IF(l_q.type = \'q\', q.c_title, c.title) AS title, l_q.type'
			. ' FROM #__quiz_lpath_quiz AS l_q'
			. ' LEFT JOIN #__quiz_t_quiz AS q ON (q.c_id = l_q.qid AND l_q.type = \'q\')'
			. ' LEFT JOIN #__content AS c ON (c.id = l_q.qid AND l_q.type = \'a\')'
			. ' WHERE lid = ' . $lid
			. ' ORDER BY l_q.order'
			;
			$database->setQuery($query);
			$lpath_all = $database->loadObjectList();
		} else {
			$lpath_all = array();
		}
		
		return $lpath_all;
	}
	
	public function getQuizzes($lid)
	{
		$database = JFactory::getDBO();
		
		$return = array();
		$query = "SELECT c_id AS value, c_title AS text"
		. "\n FROM #__quiz_t_quiz"
		. "\n WHERE published = 1"
		. "\n ORDER BY c_title"
		;
		$database->setQuery( $query );
		$return['all_quizzes'] = $database->loadObjectList();
				
		if($lid) {
			$query = 'SELECT q.`c_title` AS title, l_q.`qid` AS id, l_q.`order`'
			. ' FROM #__quiz_lpath_quiz AS l_q'
			. ' INNER JOIN #__quiz_t_quiz AS q ON q.c_id = l_q.qid'
			. ' WHERE lid = ' . $lid . ' AND `type` = \'q\''
			. ' ORDER BY l_q.order'
			;
			$database->setQuery($query);
			$lpath_quiz = $database->loadObjectList('id');		
		} else {
			$lpath_quiz = array();
		}
				
		$return['lpath_quiz'] = $lpath_quiz;
		
		$query = "SELECT `c_id` AS value, `c_title` AS text"
		. "\n FROM #__quiz_t_quiz"
		. "\n WHERE `published` = 1" . (!empty($lpath_quiz) ? ' AND `c_id` NOT IN (' . implode(',', array_keys($lpath_quiz)) . ')' : '')
		. "\n ORDER BY c_title"
		;
		$database->setQuery( $query );
		$quizzes[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_SELECT_QUIZ') );
		$quizzes = array_merge( $quizzes, $database->loadObjectList() );
		$return['all_quizzes_list'] = $quizzes;
		
		return $return;
	}
	
	public function getArticles($lid)
	{
		$database = JFactory::getDBO();
		
		$return = array();
		
		if($lid){
			$query = 'SELECT c.`title` AS title, c.id AS id, l_q.`order`'
			. ' FROM #__quiz_lpath_quiz AS l_q'
			. ' INNER JOIN #__content AS c ON c.id = l_q.qid'
			. ' WHERE lid = ' . $lid . ' AND `type` = \'a\''
			. ' ORDER BY l_q.order'
			;
			$database->setQuery($query);
			$lpath_article = $database->loadObjectList('id');
		} else {
			$lpath_article = array();
		}
		$return['lpath_article'] = $lpath_article;
		
		$catid_where = "";
		$c_catid = JComponentHelper::getParams('com_joomlaquiz')->get('lp_content_catid', 0);

        if(!JComponentHelper::getParams('com_joomlaquiz')->get('include_articles_from_subcats', 0)){
			$catid_where = ($c_catid) ? " AND `catid` = '".$c_catid."'" : "";
		} else {
			$cat_ids = $this->getAllCategories($c_catid);
			if(!empty($cat_ids)){
				$catid_where = " AND `catid` IN (".implode(",", $cat_ids).")";
			}
		}
		
		$query = "SELECT `id` AS value, `title` AS text"
		. "\n FROM #__content"
		. "\n WHERE `state` = 1" . $catid_where
		. (!empty($lpath_article) ? ' AND `id` NOT IN (' . implode(',', array_keys($lpath_article)) . ')' : '')
		. "\n ORDER BY title"
		;
		$database->setQuery( $query );
		$articles[] = JHTML::_('select.option', '0', JText::_('COM_JOOMLAQUIZ_SELECT_ARTICLE') );
		$articles = array_merge( $articles, $database->loadObjectList() );
		
		$return['articles'] = $articles;
		
		$query = "SELECT `id` AS value, `title` AS text"
		. "\n FROM #__content"
		. "\n WHERE `state` = 1 AND catid = " . $c_catid
		. "\n ORDER BY title"
		;
		$database->setQuery( $query );
		$all_articles = $database->loadObjectList();
		
		$return['all_articles'] = $all_articles;
		
		return $return;
	}

	public function getAllCategories($c_catid)
	{
		$cat_ids = array();
		$database = JFactory::getDBO();

		if($c_catid){

			$cat_exists = true;
			$cat_curr = $c_catid;
			$cat_curr_ids[] = $cat_curr;

			while($cat_exists){

				$cat_curr_tmps = array();
				foreach ($cat_curr_ids as $cat_curr) {
					$cat_ids[] = $cat_curr;

					$database->setQuery("SELECT `id` FROM `#__categories` WHERE `parent_id` = '".$cat_curr."'");
					$cat_curr_tmp = $database->loadColumn();
					$cat_curr_tmps = array_merge($cat_curr_tmps, $cat_curr_tmp);
				}

				$cat_curr_tmps = array_values($cat_curr_tmps);
				if(!empty($cat_curr_tmps)){
					$cat_curr_ids = $cat_curr_tmps;
				} else {
					$cat_exists = false;
				}
				
			}
		}

		return $cat_ids;
	}
}