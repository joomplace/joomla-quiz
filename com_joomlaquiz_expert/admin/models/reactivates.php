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
class JoomlaquizModelReactivates extends JModelList
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
				'order_id',
				'product_name',
				'order_status_name',
				'name',
				);
		}
		
		parent::__construct($config);
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		$search = $this->getUserStateFromRequest('reactivates.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$user_id = $this->getUserStateFromRequest('reactivates.filter.user_id', 'filter_user_id');
		$this->setState('filter.user_id', $user_id);
		
		// List state information.
		parent::populateState('order_id', 'asc');
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
		
		return true;
	}
	
    /**
    * Method to build an SQL query to load the list data.
	*
    * @return      string  An SQL query
    */
    protected function getListQuery()
    {
    	$search = $this->getState('filter.search');
    	$user_id = $this->getState('filter.user_id');

		if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/connection.php'))
			$no_virtuemart = false;
		else
			$no_virtuemart = true;
			
        $db = JFactory::getDBO();
        
        $where = array();
		if($user_id > 0) {
			$where[] = '(users.id = ' . $user_id . ')';
		}
		if($search) {
			$where[] = '(users.name LIKE (\'%' . $search . '%\'))';
		}

        $query = $db->getQuery(true)
            ->select($db->qn(
                array(
                    'users.name',
                    'payments.id',
                ),
                array(
                    'name',
                    'order_id',
                )
            ))
            ->select('\'\' AS `order_status`')
            ->select("CONVERT (`payments`.`status` USING utf8) COLLATE utf8_unicode_ci AS order_status_name")
            ->select('\'0\' AS `vm`')
            ->from($db->qn('#__quiz_payments', 'payments'))
            ->innerJoin($db->qn('#__users', 'users')
                . ' ON '
                . $db->qn('users.id') . ' = ' . $db->qn('payments.user_id')
            );
        if(count($where)){
            foreach ($where as $item){
                $query->where($item);
            }
        }

        if (!$no_virtuemart){
            $query_vm = $db->getQuery(true)
                ->select($db->qn(
                    array(
                        'users.name',
                        'orders.virtuemart_order_id',
                        'orders.order_status'
                    ),
                    array(
                        'name',
                        'order_id',
                        'order_status'
                    )
                ))
                ->select("CONVERT (`order_status`.`order_status_name` USING utf8) COLLATE utf8_unicode_ci AS order_status_name")
                ->select('\'1\' AS `vm`')
                ->from($db->qn('#__virtuemart_orders', 'orders'))
                ->innerJoin($db->qn('#__users', 'users')
                    . ' ON '
                    . $db->qn('users.id') . ' = ' . $db->qn('orders.virtuemart_user_id')
                )
                ->leftJoin($db->qn('#__virtuemart_orderstates', 'order_status')
                    . ' ON '
                    . $db->qn('order_status.order_status_code') . ' = ' . $db->qn('orders.order_status')
                );
            if (count($where)) {
                foreach ($where as $item) {
                    $query_vm->where($item);
                }
            }
            $query .= ' UNION ALL(' . $query_vm . ')';
        }
		$query .= "\n ORDER BY name, vm, order_id";

       	return $query;
    }
	
	public function getUsers(){
		if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/connection.php'))
			$no_virtuemart = false;
		else
			$no_virtuemart = true;
			
		$db = JFactory::getDBO();
		$query = (!$no_virtuemart ?"(SELECT DISTINCT(users.id) AS value, users.name AS text"
		. "\n FROM #__virtuemart_orders AS orders"
		. "\n INNER JOIN #__users AS users ON users.id = orders.virtuemart_user_id )"
		. "\n UNION ": '')
		. " (SELECT DISTINCT(users.id) AS value, users.name AS text"
		. "\n FROM #__quiz_payments AS o"
		. "\n INNER JOIN #__users AS users ON users.id = o.user_id)"
		;
		
		$db->setQuery( $query );
		$users = $db->loadObjectList();
		
		return $users;
	}

	public function getProducts($order_id, $vm){

		$db = JFactory::getDBO();
		$query = "";
		if ($vm) {
			$table_list = $db->getTableList();

			$i = 0;
			foreach ($table_list as $table) {
				if (strpos($table, 'virtuemart_products_') !== false) {
					if ($i > 0) $query .= " UNION ";
					$query .= "(SELECT vm_p_engb.product_name";
					$query .= "\n FROM #__virtuemart_order_items AS vm_oi";
					$query .= "\n INNER JOIN " . $table ." AS vm_p_engb ON vm_p_engb.virtuemart_product_id = vm_oi.virtuemart_order_id";
					$query .= "\n WHERE vm_oi.virtuemart_order_id = " . $order_id;
					$query .= "\n ORDER BY vm_p_engb.product_name) ";
					$i++;
				}
			}
		} else {
			$query = "SELECT qpi.name";
			$query .= "\n FROM #__quiz_payments AS p";
			$query .= "\n INNER JOIN #__quiz_product_info AS qpi ON p.pid = qpi.quiz_sku ";
			$query .= "\n WHERE p.id = " . $order_id;
			$query .= "\n ORDER BY qpi.name ";
		}

		$db->SetQuery( $query );
		$products_names = $db->loadColumn();

		return $products_names;
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
