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
     * @return    mixed    The data for the form.
     * @since    1.6
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
        $app = JFactory::getApplication();

        $form = $this->loadForm('com_joomlaquiz.product', 'product', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function getLists()
    {
        $lang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
        $lang = JFactory::getLanguage()->getTag();
        $lang = strtolower(str_replace('-', '_', $lang));
        $db = JFactory::getDbo();

        $no_virtuemart = ($this->isNotVirtuemart()) ? 1 : 0;
        $no_j2store = ($this->isNotJ2store()) ? 1 : 0;
        $no_eventbooking = ($this->isNotEventBooking()) ? 1 : 0;

        $GLOBALS['no_virtuemart'] = $no_virtuemart;

        $lists = array();
        $lists['no_virtuemart'] = $no_virtuemart;
        $lists['no_j2store'] = $no_j2store;
        $lists['no_eventbooking'] = $no_eventbooking;
        $product_id = JFactory::getApplication()->input->get('id');
        $product_pid_type = '';

        //Get product's pid and pid_type for id
        if (isset($product_id) && $product_id != 0) {
            $query = $db->getQuery(true);
            $query->select($db->qn(array('qp.pid', 'qp.pid_type'), array('pid', 'pid_type')));
            $query->from($db->qn('#__quiz_products', 'qp'));
            $query->where($db->qn('qp.id') . ' = ' . $db->q($product_id));
            $db->setQuery($query);
            $product_data = $db->loadAssoc();
            $product_id = $product_data['pid'];
            $product_pid_type = $product_data['pid_type'];
        }

        $lists['product_id'] = $product_id ? $product_id : -1;
        $lists['products'] = '';


        //Get products list
        $products = array();
        $products[] = array(
            'value' => '',
            'text' => '',
            'items' => array(
                array('value' => '-1', 'text' => '--Select product--'),
            )
        );

        //Get J2Store products
        if (!$no_j2store) {
            $query = $db->getQuery(true);
            $query->select(
                $query->concatenate(
                    array(
                        $db->qn('p.j2store_product_id'),
                        '\'_j2s\''
                    )
                ) . ' AS `value`'
            );
            $query->select($db->qn('c.title', 'text'));
            $query->from($db->qn('#__j2store_products', 'p'));
            $query->leftJoin(
                $db->qn('#__content', 'c')
                . 'ON' . $db->qn('c.id')
                . ' = ' . $db->qn('p.product_source_id')
            );
            $query->where($db->qn('p.visibility') . ' = 1');

            $db->setQuery($query);

            $j2store_products = array(
                'value' => 'J2S',
                'text' => 'J2Store',
                'items' => $db->loadAssocList()
            );

            $products[] = $j2store_products;
        }

        //Get EventBooking products
        if (!$no_eventbooking) {
            $query = $db->getQuery(true);
            $query->select($query->concatenate(array($db->qn('ebe.id'), '\'_eb\'')) . ' AS `value`')
                ->select($db->qn('ebe.title', 'text'))
                ->from($db->qn('#__eb_events', 'ebe'))
                ->where($db->qn('ebe.published') . ' =1');
            $db->setQuery($query);
            $event_booking_products = array(
                'value' => 'EB',
                'text' => 'EventBooking',
                'items' => $db->loadAssocList()
            );
            $products[] = $event_booking_products;
        }

        //Get VirtueMart products
        if (!$no_virtuemart) {
            if (!class_exists('VmConfig')) require(JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php');
            VmConfig::loadConfig();
            VmConfig::loadJLang('com_virtuemart');
            $query = $db->getQuery(true);

            $query->select(
                $query->concatenate(
                    array(
                        $db->qn('vmp.virtuemart_product_id'),
                        '\'_vm\''
                    )
                ) . ' AS `value`'
            );
            $query->select(
                $query->concatenate(
                    array(
                        $db->qn('vmp_eg.product_name'),
                        '\' (\'',
                        $db->qn('vmp.product_sku'),
                        '\')\'')
                ) . ' AS `text`'
            );
            $query->from($db->qn('#__virtuemart_products', 'vmp'));
            $query->leftJoin(
                $db->qn('#__virtuemart_products_' . VmConfig::$vmlang, 'vmp_eg')
                . 'ON'
                . $db->qn('vmp_eg.virtuemart_product_id')
                . ' = '
                . $db->qn('vmp.virtuemart_product_id')
            );
            $query->where(
                $db->qn('vmp.published')
                . ' = 1'
            );
            $query->order(
                $db->qn('text')
            );

            $db->setQuery($query);

            $vm_products = array(
                'value' => 'VM',
                'text' => 'VirtueMart',
                'items' => $db->loadAssocList()
            );

            $products[] = $vm_products;
        }

        if (!$no_eventbooking || !$no_j2store || !$no_virtuemart) {
            $attr = array(
                'id' => 'product_id',
                'list.attr' => 'class="text_area" style="max-width: 300px;" size="1"' . ($product_id ? ' disabled' : ''),
                'list.select' => $product_id . '_' . $product_pid_type
            );

            $products_list = JHtml::_('select.groupedlist', $products, 'product_id', $attr);

            $lists['products'] = $products_list;

        }

        $prod_rel = array();
        if ($product_id) {
            $lists['products'] .= '<input type="hidden" name="product_id" value="' . $product_id . '_' . $product_pid_type . '" />';

            $query->clear();
            $query->select('*');
            $query->from($db->qn('#__quiz_products', 'qp'));
            $query->where($db->qn('qp.pid') . ' = ' . $db->q($product_id))
                ->where($db->qn('qp.pid_type') . ' = ' . $db->q($product_pid_type));

            $db->setQuery($query);
            $temp_rel = $db->loadAssocList();
            foreach ($temp_rel as $rel) {
                $prod_rel[$rel['type']][$rel['rel_id']] = $rel;
            }

            $query = "SELECT name FROM #__quiz_product_info WHERE quiz_sku = '{$product_id}'";
            $db->setQuery($query);
            $lists['name'] = $db->loadResult();
        }

        if ($product_id == '-1') {
            $lists['name'] = JFactory::getApplication()->input->get('name');
        }

        $lists['relation'] = $prod_rel;

        $query = "SELECT c_id AS value, c_title AS text"
            . "\n FROM #__quiz_t_quiz"
            . "\n WHERE published = 1"
            . "\n ORDER BY c_title";
        $db->setQuery($query);
        $quizzes = $db->loadObjectList();
        $lists['quiz'] = $quizzes;

        $query = "SELECT *, id AS value, title AS text"
            . "\n FROM #__quiz_lpath"
            . "\n WHERE published = 1"
            . "\n ORDER BY title";
        $db->setQuery($query);
        $lpaths = $db->loadObjectList();
        $lists['lpath'] = $lpaths;

        return $lists;

    }

    protected function isNotVirtuemart()
    {

        $no_virtuemart = false;
        if (!defined('DS')) define('DS', '/');
        if (!defined('JPATH_VM_ADMINISTRATOR')) define('JPATH_VM_ADMINISTRATOR', JPATH_BASE . DS . 'components' . DS . 'com_virtuemart');

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

        if (empty($_SESSION["ps_vendor_id"])) {
            $_SESSION["ps_vendor_id"] = 1;
        }

        return $no_virtuemart;
    }

    //Check that J2Store component exists
    protected function isNotJ2store()
    {
        if (file_exists(JPATH_ADMINISTRATOR . '/components/com_j2store/config.xml')) {
            return false;
        } else {
            return true;
        }
    }

    //Check that EventBooking component exists
    protected function isNotEventBooking()
    {
        if (file_exists(JPATH_ADMINISTRATOR . '/components/com_eventbooking/config.xml')) {
            return false;
        } else {
            return true;
        }
    }
}