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
		
		$lang = JComponentHelper::getParams('com_languages')->get('site','en-GB');
		$lang = JFactory::getLanguage()->getTag();
		$lang = strtolower(str_replace('-', '_', $lang));
		$database = JFactory::getDBO();
		
		$no_virtuemart = ($this->isNotVirtuemart()) ? 1 : 0;
		$GLOBALS['no_virtuemart'] = $no_virtuemart;
			
		$lists = array();
		$lists['no_virtuemart'] = $no_virtuemart;
		$product_id = JFactory::getApplication()->input->get('pid');
		
		$lists['product_id'] = $product_id ? $product_id : -1;
		$lists['products'] = '';
	
		if (!$no_virtuemart) {
			if (!class_exists( 'VmConfig' )) require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			VmConfig::loadConfig();
			VmConfig::loadJLang('com_virtuemart');
			$query = "SELECT CONCAT(vmp_eg.product_name, ' (', vmp.product_sku, ')') AS text, vmp.virtuemart_product_id AS value"
			. "\n FROM #__virtuemart_products as vmp"
			. "\n LEFT JOIN #__virtuemart_products_" . VmConfig::$vmlang ." as vmp_eg ON vmp_eg.virtuemart_product_id = vmp.virtuemart_product_id"
			. "\n WHERE vmp.published = '1'"
			. "\n ORDER BY text"
			;
			
			$database->setQuery( $query );
			$products[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_SELECT_PRODUCT') );
			$products = @array_merge( $products, $database->loadObjectList() );
			$lists['products'] = JHTML::_('select.genericlist', $products, 'product_id', 'class="text_area" style="max-width: 300px;" size="1"' . ($product_id ? ' disabled' : ''), 'value', 'text', $product_id );
		}
		
		$prod_rel = array();
		if($product_id) {
			$lists['products'] .= '<input type="hidden" name="product_id" value="' . $product_id . '" />';
			$query = "SELECT * FROM #__quiz_products WHERE `pid` = '" . $product_id ."'";
			$database->setQuery($query);
			$temp_rel = $database->loadAssocList();
			foreach($temp_rel as $rel) {
				$prod_rel[$rel['type']][$rel['rel_id']] = $rel;
			}
			
			$query = "SELECT name FROM #__quiz_product_info WHERE quiz_sku = '{$product_id}'";
			$database->setQuery( $query );
			$lists['name'] = $database->loadResult();
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
		$database->setQuery( $query );
		$quizzes = $database->loadObjectList();
		$lists['quiz'] = $quizzes;

		$query = "SELECT *, id AS value, title AS text"
		. "\n FROM #__quiz_lpath"
		. "\n WHERE published = 1"
		. "\n ORDER BY title"
		;
		$database->setQuery( $query );
		$lpaths = $database->loadObjectList();
		$lists['lpath'] = $lpaths;
		
		return $lists;
	}

    protected function isNotVirtuemart()
    {
        $no_virtuemart = false;

        if (!defined('JPATH_VM_ADMINISTRATOR')){
            define('JPATH_VM_ADMINISTRATOR', JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart');
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
}