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
class JoomlaquizTableQuiz extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db) 
        {
                parent::__construct('#__quiz_t_quiz', 'c_id', $db);
                $this->_trackAssets = true;
        }

		function store($updateNulls = false){

            $jform = JFactory::getApplication()->input->get('jform',array(),'array');
            $database = JFactory::getDBO();
			
			if ((int)$jform['c_id'] < 1)
			{
				$query = "SELECT COUNT(*) "
				. "\n FROM #__quiz_t_quiz"
				. "\n WHERE  c_title = '".$jform['c_title']."'";
				
				$database->setQuery( $query );
				$rows_dubl = $database->loadResult();

				if((int)$rows_dubl>0)
				{
					echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_QUIZ_WITH_THE_SAME_TITLE')."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			
			if (!(int)$jform['c_id']) {
				$date = strtotime(JFactory::getDate());
				$s_day = mktime(0,0,0,JHtml::_('date',strtotime($date), 'm'), JHtml::_('date',strtotime($date), 'd'), JHtml::_('date',strtotime($date), 'Y'));
				$this->c_created_time = JHtml::_('date',strtotime($s_day), 'Y-m-d');
			}
			
			if($jform['c_id']){
				$this->c_id = $jform['c_id'];
			}
			
			if(!$this->c_user_id) $this->c_user_id = JFactory::getUser()->id;

			$this->c_pass_message = $jform['c_pass_message'];
			$this->c_unpass_message = $jform['c_unpass_message'];

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

			$this->c_category_id = $jform['c_category_id'];
			$this->c_skin = $jform['c_skin'];
			$this->c_certificate = $jform['c_certificate'];
			$res = parent::store($updateNulls);			
			// -- add pool ----//

			$query = "DELETE FROM #__quiz_pool WHERE q_id=".$this->c_id;
			$database->setQuery( $query );
			if (!$database->execute()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			switch((int)$jform['c_pool']){
				case 1:
					$query = "INSERT INTO #__quiz_pool(q_id,q_cat,q_count) VALUES('".$this->c_id."','0','".(int)$jform['pool_rand']."')";
					$database->setQuery( $query );
					if (!$database->execute()) {
						echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
						exit();
					}
					break;
				case 2:
                case 3:
                    if(isset($_POST['pool_cats']) && !empty($_POST['pool_cats'])) {
                        foreach ($_POST['pool_cats'] as $hid_pcat) {
                            if (isset($_POST['pnumber_' . $hid_pcat]) && (int)$_POST['pnumber_' . $hid_pcat] > 0) {
                                $query = "INSERT INTO #__quiz_pool(q_id,q_cat,q_count) VALUES('" . $this->c_id . "','" . $hid_pcat . "','" . $_POST['pnumber_' . $hid_pcat] . "')";
                                $database->setQuery($query);
                                if (!$database->execute()) {
                                    echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                                    exit();
                                }
                            }
                        }
                    }
                    break;
				default : break;
			}	
			
			// -- add feedback options --//
			if(isset($jform['c_feed_option']) && $jform['c_feed_option'] )
			{
				$query = "DELETE FROM #__quiz_feed_option WHERE quiz_id=".(int)$this->c_id;
				$database->setQuery( $query );
				if (!$database->execute()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
					exit();
				}
					
				if(count($_POST['from_percent']))
				{	
					for($i=0;$i<count($_POST['from_percent']);$i++)
					{
						if($_POST['feed_by_percent'][$i])
						{
							if(intval($_POST['from_percent'][$i]) <= intval($_POST['to_percent'][$i]))
							{
								if((intval($_POST['from_percent'][$i])<101 && intval($_POST['to_percent'][$i])<101) || $_POST['jform']['c_feed_option']!= 1){

									$query = new stdClass();
									$query->quiz_id = $this->c_id;
									$query->from_percent = intval($_POST['from_percent'][$i]);
									$query->to_percent = intval($_POST['to_percent'][$i]);
									$query->fmessage = stripslashes($_POST['feed_by_percent'][$i]);

									$result = $database->insertObject('#__quiz_feed_option', $query);

									if (!$result) {
										echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
										exit();
									}
								}
							}
						}
					}
				}
			}
			
			return $res;
		}
		
		//----------------------------------------------------------------------------------------------------
		protected function _getAssetName()
		{
			$k = $this->_tbl_key;
			
			return 'com_joomlaquiz.quiz.'.(int) $this->$k;
		}
		//----------------------------------------------------------------------------------------------------
		protected function _getAssetTitle()
		{
			return $this->c_title;
		}
		//----------------------------------------------------------------------------------------------------
		protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
		{
			$assetsTable = JTable::getInstance('Asset', 'JTable');
			
			$assetsTable->loadByName('com_joomlaquiz');
			
			return $assetsTable->id;
		}
		
}