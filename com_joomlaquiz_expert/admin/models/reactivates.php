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
		return true;
	}
	
    /**
    * Method to build an SQL query to load the list data.
	*
    * @return      string  An SQL query
    */
    protected function getListQuery()
    {
    	$search = $this->getState('filter.search', '');
    	$user_id = $this->getState('filter.user_id', 0);

        $no_virtuemart = true;
		if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/connection.php')) {
            $no_virtuemart = false;
        }
        $isHikaShop = false;
        if (file_exists(JPATH_BASE . '/components/com_hikashop/config.xml')){
            $isHikaShop = true;
        }

        $db = \JFactory::getDBO();
        $where = array();
		if((int)$user_id > 0) {
			$where[] = '(users.id = ' . (int)$user_id . ')';
		}
        if ($search) {
            $search = $db->q('%' . str_replace(' ', '%', $db->escape(trim($search), true)) . '%');
            $where[] = '(users.name LIKE '. $search .')';
        }

        $no_shops_query = "SELECT `users`.`name`, `payments`.`id` AS `order_id`, '' AS `order_status`," .
            " CONVERT (`payments`.`status` USING utf8) COLLATE utf8_unicode_ci AS `order_status_name`, '' AS `shop`" .
            " FROM `#__quiz_payments` AS `payments`" .
            " INNER JOIN `#__users` AS `users` ON `users`.`id` = `payments`.`user_id`" .
            (count($where) ? " WHERE ".implode(' AND ', $where) : "");

		$vm_sub_query = "SELECT `virtuemart_order_id` FROM `#__virtuemart_order_items` WHERE `virtuemart_product_id` IN (SELECT `pid` FROM `#__quiz_products`)";
        $vm_query = "(SELECT `users`.`name`, `orders`.`virtuemart_order_id` as `order_id`, `orders`.`order_status`," .
            " CONVERT (`order_status`.`order_status_name` USING utf8) COLLATE utf8_unicode_ci AS `order_status_name`, 'virtuemart' AS `shop`" .
            " FROM `#__virtuemart_orders` AS `orders`" .
            " INNER JOIN `#__users` AS `users` ON `users`.`id` = `orders`.`virtuemart_user_id`" .
            " LEFT JOIN `#__virtuemart_orderstates` AS `order_status` ON `order_status`.`order_status_code` = `orders`.`order_status`" .
            " WHERE `orders`.`virtuemart_order_id` IN (". $vm_sub_query .")" .
            (count($where) ? " AND ".implode(' AND ', $where) : "").")";

        $hikashop_sub_query = "SELECT `order_id` FROM `#__hikashop_order_product` WHERE `product_id` IN (SELECT `pid` FROM `#__quiz_products`)";
        $hikashop_query = "(SELECT `users`.`name`, `orders`.`order_id` as `order_id`, `orders`.`order_status`," .
            " CONVERT (`order_status`.`orderstatus_namekey` USING utf8) COLLATE utf8_unicode_ci AS `order_status_name`, 'hikashop' AS `shop`" .
            " FROM `#__hikashop_order` AS `orders`" .
            " LEFT JOIN `#__hikashop_user` AS `hu` ON `hu`.`user_id` = `orders`.`order_user_id`" .
            " INNER JOIN `#__users` AS `users` ON `users`.`id` = `hu`.`user_cms_id`" .
            " LEFT JOIN `#__hikashop_orderstatus` AS `order_status` ON `order_status`.`orderstatus_namekey` = `orders`.`order_status`" .
            " WHERE `orders`.`order_id` IN (". $hikashop_sub_query .")" .
            (count($where) ? " AND ".implode(' AND ', $where) : "").")";

        // Virtuemart == false , Hikashop == false
        if($no_virtuemart && !$isHikaShop) {
            $query = $no_shops_query;
        }
        // Virtuemart == true , Hikashop == false
        else if(!$no_virtuemart && !$isHikaShop) {
            $query = $vm_query . " UNION (".$no_shops_query.")";
        }
        // Virtuemart == false , Hikashop == true
        else if($no_virtuemart && $isHikaShop){
            $query = $hikashop_query . " UNION (".$no_shops_query.")";
        }
        // Virtuemart == true , Hikashop == true
        else if(!$no_virtuemart && $isHikaShop){
            $query = $no_shops_query . " UNION (".$vm_query.") UNION (".$hikashop_query.")";
        }
		
		$query .= " ORDER BY name, shop, order_id";

       	return $query;
    }
	
	public function getUsers()
    {
        $no_virtuemart = true;
        if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/connection.php')) {
            $no_virtuemart = false;
        }
        $isHikaShop = false;
        if (file_exists(JPATH_BASE . '/components/com_hikashop/config.xml')){
            $isHikaShop = true;
        }
			
		$db = \JFactory::getDBO();

        $no_shops_query = "(SELECT DISTINCT(`users`.`id`) AS `value`, `users`.`name` AS `text` FROM `#__quiz_payments` AS `o` INNER JOIN `#__users` AS `users` ON `users`.`id` = `o`.`user_id`) ";

        $vm_query = "(SELECT DISTINCT(`users`.`id`) AS `value`, `users`.`name` AS `text` FROM `#__virtuemart_orders` AS `orders` INNER JOIN `#__users` AS `users` ON `users`.`id` = `orders`.`virtuemart_user_id`) ";

        $hikashop_query = "(SELECT DISTINCT(`users`.`id`) AS `value`, `users`.`name` AS `text` " .
            "FROM `#__hikashop_order` AS `orders` " .
            "LEFT JOIN `#__hikashop_user` AS `hu` ON `hu`.`user_id` = `orders`.`order_user_id` " .
            "INNER JOIN `#__users` AS `users` ON `users`.`id` = `hu`.`user_cms_id`) ";

        // Virtuemart == false , Hikashop == false
        if($no_virtuemart && !$isHikaShop) {
            $query = $no_shops_query;
        }
        // Virtuemart == true , Hikashop == false
        else if(!$no_virtuemart && !$isHikaShop) {
            $query = $vm_query . " UNION (".$no_shops_query.")";
        }
        // Virtuemart == false , Hikashop == true
        else if($no_virtuemart && $isHikaShop){
            $query = $hikashop_query . " UNION (".$no_shops_query.")";
        }
        // Virtuemart == true , Hikashop == true
        else if(!$no_virtuemart && $isHikaShop){
            $query = $no_shops_query . " UNION (".$vm_query.") UNION (".$hikashop_query.")";
        }
		
		$db->setQuery( $query );
		$users = $db->loadObjectList();
		
		return $users;
	}

	public function getProducts($order_id, $shop=''){

		$db = \JFactory::getDBO();
		$query = '';
        $products_names = array();

        if (!$shop) {
            $query = "SELECT `qpi`.`name`";
            $query .= "\n FROM `#__quiz_payments` AS `p`";
            $query .= "\n INNER JOIN `#__quiz_product_info` AS `qpi` ON `p`.`pid` = `qpi`.`quiz_sku`";
            $query .= "\n WHERE `p`.`id` = '". (int)$order_id ."'";
            $query .= "\n ORDER BY `qpi`.`name` ";
        }
		else if ($shop == 'virtuemart') {
			$table_list = $db->getTableList();
			$i = 0;
			foreach ($table_list as $table) {
				if (strpos($table, 'virtuemart_products_') !== false) {
					if ($i > 0){
					    $query .= " UNION ";
                    }
					$query .= "(SELECT `vm_p_engb`.`product_name`";
					$query .= "\n FROM `#__virtuemart_order_items` AS `vm_oi`";
					$query .= "\n INNER JOIN `" . $table ."` AS `vm_p_engb` ON `vm_p_engb`.`virtuemart_product_id` = `vm_oi`.`virtuemart_product_id`";
					$query .= "\n WHERE `vm_oi`.`virtuemart_order_id` = '". (int)$order_id ."'";
					$query .= "\n ORDER BY `vm_p_engb`.`product_name`) ";
					$i++;
				}
			}
		}
        else if ($shop == 'hikashop') {
            $query = "SELECT `order_product_name` FROM `#__hikashop_order_product` WHERE `order_id` = '".(int)$order_id."' ORDER BY `order_product_name`";
        }

        if($query) {
            $db->setQuery($query);
            $products_names = $db->loadColumn();
        }

		return $products_names;
	}

    public function getCurrDate()
    {
        $db = \JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select($db->qn('c_par_value'))
            ->from($db->qn('#__quiz_setup'))
            ->where($db->qn('c_par_name') . '=' . $db->q('curr_date'));

        $result = $db->setQuery($query)->loadResult();
        if ($result && strtotime("+2 month",strtotime($result))<=strtotime(\JFactory::getDate())) {
            return true;
        } else {
            return false;
        }
    }
}
