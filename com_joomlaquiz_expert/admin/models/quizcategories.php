<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * Joomlaquiz Deluxe Model
 */
class JoomlaquizModelQuizcategories extends JModelList
{
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'c_category',
                'c_instruction',);
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        $search = $this->getUserStateFromRequest('categories.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // List state information.
        parent::populateState('c_id', 'asc');
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        return parent::getStoreId($id);
    }

    public static function delete($cid)
    {
        if (count($cid)) {
            $cids = implode(',', $cid);
            $db = JFactory::getDBO();

            $query = "DELETE FROM #__quiz_t_category"
                . "\n WHERE c_id IN ( $cids )";
            $db->setQuery($query);
            if (!$db->execute()) {
                echo "<script> alert('" . $db->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            }
        }
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("*");
        $query->from('`#__quiz_t_category`');

        // Filter by search in title.
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('c_id = ' . (int)substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('(c_category LIKE ' . $search . ')');
            }
        }

        $orderCol = $this->state->get('list.ordering', 'c_category');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    public function getCurrDate()
    {
        $db = $this->_db;
        $query = $db->getQuery(true);
        $query->select('c_par_value');
        $query->from('`#__quiz_setup`');
        $query->where("c_par_name='curr_date'");


        $result = $db->setQuery($query)->loadResult();
        if (strtotime("+2 month", strtotime($result)) <= strtotime(JFactory::getDate())) {
            return true;
        } else {
            return false;
        }
    }
}
