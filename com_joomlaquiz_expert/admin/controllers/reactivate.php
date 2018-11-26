<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controllerform');
 
/**
 * Reactivate Controller
 */
class JoomlaquizControllerReactivate extends JControllerForm
{
    protected function allowEdit($data = array(), $key = 'id')
    {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_joomlaquiz');             
    }
	
	public function apply_reactivate()
	{
		$database = JFactory::getDBO();
		$oid = JFactory::getApplication()->input->get('oid', 0);
		if (!$oid) {
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_WRONG_ID')."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$vm	= $oid < 1000000000;
		
		$cids = JFactory::getApplication()->input->get('cid', array(), '');
		if (empty($cids)) {
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_QUIZZES_OR_LEARNING')."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if ($vm){
			$query = "SELECT virtuemart_user_id FROM #__virtuemart_orders WHERE virtuemart_order_id = '{$oid}' ";
		} else {
			$query = "SELECT user_id FROM #__quiz_payments WHERE id = '".($oid-1000000000)."' ";
		}
		$database->SetQuery( $query );
		$uid = $database->loadResult();
		if (!$uid) {
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_WRONG_ORDER')."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$query = "SELECT *"
		. "\n FROM #__quiz_products"
		. "\n WHERE id IN (" . implode(',', $cids) . ")"
		;
		$database->SetQuery( $query );
		$quiz_products = $database->loadObjectList('id');
		
		$query = "SELECT id, qp_id"
			. "\n FROM #__quiz_products_stat"
			. "\n WHERE oid = '{$oid}' AND uid = $uid AND qp_id IN (" . implode(',', $cids) . ")"
			;

		$database->SetQuery( $query );
		$quiz_products_stat = $database->loadObjectList('qp_id');
		
		$xdays = JFactory::getApplication()->input->get('xdays', array(), '');
		$period = JFactory::getApplication()->input->get('period', array(), '');
		$attempts = JFactory::getApplication()->input->get('attempts', array(), '');
		
		foreach($quiz_products as $qp) {
			$set = array();
			if($qp->xdays && in_array($qp->id, $xdays)) {
				$set[] = "`xdays_start` = '" . date('Y-m-d H:i:s') . "'";
				$set[] = "`period_start` = ''";	
				$set[] = "`period_end` = ''";	
			} else if(in_array($qp->id, $period)) {
				$set[] = "`xdays_start` = ''";
				$set[] = "`period_start` = '" . date('Y-m-d') . "'";
				if(!$qp->period_end || $qp->period_end == '0000-00-00') {
					$set[] = "`period_end` = ''";
				} else {
					$ts_period = strtotime($qp->period_end . ' 23:59:59') - strtotime($qp->period_start . ' 00:00:00');
					$ts_needed_end = strtotime(JFactory::getDate()) + $ts_period;
					$set[] = "`period_end` = '" . JHtml::_('date',$ts_needed_end, 'Y-m-d') . "'";	
				}
			}
			
			if(in_array($qp->id, $attempts)) {
				$set[] = "`attempts` = '0'";			
				$query = "UPDATE #__quiz_lpath_stage SET `attempts` = '0' WHERE oid = '{$oid}' AND uid = '{$uid}' AND rel_id IN (" . implode(',', $cids) . ") ";
				$database->setQuery($query);
				$database->execute();
			}

			if(!empty($set) && array_key_exists($qp->id, $quiz_products_stat)) {
				$query = 'UPDATE #__quiz_products_stat SET ' . implode(', ', $set)
				. ' WHERE `id` = ' . $quiz_products_stat[$qp->id]->id;
			} else {
				$set[] = "`uid` = '" . $uid . "'";
				$set[] = "`oid` = '" . $oid . "'";
				$set[] = "`qp_id` = '" . $qp->id . "'";
				$query = 'INSERT INTO #__quiz_products_stat SET ' . implode(', ', $set);
			}
			$database->setQuery($query);

			if (!$database->execute()) {
				echo "<script> alert('".addslashes($database->getErrorMsg())."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
		
		$this->setRedirect('index.php?option=com_joomlaquiz&view=reactivate&layout=edit&id=' . $oid, JText::_('COM_JOOMLAQUIZ_ACCOUNT_WAS_REACTIVATED'));
	}

}
