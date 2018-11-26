<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
 
/**
 * Joomlaquiz Deluxe Model
 */
class JoomlaquizModelLpaths extends JModelList
{
     /**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'title', 'q_lp.title',
				'published','q_lp.published',);
		}
		
		parent::__construct($config);
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		$search = $this->getUserStateFromRequest('lpaths.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$quiz_id = $this->getUserStateFromRequest('lpaths.filter.quiz_id', 'filter_quiz_id');
		$this->setState('filter.quiz_id', $quiz_id);
		
		// List state information.
		parent::populateState('id', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.quiz_id');
		
		return parent::getStoreId($id);
	}
	
	public static function delete($cid){
		$database = JFactory::getDBO();
		
		if (!is_array( $cid ) || empty( $cid )) {
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_DELETE')."'); window.history.go(-1);</script>\n";
			exit();
		}

		$cids = implode( ',', $cid );

		$query = "DELETE FROM #__quiz_lpath"
		. "\n WHERE id IN ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->execute()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}

		$query = "DELETE FROM #__quiz_lpath_quiz"
		. "\n WHERE lid IN ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->execute()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		return true;
	}
		
    /**
    * Method to build an SQL query to load the list data.
	*
    * @return      string  An SQL query
    */
    protected function getListQuery()
    {        
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
		$query->select("DISTINCT(q_lp.id), q_lp.*");
		$query->from('`#__quiz_lpath` AS `q_lp`');
		$query->join('INNER', '`#__quiz_lpath_quiz` AS `q_lp_q` ON q_lp_q.lid = q_lp.id');
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('q_lp.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(q_lp.title LIKE '.$search.')');
			}
		}
		
		$quiz_id = $this->getState('filter.quiz_id');
		if ($quiz_id)
		{			
			$query->where('q_lp_q.qid = ' . $quiz_id);
		}
		
        $orderCol	= $this->state->get('list.ordering', 'q_lp.title, q_lp.id');	
		$orderDirn	= $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol.' '.$orderDirn));
				
        return $query;
    }
	
	static public function publish(&$cid, $value = 1){
			$database = JFactory::getDBO();
			$task = JFactory::getApplication()->input->getCmd('task');
			$state = ($task == 'publish') ? 1 : 0;
			
			if (!is_array( $cid ) || empty( $cid )) {
				$action = ($task == 'publish') ? 'publish' : 'unpublish';
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_ITEM_TO')." $action'); window.history.go(-1);</script>\n";
				exit();
			}
			
			$cids = implode( ',', $cid );

			$query = "UPDATE #__quiz_lpath"
			. "\n SET published = " . intval( $state )
			. "\n WHERE id IN ( $cids )"
			;
			$database->setQuery( $query );
			if (!$database->execute()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			
			return true;
	}
	
	public function getQuizzes(){
		$database = JFactory::getDBO();
		
		$query = "SELECT c_id AS value, c_title AS text"
		. "\n FROM #__quiz_t_quiz"
		. "\n WHERE published = 1"
		. "\n ORDER BY c_title"
		;
		$database->setQuery( $query );
		$quizzes = $database->loadObjectList();
		
		return $quizzes;
	}

    public function getCurrDate()
    {
        $db = $this->_db;
        $query = $db->getQuery(true);
        $query->select('c_par_value');
        $query->from('`#__quiz_setup`');
        $query->where("c_par_name='curr_date'");


        $result = $db->setQuery($query)->loadResult();
        if (strtotime("+2 month",strtotime($result))<=strtotime(JFactory::getDate())) {
            return true;
        } else {
            return false;
        }
    }
}
