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
class JoomlaquizTableQuestion extends JTable
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
				
		function store($updateNulls = false){
			
			require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

			$input = JFactory::getApplication()->input;
            $jform = $input->get('jform', array(), 'ARRAY');
			$appsLib = JqAppPlugins::getInstance();
			$appsLib->loadApplications();
			
			$app = JFactory::getApplication();			
			$database = JFactory::getDBO();
			$this->c_question = stripslashes($this->c_question);
			$this->c_right_message = stripslashes($this->c_right_message);
			$this->c_wrong_message = stripslashes($this->c_wrong_message);
						
			if(empty($jform['c_id']) && !$this->c_type){
				$query = "SELECT MAX(`ordering`) FROM #__quiz_t_question
				WHERE `c_quiz_id`='" . $this->c_quiz_id . "'
				";
				$database->setQuery($query);
				$this->ordering = $database->loadResult() + 1;
				
				$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
				$this->c_type = $new_qtype_id;
			}

            if(!empty($jform['c_id'])){
				$this->c_id = $jform['c_id'];
                if(!empty($jform['c_type'])) {   //we are not in the quiz (copy quiz)
                    $this->c_type = $jform['c_type'];
                }
			}
			
			if ($input->get('task') == 'save2copy')
			{
				$this->c_id = '';
			}

			$res = parent::store($updateNulls);
			
			$data = array();
			$type = JoomlaquizHelper::getQuestionType($this->c_type);
			$data['quest_type'] = $type;
			$data['qid'] = $this->c_id;
			
			$appsLib->triggerEvent( 'onAdminSaveOptions' , $data );
			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($this->c_quiz_id);
			
			return $res;
		}
		
		function storeCopy($updateNulls = false){
			
			require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

			$appsLib = JqAppPlugins::getInstance();
			$appsLib->loadApplications();
			
			$app = JFactory::getApplication();
            $input = $app->input;
            $jform = $input->get('jform', array(), 'ARRAY');

			$database = JFactory::getDBO();
			$this->c_question = stripslashes($this->c_question);
			$this->c_right_message = stripslashes($this->c_right_message);
			$this->c_wrong_message = stripslashes($this->c_wrong_message);
						
			if(empty($jform['c_id']) && !$this->c_type){
				$query = "SELECT MAX(`ordering`) FROM #__quiz_t_question
				WHERE `c_quiz_id`='" . $this->c_quiz_id . "'
				";
				$database->setQuery($query);
				$this->ordering = $database->loadResult() + 1;
				
				$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
				$this->c_type = $new_qtype_id;
			}
			
			if(!empty($jform['c_id'])){
				$this->c_id = $jform['c_id'];
                if(!empty($jform['c_type'])) {   //we are not in the quiz (copy quiz)
                    $this->c_type = $jform['c_type'];
                }
			}
			
			$res = parent::store($updateNulls);			
			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($this->c_quiz_id);
			
			return $res;
		}
}