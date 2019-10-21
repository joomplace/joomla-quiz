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
		$app = JFactory::getApplication();
		
		$search = $this->getUserStateFromRequest('products.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$category_id = $this->getUserStateFromRequest('products.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $category_id);
		
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
		$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.quiz_id');
		$id	.= ':'.$this->getState('filter.lpath_id');
		
		return parent::getStoreId($id);
	}
	
	public function delete($cid){
		$database = JFactory::getDBO();
		
		if (!is_array( $cid ) || empty( $cid )) {
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
		$no_virtuemart = ($this->isNotVirtuemart()) ? 1 : 0;
		$GLOBALS['no_virtuemart'] = $no_virtuemart;
		
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
       
		if($no_virtuemart){
			$query->select("DISTINCT(qp.pid), quiz_p.name, '' AS `product_sku`, '' AS `category_name`");
			$query->from("#__quiz_products AS qp");
			$query->join("LEFT", "#__quiz_product_info AS quiz_p ON quiz_p.quiz_sku = qp.pid");
		} else {
			if(class_exists('ShopFunctions')){
				VmConfig::loadConfig();
				VmConfig::loadJLang('com_virtuemart');		
				
				$query->select("DISTINCT(`qp`.`pid`), `vm_p_engb`.`product_name`, `vm_p`.`product_sku`, `vm_c`.`category_name`, `quiz_p`.`name`");
				$query->from("`#__quiz_products` AS `qp`");
				$query->join("LEFT", "`#__quiz_product_info` AS `quiz_p` ON quiz_p.quiz_sku = qp.pid");
				$query->join("LEFT", "`#__virtuemart_products` AS `vm_p` ON vm_p.virtuemart_product_id = qp.pid");
				$query->join("LEFT", "`#__virtuemart_products_" . VmConfig::$vmlang ."` AS `vm_p_engb` ON vm_p_engb.virtuemart_product_id = qp.pid");
				$query->join("LEFT", "`#__virtuemart_product_categories` AS `vm_pc` ON ((vm_pc.virtuemart_product_id = vm_p.virtuemart_product_id AND vm_p.product_parent_id = 0) OR (vm_pc.virtuemart_product_id = vm_p.product_parent_id AND vm_p.product_parent_id > 0))");
				$query->join("LEFT", "`#__virtuemart_categories_" . VmConfig::$vmlang ."` AS `vm_c` ON vm_c.virtuemart_category_id = vm_pc.virtuemart_category_id");
			} else {
				$query->select("DISTINCT(qp.pid), quiz_p.name, '' AS `product_sku`, '' AS `category_name`");
				$query->from("#__quiz_products AS qp");
				$query->join("LEFT", "#__quiz_product_info AS quiz_p ON quiz_p.quiz_sku = qp.pid");
			}		
		}
			
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			if ($no_virtuemart) {
				$query->where('(quiz_p.name LIKE '.$search.')');
			} else {
				$query->where('(vm_p_engb.product_name LIKE '.$search.') OR (quiz_p.name LIKE '.$search.')');
			}
		}
		
		$category_id = $this->getState('filter.category_id');
		if($category_id && $category_id != -1){
			if (!$no_virtuemart) {
				$query->where('vm_pc.virtuemart_category_id = '.$category_id);
			}
		}
		
		$quiz_id = $this->getState('filter.quiz_id');
		if($quiz_id > 0){
			$query->where('(qp.type = \'q\' AND qp.rel_id = ' . $quiz_id . ')');
		}
		
		$lpath_id = $this->getState('filter.lpath_id');
		if($lpath_id > 0){
			if($quiz_id > 0) {
				$query->where('((SELECT COUNT( qp_2.id ) FROM #__quiz_products AS qp_2 WHERE qp_2.pid = qp.pid AND qp_2.type = \'l\' AND qp_2.rel_id = ' . $lpath_id . ') > 0)');
			} else {
				$query->where('(qp.type = \'l\' AND qp.rel_id = ' . $lpath_id . ')');
			}
		}
		
		if($no_virtuemart){		
			$orderCol	= $this->state->get('list.ordering', 'quiz_p.name');
		} else {
			$orderCol	= $this->state->get('list.ordering', 'vm_p_engb.product_name');
		}

		$orderDirn	= $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol.' '.$orderDirn));	
		
        return $query;
    }
	
	public function getQuizzes(){
		
		$db = JFactory::getDBO();
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
		$db = JFactory::getDBO();
		
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
	
	public function getCategories(){

		$app = JFactory::getApplication();
		$category_id = $app->getUserStateFromRequest('products.filter.category_id', 'filter_category_id');
		$category_id  = $category_id ? $category_id : -1;

		if(!$GLOBALS['no_virtuemart']) {

			$categories = JoomlaquizHelper::getVirtuemartCategories();
			$category_options[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_SELECT_CATEGORY'));
			foreach ($categories as $id => $name) {
				$category_options[] = JHTML::_('select.option', $id, $name);
			}

		} else {
			$category_options[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_SELECT_CATEGORY'));
		}

		$category = JHTML::_('select.genericlist', $category_options, 'filter_category_id', 'class="text_area" size="1"" onchange="document.adminForm.submit();"', 'value', 'text', $category_id );

		return $category;
	}
	
	protected function isNotVirtuemart()
    {
		$no_virtuemart = false;

		if (!defined('JPATH_VM_ADMINISTRATOR')) {
            define('JPATH_VM_ADMINISTRATOR', JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart');
        }
						
		if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/config.php')) {
            require_once(JPATH_BASE . '/components/com_virtuemart/helpers/config.php');
        } else {
            $no_virtuemart = true;
        }

        if (file_exists(JPATH_BASE . '/components/com_virtuemart/tables/categories.php')) {
            if (file_exists(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/vobject.php')) {
                JLoader::register('vObject', JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/vobject.php');
            }
            if (file_exists(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/vmtable.php')) {
                JLoader::register('VmTable', JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/vmtable.php');
            }
            require_once(JPATH_BASE . '/components/com_virtuemart/tables/categories.php');
        } else {
            $no_virtuemart = true;
        }
			
		if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/vmmodel.php')) {
            require_once(JPATH_BASE . '/components/com_virtuemart/helpers/vmmodel.php');
        } else {
            $no_virtuemart = true;
        }
			
		if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/shopfunctions.php')) {
            require_once(JPATH_BASE . '/components/com_virtuemart/helpers/shopfunctions.php');
        } else {
            $no_virtuemart = true;
        }
		
		return $no_virtuemart;
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
