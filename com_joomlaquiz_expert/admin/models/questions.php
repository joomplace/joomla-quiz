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
class JoomlaquizModelQuestions extends JModelList
{
     /**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'c_id', 'a.c_id',
				'c_question', 'a.c_question',
				'published', 'a.published',
				'ordering', 'a.ordering',
				'category', 'cat.title',
				'c_type', 'b.qtype_full',
				'c_title', 'c.c_title',);
		}
		
		parent::__construct($config);
	}
    
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();
		
		$search = $this->getUserStateFromRequest('questions.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$quiz_id = $this->getUserStateFromRequest('questions.filter.quiz_id', 'quiz_id', 0);
		$this->setState('filter.quiz_id', $quiz_id);

		$qtype_id = $this->getUserStateFromRequest('questions.filter.qtype_id', 'filter_qtype_id');
		$this->setState('filter.qtype_id', $qtype_id);
		
		$ques_cat = $this->getUserStateFromRequest('questions.filter.ques_cat', 'filter_ques_cat');
		$this->setState('filter.ques_cat', $ques_cat);
		
		$enabled = $this->getUserStateFromRequest('questions.filter.enabled', 'filter_enabled');
		$this->setState('filter.enabled', $enabled);
		
		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		return parent::getStoreId($id);
	}
	
	public function publish(&$cid, $value = 1){
			$database = JFactory::getDBO();
			$task = JFactory::getApplication()->input->getCmd('task');
			$state = ($task == 'publish') ? 1 : 0;
			
			if (!is_array( $cid ) || empty( $cid )) {
				$action = ($task == 'publish') ? 'publish' : 'unpublish';
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_ITEM_TO')." $action'); window.history.go(-1);</script>\n";
				exit();
			}
			
			$cids = implode( ',', $cid );

			$query = "UPDATE #__quiz_t_question"
			. "\n SET published = ". intval($state)
			. "\n WHERE c_id IN ( $cids )"
			;
			$database->setQuery( $query );
			if (!$database->execute()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			
			return true;
	}
	
	public function delete($cid){
		if (!empty( $cid )) {
			$cids = implode( ',', $cid );
			$database = JFactory::getDBO();
		
			$query = "SELECT distinct c_quiz_id FROM #__quiz_t_question WHERE c_id IN ( $cids )";
			$database->SetQuery( $query );
			$ch_quizzes = $database->LoadObjectList();
			$query = "DELETE FROM #__quiz_t_question"
			. "\n WHERE c_id IN ( $cids )"
			;
			$database->setQuery( $query );
			if (!$database->execute()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			} else {
				JoomlaquizHelper::JQ_Delete_Items($cids, 'remove/questions/', 'removeQuestions');		
			}
			//recalculate quizzes TotalScore
			if (!empty($ch_quizzes)) {
				foreach ($ch_quizzes as $c_q) {
					JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($c_q->c_quiz_id);
				}
			}
		}
		
		return true;
	}
		
	public function getMoveQuestions(){
		$db = JFactory::getDBO();
        $session = JFactory::getSession();
        $cid = $session->get('com_joomlaquiz.move.questions.cids');
		$cids = implode( ',', $cid );
		$query = "SELECT a.c_question as question_name, b.c_title as quiz_name"
		. "\n FROM #__quiz_t_question AS a LEFT JOIN #__quiz_t_quiz AS b ON b.c_id = a.c_quiz_id"
		. "\n WHERE a.c_id IN ( $cids )"
		;
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		return $items;
	}
	
	public function getMoveQuestionsCat(){
		$db = JFactory::getDBO();
        $session = JFactory::getSession();
        $cid = $session->get('com_joomlaquiz.move.questions.cids');
		$cids = implode( ',', $cid );
		$query = "SELECT a.c_question as question_name, b.title as cat_name"
		. "\n FROM #__quiz_t_question AS a LEFT JOIN #__categories AS b ON b.id = a.c_ques_cat"
		. "\n WHERE a.c_id IN ( $cids )"
		;
				
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		return $items;
	}
	
	public function getCopyQuestions(){
		$db = JFactory::getDBO();
        $session = JFactory::getSession();
        $cid = $session->get('com_joomlaquiz.copy.questions.cids');
		$cids = implode( ',', $cid );
		$query = "SELECT a.c_question as question_name, b.c_title as quiz_name"
		. "\n FROM #__quiz_t_question AS a LEFT JOIN #__quiz_t_quiz AS b ON b.c_id = a.c_quiz_id"
		. "\n WHERE a.c_id IN ( $cids )"
		;
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		return $items;
	}
		
    public function copyQuestions()
    {
        $db = JFactory::getDBO();
        $session = JFactory::getSession();
        $cid = $session->get('com_joomlaquiz.copy.questions.cids');

		$quizMove = intval(JFactory::getApplication()->input->get('quizcopy'));
		$cids = implode( ',', $cid );
		$total = count( $cid );
		$query = "SELECT * FROM #__quiz_t_question WHERE c_id IN ( $cids ) ORDER BY ordering";
        $db->setQuery($query);
        $quests_to_copy = $db->loadAssocList();

		foreach ($quests_to_copy as $quest2copy) {
			$old_quest_id = $quest2copy['c_id'];
			$new_quest = $this->getTable();
            if (!$new_quest->bind($quest2copy)) {
                echo "<script> alert('" . $new_quest->getError() . "'); window.history.go(-1); </script>\n";
                exit();
            }

            $new_quest->c_id = 0;

            $query = "SELECT MAX(`ordering`) FROM `#__quiz_t_question` WHERE `c_quiz_id` = '" . $quizMove . "'";
            $db->setQuery($query);
            $max_order = $db->loadResult();

            $new_quest->ordering = $max_order + 1;
            $new_quest->c_quiz_id = $quizMove;

            if (!$new_quest->check()) {
                echo "<script> alert('" . $new_quest->getError() . "'); window.history.go(-1); </script>\n";
                exit();
            }
            if (!$new_quest->store()) {
                echo "<script> alert('" . $new_quest->getError() . "'); window.history.go(-1); </script>\n";
                exit();
            }
			$new_quest_id = $new_quest->c_id;
			if ( ($quest2copy['c_type'] == 1) || ($quest2copy['c_type'] == 2) || ($quest2copy['c_type'] == 3) || ($quest2copy['c_type'] == 10) ) {
				$query = "SELECT * FROM #__quiz_t_choice WHERE c_question_id = '".$old_quest_id."'";
                $db->setQuery($query);
                $fields_to_copy = $db->loadAssocList();

				foreach ($fields_to_copy as $field2copy) {
					$new_field = $this->getTable("Choice");
                    if (!$new_field->bind($field2copy)) {
                        echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
                        exit();
                    }
					$new_field->c_id = 0;
					$new_quest->ordering = 0;
					$new_field->c_question_id = $new_quest_id;
                    if (!$new_field->check()) {
                        echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
                        exit();
                    }
                    if (!$new_field->store()) {
                        echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
                        exit();
                    }
				}
			}
			if  ($quest2copy['c_type'] == 4 || $quest2copy['c_type'] == 5) {
				$query = "SELECT * FROM #__quiz_t_matching WHERE c_question_id = '".$old_quest_id."'";
                $db->setQuery($query);
                $fields_to_copy = $db->loadAssocList();
				$profile = new stdClass();

				foreach ($fields_to_copy as $field2copy) {

					// Create and populate an object.
					$profile->c_question_id = $new_quest_id;
					$profile->c_left_text = $field2copy['c_left_text'];
					$profile->c_right_text = $field2copy['c_right_text'];
					$profile->ordering = 0;
					$profile->c_quiz_id = $field2copy['c_quiz_id'];
					$profile->a_points = $field2copy['a_points'];

					// Insert the object into the user profile table.
					$result = JFactory::getDbo()->insertObject('#__quiz_t_matching', $profile);

                    if (!$result) {
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_JOOMLAQUIZ_COPY_ANSWER_TO_QUIZ_ERROR'));
                    }
				}
			}
			if ( ($quest2copy['c_type'] == 6)) {
				$query = "SELECT * FROM #__quiz_t_blank WHERE c_question_id = '".$old_quest_id."'";
                $db->setQuery($query);
                $blanks_to_copy = $db->LoadObjectList();
				if (!empty($blanks_to_copy)) {
					foreach($blanks_to_copy as $blank_to_copy) {
						$old_blank_id = $blank_to_copy->c_id;

						$query = "SELECT * FROM #__quiz_t_text WHERE c_blank_id = '".$old_blank_id."'";
                        $db->setQuery($query);
                        $fields_to_copy = $db->loadAssocList();

						$query = "INSERT INTO #__quiz_t_blank (`c_question_id`, `ordering`, `points`, `css_class`, `c_quiz_id`, `gtype`) VALUES('".(int)$new_quest_id."', '".(int)$blank_to_copy->ordering."', '".$blank_to_copy->points."', '".$blank_to_copy->css_class."', '".(int)$blank_to_copy->c_quiz_id."', '".$blank_to_copy->gtype."')";
                        $db->SetQuery($query);
                        $db->execute();
                        $new_blank_id = $db->insertid();
						foreach ($fields_to_copy as $field2copy) {
							$new_field = $this->getTable("Blanktext");
                            if (!$new_field->bind($field2copy)) {
                                echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
                                exit();
                            }
							$new_field->c_id = 0;
							$new_quest->ordering = 0;
							$new_field->c_blank_id = $new_blank_id;
                            if (!$new_field->check()) {
                                echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
                                exit();
                            }
                            if (!$new_field->store()) {
                                echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
                                exit();
                            }
						}
					}
				}
                $query = "SELECT `c_text` FROM `#__quiz_t_faketext` WHERE `c_quest_id` = ".(int)$old_quest_id;
                $db->SetQuery($query);
                $faketext_to_copy = $db->loadObjectList();
                if (!empty($faketext_to_copy)) {
                    foreach($faketext_to_copy as $faketext) {
                        $query = "INSERT INTO `#__quiz_t_faketext` (`c_id`, `c_quest_id`, `c_text`) VALUES('','".(int)$new_quest_id."','".$faketext->c_text."')";
                        $db->setQuery($query);
                        if (!$db->execute()) {
                            echo "<script> alert('" . $db->getErrorMsg() . "'); window.history.go(-1); </script>\n";
                        }
                    }
                }
			}
			if ( ($quest2copy['c_type'] == 7)) {
				$query = "SELECT * FROM #__quiz_t_ext_hotspot WHERE c_question_id = '".$old_quest_id."'";
                $db->setQuery($query);
                $fields_to_copy = $db->loadAssocList();
				foreach ($fields_to_copy as $field2copy) {
					$new_field = $this->getTable("Hotspot");
                    if (!$new_field->bind($field2copy)) {
                        echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
                        exit();
                    }
					$new_field->c_id = 0;
					$new_quest->ordering = 0;
					$new_field->c_question_id = $new_quest_id;
                    if (!$new_field->check()) {
                        echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
                        exit();
                    }
                    if (!$new_field->store()) {
                        echo "<script> alert('" . $new_field->getError() . "'); window.history.go(-1); </script>\n";
                        exit();
				}
			}
		}
        }

			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($quizMove);
        $db->setQuery("SELECT `c_title` FROM #__quiz_t_quiz WHERE c_id = '" . $quizMove . "'");
        $c_title = $db->loadResult();
			$msg = $total ." Questions copied to ". $c_title;


        $session->clear('com_joomlaquiz.copy.questions.cids');

		return $msg;
	}

	public function getItems(){
		$items = parent::getItems();

		foreach ($items as $item) {
			$item->c_question = html_entity_decode(strip_tags($item->c_question), ENT_COMPAT, 'UTF-8');
		}

		return $items;
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
		$enabled = $this->getState('filter.enabled');
		     
		$query->select("a.*, b.c_qtype as qtype_full, c.c_title as quiz_name");
		$query->select('`cat`.`title` AS `category`');
		$query->from('`#__quiz_t_question` AS `a`');
		$query->join('LEFT', '`#__quiz_t_qtypes` as `b` ON b.c_id = a.c_type');
		$query->join('LEFT', '`#__quiz_t_quiz` as `c` ON a.c_quiz_id = c.c_id');
		$query->join('LEFT','`#__categories` AS `cat` ON `cat`.`id` = `a`.`c_ques_cat` ');
		
		if($enabled != ''){
			$query->where('a.published = "'.$enabled.'"');
		}
		
		$query->select("e.enabled");

        $query->join('LEFT', '`#__extensions` as `e` ON (CONVERT (e.element USING utf8) COLLATE utf8_unicode_ci) = b.c_type');
		$query->where('e.folder = "joomlaquiz" AND e.type = "plugin"');
		/*
		if ($enabled == '')
		{
		} elseif(!$enabled){
			$query->select("e.enabled");
			$join = $query->join('LEFT', '`#__extensions` as `e` ON e.element = b.c_type');
			$query->where('e.folder = "joomlaquiz" AND e.type = "plugin" AND e.enabled = 0');
		} elseif($enabled){
			$query->select("e.enabled");
			$join = $query->join('LEFT', '`#__extensions` as `e` ON e.element = b.c_type');
			$query->where('e.folder = "joomlaquiz" AND e.type = "plugin" AND e.enabled = 1');
		}
		*/
				
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.c_id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.c_question LIKE '.$search.')');
			}
		}
				
		$quiz_id = $this->getState('filter.quiz_id');
		if(!empty(JFactory::getApplication()->input->getInt('quiz_id', 0))){
            $quiz_id = JFactory::getApplication()->input->getInt('quiz_id', 0);
            $this->setState('filter.quiz_id', $quiz_id);
        }
		if (isset($quiz_id) && $quiz_id != '')
		{
		    $query->where("a.c_quiz_id = $quiz_id");
		}
		
		$qtype_id = $this->getState('filter.qtype_id');
		if ($qtype_id)
		{
			$query->where("a.c_type = $qtype_id");
		}
		
		$ques_cat = $this->getState('filter.ques_cat');
		if ($ques_cat)
		{
			$query->where("a.c_ques_cat = $ques_cat");
		}
		
        $orderCol	= $this->state->get('list.ordering', 'a.c_question, a.c_id');	
		$orderDirn	= $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol.' '.$orderDirn));
	
        return $query;
    }
	
	public function getQuizzes(){
		$db = JFactory::getDBO();
		
		$query = "SELECT c_id AS value, c_title AS text"
		. "\n FROM #__quiz_t_quiz"
		. "\n ORDER BY c_title"
		;
		$db->setQuery( $query );
		$quizzes = $db->loadObjectList();
		
		return $quizzes;
	}
	
	public function getQuestionType(){
		$db = JFactory::getDBO();
		
		$query = "SELECT c_id AS value, c_qtype AS text"
		. "\n FROM #__quiz_t_qtypes"
		. "\n ORDER BY c_id"
		;
		$db->setQuery( $query );
		$qtypes = $db->loadObjectList();
		
		return $qtypes;
	}
	
	public function getQuestionCategories(){
		$db = JFactory::getDBO();
		
		$query = "SELECT id AS value, title as text FROM #__categories WHERE `extension` = 'com_joomlaquiz.questions' AND `published` = 1 order by lft";
		$db->setQuery( $query );
		$jq_qcat = $db->loadObjectList();
		
		return $jq_qcat;
	}
	
	public function getPageBreaks(){
		
		$db = JFactory::getDBO();
		$quiz_id = $this->getState('filter.quiz_id');
		if($this->state->get('list.ordering')=='ordering'){
			$query = "SELECT c_question_id FROM `#__quiz_t_pbreaks` WHERE `c_quiz_id` = '$quiz_id'";
			$db->setQuery( $query );
			$pbreaks = $db->loadColumn();
			$pbreaks = (is_array($pbreaks)? $pbreaks: array());
		}else{
			$pbreaks = array();
		}
		
		return $pbreaks;
	}
	
	public function uploadQuestions(){
			$database = JFactory::getDBO();
			@set_time_limit(0);

            $userfile = JFactory::getApplication()->input->files->get('importme', array(), 'array');

			if(empty($userfile)){
				$this->setRedirect( "index.php?option=com_joomlaquiz&view=questions&layout=uploadquestions", JText::_('COM_JOOMLAQUIZ_NO_FILE_SELECTED') );
			}
			$userfileTempName	= !empty($userfile) ? $userfile['tmp_name']: '';
			$userfileName 		= !empty($userfile) ? $userfile['name']: '';
			
			$quiz_id			= JFactory::getApplication()->input->get('filter_quiz_id', 0);
			
			JLoader::register('JoomlaquizControllerQuizzes', JPATH_COMPONENT_ADMINISTRATOR.'/controllers/quizzes.php');
			$quiz_controller = new JoomlaquizControllerQuizzes();
			
			$ii = 0;
			$qcat_id = 0;
			$quest_id = 0;
			$opt_ordering = 0;

			if (!$userfileTempName) {

				$app = JFactory::getApplication();

				$app->enqueueMessage('File is not selected.', 'Warning');

				JFactory::getApplication()->redirect('index.php?option=com_joomlaquiz&view=questions&layout=uploadquestions');
			}

			/*******************PARSE CSV FILE***********************/

			$csv = file_get_contents($userfileTempName);
			$rows = explode("\r",str_replace("\n","\r",$csv));
			$rows = array_filter($rows);

			$rowsAssoc = array();
			$keys = array();

			$i = 0;

			if(!$rows) {
				$msg = JText::_('COM_JOOMLAQUIZ_UPLOAD_FAILED');
				echo "<script> alert('".$msg."'); window.history.go(-1); </script>\n"; exit();
			}

			foreach ($rows as $row){
				$row = str_getcsv($row);
				if ($i === 0) $keys = $row;
				else {
					$rowAssoc = array();
					for ($i = 0; $i < count($row); $i++) {
						$rowAssoc[$keys[$i]] = $row[$i];
					}
					array_push($rowsAssoc, $rowAssoc);
				}
				$i++;
			}

			$rows = $rowsAssoc;
			/*********************************************************/

			foreach($rows as $values) {
				if (!$values['question/answer text']) continue;
			
				if ($values['question category']) {
					$query = $database->getQuery(true);
					$query->select('*')
						->from('`#__categories`')
						->where('`extension` = "com_joomlaquiz.questions"')
						->where('`title` = "'.$values['question category'].'"');
					$category = $database->setQuery($query)->loadObject();

					if(!$category->id){				
						$extension = 'com_joomlaquiz.questions';
						$title     = $values['question category'];
						$desc      = '';
						$parent_id = 1;
						$category = $quiz_controller->createCategory($extension, $title, $desc, $parent_id, 'upload '.$values['question category']);
					}
				}
				
				if ($values['question category'] && !$values['is correct'] && $values['question/answer text']) {
					$question = $this->getTable('questions');
					$question->c_question = $values['question/answer text'];
					$question->c_type = ($values['question type'] == 'mchoice'? 1: 2);
					$question->c_ques_cat = $category->id;
					$question->c_quiz_id = $quiz_id;
					$question->c_point = isset($values['points'])? $values['points']: 0;
					$question->c_attempts = isset($values['attempts'])? $values['attempts']: 0;
					$question->c_random = isset($values['random'])? $values['random']: 0;
					
					$question->c_feedback = isset($values['is feedback'])? (strpos(strtolower($values['is feedback']), 'true') !== false? 1: 0): 0;
					$question->c_right_message =  isset($values['correct feedback text'])? $values['correct feedback text']: '';
					$question->c_wrong_message =  isset($values['incorrect feedback text'])? $values['incorrect feedback text']: '';
					
					if (!$question->store()) {
						$quest_id = 0;
						continue;
					}
					
					$quest_id = $question->c_id;
					$opt_ordering = 0;
					$ii++;
				}
				
				if ($quest_id && !$values['question category'] && !$values['question type'] && $values['is correct'] && $values['question/answer text']) {
					$opt_ordering++;
					$choice = $this->getTable('choice');
					$choice->c_choice	 		= $values['question/answer text'];
					$choice->c_quiz_id			= $quiz_id;
					$choice->c_right			= strtolower($values['is correct']) == 'true'? 1: 0;
					$choice->c_question_id		= $quest_id;
					$choice->ordering			= $opt_ordering;
					$choice->a_point			= isset($values['points'])? $values['points']: 0;
					$choice->c_incorrect_feed 	= isset($values['correct feedback text'])? $values['correct feedback text']: '';
					
					$choice->store();
				}
			}

		return $ii;
	}
	
	public function getTable($type = 'Questions', $prefix = 'JoomlaquizTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function reorder($pks, $delta){
		
		$table = $this->getTable("Questions");
		$table->reorder($pks, $delta);
				
		return true;
	}
	
	public function saveorder($idArray = null, $lft_array = null)
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->saveorder($idArray, $lft_array))
		{
			$this->setError($table->getError());
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}
		
    public function getCurrDate()
    {
        $db = $this->_db;
        $query = $db->getQuery(true);
        $query->select('c_par_value');
        $query->from('`#__quiz_setup`');
        $query->where("c_par_name='curr_date'");


        $result = $db->setQuery($query)->loadResult();
        if (strtotime("+2 month",strtotime($result))<=strtotime(JFactory::getDate())) {
            return true;
        } else {
            return false;
        }
    }
}
