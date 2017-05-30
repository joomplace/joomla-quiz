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

        $lang = JFactory::getLanguage()->getTag();
        $lang = strtolower(str_replace('-', '_', $lang));

        $db = JFactory::getDBO();
        $my = JFactory::getUser();

        $jq_language = array();
        if (!$my->id) {
            $packages = array();
            $packages[0] = new stdClass;
            $packages[0]->error = 1;
            $packages[0]->message = JText::_('COM_PACKAGES_FOR_REGISTERED');
            return $packages[0];
        }

        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->quoteName('#__quiz_t_quiz'))
            ->where($db->quoteName('published') . ' =1');
//		$query = "SELECT * FROM `#__quiz_t_quiz` WHERE published = 1";
        $db->SetQuery($query);
        $all_quizzez = $db->loadObjectList('c_id');

        $orders = array();

        if (file_exists(JPATH_SITE . '/administrator/components/com_virtuemart/helpers/config.php'))
            $no_virtuemart = false;
        else
            $no_virtuemart = true;

        if (file_exists(JPATH_SITE . '/administrator/components/com_j2store/config.xml')) {
            $no_j2store = false;
        } else {
            $no_j2store = true;
        }

        if (file_exists(JPATH_SITE . '/administrator/components/com_eventbooking/config.xml')) {
            $no_event_booking = false;
        } else {
            $no_event_booking = true;
        }

        //Get orders for j2store for current user
        if (!$no_j2store) {
            $j2s_orders = array();
            $query->clear();
            $query->select($db->quoteName(
                array(
                    'jo.j2store_order_id',
                    'jo.user_id'
                ),
                array(
                    'order_id',
                    'user_id'
                )
            ))
                ->select('\'j2s\' AS `product_type`')
                ->from($db->quoteName('#__j2store_orders', 'jo'))
                ->where($db->quoteName('jo.user_id') . ' = ' . $db->quote($my->id))
                ->order($db->quoteName('jo.created_on') . ' DESC');
            $db->setQuery($query);
            $j2s_orders = $db->loadObjectList();

        //Add j2store order if available
            if (is_array($j2s_orders) && count($j2s_orders)) {
                $orders = array_merge($orders, $j2s_orders);
            }
        }

        //Get orders for Event Booking for current user
        if (!$no_event_booking) {
            $event_booking_orders = array();
            $query->clear();
            $query->select($db->quoteName(
                array(
                    'ebr.id',
                    'ebr.user_id'
                ),
                array(
                    'order_id',
                    'user_id'
                )
            ))
                ->select('\'eb\' AS `product_type`')
                ->from($db->quoteName('#__eb_registrants', 'ebr'))
                ->where($db->quoteName('ebr.user_id') . ' = ' . $db->quote($my->id))
                ->where($db->quoteName('ebr.published') . ' = 1')
                ->order($db->quoteName('ebr.payment_date') . ' DESC');
            $db->setQuery($query);
            $event_booking_orders = $db->loadObjectList();

        //Add event booking orders if available
            if (is_array($event_booking_orders) && count($event_booking_orders)) {
                $orders = array_merge($orders, $event_booking_orders);
            }
        }

        //Get orders for Virtue Mart for current user
        if (!$no_virtuemart) {
            $vm_orders = array();
            $query = "SELECT vm_orders.virtuemart_order_id as order_id, vm_orders.virtuemart_user_id as user_id, vm_orders.virtuemart_vendor_id as vendor_id, 'vm' AS `product_type`"
                . "\n FROM `#__virtuemart_orders` AS vm_orders"
                . "\n WHERE vm_orders.virtuemart_user_id = '{$my->id}'"
                . "\n ORDER BY vm_orders.created_on DESC";

            $db->SetQuery($query);
            $vm_orders = $db->loadObjectList();

            //Add Virtue Mart orders if available
            if (is_array($vm_orders) && count($vm_orders)) {
                $orders = array_merge($orders, $vm_orders);
            }
        }

        //Get payments for orders through JoomQuiz
        $query = "SELECT `payments`.*, 'jq' AS `product_type`"
            . "\n FROM `#__quiz_payments` AS `payments` "
            . "\n WHERE `payments`.`user_id` = '{$my->id}'"
            . "\n ORDER BY `payments`.`date` DESC";
        $db->SetQuery($query);
        $payments = $db->loadObjectList();

        if (is_array($payments) && count($payments)) {
            if (is_array($orders))
                $orders = array_merge($orders, $payments);
            else
                $orders = $payments;
        }

        $packages = array();
        if (is_array($orders) && count($orders))
            foreach ($orders as $i => $order) {
                $package = new stdClass;

                if ($order->product_type == 'vm') {
                    $query = "SELECT vm_order_history.*, vm_order_history.created_on as date_added, vm_order_history.virtuemart_order_id as order_id, vm_order_status.virtuemart_orderstate_id as order_status_id, vm_order_status.order_status_name"
                        . "\n FROM `#__virtuemart_order_histories` AS vm_order_history"
                        . "\n INNER JOIN `#__virtuemart_orderstates` AS vm_order_status ON (vm_order_status.order_status_code = vm_order_history.order_status_code AND vm_order_status.virtuemart_vendor_id = " . $order->vendor_id . ')'
                        . "\n WHERE vm_order_history.virtuemart_order_id = " . $order->order_id
                        . "\n ORDER BY vm_order_history.virtuemart_order_history_id DESC, vm_order_history.created_on DESC";

                } elseif ($order->product_type == 'j2s') {
                    $query = $db->getQuery(true);
                    $query->select($db->quoteName(
                        array(
                            'jo.j2store_order_id',
                            'jo.created_on',
                            'jo.created_by',
                            'jo.modified_on',
                            'jo.modified_by',
                            'jo.order_state_id',
                            'js.orderstatus_name'
                        ),
                        array(
                            'order_id',
                            'date_added',
                            'created_by',
                            'modified_on',
                            'modified_by',
                            'order_status_code',
                            'order_status_name'
                        )
                    ))
                        ->from($db->quoteName('#__j2store_orders', 'jo'))
                        ->innerJoin($db->quoteName('#__j2store_orderstatuses', 'js')
                            . ' ON '
                            . $db->quoteName('js.j2store_orderstatus_id') . ' = ' . $db->quoteName('jo.order_state_id')
                        )
                        ->where($db->quoteName('jo.j2store_order_id') . ' = ' . $db->quote($order->order_id))
                        ->order($db->quoteName('jo.created_on') . ' DESC');
                } else if($order->product_type == 'eb'){
                    $query = $db->getQuery(true);
                    $query->select($db->quoteName(
                        array(
                            'ebr.id',
                            'ebr.register_date',
                            'ebr.published',
                        ),
                        array(
                            'order_id',
                            'date_added',
                            'order_status_code',
                        )
                    ))
                        ->select("IF (`ebr`.`published`, 'Confirmed', 'No comfirmed') AS `order_status_name`")
                        ->from($db->quoteName('#__eb_registrants', 'ebr'))
                        ->where($db->quoteName('ebr.id') . ' = ' . $db->quote($order->order_id))
                        ->order($db->quoteName('ebr.register_date') . ' DESC');
                    $db->setQuery($query);
                } else {
                    $query = "SELECT p.id, p.id AS `order_id`, p.confirmed_time AS `date_added`, '' AS `order_status_id`, (IF(`status` = 'Confirmed', 'C', '')) AS `order_status_code`, `status` AS `order_status_name` "
                        . "\n FROM #__quiz_payments AS p"
                        . "\n WHERE p.id = '" . $order->id . "' "
                        . "\n ORDER BY p.confirmed_time";
                }

                $db->SetQuery($query);
                $orders_status = $db->loadObject();

                if ($order->product_type == 'vm') {
                    JoomlaquizHelper::JQ_GetJoomFish($orders_status->order_status_name, 'vm_order_status', 'order_status_name', $orders_status->order_status_id);
                } else {
                    $orders_status->order_status_name = isset($jq_language[$orders_status->order_status_name]) ? $jq_language[$orders_status->order_status_name] : $orders_status->order_status_name;
                }

                if ($order->product_type == 'vm') {
                    $query = "SELECT qp.*"
                        . "\n FROM #__virtuemart_order_items AS vm_oi"
                        . "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id AND qp.pid_type = 'vm'"
                        . "\n LEFT JOIN #__quiz_t_quiz AS q ON qp.type = 'q' AND q.c_id = qp.rel_id "
                        . "\n LEFT JOIN #__quiz_lpath AS lp ON qp.type = 'l' AND lp.id = qp.rel_id"
                        . "\n WHERE vm_oi.virtuemart_order_id = " . $order->order_id
                        . "\n ORDER BY q.c_title, lp.title";
                } elseif ($order->product_type == 'j2s') {
                    //Get quiz product for j2store
                    $query->clear();
                    $query->select($db->quoteName('qp') . '.*')
                        ->from($db->quoteName('#__quiz_products', 'qp'))
                        ->leftJoin($db->quoteName('#__j2store_orderitems', 'ji')
                            . 'ON'
                            . $db->quoteName('ji.product_id') . ' = ' . $db->quoteName('qp.pid')
                            . ' AND '
                            . $db->quoteName('qp.pid_type') . ' = \'j2s\''
                        )
                        ->leftJoin($db->quoteName('#__j2store_orders', 'jo')
                            . ' ON '
                            . $db->quoteName('jo.cart_id') . ' = ' . $db->quoteName('ji.cart_id')
                        )
                        ->leftJoin($db->quoteName('#__quiz_t_quiz', 'q')
                            . ' ON '
                            . $db->quoteName('q.c_id') . ' = ' . $db->quoteName('qp.rel_id')
                            . ' AND '
                            . $db->quoteName('qp.type') . ' = \'q\''
                        )
                        ->leftJoin($db->quoteName('#__quiz_lpath', 'lp')
                            . ' ON '
                            . $db->quoteName('lp.id') . ' = ' . $db->quoteName('qp.rel_id')
                            . ' AND '
                            . $db->quoteName('qp.type') . ' = \'l\''
                        )
                        ->where($db->quoteName('jo.j2store_order_id') . ' = ' . $db->quote($order->order_id))
                        ->order($db->quoteName(array(
                            'q.c_title',
                            'lp.title'
                        )));
                } elseif($order->product_type == 'eb'){
                    //Get quiz product (quizes and learning passes) for Event Booking
                    $query->clear();
                    $query->select($db->quoteName('qp') . '.*')
                        ->from($db->quoteName('#__quiz_products', 'qp'))
                        ->leftJoin($db->quoteName('#__eb_registrants', 'ebr')
                            . 'ON'
                            . $db->quoteName('ebr.event_id') . ' = ' . $db->quoteName('qp.pid')
                            . ' AND '
                            . $db->quoteName('qp.pid_type') . ' = \'eb\''
                        )
                        ->leftJoin($db->quoteName('#__quiz_t_quiz', 'q')
                            . ' ON '
                            . $db->quoteName('q.c_id') . ' = ' . $db->quoteName('qp.rel_id')
                            . ' AND '
                            . $db->quoteName('qp.type') . ' = \'q\''
                        )
                        ->leftJoin($db->quoteName('#__quiz_lpath', 'lp')
                            . ' ON '
                            . $db->quoteName('lp.id') . ' = ' . $db->quoteName('qp.rel_id')
                            . ' AND '
                            . $db->quoteName('qp.type') . ' = \'l\''
                        )
                        ->where($db->quoteName('ebr.id') . ' = ' . $db->quote($order->order_id))
                        ->order($db->quoteName(array(
                            'q.c_title',
                            'lp.title'
                        )));
                } else {
                    $query = "SELECT qp.*"
                        . "\n FROM #__quiz_payments AS p"
                        . "\n INNER JOIN #__quiz_products AS qp ON qp.pid = p.pid"
                        . "\n LEFT JOIN #__quiz_t_quiz AS q ON qp.type = 'q' AND q.c_id = qp.rel_id "
                        . "\n LEFT JOIN #__quiz_lpath AS lp ON qp.type = 'l' AND lp.id = qp.rel_id"
                        . "\n WHERE p.id = " . $order->id
                        . "\n ORDER BY q.c_title, lp.title";
                }
                $db->SetQuery($query);
                $quiz_products = $db->loadObjectList();

                $rel_quizzes = $products = array();

                if (is_array($quiz_products) && count($quiz_products))
                    foreach ($quiz_products as $q) {
                        $rel_quizzes[$q->type][] = $q;
                    }

                if (!count($rel_quizzes)) {
                    continue;
                }

                if ($order->product_type == 'vm') {
                    $query = "SELECT vm_p.*, vm_p_engb.product_name, vm_p.virtuemart_product_id as product_id"
                        . "\n FROM #__virtuemart_order_items AS vm_oi"
                        . "\n INNER JOIN #__virtuemart_products AS vm_p ON vm_p.virtuemart_product_id = vm_oi.virtuemart_product_id"
                        . "\n INNER JOIN #__virtuemart_products_" . $lang . " AS vm_p_engb ON vm_p_engb.virtuemart_product_id = vm_oi.virtuemart_product_id"
                        . "\n WHERE vm_oi.virtuemart_order_id = " . $order->order_id
                        . "\n ORDER BY vm_p_engb.product_name";


                    $db->SetQuery($query);
                    $products_all = $db->loadObjectList();

                    if (is_array($products_all) && count($products_all))
                        foreach ($products_all as $product) {
                            JoomlaquizHelper::JQ_GetJoomFish($product->product_name, 'vm_product', 'product_name', $product->product_id);
                            $products[] = $product->product_name;
                        }

                } elseif ($order->product_type == 'j2s') {
                    $query->clear();
                    $query->select($db->quoteName(
                        array(
                            'ji.orderitem_name',
                            'ji.product_id'
                        ),
                        array(
                            'product_name',
                            'product_id'
                        )
                    ))
                        ->from($db->quoteName('#__j2store_orderitems', 'ji'))
                        ->leftJoin($db->quoteName('#__j2store_orders', 'jo')
                            . ' ON '
                            . $db->quoteName('jo.cart_id') . ' = ' . $db->quoteName('ji.cart_id')
                        )
                        ->where($db->quoteName('jo.j2store_order_id') . ' = ' . $db->quote($order->order_id));
                    $db->SetQuery($query);
                    $products_all = $db->loadObjectList();

                    if (is_array($products_all) && count($products_all)) {
                        foreach ($products_all as $product) {
                            $products[] = $product->product_name;
                        }
                    }
                } elseif($order->product_type == 'eb'){
                    //Get package name and package id
                    $query->clear();
                    $query->select($db->quoteName(
                        array(
                            'ebe.title',
                            'ebe.id'
                        ),
                        array(
                            'product_name',
                            'product_id'
                        )
                    ))
                        ->from($db->quoteName('#__eb_events', 'ebe'))
                        ->leftJoin($db->quoteName('#__eb_registrants', 'ebr')
                            . ' ON '
                            . $db->quoteName('ebr.event_id') . ' = ' . $db->quoteName('ebe.id')
                        )
                        ->where($db->quoteName('ebr.id') . ' = ' . $db->quote($order->order_id));
                    $db->SetQuery($query);
                    $products_all = $db->loadObjectList();

                    if (is_array($products_all) && count($products_all)) {
                        foreach ($products_all as $product) {
                            $products[] = $product->product_name;
                        }
                    }
                } else {
                    $query = "SELECT qpi.* "
                        . "\n FROM #__quiz_payments AS p"
                        . "\n INNER JOIN #__quiz_product_info AS qpi ON qpi.quiz_sku = p.pid"
                        . "\n WHERE p.id = " . $order->order_id
                        . "\n ORDER BY `qpi`.`name`";

                    $db->SetQuery($query);
                    $products_all = $db->loadObjectList();

                    if (is_array($products_all) && count($products_all))
                        foreach ($products_all as $product) {
                            JoomlaquizHelper::JQ_GetJoomFish($product->name, 'quiz_product_info', 'name', $product->id);
                            $products[] = $product->name;
                        }
                }

                $products_stat = array();
                if ($order->product_type == 'vm') {
                    $query = "SELECT *"
                        . "\n FROM #__quiz_products_stat"
                        . "\n WHERE uid = $my->id AND oid = " . $order->order_id;
                } elseif ($order->product_type == 'j2s') {
                    $query->clear();
                    $query->select('*')
                        ->from($db->quoteName('#__quiz_products_stat'))
                        ->where($db->quoteName('uid') . ' = ' . $my->id)
                        ->where($db->quoteName('oid') . ' = ' . $order->order_id);
                } elseif($order->product_type == 'eb'){
                    $query->clear();
                    $query->select('*')
                        ->from($db->quoteName('#__quiz_products_stat'))
                        ->where($db->quoteName('uid') . ' = ' . $my->id)
                        ->where($db->quoteName('oid') . ' = ' . $order->order_id);
                } else {
                    $query = "SELECT *"
                        . "\n FROM #__quiz_products_stat"
                        . "\n WHERE uid = '{$my->id}' AND oid = '" . ($order->id + 1000000000) . "'";
                }
                $db->SetQuery($query);
                $products_stat = $db->loadObjectList('qp_id');

                $ts = strtotime(JFactory::getDate());

                $bought_quizzes = array();
                $bq_count = $bq_counter_exiped = 0;

                //Проверяем наличие квизов в продукте
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
                                if (strtotime(JFactory::getDate()) > $ts_day_end) {
                                    $days_left = 0;
                                    $bq_counter_exiped++;
                                    $data->expired = true;
                                    $data->suffix = JText::_('COM_QUIZ_EXPIRED');
                                } else {
                                    $days_left = ceil(($ts_day_end - strtotime(JFactory::getDate())) / (24 * 60 * 60));
                                }
                            } else {
                                $days_left = 0;
                                $bq_counter_exiped++;
                                $data->expired = true;
                            }

                            $data->suffix = sprintf(JText::_('COM_QUIZ_XDAYS'), $days_left);

                        } else if (($data->period_start && $data->period_start != '0000-00-00') || ($data->period_end && $data->period_end != '0000-00-00')) {
                            if (!empty($products_stat) && array_key_exists($data->id, $products_stat)) {
                                $data->period_start = $products_stat[$data->id]->period_start;
                                $data->period_end = $products_stat[$data->id]->period_end;
                            }

                            $period = array();

                            $ts_start = null;
                            if ($data->period_start && $data->period_start != '0000-00-00') {
                                $ts_start = strtotime($data->period_start . ' 00:00:00');
                                $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_FROM'), date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_start));
                            }

                            $ts_end = null;
                            if ($data->period_end && $data->period_end != '0000-00-00') {
                                $ts_end = strtotime($data->period_end . ' 23:59:59');
                                $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_TO'), date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_end));
                            }

                            $data->suffix = sprintf(JText::_('COM_QUIZ_PERIOD'), implode(' ', $period));

                            if (($ts_start && $ts_start > $ts) || ($ts_end && $ts_end < $ts)) {
                                $bq_counter_exiped++;
                                $data->expired = true;
                            }

                        }

                        $product_quantity = 1;
                        if ($data->attempts > 0) {
                            if ($order->vm) {
                                if ($version == '1.5') {
                                    $query = "SELECT vm_oi.product_quantity"
                                        . "\n FROM #__vm_orders AS vm_o"
                                        . "\n INNER JOIN #__vm_order_item AS vm_oi ON vm_oi.order_id = vm_o.order_id"
                                        . "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.product_id"
                                        . "\n WHERE vm_o.user_id = {$my->id} AND vm_o.order_id = " . $order->order_id . " AND qp.id = " . $data->id . " AND vm_o.order_status IN ('C')";
                                } else {
                                    $query = "SELECT vm_oi.product_quantity"
                                        . "\n FROM #__virtuemart_orders AS vm_o"
                                        . "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
                                        . "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
                                        . "\n WHERE vm_o.virtuemart_user_id = {$my->id} AND vm_o.virtuemart_order_id = " . $order->order_id . " AND qp.id = " . $data->id . " AND vm_o.order_status IN ('C')";
                                }

                                $db->SetQuery($query);
                                $product_quantity = ($db->loadResult()) ? (int)$db->loadResult() : 1;
                            }

                            $attempts = (!empty($products_stat) && array_key_exists($data->id, $products_stat) && $products_stat[$data->id]->attempts ? $products_stat[$data->id]->attempts : 0);
                            $attempts_left = ($data->attempts * $product_quantity) - $attempts;

                            if ($data->xdays > 0) {
                                $data->suffix = sprintf(JText::_('COM_QUIZ_XDAYS_ATTEMPTS'), $attempts_left, $days_left);
                            } else if (($data->period_start && $data->period_start != '0000-00-00') || ($data->period_end && $data->period_end != '0000-00-00')) {
                                $data->suffix = sprintf(JText::_('COM_QUIZ_PERIOD_ATTEMPTS'), $attempts_left, implode(' ', $period));
                            } else {
                                $data->suffix .= ($data->suffix ? ' ' : '') . sprintf(JText::_('COM_QUIZ_ATTEMPTS'), $attempts_left);
                            }

                            if (($data->attempts * $product_quantity) <= $attempts) {
                                $bq_counter_exiped++;
                                $data->expired_a = true;
                                $data->expired = true;
                            }
                        }



                        $quiz = $all_quizzez[$data->rel_id];
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_title, 'quiz_t_quiz', 'c_title', $quiz->c_id);
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_description, 'quiz_t_quiz', 'c_description', $quiz->c_id);
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_short_description, 'quiz_t_quiz', 'c_short_description', $quiz->c_id);
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_right_message, 'quiz_t_quiz', 'c_right_message', $quiz->c_id);
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_wrong_message, 'quiz_t_quiz', 'c_wrong_message', $quiz->c_id);
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_pass_message, 'quiz_t_quiz', 'c_pass_message', $quiz->c_id);
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_unpass_message, 'quiz_t_quiz', 'c_unpass_message', $quiz->c_id);
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_metadescr, 'quiz_t_quiz', 'c_metadescr', $quiz->c_id);
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_keywords, 'quiz_t_quiz', 'c_keywords', $quiz->c_id);
                        JoomlaquizHelper::JQ_GetJoomFish($quiz->c_metatitle, 'quiz_t_quiz', 'c_metatitle', $quiz->c_id);

                        if ($data->expired)
                            $data->suffix = JText::_('COM_QUIZ_EXPIRED');

                        if ($data->expired_a)
                            $data->suffix = JText::_('COM_QUIZ_NOT_ATTEMPTS2');


                        $bought_quizze = new stdClass;
                        $bought_quizze->quiz = $quiz;
                        $bought_quizze->rel_id = $data->id;
                        $bought_quizze->suffix = $data->suffix;
                        $bought_quizze->expired = $data->expired;
                        $bought_quizzes[] = $bought_quizze;

                    }
                }

                $lpaths = array();
                $l_count = $l_counter_exiped = 0;
                if (array_key_exists('l', $rel_quizzes) && count($rel_quizzes['l'])) {
                    $query = 'SELECT * FROM #__quiz_lpath WHERE published = 1';
                    $db->setQuery($query);
                    $lpath = $db->loadObjectList('id');
                    if (!empty($lpath)) {
                        $l_count = count($rel_quizzes['l']);

                        if (is_array($rel_quizzes['l']) && count($rel_quizzes['l']))
                            foreach ($rel_quizzes['l'] as $data) {
                                if (empty($lpath[$data->rel_id])) {
                                    continue;
                                }

                                JoomlaquizHelper::JQ_GetJoomFish($lpath[$data->rel_id]->title, 'quiz_lpath', 'title', $lpath[$data->rel_id]->id);
                                JoomlaquizHelper::JQ_GetJoomFish($lpath[$data->rel_id]->short_descr, 'quiz_lpath', 'short_descr', $lpath[$data->rel_id]->id);
                                JoomlaquizHelper::JQ_GetJoomFish($lpath[$data->rel_id]->descr, 'quiz_lpath', 'descr', $lpath[$data->rel_id]->id);

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

                                } else if (($data->period_start && $data->period_start != '0000-00-00') || ($data->period_end && $data->period_end != '0000-00-00')) {
                                    if (!empty($products_stat) && array_key_exists($data->id, $products_stat)) {
                                        $data->period_start = $products_stat[$data->id]->period_start;
                                        $data->period_end = $products_stat[$data->id]->period_end;
                                    }

                                    $period = array();

                                    $ts_start = null;
                                    if ($data->period_start && $data->period_start != '0000-00-00') {
                                        $ts_start = strtotime($data->period_start . ' 00:00:00');
                                        $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_FROM'), date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_start));
                                    }

                                    $ts_end = null;
                                    if ($data->period_end && $data->period_end != '0000-00-00') {
                                        $ts_end = strtotime($data->period_end . ' 23:59:59');
                                        $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_TO'), date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_end));
                                    }

                                    $data->suffix = sprintf(JText::_('COM_LPATH_PERIOD'), implode(' ', $period));

                                    if (($ts_start && $ts_start > $ts) || ($ts_end && $ts_end < $ts)) {
                                        $l_counter_exiped++;
                                        $data->expired = true;
                                    }
                                }

                                $product_quantity = 1;
                                if ($data->attempts > 0) {
                                    if ($order->vm) {

                                        $query = "SELECT vm_oi.product_quantity"
                                            . "\n FROM #__virtuemart_orders AS vm_o"
                                            . "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
                                            . "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
                                            . "\n WHERE vm_o.virtuemart_user_id = {$my->id} AND vm_o.virtuemart_order_id = " . $order->order_id . " AND qp.id = " . $data->id . " AND vm_o.order_status IN ('C')";

                                        $db->SetQuery($query);
                                        $product_quantity = ($db->loadResult()) ? (int)$db->loadResult() : 1;
                                    }
                                    $data->suffix .= ($data->suffix ? ' ' : '') . sprintf(JText::_('COM_LPATH_ATTEMPTS'), $data->attempts * $product_quantity);

                                    $attempts = (!empty($products_stat) && array_key_exists($data->id, $products_stat) && $products_stat[$data->id]->attempts ? $products_stat[$data->id]->attempts : 0);
                                    if (($data->attempts * $product_quantity) <= $attempts) {
                                        $l_counter_exiped++;
                                        $data->expired = true;

                                    }
                                }
                                if ($data->expired)
                                    $data->suffix = JText::_('COM_LPATH_EXPIRED');

                                $lpaths[] = $data;
                            }
                    }
                }

                if (!count($bq_count) && !count($l_count)) {
                    continue;
                }


//                $package->vm = $order->vm;
//                $package->j2s = $order->j2s;
                $package->product_type = $order->product_type;
//                $package->package_number = $order->vm ? $orders_status->order_id : $orders_status->order_id + 1000000000;
                $package->package_number = $orders_status->order_id;
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

        return $packages;
    }
}
