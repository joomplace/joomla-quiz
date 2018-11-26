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
JLoader::register('JoomlaquizHelper', JPATH_SITE . '/components/com_joomlaquiz/helpers/joomlaquiz.php');
/**
 * Category Model.
 *
 */
class JoomlaquizModelQcategory extends JModelList
{
    public static function getAvaliableQuizzes($cat_id = null){
        list($rows) = self::getAvaliableQuizzesByType($cat_id);
        return $rows;
    }

    /**
     * Get Avaliable quizzes
     *
     * @param $cat_id int Category id to filter by
     *
     * @return array
     * @since 3.9
     */
    public static function getAvaliableQuizzesByType($cat_id = null)
    {
        $my = JFactory::getUser();
        $database = JFactory::getDbo();
        $rel_quizzes = array();
        $lpath_ids   = array();
        $quiz_ids    = array();
        if ($my->id) {

            $VM_quiz_products = array();
            if (file_exists(JPATH_BASE
                . '/components/com_virtuemart/helpers/config.php')) {
                $no_virtuemart = false;
            } else {
                $no_virtuemart = true;
            }

            if (!$no_virtuemart) {
                $query = "SELECT DISTINCT qp.*, vm_o.virtuemart_order_id "
                    . "\n FROM #__virtuemart_orders AS vm_o"
                    . "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
                    . "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
                    . "\n WHERE vm_o.virtuemart_user_id = " . $my->id
                    . " AND vm_o.order_status IN ('C')";
                $database->SetQuery($query);
                $VM_quiz_products = $database->loadObjectList();
            }

            $query = "SELECT DISTINCT qp.*, (p.id+1000000000) AS `order_id`"
                . "\n FROM #__quiz_payments AS p"
                . "\n INNER JOIN #__quiz_products AS qp ON qp.pid = p.pid"
                . "\n WHERE p.user_id = '" . $my->id
                . "' AND p.status IN ('Confirmed')";
            $database->SetQuery($query);
            $quiz_products = $database->loadObjectList();
            if (!is_array($quiz_products)) {
                $quiz_products = array();
            }

            $quiz_products = array_merge($quiz_products, $VM_quiz_products);

            if (is_array($quiz_products) && !empty($quiz_products)) {
                foreach ($quiz_products as $q) {
                    $rel_quizzes[$q->type][] = $q;
                    if ($q->type == 'l') {
                        $lpath_ids[] = $q->rel_id;
                    } else {
                        $quiz_ids[] = $q->rel_id;
                    }
                }
            }

            $query = "SELECT * "
                . "\n FROM #__quiz_products_stat "
                . "\n WHERE uid = '{$my->id}' ";
            $database->SetQuery($query);
            $products_stat = $database->loadObjectList('qp_id');
        }

        $query = "SELECT *"
            . "\n FROM `#__quiz_t_quiz`"
            . "\n WHERE published = 1 ";
        if($cat_id){
            $query .= "\n AND c_category_id = '$cat_id' ";
        }
        $query .= "\n AND (one_time != 1 OR c_id NOT IN (SELECT c_quiz_id FROM `#__quiz_r_student_quiz` WHERE `c_student_id` = '"
            . $my->id . "' AND c_passed ='1'))"
            . "\n ORDER BY c_title ";
        $database->SetQuery($query);
        $all_quizzez = $database->loadObjectList('c_id');

        $query = "SELECT `c_title`"
            . "\n FROM `#__quiz_t_quiz`"
            . "\n WHERE published = 1 ";
        if($cat_id){
            $query .= "\n AND c_category_id = '$cat_id' ";
        }
        $query .= "\n ORDER BY `c_title` ";
        $database->SetQuery($query);
        $c_titles = $database->loadColumn();
        uasort($c_titles, "strnatcmp");
        $c_titles = array_values($c_titles);

        $sort_quizzez = array();
        if (!empty($all_quizzez)) {
            foreach ($c_titles as $title) {
                foreach ($all_quizzez as $i => $row) {
                    if ($row->c_title == $title) {
                        $sort_quizzez[] = $row;
                    }
                }
            }
        }
        $all_quizzez = $sort_quizzez;

        $rows = $purch_quizzes = array();
        if (is_array($all_quizzez)) {
            foreach ($all_quizzez as $i => $row) {
                if ($all_quizzez[$i]->paid_check == 0) {
                    $all_quizzez[$i]->payment = JText::_('COM_QUIZ_PAYMENT_FREE');
                    $rows[] = $all_quizzez[$i];
                } else {
                    $purch_quizzes[] = $all_quizzez[$i]->c_id;
                }
            }
        }

        $bought_quizzes = array();
        if (array_key_exists('q', $rel_quizzes) && !empty($rel_quizzes['q'])) {
            foreach ($rel_quizzes['q'] as $data) {

                $inAll = false;
                foreach($all_quizzez as $quiz){
                    if((int)$quiz->c_id == (int)$data->rel_id){
                        $inAll = true;
                        break;
                    }
                }

                if (!in_array($data->rel_id, $purch_quizzes) || !$inAll){
                    continue;
                }

                $expired = false;
                $ts = strtotime(\JFactory::getDate('now', JFactory::getConfig()->get('offset', 'UTC')));
                if ($data->xdays > 0) {
                    if(!empty($products_stat) && array_key_exists($data->id, $products_stat)) {
                        $confirm_date = strtotime($products_stat[$data->id]->xdays_start);
                    }
                    if($confirm_date) {
                        $ts_day_end = $confirm_date + $data->xdays*24*60*60;
                        if (strtotime(JFactory::getDate()) > $ts_day_end) {
                            $days_left = 0;
                            $expired = true;
                            $data->suffix = JText::_('COM_QUIZ_EXPIRED');
                        } else {
                            $days_left = ceil(($ts_day_end - strtotime(JFactory::getDate()))/(24*60*60));
                        }
                    } else {
                        $days_left = 0;
                        $expired = true;
                    }
                    $data->suffix = sprintf(JText::_('COM_QUIZ_XDAYS'), $days_left);
                } else {
                    if(!empty($products_stat) && array_key_exists($data->id, $products_stat)) {
                        $data->period_start = $products_stat[$data->id]->period_start;
                        $data->period_end = $products_stat[$data->id]->period_end;
                    }
                    $period = array();
                    $ts_start = null;
                    if($data->period_start && $data->period_start != '0000-00-00') {
                        $ts_start = strtotime($data->period_start . ' 00:00:00');
                        $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_FROM'), date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_start));
                    }
                    $ts_end = null;
                    if($data->period_end && $data->period_end != '0000-00-00') {
                        $ts_end = strtotime($data->period_end . ' 23:59:59');
                        $period[] = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_TO'), date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'), $ts_end));
                    }
                    $data->suffix = sprintf(JText::_('COM_QUIZ_PERIOD'), implode(' ', $period));
                    if(($ts_start && $ts_start > $ts) || ($ts_end && $ts_end < $ts)) {
                        $expired = true;
                    }
                }

                if($data->attempts > 0) {
                    $attempts = (!empty($products_stat) && array_key_exists($data->id, $products_stat) && $products_stat[$data->id]->attempts ? $products_stat[$data->id]->attempts : 0);
                    $attempts_left = $data->attempts - $attempts;

                    if($data->xdays > 0) {
                        $data->suffix = sprintf(JText::_('COM_QUIZ_XDAYS_ATTEMPTS'), $attempts_left, $days_left);
                    } else if (($data->period_start && $data->period_start != '0000-00-00') || ($data->period_end && $data->period_end != '0000-00-00')) {
                        $data->suffix = sprintf(JText::_('COM_QUIZ_PERIOD_ATTEMPTS'), $attempts_left, implode(' ', $period));
                    } else {
                        $data->suffix .= ($data->suffix ? ' ' : '') . sprintf(JText::_('COM_QUIZ_ATTEMPTS'), $attempts_left);
                    }
                    if($data->attempts <= $attempts) {
                        $expired = true;
                    }
                }

                foreach($all_quizzez as $quiz){
                    if((int)$quiz->c_id == (int)$data->rel_id){
                        $data->row = $quiz;
                        break;
                    }
                }

                $data->pid = $data->order_id;

                if(!$expired) {
                    $bought_quizzes[] = $data;
                }
            }
        }

        $lpaths = array();
        if (array_key_exists('l', $rel_quizzes) && !empty($rel_quizzes['l'])) {
            $query = 'SELECT * FROM #__quiz_lpath WHERE published = 1';
            $database->setQuery($query);
            $lpath = $database->loadObjectList('id');
            if (!empty($lpath)) {
                foreach ($lpath as $i => $row) {
                    JoomlaquizHelper::JQ_GetJoomFish($lpath[$i]->title,
                        'quiz_lpath', 'title', $lpath[$i]->id);
                    JoomlaquizHelper::JQ_GetJoomFish($lpath[$i]->short_descr,
                        'quiz_lpath', 'short_descr', $lpath[$i]->id);
                    JoomlaquizHelper::JQ_GetJoomFish($lpath[$i]->descr,
                        'quiz_lpath', 'descr', $lpath[$i]->id);
                }
                if (is_array($rel_quizzes['l']) && !empty($rel_quizzes['l'])) {
                    foreach ($rel_quizzes['l'] as $data) {
                        if (empty($lpath[$data->rel_id])) {
                            continue;
                        }
                        $data->suffix = '';
                        $data->title       = $lpath[$data->rel_id]->title;
                        $data->short_descr = $lpath[$data->rel_id]->short_descr;
                        if ($data->xdays > 0) {
                            $data->suffix = sprintf(JText::_('COM_LPATH_XDAYS'),
                                $data->xdays);
                        } else {
                            if (($data->period_start
                                    && $data->period_start != '0000-00-00')
                                || ($data->period_end
                                    && $data->period_end != '0000-00-00')
                            ) {
                                if (!empty($products_stat)
                                    && array_key_exists($data->id,
                                        $products_stat)
                                ) {
                                    $data->period_start
                                        = $products_stat[$data->id]->period_start;
                                    $data->period_end
                                        = $products_stat[$data->id]->period_end;
                                }
                                $period = array();
                                if ($data->period_start
                                    && $data->period_start != '0000-00-00'
                                ) {
                                    $period[]
                                        = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_FROM'),
                                        date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'),
                                            strtotime($data->period_start)));
                                }
                                if ($data->period_end
                                    && $data->period_end != '0000-00-00'
                                ) {
                                    $period[]
                                        = sprintf(JText::_('COM_QUIZ_LPATH_PERIOD_TO'),
                                        date(JText::_('COM_QUIZ_LPATH_PERIOD_FORMAT'),
                                            strtotime($data->period_end)));
                                }
                                $data->suffix
                                    = sprintf(JText::_('COM_LPATH_PERIOD'),
                                    implode(' ', $period));
                            }
                        }
                        if ($data->attempts > 0) {
                            $data->suffix .= ($data->suffix ? ' ' : '')
                                . sprintf(JText::_('COM_LPATH_ATTEMPTS'),
                                    $data->attempts);
                        }
                        $data->pid = $data->order_id;
                        $lpaths[]  = $data;
                    }
                }
            }
        }

        $user = JFactory::getUser();
        $category = JTable::getInstance('Category');
        $my_acl = $user->getAuthorisedViewLevels();
        foreach ($rows as $i => $quizz) {
            if ($quizz->paid_check) {
                // need to run checks
                // need to check packages
                // feature to release(after refactoring)
            }

            // need to check permissions anyway
            $category->load($quizz->c_category_id);
            if (!$user->authorise('core.view',
                        'com_joomlaquiz.quiz.' . $quizz->c_id)
                    || !in_array($category->access, $my_acl)) {
                // TODO: migrate c_guest to permissions (via SQL)
                unset($rows[$i]);
            }
        }

        return array($rows, $bought_quizzes, $lpaths);
    }

    public function getCategories(){
        jimport('joomla.application.categories');
        $input = JFactory::getApplication()->input;
        $categories = new JCategories(array('extension'=>'com_joomlaquiz', 'table'=>'#__categories', 'access'=>true));
        $cur_cat = $categories->get($input->getInt( 'cat_id', 0));

        if($cur_cat){
            $subs = $cur_cat->getChildren(true);
            $rel_level = $cur_cat->level;
        }else{
            $subs = array();
            $rel_level = 0;
        }

        $ids = array($cur_cat->id);
        foreach($subs as $s){
            $ids[] = $s->id;
        }
        $return_data = array();

        foreach($ids as $cat_id){
            $cat = array();
            if (!$cat_id) {
                $cat[0] = new stdClass;
                $cat[0]->error = 1;
                $cat[0]->message = '';
                return $cat[0];
            }

            $cat = $categories->get($cat_id);
            $cat->level =  $cat->level - $rel_level;

            $data = new stdClass();
            $data->cat = $cat;
            list($rows, $bought_quizzes, $lpaths) = self::getAvaliableQuizzesByType($cat_id);

            $data->rows = $rows;
            $data->lpaths = $lpaths;
            $data->bought_quizzes = $bought_quizzes;

            $return_data[$cat_id] = $data;
        }

        return $return_data;
    }
}
