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
class JoomlaquizTableLpath extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db) 
        {
                parent::__construct('#__quiz_lpath', 'id', $db);
                $this->_trackAssets = true;
        }

		function store($updateNulls = false)
        {
			$database = JFactory::getDBO();
            $jinput = JFactory::getApplication()->input;
            $jform = $jinput->get('jform', array(), 'ARRAY');

			if (!empty($jform['id'])) {
				$this->id = $jform['id'];
			}

            //==================================================
            // Access rules.
            //==================================================

            if (isset($jform['rules']))
            {
                $rulesArray = $jform['rules'];

                // Removing 'Inherited' permissons. Otherwise they will be converted to 'Denied'.

                foreach ($rulesArray as $actionName => $permissions)
                {
                    foreach ($permissions as $userGroupId => $permisson)
                    {
                        if ($permisson == '')
                        {
                            unset($rulesArray[$actionName][$userGroupId]);
                        }
                    }
                }

                $rules = new JAccessRules($rulesArray);
                $this->setRules($rules);
            }


			$res = parent::store($updateNulls);
            if (!empty($jform['id'])) {
				$query = 'DELETE FROM #__quiz_lpath_quiz WHERE `lid` = ' . $this->id;
				$database->setQuery($query);
				if(!$database->execute()) {
					echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			
			$insert = array();
			$quiz_ids = JFactory::getApplication()->input->get('jq_hid_fields_ids', array(), 'array');
			$quiz_types = JFactory::getApplication()->input->get('jq_hid_fields_types', array(), 'array');
			
			if(!empty($quiz_ids)) {
				foreach($quiz_ids as $order => $quiz_id) {
					$insert[] = "('{$this->id}', '{$quiz_types[$order]}', '$quiz_id', '$order')";
				}
				
				$query = 'INSERT INTO #__quiz_lpath_quiz'
				. "\n (`lid`, `type`, `qid`, `order`)"
				. "\n VALUES"
				. "\n " . implode(", \n", $insert)
				;
				$database->setQuery($query);
				if(!$database->execute()) {
					echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			
			return $res;
		}


        //----------------------------------------------------------------------------------------------------
        protected function _getAssetName()
        {
            $k = $this->_tbl_key;

            return 'com_joomlaquiz.lp.'.(int) $this->$k;
        }
        //----------------------------------------------------------------------------------------------------
        protected function _getAssetTitle()
        {
            return $this->title;
        }
        //----------------------------------------------------------------------------------------------------
        protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
        {
            $assetsTable = JTable::getInstance('Asset', 'JTable');

            $assetsTable->loadByName('com_joomlaquiz');

            return $assetsTable->id;
        }


}