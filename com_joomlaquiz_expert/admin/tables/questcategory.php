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
class JoomlaquizTableQuestcategory extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db)
        {
                parent::__construct('#__quiz_q_cat', 'qc_id', $db);
        }
		
		function store($updateNulls = false)
        {
            $jinput = JFactory::getApplication()->input;
            $jform = $jinput->get('jform', array(), 'ARRAY');
            $qc_tag_t = $jform['qc_tag_t'];
            $qc_tag_dd = $jform['qc_tag_dd'];
			$qc_tag = ($qc_tag_t ? $qc_tag_t : $qc_tag_dd);
			$this->qc_tag = stripslashes($qc_tag);
			$res = parent::store($updateNulls);
			return $res;
		}
}