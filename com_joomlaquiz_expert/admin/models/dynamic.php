<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
* Dynamic model.
*
*/
class JoomlaquizModelDynamic extends JModelAdmin
{		
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		return;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		return;
	}
	
	public function getLists(){
		
		$app = JFactory::getApplication();
		$database = JFactory::getDBO();
		
		$lists = array();
		// $quiz_id	= intval( $app->getUserStateFromRequest( "filter.quiz_id", 'filter_quiz_id', -1 ) );
		$post=JFactory::getApplication()->input->post;
		$quiz_id = $post->get('quiz_id');
		if ($quiz_id < 1) {
			$query = "SELECT c_quiz_id FROM #__quiz_r_student_quiz ORDER BY c_id DESC";
			$database->setQuery( $query );
			$quiz_id = (int)$database->loadResult();
		}
		
		$query = "SELECT c_id AS value, c_title AS text FROM #__quiz_t_quiz WHERE c_id > 0 AND c_title <> 'Questions Pool' ORDER BY c_title";
		$database->setQuery( $query );
		$quizzes = $database->loadObjectList();
		$javascript = 'onchange="document.adminForm.submit();"';
		$quizzes = JHTML::_('select.genericlist', $quizzes, 'quiz_id', 'class="text_area" size="1" style="max-width: 300px;" '.$javascript, 'value', 'text', $quiz_id ); 
		$lists['quizzes'] = $quizzes;
		
		$fields = array();
		$fields[] = JHTML::_('select.option','id', 'ID');
		$fields[] = JHTML::_('select.option','user_id', 'user ID');
		$fields[] = JHTML::_('select.option','user_name', 'user Name');
		$fields[] = JHTML::_('select.option','user_email', 'user Email');	
		$fields[] = JHTML::_('select.option','spent_time', 'Spent time');
		$fields[] = JHTML::_('select.option','start_date', 'Start date');
		$fields[] = JHTML::_('select.option','score', 'Score');
		$fields[] = JHTML::_('select.option','passed', 'Passed');
		$fields[] = JHTML::_('select.option','order_id', 'Order_id');
		$fields[] = JHTML::_('select.option','quiz_name', 'Quiz name');
		
		$query = "SELECT * FROM #__quiz_t_question WHERE c_type IN (1,2,3,6,8) AND c_quiz_id = '".$quiz_id."'";
		$database->setQuery( $query );
		$questions = $database->loadObjectList( );

        if(!$questions || empty($questions)) {
            //Perhaps it is a Questions Pool?
            $db = $database;
            $query = $db->getQuery(true);
            $query->select($db->qn('q_count'))
                ->from($db->qn('#__quiz_pool'))
                ->where($db->qn('q_id') . '=' . (int)$quiz_id);
            $db->setQuery($query);
            $qpool = $db->loadResult();

            if ($qpool) {
                //Yes, it is a Questions Pool
                $query->clear();
                $query->select($db->qn('c_question_id'))
                    ->from($db->qn('#__quiz_r_student_question', 'sqn'))
                    ->leftJoin($db->qn('#__quiz_r_student_quiz', 'sq') . ' ON ' . $db->qn('sq.c_id').'='.$db->qn('sqn.c_stu_quiz_id'))
                    ->where($db->qn('sq.c_quiz_id') . '=' . (int)$quiz_id)
                    ->group($db->qn('c_question_id'));
                $db->setQuery($query);
                $qpool_question_ids = $db->loadObjectList();

                if($qpool_question_ids && !empty($qpool_question_ids)){
                    $questList = array();
                    foreach ($qpool_question_ids as $key => $value) {
                        $questList[] = $db->q($value->c_question_id);
                    }
                    $questList = implode(',', $questList);

                    $query->clear();
                    $query->select('*')
                        ->from($db->qn('#__quiz_t_question'))
                        ->where($db->qn('c_type') . " IN ('1','2','3','6','8')")
                        ->where($db->qn('c_id') . ' IN ('.$questList.')');
                    $db->setQuery($query);
                    $questions = $db->loadObjectList();
                }
            }
        }

        if (is_array($questions ))
		foreach($questions as $question){
			$text = $question->report_name? $question->report_name: strip_tags( $question->c_question);
			$fields[] = JHTML::_('select.option',$question->c_id, $text);
		}
		
		$lists['fields'] = $fields;
		return $lists;
	}
	
	public function get_dynamic_csv($csv){
		$database = JFactory::getDBO();
		$app = JFactory::getApplication();
		@set_time_limit(0);
		
		$quiz_id	= intval( JFactory::getApplication()->input->get( 'quiz_id', -1 ) );
		$selected_fields = JFactory::getApplication()->input->get('selected_field', array(),'');
		
		$startdate = strtotime(JFactory::getApplication()->input->get('startdate', '' ));
		$enddate = strtotime(JFactory::getApplication()->input->get( 'enddate', '' ));
		
		$value11 = JFactory::getApplication()->input->get( 'value11', '0' );
		$condition1 = JFactory::getApplication()->input->get( 'condition1', '0' );
		$value12 = JFactory::getApplication()->input->get( 'value12', '' );
		
		$operation = JFactory::getApplication()->input->get( 'operation', '0' );
		if ($operation == 'OR') {
			$operation = ' OR ';
		} elseif ($operation == 'AND') {
			$operation = ' AND ';
		} else {
			$operation = '';
		}
		
		$value21 = JFactory::getApplication()->input->get( 'value21', '0' );
		$condition2 = JFactory::getApplication()->input->get( 'condition2', '0' );
		$value22 = JFactory::getApplication()->input->get( 'value22', '' );
		
		$sql_condition1 = $this->JQ_parse_condition($value11, $condition1, $value12);
		$sql_condition2 = $this->JQ_parse_condition($value21, $condition2, $value22);
		
		$where = '';
		if($sql_condition1){	
			$where .= $sql_condition1;
		}
		if($operation && $sql_condition2){	
			$where .= $operation.$sql_condition2;
		} elseif(!$sql_condition1 && !$operation && $sql_condition2){
			$where .= $sql_condition2;
		}
		$left_join = '';
		if(	(intval($value11) && intval($value11) == $value11) ||
			(intval($value21) && intval($value21) == $value21) ){
			$left_join .= "\n LEFT JOIN #__quiz_r_student_question as squest ON (squest.c_stu_quiz_id = sq.c_id AND squest.c_question_id IN ('".intval($value11)."', '".intval($value21)."')) ";
			$left_join .= "\n LEFT JOIN #__quiz_r_student_choice as sc ON sc.c_sq_id = squest.c_id ";
			$left_join .= "\n LEFT JOIN #__quiz_r_student_blank as sb ON sb.c_sq_id = squest.c_id ";
			$left_join .= "\n LEFT JOIN #__quiz_r_student_survey AS ss ON ss.c_sq_id = squest.c_id ";
		}
		
		$query = "SELECT DISTINCT sq.c_id AS `x_id`, sq.c_passed AS `x_passed`, sq.c_total_score AS `x_score`, SEC_TO_TIME(sq.c_total_time) AS `x_spent_time`, sq.c_date_time AS `x_start_date`, sq.c_order_id AS `x_order_id`, q.c_title AS `x_quiz_name`, sq.c_student_id AS `x_user_id`, u.name AS `x_user_name`, u.email AS `x_user_email` "
			. "\n FROM #__quiz_r_student_quiz AS sq "
			. "\n LEFT JOIN #__users as u ON sq.c_student_id = u.id"
			. "\n LEFT JOIN #__quiz_t_quiz as q ON sq.c_quiz_id = q.c_id "
			. $left_join
			. "\n WHERE 1=1 AND sq.c_quiz_id = '{$quiz_id}' "	
			. ($startdate? "\n AND sq.c_date_time >= '".date('Y-m-d H:i:s', $startdate)."'": '')
			. ($enddate? "\n AND sq.c_date_time <= '".date('Y-m-d H:i:s', $enddate)."'": '')
			. ( $where ? "\n AND ({$where})" : '' )		
			. "\n ORDER BY sq.c_date_time DESC"	
		;
		$database->SetQuery( $query );
		$rows = $database->LoadObjectList();

        foreach($rows as $key=>$row){
            $query = "SELECT user_email,user_name from #__quiz_r_student_quiz where c_id='".$row->x_id."'";
            $database->SetQuery( $query );
            $unreg_user_info = $database->LoadObjectList();
            if(!empty($unreg_user_info[0]->user_name)||!empty($unreg_user_info[0]->user_email)){
                $rows[$key]->x_user_name = $unreg_user_info[0]->user_name;
                $rows[$key]->x_user_email = $unreg_user_info[0]->user_email;
            }
        }
		
		$qids = array();
		foreach($selected_fields as $selected_field) {
			if (intval($selected_field) && intval($selected_field) == $selected_field ) {
				$qids[] = intval($selected_field);
			}
		}
		
		$fields['id'] = 'ID';
		$fields['user_id'] = 'user ID';
		$fields['user_name'] = 'user Name';
		$fields['user_email'] = 'user Email';	
		$fields['spent_time'] = 'Spent time';
		$fields['start_date'] = 'Start date';
		$fields['score'] = 'Score';
		$fields['passed'] = 'Passed';
		$fields['order_id'] = 'Order_id';
		$fields['quiz_name'] = 'Quiz name';
		
		if (!empty($qids) && is_array($rows)) {
			$query = "SELECT * FROM #__quiz_t_question WHERE c_id IN ('".implode("','", $qids)."')";
			$database->SetQuery( $query );
			$questions = $database->LoadObjectList();
			
			$query = "SELECT * FROM `#__quiz_t_choice` WHERE `c_question_id` IN ('".implode("','", $qids)."') ORDER BY `c_question_id`, `ordering`, `c_id`";
			$database->setQuery( $query );
			$choices = $database->loadObjectList();
				
			foreach($rows as $i=>$row) {
				foreach($questions as $question) {
					$answer = '';
					$fields[$question->c_id] = $question->report_name? $question->report_name: strip_tags( $question->c_question);
					$qid = 'x_'.$question->c_id;
					switch($question->c_type) {
						case '1':
						case '2':
						case '3':
							$query = "SELECT `b`.`c_choice_id` FROM `#__quiz_r_student_question` AS `a`, `#__quiz_r_student_choice` AS `b` WHERE `a`.`c_stu_quiz_id` = '{$row->x_id}' AND `a`.`c_question_id` = '{$question->c_id}' AND `a`.`c_id` = `b`.`c_sq_id` ";
							$database->setQuery( $query );
							$stu_choices = $database->loadColumn();	
							
							if(!empty($stu_choices)){
								foreach($choices as $choice) {	
									if ( in_array($choice->c_id, $stu_choices) ) {
										$answer .= $choice->c_choice.'; ';								
									}
								}
							}
							if ($answer != '')
								$answer = JoomlaquizHelper::jq_substr($answer, 0, strlen($answer)-2);

						break;
						case '6':
							$query = "SELECT `b`.`c_answer` FROM `#__quiz_r_student_question` AS `a`, `#__quiz_r_student_blank` AS `b` WHERE `a`.`c_stu_quiz_id` = '{$row->x_id}' AND `a`.`c_question_id` = '{$question->c_id}' AND `a`.`c_id` = `b`.`c_sq_id` ";
							$database->setQuery( $query );
							$stu_answers = $database->loadColumn();
							$answer = @implode('; ', $stu_answers);
						break;
						case '8':
							$query = "SELECT `b`.`c_answer` FROM `#__quiz_r_student_question` AS `a`, `#__quiz_r_student_survey` AS `b` WHERE `a`.`c_stu_quiz_id` = '{$row->x_id}' AND `a`.`c_question_id` = '{$question->c_id}' AND `a`.`c_id` = `b`.`c_sq_id` ";
							$database->setQuery( $query );
							$answer = $database->loadResult();
						break;
						default:
							$type = JoomlaquizHelper::getQuestionType($question->c_type);
						break;
					}
					$rows[$i]->$qid = $answer;
				}
			}		
		}
		$csv_data = '';
		foreach($selected_fields as $selected_field) {
			if(isset($fields[$selected_field]))
				$csv_data .= $this->JQ_processCSVField($fields[$selected_field]).',';
		}
		if ($csv_data)
			$csv_data = JoomlaquizHelper::jq_substr($csv_data, 0, strlen($csv_data)-1);
		$csv_data .= "\n";
		
		if (is_array($rows ))	
		foreach($rows as $i=>$row) {
			foreach($selected_fields as $selected_field) { 
				$key = 'x_'.$selected_field;
				if(isset($fields[$selected_field]) && isset($row->$key))
					$csv_data .= $this->JQ_processCSVField($row->$key).',';
				else 
					$csv_data .= ',';
			}
			if ($csv_data)
				$csv_data = JoomlaquizHelper::jq_substr($csv_data, 0, strlen($csv_data)-1);
			
			$csv_data .= "\n";
		}
		
		if($csv){
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

            //add BOM to fix UTF-8 in Excel
            $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) );
            $csv_data = $bom.$csv_data;

            echo $csv_data;
			die();
		}
		
		$lists = array();
		$lists['fields'] = $fields;
		$lists['selected_fields'] = $selected_fields;
		$lists['rows'] = $rows;
		
		return $lists;
	}
	
	public function JQ_parse_condition($value11, $condition1, $value12){
		
		$database = JFactory::getDBO();
		
		$sql = '';
		switch($value11){
			case 'id': 
				$sql = 'sq.c_id'; 
			break;
			case 'user_id': 
				$sql = 'sq.c_student_id'; 
			break;
			case 'user_name': 
				$sql = 'u.name'; 
			break;
			case 'user_email': 
				$sql = 'u.email'; 
			break;
			case 'spent_time': 
				$sql = 'sq.c_total_time'; 
			break;
			case 'start_date': 
				$sql = 'sq.c_date_time'; 
			break;
			case 'score': 
				$sql = 'sq.c_total_score'; 
			break;
			case 'passed': 
				$sql = 'sq.c_passed'; 
			break;
			case 'order_id': 
				$sql = 'sq.c_order_id'; 
			break;
			case 'quiz_name': 
				$sql = 'q.c_title'; 
			break;
		}
		
		$q = false;
		if (!$sql && intval($value11) && intval($value11) == $value11){
			$q = true;
			$query = "SELECT c_type FROM #__quiz_t_question WHERE c_id = ".intval($value11);
			$database->SetQuery( $query );
			$c_type = $database->loadResult();
			switch($c_type) {
				case '1':
				case '2':
				case '3':
					$sql = "sc.c_choice_id";
				break;
				case '6':
					$sql = "sb.c_answer";
				break;
				case '8':
					$sql = "ss.c_answer";
				break;
				default:
					$type = JoomlaquizHelper::getQuestionType($question->c_type);
				break;
			}
		}
		
		if ($sql)
		switch($condition1){
			case '1': 
				$sql .= ' < '; 
			break;
			case '2': 
				$sql .= ' = '; 
			break;
			case '3': 
				$sql .= ' > '; 
			break;
			case '4': 
				$sql .= ' <> '; 
			break;
		}
		
		if ($sql && !$q) {
			$sql .= $database->Quote($value12);
		} elseif ($q) {
			switch($c_type) {
				case '1':
				case '2':
				case '3':
					$query = "SELECT `c_id` FROM #__quiz_t_choice WHERE c_question_id = ".intval($value11)." AND c_choice = ".$database->Quote($value12);
					$database->SetQuery( $query );
					$choice_id = $database->loadResult();
					$sql .= $database->Quote($choice_id);
				break;
				case '6':
				case '8':
					$sql .= $database->Quote($value12);
				break;
				default:
					$type = JoomlaquizHelper::getQuestionType($question->c_type);
				break;
			}
		}
		
		return $sql;
	}
	
	public function JQ_processCSVField($field_text) {
		$field_text = strip_tags($field_text);
		$field_text = str_replace( '&#039;', "'", $field_text );
		$field_text = str_replace( '&#39;', "'", $field_text );
		$field_text = str_replace('&quot;',  '"', $field_text );
		$field_text = str_replace( '"', '""', $field_text );
		$field_text = str_replace( "\n", ' ', $field_text );
		$field_text = str_replace( "\r", ' ', $field_text );
		$field_text = '"'.$field_text.'"';
		return $field_text;
	}
}