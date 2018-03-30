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
* Product model.
*
*/
class JoomlaquizModelProduct extends JModelAdmin
{
	protected $text_prefix = 'COM_JOOMLAQUIZ';
		
	public function getTable($type = 'products', $prefix = 'JoomlaquizTable', $config = array())
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
		$data = JFactory::getApplication()->getUserState('com_joomlaquiz.edit.product.data', array());

		if (empty($data)) {
			$data = $this->getItem();
			// Prime some default values.
			if ($this->getState('product.pid') == 0) {
				$app = JFactory::getApplication();
				$id = $app->getUserState('com_joomlaquiz.edit.product.pid');
				if ($id) $data->set('pid', JFactory::getApplication()->input->getInt('pid', $id));
			}
		}
		return $data;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$app	= JFactory::getApplication();

		$form = $this->loadForm('com_joomlaquiz.product', 'product', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	public function getLists(){

		$lang = strtolower(str_replace('-', '_', JFactory::getLanguage()->getTag()));
		$db = JFactory::getDBO();
        $product_id = JFactory::getApplication()->input->getInt('pid', 0);

        $lists = array();
        $lists['product_id'] = $product_id ? $product_id : -1;

        $no_virtuemart = $this->isNotVirtuemart() ? 1 : 0;
        $lists['no_virtuemart'] = $no_virtuemart;
		$GLOBALS['no_virtuemart'] = $no_virtuemart;
        $lists['vm_products'] = '';

		$isHikaShop = $this->isHikaShop() ? 1 : 0;
        $lists['isHikaShop'] = $isHikaShop;
        $GLOBALS['isHikaShop'] = $isHikaShop;
        $lists['hikashop_products'] = '';

		if (!$no_virtuemart) {
			if (!class_exists( 'VmConfig' )) require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
			VmConfig::loadConfig();
			VmConfig::loadJLang('com_virtuemart');
			$query = "SELECT CONCAT(vmp_eg.product_name, ' (', vmp.product_sku, ')') AS text, vmp.virtuemart_product_id AS value"
			. "\n FROM #__virtuemart_products as vmp"
			. "\n LEFT JOIN #__virtuemart_products_" . VmConfig::$vmlang ." as vmp_eg ON vmp_eg.virtuemart_product_id = vmp.virtuemart_product_id"
			. "\n WHERE vmp.published = '1'"
			. "\n ORDER BY text"
			;
			
			$db->setQuery( $query );
            $products = array();
			$products[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_SELECT_PRODUCT') );
			$products = @array_merge( $products, $db->loadObjectList() );
			$lists['vm_products'] = JHTML::_('select.genericlist', $products, 'vm_product_id', 'class="text_area" style="max-width: 300px;" size="1"' . ($product_id ? ' disabled' : ''), 'value', 'text', $product_id );
    	}

		if($isHikaShop){
            $query = "SELECT CONCAT(product_name, ' (', product_code, ')') AS text, product_id AS value FROM #__hikashop_product WHERE product_published = '1' ORDER BY text";
            $db->setQuery( $query );
            $products = array();
			$products[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_SELECT_PRODUCT') );
			$products = @array_merge( $products, $db->loadObjectList() );
			$lists['hikashop_products'] = JHTML::_('select.genericlist', $products, 'hikashop_product_id', 'class="text_area" style="max-width: 300px;" size="1"' . ($product_id ? ' disabled' : ''), 'value', 'text', $product_id );
		}
		
		$prod_rel = array();
		if($product_id) {
			$query = "SELECT * FROM #__quiz_products WHERE `pid` = '" . $product_id ."'";
			$db->setQuery($query);
			$temp_rel = $db->loadAssocList();
			foreach($temp_rel as $rel) {
				$prod_rel[$rel['type']][$rel['rel_id']] = $rel;
			}
			
			$query = "SELECT name FROM #__quiz_product_info WHERE quiz_sku = '{$product_id}'";
			$db->setQuery( $query );
			$lists['name'] = $db->loadResult();
		}

		if($product_id == '-1'){
			$lists['name'] = JFactory::getApplication()->input->get('name');
		}

		$lists['relation'] = $prod_rel;
		
		$query = "SELECT c_id AS value, c_title AS text"
		. "\n FROM #__quiz_t_quiz"
		. "\n WHERE published = 1"
		. "\n ORDER BY c_title"
		;
		$db->setQuery( $query );
		$quizzes = $db->loadObjectList();
		$lists['quiz'] = $quizzes;

		$query = "SELECT *, id AS value, title AS text"
		. "\n FROM #__quiz_lpath"
		. "\n WHERE published = 1"
		. "\n ORDER BY title"
		;
		$db->setQuery( $query );
		$lpaths = $db->loadObjectList();
		$lists['lpath'] = $lpaths;
		
		return $lists;
	}
	
	public function isNotVirtuemart(){
		
		$no_virtuemart = false;
		if(!defined('DS')) define('DS', '/');
		if (!defined('JPATH_VM_ADMINISTRATOR')) define('JPATH_VM_ADMINISTRATOR', JPATH_BASE . DS. 'components'.DS.'com_virtuemart');
						
		if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/config.php'))
			require_once(JPATH_BASE . '/components/com_virtuemart/helpers/config.php');
		else
			$no_virtuemart = true;
			
		if (file_exists(JPATH_BASE . '/components/com_virtuemart/tables/categories.php'))
			require_once(JPATH_BASE . '/components/com_virtuemart/tables/categories.php');
		else
			$no_virtuemart = true;
			
		if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/vmmodel.php'))
			require_once(JPATH_BASE . '/components/com_virtuemart/helpers/vmmodel.php');
		else
			$no_virtuemart = true;
			
		if (file_exists(JPATH_BASE . '/components/com_virtuemart/helpers/shopfunctions.php'))
			require_once(JPATH_BASE . '/components/com_virtuemart/helpers/shopfunctions.php');
		else
			$no_virtuemart = true;
	
		if(empty($_SESSION["ps_vendor_id"])) {
				$_SESSION["ps_vendor_id"] = 1;
		}
		
		return $no_virtuemart;
	}

	public function isHikaShop(){
	    $isHikaShop = false;
	    if (file_exists(JPATH_BASE . '/components/com_hikashop/config.xml')){
            $isHikaShop = true;
        }
	    return $isHikaShop;
    }

}