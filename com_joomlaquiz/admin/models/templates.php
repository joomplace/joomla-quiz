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
class JoomlaquizModelTemplates extends JModelList
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
				'template_name',
			);
		}
		
		parent::__construct($config);
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		$search = $this->getUserStateFromRequest('categories.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		// List state information.
		parent::populateState('id', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		return parent::getStoreId($id);
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
        
		$query->select("*");
		$query->from('`#__quiz_templates`');
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(template_name LIKE '.$search.')');
			}
		}
		
        $orderCol	= $this->state->get('list.ordering', 'id');	
		$orderDirn	= $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol.' '.$orderDirn));
	
        return $query;
    }
	
	public function getCssContent(){
		
		$database = JFactory::getDBO();
		
		$app = JFactory::getApplication();
		$cid = $app->input->get('cid');
		
		$query = "SELECT template_name FROM #__quiz_templates WHERE id ='".$cid."'";
		$database->SetQuery( $query );
		$p_tname = $database->LoadResult();
		$file = JPATH_SITE .'/components/com_joomlaquiz/views/templates/tmpl/'. $p_tname .'/css/jq_template.css';

		if ($fp = fopen( $file, 'r' )) {
			$content = fread( $fp, filesize( $file ) );
			$content = htmlspecialchars( $content );
			
			return $content;
		} else {
			$app->redirect( 'index.php?option=com_joomlaquiz&task=templates', JText::_('COM_JOOMLAQUIZ_OPERATION_FAILED'). $file );
		}
	}
	
	public function getTemplateName(){
		$database = JFactory::getDBO();
		
		$app = JFactory::getApplication();
		$cid = $app->input->get('cid');
		
		$query = "SELECT template_name FROM #__quiz_templates WHERE id ='".$cid."'";
		$database->SetQuery( $query );
		$p_tname = $database->LoadResult();
		
		return $p_tname;
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
