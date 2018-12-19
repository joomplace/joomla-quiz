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
class JoomlaquizModelQuizzes extends JModelList
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
				'published', 'a.published',
				'c_title', 'a.c_title',
				'c_category', 'b.c_category',
				'c_author', 'a.c_author',
				'c_full_score', 'a.c_full_score',
				'c_passing_score', 'a.c_passing_score',
				'c_time_limit', 'a.c_time_limit',
				'c_created_time', 'a.c_created_time',
			);
		}

		parent::__construct($config);
	}
    

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		$search = $this->getUserStateFromRequest('quizzes.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$categoryId = $this->getUserStateFromRequest('quizzes.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		// List state information.
		parent::populateState('a.c_category_id', 'asc');
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
                                
        $query->select('a.*, b.title as c_category');
        $query->from('`#__quiz_t_quiz` as `a`');
		$query->join('LEFT', '`#__categories` AS `b` ON a.c_category_id = b.id');
		
        // Filter by categories
		$categoryId = $this->getState('filter.category_id');
		if ($categoryId)
		{			
			$query->where('a.c_id!=0 AND a.c_category_id = '.$categoryId);
		} else {
			$query->where('a.c_id!=0');
		}
		
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
				$query->where('(a.c_title LIKE '.$search.')');
			}
		}
		
        $orderCol	= $this->state->get('list.ordering', 'a.c_id');
		$orderDirn	= $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol.' '.$orderDirn));
        		
        return $query;
    }
	
	public function getMovingQuizzes(){
		$db = JFactory::getDBO();
        $session = JFactory::getSession();
        $cid = $session->get('com_joomlaquiz.move.quizzes.cids');
		$cids = implode( ',', $cid );
		$query = "SELECT a.c_title as quiz_name, b.title as category_name"
		. "\n FROM #__quiz_t_quiz AS a LEFT JOIN #__categories AS b ON b.id = a.c_category_id"
		. "\n WHERE a.c_id IN ( $cids )"
		;
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		return $items;
	}
	
	public function getCopyQuizzes(){
		$db = JFactory::getDBO();
        $session = JFactory::getSession();
        $cid = $session->get('com_joomlaquiz.copy.quizzes.cids');
		$cids = implode( ',', $cid );
		$query = "SELECT a.c_title as quiz_name, b.title as category_name"
		. "\n FROM #__quiz_t_quiz AS a LEFT JOIN #__categories AS b ON b.id = a.c_category_id"
		. "\n WHERE a.c_id IN ( $cids )"
		;
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		return $items;
	}
	
	public function getTable($type = 'Quizzes', $prefix = 'JoomlaquizTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function copyQuizzes(){
		$database = JFactory::getDBO();
        $session = JFactory::getSession();
        $cid = $session->get('com_joomlaquiz.copy.quizzes.cids');
		$categoryCopy = intval(JFactory::getApplication()->input->get('categorycopy'));
		$cids = implode( ',', $cid );
		$total = count( $cid );
		
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id IN ( $cids )";
		$database->setQuery( $query );
		$quizzes_to_copy = $database->loadAssocList();

		foreach ($quizzes_to_copy as $quiz2copy) {
			$new_quiz = $this->getTable();
			
			if (!$new_quiz->bind( $quiz2copy )) { 
				echo "<script> alert('".$new_quiz->getError()."'); window.history.go(-1); </script>\n"; exit(); 
			}
			$new_quiz->published = 0;
			$new_quiz->c_id = 0; 
			$new_quiz->c_category_id = $categoryCopy; 
			$new_quiz->c_title = JText::_('COM_JOOMLAQUIZ_COPY_OF') . $new_quiz->c_title;
			if (!$new_quiz->check()) { 
				echo "<script> alert('".$new_quiz->getError()."'); window.history.go(-1); </script>\n"; exit(); 
			}
			if (!$new_quiz->store()) { 
				echo "<script> alert('".$new_quiz->getError()."'); window.history.go(-1); </script>\n"; exit(); 
			}
			$new_quiz_id = $new_quiz->c_id;

			$query = "SELECT * FROM #__quiz_pool WHERE q_id = '".$quiz2copy['c_id']."'";
			$pool = $database->SetQuery( $query )->loadObjectList();
			foreach($pool as $pool_conf_item){
				$pool_conf_item->q_id = $new_quiz_id;
				$database->insertObject('#__quiz_pool', $pool_conf_item);
			}

            $query = "SELECT * FROM #__quiz_feed_option WHERE quiz_id = '".$quiz2copy['c_id']."'";
            $feed = $database->SetQuery( $query )->loadObjectList();
            foreach($feed as $feed_opt){
                $feed_opt->quiz_id = $new_quiz_id;
                $database->insertObject('#__quiz_feed_option', $feed_opt);
            }

            $query = "SELECT c_id FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz2copy['c_id']."'";
			$database->SetQuery( $query );
			$cid = $database->loadColumn();
			if (!is_array( $cid )) {
				$cid = array(0);
			} 
			$this->JQ_copyQuestionSave( $cid, 1, $new_quiz_id );
		}
		
		$query = "SELECT *"
		. "\n FROM #__quiz_t_category"
		. "\n WHERE c_id = '".$categoryCopy."'"
		;
		$database->setQuery( $query );
		$categoryNew = $database->loadObject();

		$session = JFactory::getSession();
        $session->clear('com_joomlaquiz.copy.quizzes.cids');

		$msg = " Quizzes including all questions was copied to ". $categoryNew->c_category;
		return $msg;
	}
	
	public function JQ_copyQuestionSave( $cid, $run_from_quiz_copy = 0, $quizMove = 0 ) {
		$database = JFactory::getDBO();
		$total = 0;
	
		if(!empty($cid)){
			$cids = implode( ',', $cid );
			$total = count( $cid );		
			$query = "SELECT * FROM #__quiz_t_question WHERE c_id IN ( $cids ) ORDER BY ordering";
			$database->setQuery( $query );
			$quests_to_copy = $database->loadAssocList();
		} else {
			$quests_to_copy = array();
		}
		$new_order = 0;

		if(!empty($quests_to_copy)){
			foreach ($quests_to_copy as $quest2copy) {
				$old_quest_id = $quest2copy['c_id'];
				$new_quest = $this->getTable('Question');
				if (!$new_quest->bind( $quest2copy )) { echo "<script> alert('".$new_quest->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$new_quest->c_id = 0;
				$new_quest->ordering = $new_order;
				$new_quest->c_quiz_id = $quizMove;
				if ($run_from_quiz_copy) { $new_order++; }
				if (!$new_quest->check()) { echo "<script> alert('".$new_quest->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_quest->store()) { echo "<script> alert('".$new_quest->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$new_quest_id = $new_quest->c_id;
				if ( ($quest2copy['c_type'] == 1) || ($quest2copy['c_type'] == 2) || ($quest2copy['c_type'] == 3) || ($quest2copy['c_type'] == 10) ) {

                    if($quest2copy['c_type'] == 3){ //True/False
                        $db = JFactory::getDbo();
                        $query = $db->getQuery(true);
                        $conditions = array(
                            $db->qn('c_question_id') .'='. $db->q((int)$new_quest_id)
                        );
                        $query->delete($db->qn('#__quiz_t_choice'))
                            ->where($conditions);
                        $db->setQuery($query)->execute();
                    }

					$query = "SELECT * FROM #__quiz_t_choice WHERE c_question_id = '".$old_quest_id."'";
					$database->setQuery( $query );
					$fields_to_copy = $database->loadAssocList();
					foreach ($fields_to_copy as $field2copy) {
						$new_field = $this->getTable('Choice');
						if (!$new_field->bind( $field2copy )) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
						$new_field->c_id = 0;
						$new_quest->ordering = 0;
						$new_field->c_question_id = $new_quest_id;
						if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
						if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
					}
				}
				if ( ($quest2copy['c_type'] == 4) || ($quest2copy['c_type'] == 5)) {
					$query = "SELECT * FROM #__quiz_t_matching WHERE c_question_id = '".$old_quest_id."'";
					$database->setQuery( $query );
					$fields_to_copy = $database->loadAssocList();
					foreach ($fields_to_copy as $field2copy) {
						$new_field = $this->getTable('Matching');
						if (!$new_field->bind( $field2copy )) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
						$new_field->c_id = 0;
						$new_quest->ordering = 0;
						$new_field->c_question_id = $new_quest_id;
						if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
						if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
					}
				}
				if ( ($quest2copy['c_type'] == 6)) {
					$query = "SELECT * FROM #__quiz_t_blank WHERE c_question_id = '".$old_quest_id."'";
					$database->setQuery( $query );
					$blanks_to_copy = $database->LoadObjectList();
					if (!empty($blanks_to_copy)) {
						foreach($blanks_to_copy as $blank_to_copy) {
							$old_blank_id = $blank_to_copy->c_id;

							$query = "SELECT * FROM #__quiz_t_text WHERE c_blank_id = '".$old_blank_id."'";
							$database->setQuery( $query );
							$fields_to_copy = $database->loadAssocList();

                            $query = "INSERT INTO #__quiz_t_blank (`c_question_id`, `ordering`, `points`, `css_class`, `c_quiz_id`, `gtype`) VALUES('".(int)$new_quest_id."', '".(int)$blank_to_copy->ordering."', '".$blank_to_copy->points."', '".$blank_to_copy->css_class."', '".(int)$blank_to_copy->c_quiz_id."', '".$blank_to_copy->gtype."')";
                            $database->SetQuery( $query );
							$database->execute();
							$new_blank_id = $database->insertid();
							foreach ($fields_to_copy as $field2copy) {
								$new_field = $this->getTable('Blanktext');
								if (!$new_field->bind( $field2copy )) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
								$new_field->c_id = 0;
								$new_quest->ordering = 0;
								$new_field->c_blank_id = $new_blank_id;
								if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
								if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
							}
						}
					}
                    $query = "SELECT `c_text` FROM `#__quiz_t_faketext` WHERE `c_quest_id` = ".(int)$old_quest_id;
                    $database->SetQuery($query);
                    $faketext_to_copy = $database->loadObjectList();
                    if($faketext_to_copy){
                        foreach($faketext_to_copy as $faketext) {
                            $query = "INSERT INTO `#__quiz_t_faketext` (`c_id`, `c_quest_id`, `c_text`) VALUES('','".(int)$new_quest_id."','".$faketext->c_text."')";
                            $database->setQuery($query);
                            if(!$database->execute()){
                                echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
                            }
                        }
                    }
				}
				if ( ($quest2copy['c_type'] == 7)) {

                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query->select($db->qn('c_image'))
                        ->from($db->qn('#__quiz_t_question'))
                        ->where($db->qn('c_id') .'='. $db->q((int)$old_quest_id));
                    $db->setQuery($query);
                    $image = $db->loadResult();
                    if($image){
                        $query->clear();
                        $fields = array(
                            $db->qn('c_image') .'='. $db->q($image)
                        );
                        $conditions = array(
                            $db->qn('c_id') .'='. $db->q((int)$new_quest_id)
                        );
                        $query->update($db->qn('#__quiz_t_question'))
                            ->set($fields)
                            ->where($conditions);
                        $db->setQuery($query)
                            ->execute();
                    }

				    $query = "SELECT * FROM `#__quiz_t_ext_hotspot` WHERE c_question_id = '".$old_quest_id."'";
					$database->setQuery( $query );
					$fields_to_copy = $database->loadAssocList();
					foreach ($fields_to_copy as $field2copy) {
						$new_field = $this->getTable('Hotspot');
						if (!$new_field->bind( $field2copy )) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
						$new_field->c_id = 0;
						$new_quest->ordering = 0;
						$new_field->c_question_id = $new_quest_id;
						if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
						if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
					}
				}
				$this->copyPageBreak($old_quest_id, $new_quest_id, $quizMove);
			}
		}
		if (!$run_from_quiz_copy) {
			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($quizMove);
		}
	}

    public function copyPageBreak($old_quest_id, $new_quest_id, $new_quiz_id){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('1')
            ->from($db->qn('#__quiz_t_pbreaks'))
            ->where($db->qn('c_question_id') .'='. $db->q((int)$old_quest_id));
        $db->setQuery($query);
        $result = $db->loadResult();

        if($result){
            $query->clear();
            $columns = array('c_quiz_id', 'c_question_id');
            $values = array($db->q((int)$new_quiz_id), $db->q((int)$new_quest_id));
            $query->insert($db->qn('#__quiz_t_pbreaks'))
                ->columns($db->qn($columns))
                ->values(implode(',', $values));
            $db->setQuery($query)->execute();
        }

        return true;
    }

	public function getCategories(){
		$db = JFactory::getDBO();
		
		$query = "SELECT id AS value, title AS text"
		. "\n FROM #__categories"
		. "\n WHERE `extension` = 'com_joomlaquiz' AND `published` IN ('0', '1')"
		. "\n ORDER BY lft";
		$db->setQuery( $query );
		
		$categories = $db->loadObjectList();
		
		return $categories;
	}
	
	static public function delete($cid){
		$db = JFactory::getDBO();
		$option = "com_joomlaquiz";
		if (!empty( $cid )) {
			
			$query = "DELETE FROM #__quiz_pool" . "\n WHERE " . $db->qn('q_id') . " IN ( ". implode(', ', $cid) . ")";
            $db->setQuery( $query );
            $db->execute();

            $query = "DELETE FROM #__quiz_feed_option" . "\n WHERE " . $db->qn('quiz_id') . " IN ( ". implode(', ', $cid) . ")";
            $db->setQuery( $query );
            $db->execute();

			$names = "'com_joomlaquiz.quiz.".implode( "', 'com_joomlaquiz.quiz.", $cid )."'";
            $query = "DELETE FROM #__assets"
                . "\n WHERE ".$db->qn('name')." IN ( $names )"
            ;
            $db->setQuery( $query );
            if (!$db->execute()) {
                echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
                exit();
            }

			$cids = implode( ',', $cid );
			$query = "DELETE FROM #__quiz_t_quiz"
			. "\n WHERE c_id IN ( $cids )"
			;
			$db->setQuery( $query );
			if (!$db->execute()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			$query = "SELECT c_id FROM #__quiz_t_question WHERE c_quiz_id IN ( $cids )";
			$db->SetQuery( $query );
			$cid = $db->loadColumn();
			if (!is_array( $cid )) {
				$cid = array(0);
			} 
			JoomlaquizModelQuizzes::JQ_removeQuestion( $cid, $option, 1);
		}
		
		return true;
	}
	
	static public function JQ_removeQuestion(&$cid, $option, $run_from_quiz_remove = 0){
		$db = JFactory::getDBO();
		if (!empty( $cid )) {
			$cids = implode( ',', $cid );
			$query = "SELECT distinct c_quiz_id FROM #__quiz_t_question WHERE c_id IN ( $cids )";
			$db->SetQuery( $query );
			$ch_quizzes = $db->LoadObjectList();
			$query = "DELETE FROM #__quiz_t_question"
			. "\n WHERE c_id IN ( $cids )"
			;
			$db->setQuery( $query );
			if (!$db->execute()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			} else {
				$query = "DELETE FROM #__quiz_t_faketext WHERE c_quest_id  IN ( $cids )";
				$db->setQuery( $query );
				$db->execute();
				$query = "DELETE FROM #__quiz_t_choice WHERE c_question_id IN ( $cids )";
				$db->setQuery( $query );
				$db->execute();
				$query = "DELETE FROM #__quiz_t_ext_hotspot WHERE c_question_id IN ( $cids )";
				$db->setQuery( $query );
				$db->execute();
				$query = "DELETE FROM #__quiz_t_matching WHERE c_question_id IN ( $cids )";
				$db->setQuery( $query );
				$db->execute();
				$query = "SELECT c_id FROM #__quiz_t_blank WHERE c_question_id IN ( $cids )";
				$db->SetQuery( $query );
				$blank_cid = $db->loadColumn();
				if (is_array( $blank_cid ) && !empty($blank_cid)) {
					$blank_cids = implode( ',', $blank_cid );
					$query = "DELETE FROM #__quiz_t_text"
					. "\n WHERE c_blank_id IN ( $blank_cids )"
					;
					$db->setQuery( $query );
					$db->execute();
				}
                $query = "DELETE FROM `#__quiz_t_pbreaks` WHERE `c_question_id` IN ( $cids )";
                $db->setQuery( $query );
                $db->execute();
			}
			//recalculate quizzes TotalScore
			if (!empty($ch_quizzes)) {
				foreach ($ch_quizzes as $c_q) {
					JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($c_q->c_quiz_id);
				}
			}
		}
	}
	
	static public function publish(&$cid, $value = 1){
			$database = JFactory::getDBO();
			$task = JFactory::getApplication()->input->getCmd('task');
			$state = ($task == 'publish') ? 1 : 0;
			
			if (!is_array( $cid ) || empty( $cid )) {
				$action = ($task == 'publish') ? 'publish' : 'unpublish';
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_ITEM_TO')." $action'); window.history.go(-1);</script>\n";
				exit();
			}
			
			$cids = implode( ',', $cid );

			$query = "UPDATE #__quiz_t_quiz"
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
