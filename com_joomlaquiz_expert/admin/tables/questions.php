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
class JoomlaquizTableQuestions extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db) 
        {
                parent::__construct('#__quiz_t_question', 'c_id', $db);
        }
		
		public function getTable($type = 'Questions', $prefix = 'JoomlaquizTable', $config = array())
		{
			return JTable::getInstance($type, $prefix, $config);
		}
		
		public function saveorder($idArray = null, $lft_array = null)
		{
			$db = JFactory::getDBO();
			$filter_quiz_id = JFactory::getApplication()->input->get('filter_quiz_id');
			if(!empty($idArray)){
				foreach($idArray as $ord => $qid){
					$db->setQuery("UPDATE #__quiz_t_question SET `ordering` = '".$ord."' WHERE `c_id` = '".$qid."' AND `c_quiz_id` = '".$filter_quiz_id."'");
					$db->execute();
				}
			}
			
			return true;
		}
}