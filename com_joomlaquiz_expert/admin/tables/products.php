<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
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
    public function __construct(&$db)
    {
        parent::__construct('#__quiz_products', 'id', $db);
    }

    public function store($updateNulls = false)
    {
        $db = JFactory::getDbo();
        $product_id = ($_POST['product_id']) ? $_POST['product_id'] : '-1';

        //Separate product_id
        list($product_id, $product_postfix) = explode('_', $product_id);

        $quiz_product_id = ($_POST['quiz_product_id']) ? $_POST['quiz_product_id'] : '-1';
        $product_id_int = (string)intval($product_id);

        $name = ($_POST['jform']['name']) ? $_POST['jform']['name'] : '';

        if ($product_id == '-1' && $quiz_product_id) {
            if ($name == '') {
                echo "<script> alert('" . JText::_('COM_JOOMLAQUIZ_SELECT_PRODUCT') . "'); window.history.go(-1); </script>\n";
                exit();
            }

            $product_id = $quiz_product_id;
        }

        $quiz_sku = '';
        if ($product_id) {
            $query = "SELECT quiz_sku"
                . "\n FROM #__quiz_product_info"
                . "\n WHERE `quiz_sku` = '{$product_id}'";
            $db->setQuery($query);
            $quiz_sku = $db->loadResult();

            if ($quiz_sku) {
                $query = "UPDATE #__quiz_product_info SET `name` = '{$name}' WHERE `quiz_sku` = '" . $quiz_sku . "' ";
                $db->setQuery($query);
                $db->execute();
            }
        }

        if (($product_id && $product_id_int != $product_id && !$quiz_sku) || ($product_id == '-1' && $name != '')) {
            $quiz_sku = strtotime(JFactory::getDate());
            $query = "INSERT INTO #__quiz_product_info SET `quiz_sku` = '" . $quiz_sku . "', `name` = '{$name}'";
            $db->setQuery($query);
            $db->execute();

            $_REQUEST['name'] = $name;
        }

        if ($quiz_sku)
            $product_id = $quiz_sku;

        $types = array('q', 'l');
        $insert = array();
        $not_for_delete = array();
        foreach ($types as $type) {
            $ids = ($_POST[$type . '_ids']) ? $_POST[$type . '_ids'] : array();

            foreach ($ids as $id) {
                $values = array();
                $values[] = $product_postfix;
                $values[] = $product_id;
                $values[] = $type;
                $values[] = $id;

                $access = ($_POST[$type . '_access_' . $id]) ? intval($_POST[$type . '_access_' . $id]) : 0;

                if ($access == 0) {
                    $xdays = ($_POST[$type . '_xdays_' . $id]) ? intval($_POST[$type . '_xdays_' . $id]) : 0;
                } else {
                    $xdays = 0;
                }
                $values[] = $xdays;

                if ($access == 0) {
                    $period_start = '0000-00-00';
                } else {
                    $period_start = ($_POST[$type . '_period_start_' . $id]) ? $_POST[$type . '_period_start_' . $id] : '0000-00-00';
                    $period_start = JHtml::_('date', strtotime($period_start), 'Y-m-d');
                }
                $values[] = $period_start;

                if ($access == 0) {
                    $period_end = '0000-00-00';
                } else {
                    $period_end = ($_POST[$type . '_period_end_' . $id]) ? $_POST[$type . '_period_end_' . $id] : '0000-00-00';
                    $period_end = ($period_end && $period_end != '0000-00-00' ? JHtml::_('date', strtotime($period_end), 'Y-m-d') : '');
                }
                $values[] = $period_end;

                $attempts = ($_POST[$type . '_attempts_' . $id]) ? intval($_POST[$type . '_attempts_' . $id]) : 0;
                $values[] = $attempts;

                $query = $db->getQuery(true);
                $query->clear();
                $query->select($db->quoteName('qp.id', 'id'));
                $query->from($db->quoteName('#__quiz_products', 'qp'));
                $query->where($db->quoteName('qp.pid') . ' = ' . $db->quote($product_id))
                    ->where($db->quoteName('qp.type') . ' = ' . $db->quote($type))
                    ->where($db->quoteName('qp.rel_id') . ' = ' . $db->quote($id))
                    ->where($db->quoteName('qp.pid_type') . ' = ' . $db->quote($product_postfix));


                $db->setQuery($query);
                $update_id = $db->loadResult();

                if ($update_id) {
                    $query = 'UPDATE #__quiz_products SET'
                        . "\n `xdays` = $xdays,"
                        . "\n `period_start` = '$period_start',"
                        . "\n `period_end` = '$period_end',"
                        . "\n `attempts` = '$attempts', "
                        . "   `pid` = '{$product_id}' "
                        . "\n WHERE `id` = '$update_id'";
                    $db->setQuery($query);
                    if (!$db->execute()) {
                        echo "<script> alert('" . $db->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                        continue;
                    }
                    $not_for_delete[] = $update_id;
                } else {
                    $insert[] = '(\'' . implode('\', \'', $values) . '\')';
                }
            }
        }

        if (!count($insert) && !count($not_for_delete)) {
            echo "<script> alert('" . JText::_('COM_JOOMLAQUIZ_SELECT_QUIZZES_OR_LEARNING') . "'); window.history.go(-1); </script>\n";
            exit();
        }

        //Удаление продукта если такой уже существует
        $query = 'DELETE FROM #__quiz_products'
            . "\n WHERE `pid` = '{$product_id}'"
            . "\n AND `pid_type` = '{$product_postfix}'"
            . (count($not_for_delete) ? ' AND id NOT IN (' . implode(',', $not_for_delete) . ')' : '');
        $db->setQuery($query);
        if (!$db->query()) {
            echo "<script> alert('" . $db->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            exit();
        }

        //Создание нового проудука
        if (count($insert)) {

            $query = 'INSERT INTO #__quiz_products'
                . "\n (`pid_type`,`pid`, `type`, `rel_id`, `xdays`, `period_start`, `period_end`, `attempts`)"
                . "\n VALUES"
                . "\n " . implode(", \n", $insert);
            $db->setQuery($query);
            if (!$db->execute()) {
                echo "<script> alert('" . $db->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                exit();
            }
        }

        //Get first id for the product
        $query = $db->getQuery(true);
        $query->clear();
        $query->select($db->quoteName('qp.id', 'id'))
            ->from($db->quoteName('#__quiz_products', 'qp'))
            ->where($db->quoteName('qp.pid') . ' = ' . $db->quote($product_id))
            ->where($db->quoteName('qp.pid_type') . ' = ' . $db->quote($product_postfix))
            ->order($db->qn('qp.id') . ' ASC')
        ;
        $db->setQuery($query);
        $first_id = $db->loadResult();

        //For redirect after save
        $_REQUEST['id'] = $first_id;

        return true;
    }
}