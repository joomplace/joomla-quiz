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
class JoomlaquizTableCertificate extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db) 
        {
                parent::__construct('#__quiz_certificates', 'id', $db);
        }
		
		function store($updateNulls = false){
			$database = JFactory::getDBO();
			if ($this->id == 0) {
                $query = "SELECT MAX(id) FROM #__quiz_certificates";
                $database->setQuery($query);
                $id = $database->loadResult()+1;
            }
			$query = "DELETE FROM #__quiz_cert_fields WHERE cert_id = '{$id}'";
			$database->setQuery($query);
			$database->execute();
			
			$jq_hid_fields = JFactory::getApplication()->input->get('jq_hid_fields', array(), '');
			$jq_hid_fields_ids = JFactory::getApplication()->input->get('jq_hid_fields_ids', array(), '');
			$jq_fields_shadow = JFactory::getApplication()->input->get('jq_fields_shadow', array(), '');
			$jq_hid_field_x = JFactory::getApplication()->input->get('jq_hid_field_x', array(), '');
			$jq_hid_field_y = JFactory::getApplication()->input->get('jq_hid_field_y', array(), '');
			$jq_hid_field_h = JFactory::getApplication()->input->get('jq_hid_field_h', array(), '');
			$jq_hid_field_font = JFactory::getApplication()->input->get('jq_hid_field_font', array(), '');
			
			if (is_array($jq_hid_fields_ids ) && count($jq_hid_fields_ids )) {
				foreach($jq_hid_fields_ids as $i=>$jq_hid_fields_id) {
					$query = "INSERT INTO #__quiz_cert_fields SET `cert_id` = '".$id."', 
								`f_text` = '".$jq_hid_fields[$i]."',
								`text_x` = '".intval($jq_hid_field_x[$i])."',
								`text_y` = '".intval($jq_hid_field_y[$i])."', 
								`text_h` = '".intval($jq_hid_field_h[$i])."',
								`shadow` = '".intval($jq_fields_shadow[$i])."',
								`font` = '".$jq_hid_field_font[$i]."'						
								";
					$database->setQuery($query);
					$database->execute();
				}
			}
			
			$this->cert_file = $_REQUEST['jform']['cert_file'];
			
			$res = parent::store();

			$post = JRequest::get('post');
			$database->setQuery("UPDATE `#__quiz_certificates` SET `text_font` = '".$post['text_font']."' WHERE `id` = '".$id."'");
			$database->execute();

			return $res;
		}
}