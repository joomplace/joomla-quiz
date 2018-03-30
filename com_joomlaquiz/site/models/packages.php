<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
/**
 * Packages Model.
 *
 */
class JoomlaquizModelPackages extends JModelList
{	
	public function getPackages()
    {
        $lang = \JFactory::getLanguage()->getTag();
        $lang = strtolower(str_replace('-', '_', $lang));

        $database = \JFactory::getDBO();
        $user = \JFactory::getUser();

        if (!$user->id) {
            $packages = array();
            $packages[0] = new stdClass;
            $packages[0]->error = 1;
            $packages[0]->message = JText::_('COM_PACKAGES_FOR_REGISTERED');
            return $packages[0];
        }

        $query = "SELECT * FROM `#__quiz_t_quiz` WHERE `published` = '1'";
        $database->SetQuery($query);
        $all_quizzez = $database->loadObjectList('c_id');

        $no_virtuemart = true;
        if (file_exists(JPATH_SITE . '/administrator/components/com_virtuemart/helpers/config.php')) {
            $no_virtuemart = false;
        }
        $orders_virtuemart = array();

        $isHikaShop = false;
        if (file_exists(JPATH_BASE . '/administrator/components/com_hikashop/config.xml')) {
            $isHikaShop = true;
        }
        $orders_hikashop = array();

        if (!$no_virtuemart) {
            $query = "SELECT `vm_orders`.`virtuemart_order_id` as `order_id`, `vm_orders`.`virtuemart_order_id` as `id`, `vm_orders`.`virtuemart_user_id` as `user_id`, `vm_orders`.`virtuemart_vendor_id` as `vendor_id`, 'virtuemart' AS `shop`"
                . "\n FROM `#__virtuemart_orders` AS `vm_orders`"
                . "\n WHERE `vm_orders`.`virtuemart_user_id` = '".$user->id."'"
                . "\n ORDER BY `vm_orders`.`created_on` DESC";
            $database->SetQuery($query);
            $orders_virtuemart = $database->loadObjectList();
        }

        if ($isHikaShop) {
            //HikaShop Starter: no vendors
            $query = "SELECT `ho`.`order_id` as `order_id`, `ho`.`order_id` as `id`, `hu`.`user_cms_id` as user_id, '1' as vendor_id, 'hikashop' AS `shop` "
                . "\n FROM `#__hikashop_order` AS `ho`"
                . "\n LEFT JOIN `#__hikashop_user` AS `hu` ON `hu`.`user_id` = `ho`.`order_user_id`"
                . "\n WHERE `hu`.`user_cms_id` = '".$user->id."'"
                . "\n ORDER BY `ho`.`order_created` DESC";
            $database->SetQuery($query);
            $orders_hikashop = $database->loadObjectList();
        }

        $query = "SELECT `payments`.*, '' AS `shop` "
            . "\n FROM `#__quiz_payments` AS `payments` "
            . "\n WHERE `payments`.`user_id` = '".$user->id."'"
            . "\n ORDER BY `payments`.`date` DESC";
        $database->SetQuery($query);
        $payments = $database->loadObjectList();

        $orders = array();
        if( (is_array($payments) && count($payments))
                || (is_array($orders_virtuemart) && count($orders_virtuemart))
                    || (is_array($orders_hikashop) && count($orders_hikashop))
        ) {
            $orders = array_merge($orders_virtuemart, $orders_hikashop, $payments);
        }

        $packages = array();
        if (is_array($orders) && count($orders)) {
            foreach ($orders as $i => $order) {

                $package = new stdClass;

                $query = '';
                if (!$order->shop) {
                    $query = "SELECT `p`.`id`, `p`.`id` AS `order_id`, `p`.`confirmed_time` AS `date_added`, '' AS `order_status_id`, (IF(`status` = 'Confirmed', 'C', '')) AS `order_status_code`, `status` AS `order_status_name` "
                        . "\n FROM `#__quiz_payments` AS `p`"
                        . "\n WHERE `p`.`id` = '" . (int)$order->id . "' "
                        . "\n ORDER BY `p`.`confirmed_time`";
                } else {
                    if ($order->shop == 'virtuemart'){
                        $query = "SELECT `vm_order_history`.*, `vm_order_history`.`created_on` as `date_added`, `vm_order_history`.`virtuemart_order_id` as `order_id`, `vm_order_status`.`virtuemart_orderstate_id` as `order_status_id`, `vm_order_status`.`order_status_name`"
                            . "\n FROM `#__virtuemart_order_histories` AS `vm_order_history`"
                            . "\n INNER JOIN `#__virtuemart_orderstates` AS `vm_order_status` ON (`vm_order_status`.`order_status_code` = `vm_order_history`.`order_status_code` AND `vm_order_status`.`virtuemart_vendor_id` = '" . (int)$order->vendor_id . "')"
                            . "\n WHERE `vm_order_history`.`virtuemart_order_id` = '". (int)$order->order_id ."'"
                            . "\n ORDER BY `vm_order_history`.`virtuemart_order_history_id` DESC, `vm_order_history`.`created_on` DESC";
                    }
                    else if ($order->shop == 'hikashop') {
                        //HikaShop Starter: no vendors
                        $query = "SELECT `hh`.*, FROM_UNIXTIME(`hh`.`history_created`) as `date_added`, `hh`.`history_order_id` as `order_id`,"
                            . "\n `hos`.`orderstatus_id` as `order_status_id`, `hos`.`orderstatus_name` AS `order_status_name`,"
                            . "\n `hh`.`history_new_status` as `order_status_code`"
                            . "\n FROM `#__hikashop_history` AS `hh`"
                            . "\n LEFT JOIN `#__hikashop_orderstatus` AS `hos` ON `hos`.`orderstatus_namekey` = `hh`.`history_new_status`"
                            . "\n WHERE `hh`.`history_order_id` = '". (int)$order->order_id ."'"
                            . "\n ORDER BY `hh`.`history_id` DESC, `hh`.`history_created` DESC";
                    }
                }
                $orders_status = array();
                if($query) {
                    $database->SetQuery($query);
                    $orders_status = $database->loadObject();
                }

                $query = '';
                if (!$order->shop) {
                    $query = "SELECT `qp`.*"
                        . "\n FROM `#__quiz_payments` AS `p`"
                        . "\n INNER JOIN `#__quiz_products` AS `qp` ON `qp`.`pid` = `p`.`pid`"
                        . "\n LEFT JOIN `#__quiz_t_quiz` AS `q` ON `qp`.`type` = 'q' AND `q`.`c_id` = `qp`.`rel_id`"
                        . "\n LEFT JOIN `#__quiz_lpath` AS `lp` ON `qp`.`type` = 'l' AND `lp`.`id` = `qp`.`rel_id`"
                        . "\n WHERE `p`.`id` = '".(int)$order->id."'"
                        . "\n ORDER BY `q`.`c_title`, `lp`.`title`";
                } else {
                    if ($order->shop == 'virtuemart'){
                        $query = "SELECT `qp`.*"
                            . "\n FROM `#__virtuemart_order_items` AS `vm_oi`"
                            . "\n INNER JOIN `#__quiz_products` AS `qp` ON `qp`.`pid` = `vm_oi`.`virtuemart_product_id`"
                            . "\n LEFT JOIN `#__quiz_t_quiz` AS `q` ON `qp`.`type` = 'q' AND `q`.`c_id` = `qp`.`rel_id`"
                            . "\n LEFT JOIN `#__quiz_lpath` AS `lp` ON `qp`.`type` = 'l' AND `lp`.`id` = `qp`.`rel_id`"
                            . "\n WHERE `vm_oi`.`virtuemart_order_id` = '".(int)$order->order_id."'"
                            . "\n ORDER BY `q`.`c_title`, `lp`.`title`";
                    }
                    else if ($order->shop == 'hikashop') {
                        $query = "SELECT `qp`.*"
                            . "\n FROM `#__hikashop_order_product` AS `hop`"
                            . "\n INNER JOIN `#__quiz_products` AS `qp` ON `qp`.`pid` = `hop`.`product_id`"
                            . "\n LEFT JOIN `#__quiz_t_quiz` AS `q` ON `qp`.`type` = 'q' AND `q`.`c_id` = `qp`.`rel_id`"
                            . "\n LEFT JOIN `#__quiz_lpath` AS `lp` ON `qp`.`type` = 'l' AND `lp`.`id` = `qp`.`rel_id`"
                            . "\n WHERE `hop`.order_id = '".(int)$order->order_id."'"
                            . "\n ORDER BY `q`.`c_title`, `lp`.`title`";
                    }
                }
                $quiz_products = array();
                if($query) {
                    $database->SetQuery($query);
                    $quiz_products = $database->loadObjectList();
                }

                $rel_quizzes = array();
                if (is_array($quiz_products) && count($quiz_products)) {
                    foreach ($quiz_products as $q) {
                        if(!isset($rel_quizzes[$q->type])){
                            $rel_quizzes[$q->type] = array();
                        }
                        $rel_quizzes[$q->type][] = $q;
                    }
                }
                if (!count($rel_quizzes)) {
                    continue;
                }

                $products = array();
                $query = '';
                if (!$order->shop) {
                    $query = "SELECT `qpi`.*, `qpi`.`name` AS `product_name`"
                        . "\n FROM `#__quiz_payments` AS `p`"
                        . "\n INNER JOIN `#__quiz_product_info` AS `qpi` ON `qpi`.`quiz_sku` = `p`.`pid`"
                        . "\n WHERE `p`.`id` = '". (int)$order->id ."'"
                        . "\n ORDER BY `qpi`.`name`";
                } else {
                    if ($order->shop == 'virtuemart'){
                        $query = "SELECT `vm_p`.*, `vm_p_engb`.`product_name`, `vm_p`.`virtuemart_product_id` as `product_id`"
                            . "\n FROM `#__virtuemart_order_items` AS `vm_oi`"
                            . "\n INNER JOIN `#__virtuemart_products` AS `vm_p` ON `vm_p`.`virtuemart_product_id` = `vm_oi`.`virtuemart_product_id`"
                            . "\n INNER JOIN `#__virtuemart_products_" . $lang . "` AS `vm_p_engb` ON `vm_p_engb`.`virtuemart_product_id` = `vm_oi`.`virtuemart_product_id`"
                            . "\n WHERE `vm_oi`.`virtuemart_order_id` = '". (int)$order->order_id ."'"
                            . "\n ORDER BY `vm_p_engb`.`product_name`";
                    }
                    else if ($order->shop == 'hikashop') {
                        $query = "SELECT `hp`.*"
                            . "\n FROM `#__hikashop_order_product` AS `hop`"
                            . "\n INNER JOIN `#__hikashop_product` AS `hp` ON `hp`.`product_id` = `hop`.`product_id`"
                            . "\n WHERE `hop`.`order_id` = '". (int)$order->order_id ."'"
                            . "\n ORDER BY `hp`.`product_name`";
                    }
                }
                if($query) {
                    $database->SetQuery($query);
                    $products_all = $database->loadObjectList();
                    if (is_array($products_all) && count($products_all)) {
                        foreach ($products_all as $product) {
                            $products[] = $product->product_name;
                        }
                    }
                }

                $query_order_id = (int)$order->id;
                if (!$order->shop) {
                    $query_order_id = (int)$order->id + 1000000000;
                }
                $query = "SELECT * FROM `#__quiz_products_stat` WHERE `uid` = '".(int)$user->id."' AND `oid` = '". $query_order_id ."'";
                unset($query_order_id);
                $database->SetQuery($query);
                $products_stat = $database->loadObjectList('qp_id');

                //$ts = strtotime(\JFactory::getDate());
                $ts = strtotime(\JFactory::getDate('now', JFactory::getConfig()->get('offset', 'UTC')));

                $bought_quizzes = array();
                $bq_count = 0;
                $bq_counter_exiped = 0;

                if (array_key_exists('q', $rel_quizzes) && count($rel_quizzes['q'])) {
                    $bq_count = count($rel_quizzes['q']);

                    foreach ($rel_quizzes['q'] as $ii => $data) {
                        if (empty($all_quizzez[$data->rel_id])) {
                            continue;
                        }

                        $days_left = 0;
                        $data->expired = false;
                        $data->expired_a = false;
                        if ($data->xdays > 0) {
                            if (!empty($products_stat) && array_key_exists($data->id, $products_stat)) {
                                $confirm_date = strtotime($products_stat[$data->id]->xdays_start);
                            } else {
                                $confirm_date = strtotime($orders_status->date_added);
                            }

                            if ($confirm_date) {
                                $ts_day_end = $confirm_date + $data->xdays * 24 * 60 * 60;
                                if ($ts > $ts_day_end) {
                                    $days_left = 0;
                                    $bq_counter_exiped++;
                                    $data->expired = true;
                                    $data->suffix = JText::_('COM_QUIZ_EXPIRED');
                                } else {
                                    $days_left = ceil(($ts_day_end - $ts) / (24 * 60 * 60));
                                }
                            } else {
                                $days_left = 0;
                                $bq_counter_exiped++;
                                $data->expired = true;
                            }

                            $data->suffix = sprintf(JText::_('COM_QUIZ_XDAYS'), $days_left);

                        } else {
                            if (($data->period_start && $data->period_start != '0000-00-00') || ($data->period_end && $data->period_end != '0000-00-00')) {
                                if (!empty($products_stat) && array_key_exists($data->id, $products_stat)) {
                                    $data->period_start = $products_stat[$data->id]->period_start;
                                    $data->period_end = $products_stat[$data->id]->period_end;
                                }

                                $period = array();

                                $ts_start = null;
                                if ($data->period_start && $data->period_start != '0000-00-00') {
                                    $ts_start = strtotime($data->period_start . ' 00:00:00');
                                    $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_FROM'),
                                        date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_start));
                                }

                                $ts_end = null;
                                if ($data->period_end && $data->period_end != '0000-00-00') {
                                    $ts_end = strtotime($data->period_end . ' 23:59:59');
                                    $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_TO'),
                                        date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_end));
                                }

                                $data->suffix = sprintf(JText::_('COM_QUIZ_PERIOD'), implode(' ', $period));

                                if (($ts_start && $ts_start > $ts) || ($ts_end && $ts_end < $ts)) {
                                    $bq_counter_exiped++;
                                    $data->expired = true;
                                }

                            }
                        }

                        $product_quantity = 1;
                        if ($data->attempts > 0) {
                            if ($order->shop) {
                                $query = '';
                                if ($order->shop == 'virtuemart'){
                                    $query = "SELECT `vm_oi`.`product_quantity`"
                                        . "\n FROM `#__virtuemart_orders` AS `vm_o`"
                                        . "\n INNER JOIN `#__virtuemart_order_items` AS `vm_oi` ON `vm_oi`.`virtuemart_order_id` = `vm_o`.`virtuemart_order_id`"
                                        . "\n INNER JOIN `#__quiz_products` AS `qp` ON `qp`.`pid` = `vm_oi`.`virtuemart_product_id`"
                                        . "\n WHERE `vm_o`.`virtuemart_user_id` = '".(int)$user->id."' AND `vm_o`.`virtuemart_order_id` = '".(int)$order->order_id."'"
                                        . "\n AND `qp`.`id` = '".(int)$data->id."' AND `vm_o`.`order_status` IN ('C')";
                                }
                                else if ($order->shop == 'hikashop') {
                                    $query = "SELECT `hop`.`order_product_quantity`"
                                        . "\n FROM `#__hikashop_order` AS `ho`"
                                        . "\n INNER JOIN `#__hikashop_order_product` AS `hop` ON `hop`.`order_id` = `ho`.`order_id`"
                                        . "\n INNER JOIN `#__quiz_products` AS `qp` ON `qp`.`pid` = `hop`.`product_id`"
                                        . "\n LEFT JOIN `#__hikashop_user` AS `hu` ON `hu`.`user_id` = `ho`.`order_user_id`"
                                        . "\n WHERE `hu`.`user_cms_id` = '".(int)$user->id."' AND `ho`.`order_id` = '".(int)$order->order_id."'"
                                        . "\n AND `qp`.`id` = '".(int)$data->id."' AND `ho`.`order_status` IN ('confirmed')";
                                }
                                if($query) {
                                    $database->SetQuery($query);
                                    $product_quantity_result = $database->loadResult();
                                    $product_quantity = (int)$product_quantity_result ? (int)$product_quantity_result : 1;
                                }
                            }

                            $attempts = (!empty($products_stat) && array_key_exists($data->id, $products_stat) && $products_stat[$data->id]->attempts ? $products_stat[$data->id]->attempts : 0);
                            $attempts_left = ($data->attempts * $product_quantity) - $attempts;

                            if ($data->xdays > 0) {
                                $data->suffix = sprintf(JText::_('COM_QUIZ_XDAYS_ATTEMPTS'), $attempts_left, $days_left);
                            } else {
                                if (($data->period_start && $data->period_start != '0000-00-00') || ($data->period_end && $data->period_end != '0000-00-00')) {
                                    $data->suffix = sprintf(JText::_('COM_QUIZ_PERIOD_ATTEMPTS'), $attempts_left, implode(' ', $period));
                                } else {
                                    if(isset($data->suffix)){
                                        $data->suffix .= ' ' . sprintf(JText::_('COM_QUIZ_ATTEMPTS'), $attempts_left);
                                    } else {
                                        $data->suffix = sprintf(JText::_('COM_QUIZ_ATTEMPTS'), $attempts_left);
                                    }
                                }
                            }

                            if (($data->attempts * $product_quantity) <= $attempts) {
                                $bq_counter_exiped++;
                                $data->expired_a = true;
                                $data->expired = true;
                            }
                        }

                        $quiz = $all_quizzez[$data->rel_id];

                        if ($data->expired) {
                            $data->suffix = JText::_('COM_QUIZ_EXPIRED');
                        }

                        if ($data->expired_a) {
                            $data->suffix = JText::_('COM_QUIZ_NOT_ATTEMPTS2');
                        }

                        $bought_quizze = new stdClass;
                        $bought_quizze->quiz = $quiz;
                        $bought_quizze->rel_id = $data->id;
                        $bought_quizze->suffix = isset($data->suffix) ? $data->suffix : '';
                        $bought_quizze->expired = $data->expired;
                        $bought_quizzes[] = $bought_quizze;
                    }
                }

                $lpaths = array();
                $l_count = 0;
                $l_counter_exiped = 0;
                if (array_key_exists('l', $rel_quizzes) && count($rel_quizzes['l'])) {
                    $query = "SELECT * FROM `#__quiz_lpath` WHERE `published` = '1'";
                    $database->setQuery($query);
                    $lpath = $database->loadObjectList('id');
                    if (!empty($lpath)) {
                        $l_count = count($rel_quizzes['l']);

                        if (is_array($rel_quizzes['l']) && count($rel_quizzes['l'])) {
                            foreach ($rel_quizzes['l'] as $data) {
                                if (empty($lpath[$data->rel_id])) {
                                    continue;
                                }

                                $data->title = $lpath[$data->rel_id]->title;
                                $data->short_descr = $lpath[$data->rel_id]->short_descr;
                                $data->descr = $lpath[$data->rel_id]->descr;
                                $data->expired = false;

                                $days_left = 0;

                                if ($data->xdays > 0) {
                                    if (!empty($products_stat) && array_key_exists($data->id, $products_stat)) {
                                        $confirm_date = strtotime($products_stat[$data->id]->xdays_start);
                                    } else {
                                        $confirm_date = strtotime($orders_status->date_added);
                                    }

                                    if ($confirm_date) {
                                        $ts_day_end = $confirm_date + $data->xdays * 24 * 60 * 60;

                                        if (time() > $ts_day_end) {
                                            $l_counter_exiped++;
                                            $days_left = 0;
                                            $data->expired = true;
                                        } else {
                                            $days_left = ceil(($ts_day_end - time()) / (24 * 60 * 60));
                                        }
                                    } else {
                                        $l_counter_exiped++;
                                        $days_left = 0;
                                        $data->expired = true;
                                    }

                                    $data->suffix = sprintf(JText::_('COM_LPATH_XDAYS'), $days_left);

                                } else {
                                    if (($data->period_start && $data->period_start != '0000-00-00') || ($data->period_end && $data->period_end != '0000-00-00')) {
                                        if (!empty($products_stat) && array_key_exists($data->id, $products_stat)) {
                                            $data->period_start = $products_stat[$data->id]->period_start;
                                            $data->period_end = $products_stat[$data->id]->period_end;
                                        }

                                        $period = array();

                                        $ts_start = null;
                                        if ($data->period_start && $data->period_start != '0000-00-00') {
                                            $ts_start = strtotime($data->period_start . ' 00:00:00');
                                            $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_FROM'),
                                                date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_start));
                                        }

                                        $ts_end = null;
                                        if ($data->period_end && $data->period_end != '0000-00-00') {
                                            $ts_end = strtotime($data->period_end . ' 23:59:59');
                                            $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_TO'),
                                                date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_end));
                                        }

                                        $data->suffix = sprintf(JText::_('COM_LPATH_PERIOD'), implode(' ', $period));

                                        if (($ts_start && $ts_start > $ts) || ($ts_end && $ts_end < $ts)) {
                                            $l_counter_exiped++;
                                            $data->expired = true;
                                        }
                                    }
                                }

                                $product_quantity = 1;
                                if ($data->attempts > 0) {
                                    if ($order->shop) {
                                        $query = '';
                                        if ($order->shop == 'virtuemart'){
                                            $query = "SELECT `vm_oi`.`product_quantity`"
                                                . "\n FROM `#__virtuemart_orders` AS `vm_o`"
                                                . "\n INNER JOIN `#__virtuemart_order_items` AS `vm_oi` ON `vm_oi`.`virtuemart_order_id` = `vm_o`.`virtuemart_order_id`"
                                                . "\n INNER JOIN `#__quiz_products` AS `qp` ON `qp`.`pid` = `vm_oi`.`virtuemart_product_id`"
                                                . "\n WHERE `vm_o`.`virtuemart_user_id` = '".(int)$user->id."' AND `vm_o`.`virtuemart_order_id` = '".(int)$order->order_id."'"
                                                . "\n AND `qp`.`id` = '".(int)$data->id."' AND `vm_o`.`order_status` IN ('C')";
                                        }
                                        else if ($order->shop == 'hikashop') {
                                            $query = "SELECT `hop`.`order_product_quantity`"
                                                . "\n FROM `#__hikashop_order` AS `ho`"
                                                . "\n INNER JOIN `#__hikashop_order_product` AS `hop` ON `hop`.`order_id` = `ho`.`order_id`"
                                                . "\n INNER JOIN `#__quiz_products` AS `qp` ON `qp`.`pid` = `hop`.`product_id`"
                                                . "\n LEFT JOIN `#__hikashop_user` AS `hu` ON `hu`.`user_id` = `ho`.`order_user_id`"
                                                . "\n WHERE `hu`.`user_cms_id` = '".(int)$user->id."' AND `ho`.`order_id` = '".(int)$order->order_id."'"
                                                . "\n AND `qp`.`id` = '".(int)$data->id."' AND `ho`.`order_status` IN ('confirmed')";
                                        }
                                        if($query) {
                                            $database->SetQuery($query);
                                            $product_quantity_result = $database->loadResult();
                                            $product_quantity = (int)$product_quantity_result ? (int)$product_quantity_result : 1;
                                        }
                                    }

                                    $data->suffix .= ($data->suffix ? ' ' : '') . sprintf(JText::_('COM_LPATH_ATTEMPTS'), $data->attempts * $product_quantity);

                                    $attempts = (!empty($products_stat) && array_key_exists($data->id, $products_stat) && $products_stat[$data->id]->attempts ? $products_stat[$data->id]->attempts : 0);
                                    if (($data->attempts * $product_quantity) <= $attempts) {
                                        $l_counter_exiped++;
                                        $data->expired = true;
                                    }
                                }
                                if ($data->expired) {
                                    $data->suffix = JText::_('COM_LPATH_EXPIRED');
                                }

                                $lpaths[] = $data;
                            }
                        }
                    }
                }

                if (!(int)$bq_count && !(int)$l_count) {
                    continue;
                }

                $package->shop = $order->shop;
                $package->package_number = $order->shop ? $orders_status->order_id : $orders_status->order_id + 1000000000;
                $package->order_status_code = $orders_status->order_status_code;
                $package->order_status_name = JText::_($orders_status->order_status_name);
                $package->order_status_date = date(JText::_('COM_PACKAGE_STATUS_FROM_FORMAT'), strtotime($orders_status->date_added));
                $package->bought_quizzes = $bought_quizzes;
                $package->lpaths = $lpaths;
                $package->products = $products;

                $package->expired = false;
                if (($bq_count && !$l_count && $bq_counter_exiped == $bq_count)
                        || (!$bq_count && $l_count && $l_counter_exiped == $l_count)
                            || ($bq_count && $l_count && $bq_counter_exiped == $bq_count && $l_counter_exiped == $l_count)
                ) {
                    $package->expired = true;
                }

                $packages[] = $package;
                unset($package);
            }
        }
		return $packages;
	}
}
