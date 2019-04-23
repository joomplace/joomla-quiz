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
			
        if ($input->get('task') == 'save2copy') {
				$this->c_id = '';
			}

			$res = parent::store($updateNulls);
			
        $this->reorder('`c_quiz_id` = '.$this->c_quiz_id);

			$data = array();
			$type = JoomlaquizHelper::getQuestionType($this->c_type);
			$data['quest_type'] = $type;
			$data['qid'] = $this->c_id;
			
			$appsLib->triggerEvent( 'onAdminSaveOptions' , $data );
			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($this->c_quiz_id);
			
			return $res;
		}
		
    function reorder($where = '')
    {
        $query = $this->_db->getQuery(true);
        $query->select('c_id,c_quiz_id,ordering');
        $query->from($this->_tbl);
        $query->where($where);
        $this->_db->setQuery($query);
        $list_questions = null;
        try{
            $list_questions = $this->_db->loadObjectList();
        }
        catch (RuntimeException $e){
            JFactory::getApplication()->enqueueMessage($e->getMessage(),'error');
        }

        $old_ordering = JFactory::getApplication()->input->get('old_ordering', 0);
        $new_ordering = $this->ordering;
        $question_id = $this->c_id;

        $list_questions = $this->list_reordering($list_questions, $old_ordering, $new_ordering, $question_id);

        if(!empty($list_questions))
        {
            foreach ($list_questions as $k=>$question){
                $db = $this->_db;
                $query = $this->_db->getQuery(true);
                // Fields to update.
                $fields = array(
                    $db->quoteName('ordering') . ' = ' . $db->quote($question->ordering)
                );
                // Conditions for which records should be updated.
                $conditions = array(
                    $db->quoteName('c_id') . ' = '.$db->quote($question->c_id)
                );
                $query->update($db->quoteName('#__quiz_t_question'))->set($fields)->where($conditions);

                $db->setQuery($query);

                try{
                    $db->execute();
                }
                catch (RuntimeException $e){
                    JFactory::getApplication()->enqueueMessage($e->getMessage(),'error');
                }
            }
        }

        return true;
    }

    function list_reordering($list_questions, $old_ordering,$new_ordering, $question_id)
    {
        if(!empty($list_questions)){
            if ($old_ordering < $new_ordering)
            {
                foreach ($list_questions as $question){
                    if($question->ordering<=$new_ordering && $question->ordering>$old_ordering && $question->c_id != $question_id ){
                        $question->ordering = $question->ordering -1;
                    }
                }
            }
            elseif($old_ordering > $new_ordering)
            {
                foreach ($list_questions as $question){
                    if($question->ordering>=$new_ordering && $question->ordering<$old_ordering && $question->c_id !=
                        $question_id ){
                        $question->ordering = $question->ordering + 1;
                    }
                }
            }
        }
        return $list_questions;
    }

    function storeCopy($updateNulls = false)
    {
			
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