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

            $data = JFactory::getApplication()->input->get('jform',array(),'ARRAY');

            JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/categories.php');

            // Cast catid to integer for comparison
            $catid = (int) $data['c_ques_cat'];
            $cat_extension = 'com_joomlaquiz.questions';

            // Check if New Category exists
            if ($catid > 0)
            {
                $catid = CategoriesHelper::validateCategoryId($data['c_ques_cat'], $cat_extension);
            }

            // Save New Categoryg
            if ($catid == 0 && JFactory::getUser()->authorise('core.create', 'com_joomlaquiz'))
            {
                $table = array();
                $table['title'] = $data['c_ques_cat'];
                $table['parent_id'] = 1;
                $table['extension'] = $cat_extension;
                $table['language'] = $data['language']?$data['language']:'*';
                $table['published'] = 1;

                // Create new category and get catid back
                $data['c_ques_cat'] = CategoriesHelper::createCategory($table);
            }

            /*
             * Somehow this is not binded!!!
             */
            $this->bind($data);

			require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

			$input = JFactory::getApplication()->input;
			$appsLib = JqAppPlugins::getInstance();
			$appsLib->loadApplications();
			
			$app = JFactory::getApplication();			
			$database = JFactory::getDBO();
			$this->c_question = stripslashes($this->c_question);
			$this->c_right_message = stripslashes($this->c_right_message);
			$this->c_wrong_message = stripslashes($this->c_wrong_message);

			if(!$_POST['jform']['c_id'] && !$this->c_type){
				$query = "SELECT MAX(`ordering`) FROM #__quiz_t_question
				WHERE `c_quiz_id`='" . $this->c_quiz_id . "'
				";
				$database->setQuery($query);
				$this->ordering = $database->loadResult() + 1;
				
				$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
				$this->c_type = $new_qtype_id;
			}
			
			if($_POST['jform']['c_id']){
				$this->c_id = $_POST['jform']['c_id'];
				$this->c_type = $_POST['jform']['c_type'];
			}
			
			if ($input->get('task') == 'save2copy')
			{
				$this->c_id = '';
			}

			if(!is_string($this->params)){
			    $this->params = new Joomla\Registry\Registry($this->params);
			    $this->params = $this->params->toString();
            }
			$res = parent::store($updateNulls);
			
			$data = array();
			$type = JoomlaquizHelper::getQuestionType($this->c_type);
			$data['quest_type'] = $type;
			$data['qid'] = $this->c_id;

			/* Legacy */
			$appsLib->triggerEvent( 'onAdminSaveOptions' , $data );
			/* new approach */
            $reg = new Joomla\Registry\Registry($this->getProperties());
            $reg->merge(new Joomla\Registry\Registry($data));
			$appsLib->triggerEvent( 'onStoreQuestion', $reg );
			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($this->c_quiz_id);
			
			return $res;
		}
		
		function storeCopy($updateNulls = false){
			
			require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

			$appsLib = JqAppPlugins::getInstance();
			$appsLib->loadApplications();
			
			$app = JFactory::getApplication();			
			$database = JFactory::getDBO();
			$this->c_question = stripslashes($this->c_question);
			$this->c_right_message = stripslashes($this->c_right_message);
			$this->c_wrong_message = stripslashes($this->c_wrong_message);
						
			if(!$_POST['jform']['c_id'] && !$this->c_type){
				$query = "SELECT MAX(`ordering`) FROM #__quiz_t_question
				WHERE `c_quiz_id`='" . $this->c_quiz_id . "'
				";
				$database->setQuery($query);
				$this->ordering = $database->loadResult() + 1;
				
				$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
				$this->c_type = $new_qtype_id;
			}
			
			if($_POST['jform']['c_id']){
				$this->c_id = $_POST['jform']['c_id'];
				$this->c_type = $_POST['jform']['c_type'];
			}
			
			$res = parent::store($updateNulls);			
			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($this->c_quiz_id);
			
			return $res;
		}
}