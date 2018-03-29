<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');
 
/**
 * Joomlaquiz Deluxe Table class
 */
class JoomlaquizTableProducts extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db) 
        {
            parent::__construct('#__quiz_products', 'pid', $db);
        }
		
		function store($updateNulls = false){

            $app   = \JFactory::getApplication();
            $database = \JFactory::getDBO();
            $dataPOST = $app->input->getArray(array());

            $product_id = '-1';
            if(isset($dataPOST['vm_product_id']) && $dataPOST['vm_product_id'] != -1){
                $product_id = (int)$dataPOST['vm_product_id'];
            }
            if(isset($dataPOST['hikashop_product_id']) && $dataPOST['hikashop_product_id'] != -1){
                $product_id = (int)$dataPOST['hikashop_product_id'];
            }
            $product_id_int = (string)intval($product_id);

            $name = htmlspecialchars(trim($dataPOST['jform']['name']), ENT_QUOTES, 'UTF-8');

            if($product_id == -1 && $name == ''){
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_PRODUCT')."'); window.history.go(-1); </script>\n";
				exit();
			}

			$quiz_sku = '';
			if ($product_id) {
				$query = "SELECT quiz_sku"
					. "\n FROM #__quiz_product_info"
					. "\n WHERE `quiz_sku` = '{$product_id}'"
					;
				$database->setQuery($query);
				$quiz_sku = $database->loadResult();			
				
				if ($quiz_sku) {
					$query = "UPDATE #__quiz_product_info SET `name` = '{$name}' WHERE `quiz_sku` = '".$quiz_sku."' ";
					$database->setQuery($query);
					$database->execute();	
				}
			}

			if (($product_id && $product_id_int != $product_id && !$quiz_sku) || ($product_id == '-1' && $name != '')){
				$quiz_sku = strtotime(JFactory::getDate());
				$query = "INSERT INTO #__quiz_product_info SET `quiz_sku` = '".$quiz_sku."', `name` = '{$name}'";
				$database->setQuery($query);
				$database->execute();

				$_REQUEST['name'] = $name;
			}

			if ($quiz_sku)
				$product_id = $quiz_sku;

			$types = array('q', 'l');
			$insert = array();
			$not_for_delete = array();
			foreach($types as $type) {
				$ids = ($_POST[$type . '_ids']) ? $_POST[$type . '_ids'] : array();
				
				foreach($ids as $id) {
					$values = array();
					$values[] = $product_id;
					$values[] = $type;
					$values[] = $id;
					
					$access = ($_POST[$type . '_access_' . $id]) ? intval($_POST[$type . '_access_' . $id]) : 0;

					if($access == 0) {
						$xdays = ($_POST[$type . '_xdays_' . $id]) ? intval($_POST[$type . '_xdays_' . $id]) : 0;
					} else {
						$xdays = 0;
					}
					$values[] = $xdays;

					if($access == 0) {
						$period_start = '0000-00-00';
					} else {
						$period_start = ($_POST[$type . '_period_start_' . $id]) ? $_POST[$type . '_period_start_' . $id] : '0000-00-00';
						$period_start = JHtml::_('date',strtotime($period_start), 'Y-m-d');
					}
					$values[] = $period_start;

					if($access == 0) {
						$period_end = '0000-00-00';
					} else {
						$period_end = ($_POST[$type . '_period_end_' . $id]) ? $_POST[$type . '_period_end_' . $id] : '0000-00-00';
						$period_end = ($period_end && $period_end != '0000-00-00' ? JHtml::_('date',strtotime($period_end), 'Y-m-d') : '');
					}
					$values[] = $period_end;
					
					$attempts = ($_POST[$type . '_attempts_' . $id]) ? intval($_POST[$type . '_attempts_' . $id]) : 0;
					$values[] = $attempts;

					$query = "SELECT id"
					. "\n FROM #__quiz_products"
					. "\n WHERE `pid` = '{$product_id}' AND `type` = '$type' AND `rel_id` = $id"
					;
					$database->setQuery($query);
					$update_id = $database->loadResult();
					if($update_id) {
						$query = 'UPDATE #__quiz_products SET'
						. "\n `xdays` = $xdays,"
						. "\n `period_start` = '$period_start',"
						. "\n `period_end` = '$period_end',"
						. "\n `attempts` = '$attempts', "
						. "   `pid` = '{$product_id}' "
						. "\n WHERE `id` = '$update_id'"
						;
						$database->setQuery($query);
						if(!$database->execute()) {
							echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
							continue;
						}
						$not_for_delete[] = $update_id;
					} else {
						$insert[] = '(\'' . implode('\', \'', $values) . '\')';
					}
				}
			}

			if(!count($insert) && !count($not_for_delete)) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_QUIZZES_OR_LEARNING')."'); window.history.go(-1); </script>\n";
				exit();
			}

			$query = 'DELETE FROM #__quiz_products'
			. "\n WHERE `pid` = '{$product_id}'"
			. (count($not_for_delete) ? ' AND id NOT IN (' . implode(',', $not_for_delete) . ')' : '')
			;
			$database->setQuery($query);
			if(!$database->query()) {
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
				exit();
			}

			if(count($insert)) {
				$query = 'INSERT INTO #__quiz_products'
				. "\n (`pid`, `type`, `rel_id`, `xdays`, `period_start`, `period_end`, `attempts`)"
				. "\n VALUES"
				. "\n " . implode(", \n", $insert)
				;
				$database->setQuery($query);
				if(!$database->execute()) {
					echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			
			$_REQUEST['pid'] = $product_id;

			return true;
		}
}