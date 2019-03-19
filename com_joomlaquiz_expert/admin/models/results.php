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
require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

/**
 * Joomlaquiz Deluxe Model
 */
class JoomlaquizModelResults extends JModelList
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
				'c_date_time', 'sq.c_date_time',
				'c_title', 'q.c_title',
				'c_total_score', 'sq.c_total_score',
				'c_full_score', 'sq.c_max_score',
				'c_passing_score', 'q.c_passing_score',
				'c_passed', 'sq.c_passed',
				'c_total_time', 'sq.c_total_time',);
		}
		
		parent::__construct($config);
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		$search = $this->getUserStateFromRequest('results.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$quiz_id = $this->getUserStateFromRequest('results.filter.quiz_id', 'filter_quiz_id');
		$this->setState('filter.quiz_id', $quiz_id);
		
		$user_id = $this->getUserStateFromRequest('results.filter.user_id', 'filter_user_id');
		$this->setState('filter.user_id', $user_id);
		
		$passed = $this->getUserStateFromRequest('results.filter.passed', 'filter_passed');
		$this->setState('filter.passed', $passed);

		if(JFactory::getApplication()->input->get('layout')=='stu_report' && 1==1){

            $limit = JFactory::getApplication()->input->getInt('stu_limit', 0);
            $this->setState('list.limit', $limit);

            $start = JFactory::getApplication()->input->getInt('stu_limitstart', 0);
            $this->setState('list.start', $start);

            $direction = JFactory::getApplication()->input->get('stu_direction', 'ASC');
            $this->setState('list.direction', $direction);

            $ordering = JFactory::getApplication()->input->get('stu_ordering', 'sq.c_date_time');
            $this->setState('list.ordering', $ordering);

            JFactory::getApplication()->input->set('limit', $limit);
            JFactory::getApplication()->input->set('limitstart', $start);
            JFactory::getApplication()->input->set('direction', $direction);
            JFactory::getApplication()->input->set('ordering', $ordering);
		}

		// List state information.
		parent::populateState('sq.c_id', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.quiz_id');
		$id	.= ':'.$this->getState('filter.user_id');
		$id	.= ':'.$this->getState('filter.passed');		
		return parent::getStoreId($id);
	}
	
	public static function delete($cid){
		
		$database = JFactory::getDBO();
		if (!empty( $cid )) {
			$cids = implode( ',', $cid );
			
			$query = "SELECT c_id FROM #__quiz_r_student_question"
			. "\n WHERE c_stu_quiz_id IN ( $cids )";
			$database->SetQuery( $query );
			$stu_q_id = $database->loadColumn();
			if ((!is_array($stu_q_id)) || empty($stu_q_id)) $stu_q_id = array(0);
			$stu_cids = implode( ',', $stu_q_id );
			JoomlaquizHelper::JQ_Delete_Items($stu_cids, 'remove/', 'removeResults');
			
			$query = "DELETE FROM #__quiz_r_student_question"
			. "\n WHERE c_stu_quiz_id IN ( $cids )";
			$database->setQuery( $query );
			if (!$database->execute()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}
			$query = "DELETE FROM #__quiz_r_student_quiz"
			. "\n WHERE c_id IN ( $cids )";
			$database->setQuery( $query );
			if (!$database->execute()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}
		}
		
		return true;
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
		$layout = JFactory::getApplication()->input->get('layout');

        if($layout == 'stu_report'){
			$cid = JFactory::getApplication()->input->get('cid');						
			$query = $this->getSTUQuery($cid, $query);			
		}else{
			$query->select("sq.user_email, sq.user_name, sq.unique_id, sq.unique_pass_id, sq.c_id, sq.c_passed, sq.c_total_score,sq.c_max_score as c_full_score, sq.c_total_time, sq.c_date_time, sq.params, sq.c_passed,q.c_title, q.c_author, q.c_passing_score, sq.c_student_id, u.username, u.name, u.email, q.c_pool, ch.q_chain");
			$query->from('`#__quiz_r_student_quiz` as `sq`');
			$query->join('LEFT', '`#__users` as `u` ON sq.c_student_id = u.id');
			$query->join('LEFT', '`#__quiz_q_chain` AS `ch` ON ch.s_unique_id = sq.unique_id');
			$query->join('LEFT', '`#__quiz_t_quiz` as `q` ON sq.c_quiz_id = q.c_id');
//			$query->where('1=1');

			/*
			$sub_query = $db->getQuery(true);
			$sub_query->select('`rq`.`c_stu_quiz_id`, COUNT(`rq`.`c_id`) AS `q_count`')
				->from('`#__quiz_r_student_question` AS `rq`')
				->join('LEFT', '`#__quiz_t_question` AS `tq` ON `rq`.`c_question_id` = `tq`.`c_id`')
				->group('`rq`.`c_stu_quiz_id`');
			if(JComponentHelper::getParams('com_joomlaquiz')->get('hide_boilerplates')){
				$sub_query->where('`tq`.`c_type` != 9');
			}
			$sub_query = (string)$sub_query;
			
			$query->join('LEFT', "($sub_query) AS `sqr` ON `sqr`.`c_stu_quiz_id` = `sq`.`c_id`");
			$query->select('`sqr`.`q_count`');
			*/

			// Filter by search in title.
			$search = $this->getState('filter.search');
			if (!empty($search))
			{
				if (stripos($search, 'id:') === 0)
				{
					$query->where('sq.c_id = '.(int) substr($search, 3));
				}
				else {
					if (stripos(trim($search), 'code:') === 0)
					{
						$query->where('CONCAT("'.JText::_('COM_JOOMLAQUIZ_SHORTCODE_ADJUSTER').'",sq.c_id,sq.c_student_id,sq.c_total_score) = "'.base_convert(str_replace('code:','',trim($search)),36,10).'"');
					}
					else {
						$search = $db->Quote('%'.$db->escape($search, true).'%');
						$query->where('(q.c_title LIKE '.$search.')');
					}
				}
			}
			
			$quiz_id = $this->getState('filter.quiz_id');
			if($quiz_id){
				$query->where('sq.c_quiz_id = '.$quiz_id);
			}
			
			$user_id = $this->getState('filter.user_id');
			if($user_id){
				$query->where('sq.c_student_id = '.$user_id);
			}
			
			$passed = $this->getState('filter.passed');
			if($passed > -1){
				$query->where('sq.c_passed = '.$passed);
			}
			
			$orderCol	= $this->state->get('list.ordering', 'sq.c_date_time');	
			$orderDirn	= $this->state->get('list.direction', 'ASC');
			$query->order($db->escape($orderCol.' '.$orderDirn));		
		}		
		
        return $query;
    }	
	
	public function getSTUQuery($cid, $query){			
		$query->select("e.enabled, sp.c_id, sp.c_score, q.c_type, q.c_point, q.c_question, qt.c_qtype, sp.c_question_id");		
		$query->from("#__quiz_r_student_question as sp");		
		$query->leftJoin("#__quiz_t_question as q ON sp.c_question_id = q.c_id");		
		$query->leftJoin("#__quiz_t_qtypes as qt ON q.c_type = qt.c_id");
        $query->leftJoin("`#__extensions` as `e` ON (CONVERT (e.element USING utf8) COLLATE utf8_unicode_ci) = qt.c_type");
		$query->where("sp.c_stu_quiz_id = '".$cid."' AND e.folder = 'joomlaquiz' AND e.type = 'plugin'");
		//if(JComponentHelper::getParams('com_joomlaquiz')->get('hide_boilerplates')){
		//	$query->where('`q`.`c_type` != 9');
		//}
		$query->order("q.ordering, q.c_id");				
		return $query;	
	}			
	
	public function getReportItems($cid, $pagination){		
		$app = JFactory::getApplication();		
		$database = JFactory::getDBO();				
		$query = "SELECT e.enabled, sp.c_id, sp.c_score, q.c_type, q.c_point, q.c_question, qt.c_qtype, sp.c_question_id"		
        . "\n FROM #__quiz_r_student_question as sp LEFT JOIN #__quiz_t_question as q ON (sp.c_question_id = q.c_id AND q.published = 1) LEFT JOIN #__quiz_t_qtypes as qt ON q.c_type = qt.c_id LEFT JOIN `#__extensions` as `e` ON (CONVERT (e.element USING utf8) COLLATE utf8_unicode_ci) = qt.c_type"
        . "\n WHERE sp.c_stu_quiz_id = '".$cid."' AND e.folder = 'joomlaquiz' AND e.type = 'plugin'"
		. "\n ORDER BY q.ordering, q.c_id"		
		. "\n LIMIT $pagination->limitstart, $pagination->limit";				
		$database->SetQuery( $query );		
		$rows = $database->LoadObjectList();		
		$rows = $this->getItemsSumForEach($rows);		
		return $rows;	
	}		
	public function getItemsSumForEach($rows){			
		$database = JFactory::getDBO();				
		foreach($rows as &$row){			
			$row->c_point += $this->getItemSum($row);		
		}				
		return $rows;	
	}		
	public function getItemSum($row){

		$database = JFactory::getDBO();

		switch ($row->c_type) {
			case 5: // DropDown
			case 4: //Drag&Drop
				$query = "SELECT SUM(a_points) FROM #__quiz_t_matching WHERE c_question_id = '". $row->c_question_id ."'";
				break;
			case 6: //Fill the blank
				$query = "SELECT SUM(points) FROM #__quiz_t_blank WHERE c_question_id = '". $row->c_question_id ."'";
				break;
			case 10: //Muiltiple question
				$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id = '" . $row->c_question_id . "'";
				break;
			default:
				$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id = '" . $row->c_question_id . "' AND c_right = 1";
		}

		$database->SetQuery($query);
		return $database->LoadResult();
	}
	public function getItems(){
		$items = parent::getItems();

		foreach ($items as $item) {
            if(isset($item->c_question) && $item->c_question) {
                $item->c_question = html_entity_decode(strip_tags($item->c_question), ENT_COMPAT, 'UTF-8');
            }
        }

		return $items;
	}
	public function getLists(){
		
		$app = JFactory::getApplication();
		$database = JFactory::getDBO();

		$lists = array();
		$javascript = 'onchange="document.adminForm.submit();"';
		$query = "SELECT distinct q.c_id AS value, q.c_title AS text"
		. "\n FROM #__quiz_t_quiz as q, #__quiz_r_student_quiz as sq"
		. "\n WHERE q.c_id = sq.c_quiz_id"
		. "\n ORDER BY q.c_title"
		;
		$database->setQuery( $query );
		$quizzes = array();
		$quizzes[] = JHTML::_('select.option', '0', JText::_('COM_JOOMLAQUIZ_SELECT_QUIZ') );
		$quizzes = array_merge( $quizzes, $database->loadObjectList() );
		$quiz = JHTML::_('select.genericlist', $quizzes,'filter_quiz_id', 'class="text_area" style="max-width: 300px;" size="1" '. $javascript, 'value', 'text', $app->getUserStateFromRequest('results.filter.quiz_id', 'filter_quiz_id') );
		$lists['quiz'] = $quiz;

		$opt = array();
		$opt[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_ANY_RESULT') );
		$opt[] = JHTML::_('select.option', '1', JText::_('COM_JOOMLAQUIZ_PASSED') );
		$opt[] = JHTML::_('select.option', '0', JText::_('COM_JOOMLAQUIZ_FAILED2') );
		$lists['passed'] = JHTML::_('select.genericlist', $opt,'filter_passed', 'class="text_area" style="max-width: 300px;" size="1" '. $javascript, 'value', 'text', $app->getUserStateFromRequest('results.filter.passed', 'filter_passed') );
	
		$query = "SELECT distinct q.id AS value, q.username AS text"
		. "\n FROM #__users as q, #__quiz_r_student_quiz as sq"
		. "\n WHERE q.id = sq.c_student_id"
		. "\n ORDER BY q.username"
		;
		$database->setQuery( $query );
		$users = array();
		$users[] = JHTML::_('select.option', '0', JText::_('COM_JOOMLAQUIZ_SELECT_USER') );
		$users[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_ANONYMOUS') );
		$users = array_merge( $users, $database->loadObjectList() );
		$lists['user'] = JHTML::_('select.genericlist', $users,'filter_user_id', 'class="text_area" style="max-width: 300px;" size="1" '. $javascript, 'value', 'text', $app->getUserStateFromRequest('results.filter.user_id', 'filter_user_id') );
		
		return $lists;
	}
	
	public function JQ_csv_summaryReport($cid){
		
		$appsLib = JqAppPlugins::getInstance();
		$appsLib->loadApplications();
		$database = JFactory::getDBO();

		$query = "SELECT * FROM `#__quiz_r_student_quiz` ORDER BY `c_quiz_id`, `c_student_id`, `c_id`";
		$database->setQuery( $query );
		$results = $database->loadObjectList();
		
		$custom_head = JHtml::_('content.prepare','',$results,'admin.results.csv.head');
		$csv_report = '"USERID/USERNAME","MANUAL EMAIL"'.(($custom_head)?$custom_head.'':'').',"QUIZ","QUESTION","ANSWER","FEEDBACK"'."\n";
		
		$query = "SELECT DISTINCT `c_quiz_id` FROM `#__quiz_r_student_quiz` ORDER BY `c_quiz_id`";
		$database->setQuery( $query );
		$quizzies = $database->loadColumn();
		
		$query = "SELECT DISTINCT q.`c_question_id` FROM `#__quiz_r_student_quiz` AS sq, `#__quiz_r_student_question` AS q WHERE sq.`c_id` = q.`c_stu_quiz_id` ORDER BY sq.`c_quiz_id`";
		$database->setQuery( $query );
		$questions_arr = $database->loadColumn();
		
		if (is_array($quizzies) && !empty($quizzies))
		foreach($quizzies as $quiz_number=>$quiz_id) {
			$query = $database->getQuery(true);
			$query->select('*')
				->from('`#__quiz_t_question`')
				->where("`c_id` IN ('".implode("','", $questions_arr)."')")
				->where("`published` = 1")
				->order("`ordering`, `c_id`");
			//if(JComponentHelper::getParams('com_joomlaquiz')->get('hide_boilerplates')){
			//	$query->where('`c_type` != 9');
			//}
			$database->setQuery( $query );
			$questions = $database->loadObjectList();
			
							
			$query = "SELECT * FROM `#__quiz_t_choice` ORDER BY `c_question_id`, `ordering`, `c_id`";
			$database->setQuery( $query );
			$choices = $database->loadObjectList();
			
			$j=0;
			$qid = 0;
			for($i=0, $n=count($choices); $i<$n; $i++){
				if ($qid != $choices[$i]->c_question_id){
					$j=0;
					$qid = $choices[$i]->c_question_id;
				}
				$choices[$i]->number = $j;
				$j++;
			}

			foreach($results as $result) {
				if ($result->c_quiz_id == $quiz_id) {
					$query = "SELECT DISTINCT q.`c_question_id` FROM #__quiz_r_student_question AS q WHERE q.c_stu_quiz_id = '".$result->c_id."'";
					$database->setQuery( $query );
					$student_questions = $database->loadColumn();

					foreach($questions as $q_number=>$question) {
						if (in_array($question->c_id, $student_questions)) {
							$answer = '';
							$feedback = '';
							
							$data = array();
							$type = JoomlaquizHelper::getQuestionType($question->c_type);
							$className = 'plgJoomlaquiz'.ucfirst($type);
							$data['quest_type'] = $type;
							$data['result'] = $result;
							$data['question'] = $question;
							$data['choices'] = $choices;
							$data['feedback'] = $feedback;
							$data['answer'] = $answer;
							$answer = (method_exists($className, 'onGetAdminCsvData')) ? $appsLib->triggerEvent( 'onGetAdminCsvData' , $data ) : array('');
							$answer = $data['answer'];
							
							$feedback = $data['feedback'];
							$user = $result->c_student_id;
							if (!$result->c_student_id) {
								if($result->user_name != ''){
									$user = $result->user_name;
								} elseif($result->user_email != ''){
									$user = $result->user_email;
								} else {
									$user = 'Anonymous-'.$result->c_id;
								}
							}
                            if(!empty($result->user_email))
                                $manual_email = $result->user_email;
                            else
                                $manual_email = ' - ';
								
							$custom_data = JHtml::_('content.prepare','',$result,'admin.results.csv.row');

							$csv_report .= '"'.$user.'","'.$manual_email.'",'.(($custom_data)?$custom_data.',"':'"').($quiz_number+1).'","'.($q_number+1).'","'.$answer.'","'.$feedback.'"'."\n";
						}
					}
				}				
			}
		}

		$UserBrowser = '';
		if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) $UserBrowser = "IE";
		header("Content-Type:application/vnd.ms-excel");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header("Content-Length: ".strlen($csv_report)); 
		if ($UserBrowser == 'IE') {
			header("Content-Disposition: inline; filename=quiz_results_".date('d_m_Y').".csv ");
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header("Content-Disposition: inline; filename=quiz_results_".date('d_m_Y').".csv ");
			header('Pragma: no-cache');
		}
		echo $csv_report;
		die();
	}
	
	public function JQ_csv_report($cid){

		$app = JFactory::getApplication();
		$database = JFactory::getDBO();
		
		$quiz_id = $app->getUserStateFromRequest('results.filter.quiz_id', 'filter_quiz_id');
		$user_id = $app->getUserStateFromRequest('results.filter.user_id', 'filter_user_id');
		$passed = $app->getUserStateFromRequest('results.filter.passed', 'filter_passed');
		
		$query = "SELECT sq.c_id, sq.c_passed, sq.params , sq.c_total_score, sq.c_total_time, sq.c_date_time, sq.c_passed, sq.user_email, sq.user_name,"
        . "\n q.c_title, q.c_author, q.c_passing_score,sq.c_student_id, u.username, u.name, u.email, sq.c_max_score as c_full_score, q.c_pool, ch.q_chain "
		. "\n FROM #__quiz_r_student_quiz as sq"
		. "\n LEFT JOIN #__users as u ON sq.c_student_id = u.id"
		. "\n LEFT JOIN `#__quiz_q_chain` AS ch ON ch.s_unique_id = sq.unique_id "
		. "\n LEFT JOIN #__quiz_t_quiz as q ON sq.c_quiz_id = q.c_id WHERE 1=1 "
		. ( $quiz_id ? "\n AND sq.c_quiz_id = $quiz_id" : '' )
		. ( $user_id ? "\n AND c_student_id = $user_id" : '' )
		. ( $passed > -1 ? "\n AND sq.c_passed = $passed " : '' )
		. "\n AND sq.c_id IN (".implode(",", $cid).")"
		. "\n ORDER BY sq.c_date_time DESC"
		;
		$database->SetQuery( $query );
		$csv_rows = $database->LoadObjectList();
		
		for($i=0, $n=count($csv_rows); $i<$n; $i++) {
			if ($csv_rows[$i]->c_pool) {
				$qids = str_replace('*', "','", $csv_rows[$i]->q_chain);
				$total_score = 0;
				$query = "SELECT SUM(c_point) FROM #__quiz_t_question WHERE c_id IN ('".$qids."') AND published = 1";
				$database->SetQuery( $query );
				$total_score = $database->LoadResult();
	
				$query = "SELECT SUM(c.a_point) FROM #__quiz_t_choice as c, #__quiz_t_question as q WHERE c.c_question_id = q.c_id AND q.c_id IN ('".$qids."') AND c_right = 1 AND q.published = 1";
				$database->SetQuery( $query );
				$total_score += $database->LoadResult();
	
				$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 10 AND c_id IN ('".$qids."')";
				$database->SetQuery( $query );
				$qch_ids_type_10 = $database->loadColumn();
				if(!empty($qch_ids_type_10)) {
					$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id IN (".implode(',', $qch_ids_type_10).") AND c_right = 0";
					$database->SetQuery( $query );
					$total_score += $database->LoadResult();
				}
	
				$csv_rows[$i]->c_full_score = $total_score;
			}
		}
		
		$custom_head = JHtml::_('content.prepare','',$csv_rows,'admin.results.csv.head');
							
		$str = '"'.JText::_('COM_JOOMLAQUIZ_NO2').'","'.JText::_('COM_JOOMLAQUIZ_TITLE2').'","'.JText::_('COM_JOOMLAQUIZ_AUTHOR').'","'.JText::_('COM_JOOMLAQUIZ_TOTAL_SCORE').'","'.JText::_('COM_JOOMLAQUIZ_PASSING_SCORE').'","'.JText::_('COM_JOOMLAQUIZ_USERNAME2').'","'.JText::_('COM_JOOMLAQUIZ_USEREMAIL').'","'.JText::_('COM_JOOMLAQUIZ_USER_SCORE').'","'.JText::_('COM_JOOMLAQUIZ_DATE_TIME').'","'.(($custom_head)?$custom_head.'","':'').JText::_('COM_JOOMLAQUIZ_SPEND_TIME').'","'.JText::_('COM_JOOMLAQUIZ_PASSED').'"'."\n";
		for($i=0, $n = count($csv_rows); $i < $n; $i++) {
			$str .= '"'.($i+1).'","';
			$str .= $csv_rows[$i]->c_title.'","'.$csv_rows[$i]->c_author.'","';
			$str .= $csv_rows[$i]->c_full_score.'","';
			$str .= $csv_rows[$i]->c_passing_score.'%","';
			$str .= ($csv_rows[$i]->username != ''? $csv_rows[$i]->username: (($csv_rows[$i]->user_name != '') ? $csv_rows[$i]->user_name : (($csv_rows[$i]->user_email != '') ? $csv_rows[$i]->user_email : JText::_('COM_JOOMLAQUIZ_ANONYMOUS_USER')))).'","'.$csv_rows[$i]->user_email.'","'.$csv_rows[$i]->c_total_score.'","'.JHtml::_('date',$csv_rows[$i]->c_date_time,'Y-m-d H:i:s').'"';
			$custom_data = JHtml::_('content.prepare','',$csv_rows[$i],'admin.results.csv.row');
			$str .= '"'.($custom_data)?$custom_data.'':'"';
			$tot_min = floor($csv_rows[$i]->c_total_time / 60);
			$tot_sec = $csv_rows[$i]->c_total_time - $tot_min*60;
			$str .= ',"'.str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT).'","';
			if($csv_rows[$i]->c_passed)
				$str .= 'Yes';
			else
				$str .= 'No';
			$str .= "\"\n";
		}
		
		$UserBrowser = '';
		if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) $UserBrowser = "IE";
		header("Content-Type:application/vnd.ms-excel");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		if ($UserBrowser == 'IE') {
			header("Content-Disposition: inline; filename=quiz_results.csv ");
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header("Content-Disposition: inline; filename=quiz_results.csv ");
			header('Pragma: no-cache');
		}
		echo $str;
		die();
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
