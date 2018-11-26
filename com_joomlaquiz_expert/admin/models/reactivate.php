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
* Reactivate model.
*
*/
class JoomlaquizModelReactivate extends JModelAdmin
{
	protected $text_prefix = 'COM_JOOMLAQUIZ';
		
	public function getTable($type = 'payment', $prefix = 'JoomlaquizTable', $config = array())
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
		return;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		return;
	}
	
	public function getLists()
	{
		$lang = JFactory::getLanguage()->getTag();
		$lang = strtolower(str_replace('-', '_', $lang));
		
		$database = JFactory::getDBO();
		$oid = JFactory::getApplication()->input->get('id');
				
		if (!$oid) {
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_ORDER')."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$lists = array();
		$vm	= $oid < 1000000000;
		$lists['oid'] = $oid;
		$lists['vm'] = $vm;
		$product_quantity = 1;
		
		if ($vm) {
			$query = "SELECT qp.*, vm_oh.created_on as date_added, CONCAT(vm_p_engb.product_name, ' (', vm_p.product_sku, ')') AS product_title, "
			. "\n (IF(qp.type = 'l', lpath.title, quiz.c_title)) AS rel_title,"
			. "\n (IF(qp.type = 'l', 'lpaths', 'quizzes')) AS rel_type,"
			. "\n (IF(qp.type = 'l', 'learning path', 'quiz')) AS rel_type_full"
			. "\n FROM #__virtuemart_orders AS vm_o"
			. "\n LEFT JOIN #__virtuemart_order_histories AS vm_oh ON (vm_oh.virtuemart_order_id = vm_o.virtuemart_order_id AND vm_oh.order_status_code = 'C')"
			. "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
			. "\n INNER JOIN #__virtuemart_products AS vm_p ON vm_p.virtuemart_product_id = vm_oi.virtuemart_product_id"
			
			. "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_p.virtuemart_product_id"
			. "\n LEFT JOIN #__quiz_lpath AS lpath ON (qp.type = 'l' AND lpath.id = qp.rel_id)"
			. "\n LEFT JOIN #__quiz_t_quiz AS quiz ON (qp.type = 'q' AND quiz.c_id = qp.rel_id)"
			. "\n LEFT JOIN #__virtuemart_products_" . $lang ." AS vm_p_engb ON vm_p_engb.virtuemart_product_id = vm_oi.virtuemart_product_id"
			. "\n WHERE vm_o.virtuemart_order_id = '" . $oid . "' AND vm_p.published = '1'"
			. "\n ORDER BY vm_p_engb.product_name, vm_p.virtuemart_product_id, qp.type";
			
			$query2 = "SELECT vm_oi.product_quantity"
			. "\n FROM #__virtuemart_orders AS vm_o"
			. "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
			. "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
			. "\n WHERE vm_o.virtuemart_order_id = $oid AND vm_o.order_status IN ('C')"
			;
			
			$database->SetQuery( $query2 );
			$product_quantity = ($database->loadResult()) ? (int)$database->loadResult() : 1;
		} else {
			$query = "SELECT qp.*, p.confirmed_time AS `date_added`, qpi.name AS product_title, "
			. "\n (IF(qp.type = 'l', lpath.title, quiz.c_title)) AS rel_title,"
			. "\n (IF(qp.type = 'l', 'lpaths', 'quizzes')) AS rel_type,"
			. "\n (IF(qp.type = 'l', 'learning path', 'quiz')) AS rel_type_full"
			. "\n FROM #__quiz_payments AS p"
			. "\n INNER JOIN #__quiz_product_info AS qpi ON qpi.quiz_sku = p.pid"
			. "\n INNER JOIN #__quiz_products AS qp ON qp.pid = p.pid"
			. "\n LEFT JOIN #__quiz_lpath AS lpath ON (qp.type = 'l' AND lpath.id = qp.rel_id)"
			. "\n LEFT JOIN #__quiz_t_quiz AS quiz ON (qp.type = 'q' AND quiz.c_id = qp.rel_id)"
			. "\n WHERE p.id = '". ($oid-1000000000) ."' "
			. "\n ORDER BY qpi.name"
			;
		}
		
		$database->SetQuery( $query );
		$lists['all'] = $database->loadObjectList();
	
		$lists['product_quantity'] = $product_quantity;
	
		$query = "SELECT *"
			. "\n FROM #__quiz_products_stat"
			. "\n WHERE oid = '{$oid}' "
		;
		$database->SetQuery( $query );
		$lists['products_stat'] = $database->loadObjectList('qp_id');
	
		if (empty($lists['all'])) {
			$app = JFactory::getApplication();
			$app->redirect( "index.php?option=com_joomlaquiz&task=reactivate", JText::_('COM_JOOMLAQUIZ_INVALID_PAYMENT') );	
		}
		
		return $lists;
	}
}