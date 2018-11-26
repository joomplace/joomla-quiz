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
class JoomlaquizModelPayments extends JModelList
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
				'id', 'payments.id',
				'product_name', 'products.name',
				'amount', 'payments.amount',
				'status', 'payments.status',
				'name', 'users.name');
		}
		
		parent::__construct($config);
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		$search = $this->getUserStateFromRequest('payments.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$user_id = $this->getUserStateFromRequest('payments.filter.user_id', 'filter_user_id');
        $this->setState('filter.user_id', $user_id);

        $usergroup_id = $this->getUserStateFromRequest('payments.filter.usergroup_id', 'filter_usergroup_id');
        $this->setState('filter.usergroup_id', $usergroup_id);

		
		// List state information.
		parent::populateState('id', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.user_id');
		
		return parent::getStoreId($id);
	}
	
	public static function delete($cid){
		$database = JFactory::getDBO();

		if (!is_array( $cid ) || empty( $cid )) {
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_DELETE')."'); window.history.go(-1);</script>\n";
			exit();
		}

		$cids = implode( "','", $cid );

		$query = "DELETE FROM #__quiz_payments"
		. "\n WHERE `id` IN ( '{$cids}' )"
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
        
		$query->select("users.name, users.email, payments.*, products.name AS product_name");
		$query->from('`#__quiz_payments` AS `payments`');
		$query->join('LEFT', '`#__users` AS `users` ON users.id = payments.user_id');
		$query->join('LEFT', '`#__quiz_product_info` AS `products` ON products.quiz_sku = payments.pid');
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('payments.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(UPPER(users.name) LIKE '.$search.')');
			}
		}
		
		$user_id = $this->getState('filter.user_id');
        $usergroups_id = $this->getState('filter.usergroup_id');

		if($user_id){
			$query->where('users.id = '.$db->q($user_id));
		}

		if($usergroups_id){
            $query->join('LEFT', '`#__user_usergroup_map` AS `map` ON map.user_id = users.id');
            $query->join('LEFT', '`#__usergroups` AS `usergroups` ON usergroups.id = map.group_id');
            $query->where('usergroups.id = '.$db->q($usergroups_id));
        }
		
        $orderCol	= $this->state->get('list.ordering', 'users.name, payments.id');	
		$orderDirn	= $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol.' '.$orderDirn));
	
        return $query;
    }
	
	public function getUsers(){
		$db = JFactory::getDBO();
        $usergroups_id = $this->getState('filter.usergroup_id');

        $query = $db->getQuery(true);
        $query->select('DISTINCT(users.id) AS value, users.name AS text');
        $query->from('`#__quiz_payments` AS `payments`');
        $query->join('INNER', '`#__users` AS `users` ON users.id = payments.user_id');

        if($usergroups_id){
            $query->join('INNER', '`#__user_usergroup_map` AS `map` ON map.user_id = users.id');
            $query->join('INNER', '`#__usergroups` AS `usergroups` ON usergroups.id = map.group_id');
            $query->where('usergroups.id = '.$db->q($usergroups_id));
        }

		$db->setQuery( $query );
		$users = $db->loadObjectList();
		
		return $users;
	}

    public function getUsersGroup(){
        $db = JFactory::getDBO();
        $user_id = $this->getState('filter.user_id');

        $query = $db->getQuery(true);
        $query->select('DISTINCT(usergroups.id) AS value, usergroups.title AS text');
        $query->from('`#__usergroups` AS `usergroups`');
        $query->join('INNER', '`#__user_usergroup_map` AS `map` ON usergroups.id = map.group_id');
        $query->join('INNER', '`#__quiz_payments` AS `payments` ON payments.user_id = map.user_id');
        $query->join('INNER', '`#__users` AS `users` ON users.id = map.user_id');

        if($user_id){
            $query->where('users.id = '.$db->q($user_id));
        }

        $db->setQuery( $query );
        $usersgroup = $db->loadObjectList();

        return $usersgroup;
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
