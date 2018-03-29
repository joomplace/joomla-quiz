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
class JoomlaquizModelProducts extends JModelList
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
				'name', 'quiz_p.name',
				'product_sku',
				'category_name',
				);
		}
		
		parent::__construct($config);
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest('products.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

        $vm_category_id = $this->getUserStateFromRequest('products.filter.vm_category_id', 'filter_vm_category_id');
        $this->setState('filter.vm_category_id', $vm_category_id);

		$hikashop_category_id = $this->getUserStateFromRequest('products.filter.hikashop_category_id', 'filter_hikashop_category_id');
		$this->setState('filter.hikashop_category_id', $hikashop_category_id);
		
		$quiz_id = $this->getUserStateFromRequest('products.filter.quiz_id', 'filter_quiz_id');
		$this->setState('filter.quiz_id', $quiz_id);
		
		$lpath_id = $this->getUserStateFromRequest('products.filter.lpath_id', 'filter_lpath_id');
		$this->setState('filter.lpath_id', $lpath_id);

		// List state information.
		parent::populateState('pid', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.vm_category_id');
		$id	.= ':'.$this->getState('filter.hikashop_category_id');
		$id	.= ':'.$this->getState('filter.quiz_id');
		$id	.= ':'.$this->getState('filter.lpath_id');
		
		return parent::getStoreId($id);
	}
	
	public function delete($cid){
		$database = JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_DELETE')."'); window.history.go(-1);</script>\n";
			exit();
		}

		$cids = implode( "','", $cid );

		$query = "DELETE FROM #__quiz_products"
		. "\n WHERE `pid` IN ( '{$cids}' )"
		;
		$database->setQuery( $query );
		if (!$database->execute()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}

		$query = "DELETE FROM #__quiz_product_info"
		. "\n WHERE `quiz_sku` IN ( '{$cids}' )"
		;
		$database->setQuery( $query );
		$database->execute();
		
		return true;
	}
	
    /**
    * Method to build an SQL query to load the list data.
	*
    * @return      string  An SQL query
    */
    protected function getListQuery()
    {
        \JLoader::register('JoomlaquizModelProduct', JPATH_ADMINISTRATOR.'/components/com_joomlaquiz/models/product.php');
        $modelProduct = new JoomlaquizModelProduct();

        $no_virtuemart = $modelProduct->isNotVirtuemart() ? 1 : 0;
        $GLOBALS['no_virtuemart'] = $no_virtuemart;

        $isHikaShop = $modelProduct->isHikaShop() ? 1 : 0;
        $GLOBALS['isHikaShop'] = $isHikaShop;

        $db = \JFactory::getDBO();
        $query = $db->getQuery(true);

        // Virtuemart == false , Hikashop == false
		if( ($no_virtuemart && !$isHikaShop) || (!$no_virtuemart && !class_exists('ShopFunctions')) ){
			$query->select("DISTINCT(qp.pid), quiz_p.name, '' AS `product_sku`, '' AS `category_name`");
			$query->from("#__quiz_products AS qp");
			$query->join("LEFT", "#__quiz_product_info AS quiz_p ON quiz_p.quiz_sku = qp.pid");
		}
        // Virtuemart == true , Hikashop == false
		else if (!$no_virtuemart && class_exists('ShopFunctions') && !$isHikaShop) {
			VmConfig::loadConfig();
			$query->select("DISTINCT(`qp`.`pid`), `vm_p_engb`.`product_name`, `vm_p`.`product_sku`, `vm_c`.`category_name`, `quiz_p`.`name`");
			$query->from("`#__quiz_products` AS `qp`");
			$query->join("LEFT", "`#__quiz_product_info` AS `quiz_p` ON quiz_p.quiz_sku = qp.pid");
			$query->join("LEFT", "`#__virtuemart_products` AS `vm_p` ON vm_p.virtuemart_product_id = qp.pid");
			$query->join("LEFT", "`#__virtuemart_products_" . VmConfig::$vmlang ."` AS `vm_p_engb` ON vm_p_engb.virtuemart_product_id = qp.pid");
			$query->join("LEFT", "`#__virtuemart_product_categories` AS `vm_pc` ON ((vm_pc.virtuemart_product_id = vm_p.virtuemart_product_id AND vm_p.product_parent_id = 0) OR (vm_pc.virtuemart_product_id = vm_p.product_parent_id AND vm_p.product_parent_id > 0))");
			$query->join("LEFT", "`#__virtuemart_categories_" . VmConfig::$vmlang ."` AS `vm_c` ON vm_c.virtuemart_category_id = vm_pc.virtuemart_category_id");
         }
        // Virtuemart == false , Hikashop == true
		else if($no_virtuemart && $isHikaShop){
            $query->select("DISTINCT(`qp`.`pid`), `hs_p`.`product_name`, `hs_p`.`product_code`, `hs_c`.`category_name`, `quiz_p`.`name`");
            $query->from("#__quiz_products AS qp");
            $query->join("LEFT", "`#__quiz_product_info` AS quiz_p ON quiz_p.quiz_sku = qp.pid");
            $query->join("LEFT", "`#__hikashop_product` AS `hs_p` ON hs_p.product_id = qp.pid");
            $query->join("LEFT", "`#__hikashop_product_category` AS `hs_pc` ON hs_pc.product_id = hs_p.product_id");
            $query->join("LEFT", "`#__hikashop_category` AS `hs_c` ON hs_c.category_id = hs_pc.category_id");
        }
        // Virtuemart == true , Hikashop == true
        else if(!$no_virtuemart && $isHikaShop){

            // Filter by search in title.
            $search = $this->getState('filter.search', '');
            $where_qp_search = '';
            $where_vm_search = '';
            $where_HikaShop_search = '';
            if ($search) {
                $search = $db->q('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $where_qp_search = " AND `quiz_p`.`name` LIKE " . $search;
                $where_vm_search = " AND `vm_p_engb`.`product_name` LIKE " . $search;
                $where_HikaShop_search = " AND `hs_p`.`product_name` LIKE " . $search;
            }

            // Filter by shop's category id
            $quiz_products = true;
            $is_vm_category_id = false;
            $is_hikashop_category_id = false;

            $where_vm_category_id = '';
            $vm_category_id = $this->getState('filter.vm_category_id');
            if ($vm_category_id && (int)$vm_category_id != -1) {
                $where_vm_category_id = " AND `vm_pc`.`virtuemart_category_id` = " . $vm_category_id;
                $is_vm_category_id = true;
                $quiz_products = false;
            }

            $where_hikashop_category_id = '';
            $hikashop_category_id = $this->getState('filter.hikashop_category_id');
            if ($hikashop_category_id && (int)$hikashop_category_id != -1) {
                $where_hikashop_category_id = " AND `hs_pc`.`category_id` = " . $hikashop_category_id;
                $is_hikashop_category_id = true;
                $quiz_products = false;
            }

            if($is_vm_category_id){
                $where_hikashop_category_id = " AND `hs_pc`.`category_id` = -1";
            }
            if($is_hikashop_category_id){
                $where_vm_category_id = " AND `vm_pc`.`virtuemart_category_id` = -1";
            }

            // Filter by Quiz Id
            $where_quiz_id = '';
            $quiz_id = $this->getState('filter.quiz_id');
            if ($quiz_id > 0) {
                $where_quiz_id = " AND (qp.type = 'q' AND qp.rel_id = ".(int)$quiz_id.")";
            }

            // Filter by lpath Id
            $where_lpath_id = '';
            $lpath_id = $this->getState('filter.lpath_id');
            if ($lpath_id > 0) {
                if ($quiz_id > 0) {
                    $where_lpath_id = " AND ((SELECT COUNT( qp_2.id ) FROM `#__quiz_products` AS `qp_2` WHERE `qp_2`.`pid` = `qp`.`pid` AND `qp_2`.`type` = 'l' AND `qp_2`.`rel_id` = ".(int)$lpath_id.") > 0)";
                } else {
                    $where_lpath_id = " AND (qp.type = 'l' AND qp.rel_id = ".(int)$lpath_id.")";
                }
            }

            // Quiz products
            if($quiz_products) {
                $query->select("DISTINCT(qp.pid), '' AS `product_name`, '' AS `product_sku`, '' AS `category_name`, `quiz_p`.`name`")
                    ->from("`#__quiz_products` AS `qp`")
                    ->join("LEFT", "`#__quiz_product_info` AS `quiz_p` ON `quiz_p`.`quiz_sku` = `qp`.`pid`")
                    ->where("`quiz_p`.`name` IS NOT NULL" . $where_qp_search . $where_quiz_id . $where_lpath_id);
            } else {
                $query->select("DISTINCT(qp.pid), '' AS `product_name`, '' AS `product_sku`, '' AS `category_name`, `quiz_p`.`name`")
                    ->from("`#__quiz_products` AS `qp`")
                    ->join("LEFT", "`#__quiz_product_info` AS `quiz_p` ON `quiz_p`.`quiz_sku` = `qp`.`pid`")
                    ->where("`qp`.`pid` = -1");
            }

            // Virtuemart
            VmConfig::loadConfig();
            $queryVM = "SELECT DISTINCT(`qp`.`pid`), `vm_p_engb`.`product_name`, `vm_p`.`product_sku`, `vm_c`.`category_name`, `quiz_p`.`name` " .
                "FROM `#__quiz_products` AS `qp` " .
                "LEFT JOIN `#__quiz_product_info` AS `quiz_p` ON `quiz_p`.`quiz_sku` = `qp`.`pid` " .
                "LEFT JOIN `#__virtuemart_products` AS `vm_p` ON `vm_p`.`virtuemart_product_id` = `qp`.`pid` " .
                "LEFT JOIN `#__virtuemart_products_" . VmConfig::$vmlang ."` AS `vm_p_engb` ON `vm_p_engb`.`virtuemart_product_id` = `qp`.`pid` " .
                "LEFT JOIN `#__virtuemart_product_categories` AS `vm_pc` ON ((`vm_pc`.`virtuemart_product_id` = `vm_p`.`virtuemart_product_id` AND `vm_p`.`product_parent_id` = 0) OR (`vm_pc`.`virtuemart_product_id` = `vm_p`.`product_parent_id` AND `vm_p`.`product_parent_id` > 0)) " .
                "LEFT JOIN `#__virtuemart_categories_" . VmConfig::$vmlang ."` AS `vm_c` ON `vm_c`.`virtuemart_category_id` = `vm_pc`.`virtuemart_category_id` " .
                "WHERE `vm_p`.`product_sku` IS NOT NULL" . $where_vm_search . $where_vm_category_id;

            // Hikashop
            $queryHikaShop = "SELECT DISTINCT(`qp`.`pid`), `hs_p`.`product_name`, `hs_p`.`product_code`, `hs_c`.`category_name`, `quiz_p`.`name` " .
                "FROM `#__quiz_products` AS `qp` " .
                "LEFT JOIN `#__quiz_product_info` AS `quiz_p` ON `quiz_p`.`quiz_sku` = `qp`.`pid` " .
                "LEFT JOIN `#__hikashop_product` AS `hs_p` ON `hs_p`.`product_id` = `qp`.`pid` " .
                "LEFT JOIN `#__hikashop_product_category` AS `hs_pc` ON `hs_pc`.`product_id` = `hs_p`.`product_id` " .
                "LEFT JOIN `#__hikashop_category` AS `hs_c` ON `hs_c`.`category_id` = `hs_pc`.`category_id` " .
                "WHERE `hs_p`.`product_code` IS NOT NULL" . $where_HikaShop_search . $where_hikashop_category_id;

            // All
            $query->union($queryVM)
                  ->union($queryHikaShop);

            $orderCol = $this->state->get('list.ordering', 'name');
            $orderDirn = $this->state->get('list.direction', 'ASC');
            $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        }

        if( $no_virtuemart || !$isHikaShop ) {      //there are no two components of shops together

            // Filter by search in title.
            $search = $this->getState('filter.search');
            if (!empty($search)) {
                $search = $db->q('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $searches = array();
                $searches[] = '`quiz_p`.`name` LIKE ' . $search;
                if (!$no_virtuemart) {
                    $searches[] = '`vm_p_engb`.`product_name` LIKE ' . $search;
                }
                if ($isHikaShop) {
                    $searches[] = '`hs_p`.`product_name` LIKE ' . $search;
                }
                $query->where('(' . implode(' OR ', $searches) . ')');
            }

            if (!$no_virtuemart) {
                $vm_category_id = $this->getState('filter.vm_category_id');
                if ($vm_category_id && $vm_category_id != -1) {
                    $query->where('`vm_pc`.`virtuemart_category_id` = ' . $vm_category_id);
                }
            }

            if ($isHikaShop) {
                $hikashop_category_id = $this->getState('filter.hikashop_category_id');
                if ($hikashop_category_id && $hikashop_category_id != -1) {
                    $query->where('`hs_pc`.`category_id` = ' . $hikashop_category_id);
                }
            }

            $quiz_id = $this->getState('filter.quiz_id');
            if ($quiz_id > 0) {
                $query->where('(qp.type = \'q\' AND qp.rel_id = ' . $quiz_id . ')');
            }

            $lpath_id = $this->getState('filter.lpath_id');
            if ($lpath_id > 0) {
                if ($quiz_id > 0) {
                    $query->where('((SELECT COUNT( qp_2.id ) FROM #__quiz_products AS qp_2 WHERE qp_2.pid = qp.pid AND qp_2.type = \'l\' AND qp_2.rel_id = ' . $lpath_id . ') > 0)');
                } else {
                    $query->where('(qp.type = \'l\' AND qp.rel_id = ' . $lpath_id . ')');
                }
            }

            if ($no_virtuemart && !$isHikaShop) {
                $orderCol = $this->state->get('list.ordering', 'quiz_p.name');
            } else {
                if (!$no_virtuemart) {
                    $orderCol = $this->state->get('list.ordering', 'vm_p_engb.product_name');
                } else {
                    if ($isHikaShop) {
                        $orderCol = $this->state->get('list.ordering', 'hs_p.product_name');
                    }
                }
            }

            $orderDirn = $this->state->get('list.direction', 'ASC');
            $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        }
		
        return $query;
    }
	
	public function getQuizzes(){
		
		$db = \JFactory::getDBO();
		$query = "SELECT c_id AS value, c_title AS text"
		. "\n FROM #__quiz_t_quiz"
		. "\n WHERE published = 1"
		. "\n ORDER BY c_title"
		;
		
		$db->setQuery( $query );
		$quizzes = $db->loadObjectList();
		
		return $quizzes;
	}
	
	public function getLpaths(){
		$db = \JFactory::getDBO();
		
		$query = "SELECT id AS value, title AS text"
		. "\n FROM #__quiz_lpath"
		. "\n WHERE published = 1"
		. "\n ORDER BY title"
		;
		$db->setQuery( $query );
		$lpaths[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_SELECT_LEARNING_PATH') );
		$lpaths = array_merge( $lpaths, $db->loadObjectList() );
		
		return $lpaths;
	}

    public function getVmCategories(){

        $app = \JFactory::getApplication();
        $category_id = $app->getUserStateFromRequest('products.filter.vm_category_id', 'filter_vm_category_id');
        $category_id  = $category_id ? $category_id : -1;

        $category_options = array();
        $category_options[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_SELECT_VIRTUEMART_CATEGORY'));

        if(!$GLOBALS['no_virtuemart']) {
            $categories_VM = JoomlaquizHelper::getVirtuemartCategories();
            foreach ($categories_VM as $id => $name) {
                $category_options[] = JHTML::_('select.option', $id, $name);
            }
        }

        $category = JHTML::_('select.genericlist', $category_options, 'filter_vm_category_id', 'class="text_area" size="1"" onchange="document.adminForm.submit();"', 'value', 'text', $category_id );

        return $category;
    }

    public function getHikaShopCategories(){

        $app = \JFactory::getApplication();
        $category_id = $app->getUserStateFromRequest('products.filter.hikashop_category_id', 'filter_hikashop_category_id');
        $category_id  = $category_id ? $category_id : -1;

        $category_options = array();
        $category_options[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_SELECT_HIKASHOP_CATEGORY'));

        if($GLOBALS['isHikaShop']) {
            $categories_HikaShop = JoomlaquizHelper::getHikaShopCategories();
            foreach ($categories_HikaShop as $id => $name) {
                $category_options[] = JHTML::_('select.option', $id, $name);
            }
        }

        $category = JHTML::_('select.genericlist', $category_options, 'filter_hikashop_category_id', 'class="text_area" size="1"" onchange="document.adminForm.submit();"', 'value', 'text', $category_id );

        return $category;
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
