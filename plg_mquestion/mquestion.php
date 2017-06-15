<?php
/**
* JoomlaQuiz Multiple question Plugin for Joomla
* @version $Id: mquestion.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage mquestion.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizMquestion extends plgJoomlaquizQuestion
{
	var $name		= 'Mquestion';
	var $_name		= 'mquestion';
	
	public function onCreateQuestion(&$data) {
		
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);

		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, '-1' as c_right, '0' as c_review, '".($data['q_data']->c_title_true?$data['q_data']->c_title_true:JText::_('COM_QUIZ_SIMPLE_TRUE'))."' as title_true, '".($data['q_data']->c_title_false?$data['q_data']->c_title_false:JText::_('COM_QUIZ_SIMPLE_FALSE'))."' as title_false FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."'";
		if ($data['qrandom'])
			$query .=  "\n ORDER BY rand()";
		else
			$query .=  "\n ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		foreach($choice_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($choice_data[$t]->text, 'quiz_t_choice', 'c_choice', $choice_data[$t]->value);
		}

		$query = "SELECT c_choice_id FROM #__quiz_t_choice AS c, #__quiz_r_student_choice AS sc"
		. "\n WHERE c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$data['sid']."' AND "
		. "\n  c.c_question_id = '".$data['q_data']->c_id."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$answers = $database->loadColumn();

		if(is_array($answers) && count($answers))
		for($c = 0, $cn = count($choice_data); $c < $cn; $c++) {
			if (in_array($choice_data[$c]->value, $answers)) {
				$choice_data[$c]->c_right = 1;
			} else {
				$choice_data[$c]->c_right = 0;
			}
		}

		$qhtml = JoomlaQuiz_template_class::JQ_createQuestion($choice_data, $data);
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div style="width:100%;clear:both;" id="div_qoption'.$data['q_data']->c_id.'"><form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'>'. $qhtml .'</form></div>]]></quest_data_user>' . "\n";
		
		return $data;
	}
	
	public function onPointsForAnswer(&$data){
		$database = JFactory::getDBO();
		
		$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."' AND c_right = 1";
		$database->SetQuery( $query );
		$tmp_pointz = $database->LoadResult();
		if(floatval($tmp_pointz))
			$data['q_data']->c_point = $data['q_data']->c_point.' - '.(floatval($tmp_pointz) + $data['q_data']->c_point);
		
		return $data['q_data'];
	}
	
	public function onSaveQuestion(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT a.c_point, a.c_attempts FROM #__quiz_t_question as a WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		
		$query = "SELECT b.c_id, b.a_point FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$data['quest_id']."' AND b.c_question_id = a.c_id AND b.c_right = '1' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd2 = $database->LoadObjectList();
		
		$query = "SELECT b.c_id, b.a_point FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$data['quest_id']."' AND b.c_question_id = a.c_id AND b.c_right <> '1' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd3 = $database->LoadObjectList();
		
		$count_all = count($ddd2) + count($ddd3);
		$c_quest_score = 0;
		$c_dop_points = 0;
		$data['c_all_attempts'] = 1;
		$c_temp_cor = 0;
		$c_temp_incor = 0;
		$data['is_avail'] = 1;
		$ans_array = explode(',', $data['answer']);
		
		if (count($ddd) && (count($ddd2) || count($ddd3))) {
			$c_quest_score = $ddd[0]->c_point;
			$data['is_correct'] = 1;
			foreach ($ddd2 as $right_row) {
				if (!in_array($right_row->c_id, $ans_array)) {
					$c_temp_incor++;
					$c_quest_score = 0;
					$data['is_correct'] = 0;
				} else {
					$c_temp_cor++;
					$c_dop_points += $right_row->a_point;
					$data['got_one_correct'] = true;
				}
			}
			foreach ($ddd3 as $right_row) {
				if (in_array($right_row->c_id, $ans_array)) {
					$c_temp_incor++;
					$c_quest_score = 0;
					$data['is_correct'] = 0;
				} else {
					$c_temp_cor++;
					$c_dop_points += $right_row->a_point;
					$data['got_one_correct'] = true;
				}
			}
			if ($c_temp_cor < $count_all) {
				$c_quest_score = 0;
				$data['is_correct'] = 0;
			}

			//feauter  partial score
			if ($c_quest_score == 0) {
				$query = "SELECT `c_partial` FROM `#__quiz_t_question` WHERE `c_id` = '".$data['quest_id']."' AND published = 1";
				$database->SetQuery( $query );
				$c_partial = (int)$database->LoadResult();
				if ($c_partial) {
					$c_quest_score = round(($c_temp_cor/$count_all - $c_temp_incor/$count_all) * ($ddd[0]->c_point));
					$c_quest_score = ($c_quest_score>0) ? $c_quest_score : 0;
				}
			}
			if ($ddd[0]->c_attempts) {
				$data['c_all_attempts'] = $ddd[0]->c_attempts;
			}
		}
		$data['c_quest_cur_attempt'] = 0;
		$query = "SELECT c_id, c_attempts FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' and c_question_id = '".$data['quest_id']."'";
		$database->SetQuery( $query );
		$c_tmp = $database->LoadObjectList();
		
		if (count($c_tmp)) {
			$data['c_quest_cur_attempt'] = $c_tmp[0]->c_attempts;
			if ($data['c_quest_cur_attempt'] >= $data['c_all_attempts']) {
				$data['is_avail'] = 0;
				$data['is_no_attempts'] = 1;
			}
			if ($data['is_avail']) {
				$query = "DELETE FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' and c_question_id = '".$data['quest_id']."'";
				$database->SetQuery( $query );
				$database->execute();
				$query = "DELETE FROM #__quiz_r_student_choice WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
				$database->SetQuery( $query );
				$database->execute();
			}
		}
		if ($data['is_avail']) {
			$c_quest_score = ($c_quest_score+$c_dop_points);
			if ($data['c_quest_cur_attempt'] && $data['c_penalty']) {
				if (((100-$data['c_penalty']*$data['c_quest_cur_attempt'])/100) < 0)
					$c_quest_score = 0;								
				else 
					$c_quest_score = $c_quest_score * ((100-$data['c_penalty']*$data['c_quest_cur_attempt'])/100) ;
			}

			$query = "INSERT INTO #__quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, is_correct)"
			. "\n VALUES('".$data['stu_quiz_id']."', '".$data['quest_id']."', '".($c_quest_score)."', '".($data['c_quest_cur_attempt'] + 1)."', '".$data['is_correct']."')";
			$database->SetQuery($query);
			
			$database->execute();
			$c_sq_id = $database->insertid();
			$i = 0;
			while ($i < count($ans_array)) {
				$query = "INSERT INTO #__quiz_r_student_choice (c_sq_id, c_choice_id)"
				. "\n VALUES('".$c_sq_id."', '".$ans_array[$i]."')";
				$database->SetQuery($query);
				$database->execute();
				$i ++;
			}
		}
		
		$data['score'] = $c_quest_score;
		
		return true;
	}
	
	public function onTotalScore(&$data){
		
		$data['max_score'] = 0;
		$database = JFactory::getDBO();
		$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 10 AND c_id IN (".$data['qch_ids'].")";
		$database->SetQuery( $query );
		$qch_ids_type_10 = $database->loadColumn();

		if(count($qch_ids_type_10)) {
			$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id IN (".implode(',', $qch_ids_type_10).") AND c_right = 0";
			$database->SetQuery( $query );
			$data['max_score'] += $database->LoadResult();
		}
		
		return true;
	}
	
	public function onScoreByCategory(&$data){
		
		$database = JFactory::getDBO();
		$database->setQuery("SELECT SUM(a_point) FROM #__quiz_t_choice WHERE `c_question_id` = '".$data['score_bycat']->c_id."'");
		$data['score'] = $database->loadResult();
		
		return true;
	}
	
	public function onFeedbackQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$choice_data[0]->score = $data['score'];
		
		foreach($choice_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($choice_data[$t]->text, 'quiz_t_choice', 'c_choice', $choice_data[$t]->value);
		}
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();

		if($past_this) {
			$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
			$database->setQuery($query);
			$choice_this_one = $database->LoadResult();
			
			for($i=0;$i<count($choice_data);$i++)
			{
				$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
				$database->setQuery($query);
				$choice_this = $database->LoadResult();
				$temp_stat = round(($choice_this*100)/$choice_this_one);
				$choice_data[$i]->statistic = $data['q_data']->c_title_true.' '.$temp_stat.'%; '.$data['q_data']->c_title_false.' '.(100 - $temp_stat).'%';
			}
			$choice_data[0]->past_this = $past_this;
		}
		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );

		$sid = $database->loadResult( );

		$query = "SELECT * FROM #__quiz_t_choice AS c  LEFT JOIN #__quiz_r_student_choice AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$sid."'"
		. "\n WHERE c.c_question_id = '".$data['q_data']->c_id."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
		
		foreach($tmp as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_choice'], 'quiz_t_choice', 'c_choice', $tmp[$t]['c_choice_id']);
		}

		$uanswer = array();
		if(is_array($tmp))
		foreach($tmp as $t) {
			if($t['c_choice_id']) {
				$uanswer[] = $t['c_choice_id'];
			} 
		}
		$choice_data[0]->c_title_true = $data['q_data']->c_title_true;
		$choice_data[0]->c_title_false = $data['q_data']->c_title_false;
		
		$feedback_data = array();
		$feedback_data['choice_data'] = $choice_data;
		$feedback_data['user_answer'] = $uanswer;
		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($feedback_data, $data);

		if(preg_match('/pretty_green/', $data['cur_template'])){
			$data['qoption'] = "\t" . $qhtml. "\n";
		} else {
			$data['qoption'] = "\t" . '<div><form  onsubmit=\'javascript: return false;\' name=\'quest_form\'>'. $qhtml .'</form></div>' . "\n";
		}
		return $data['qoption'];
	}
	
	public function onNextPreviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT a.c_point, a.c_attempts FROM #__quiz_t_question as a WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		
		$query = "SELECT b.c_id FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$data['quest_id']."' AND b.c_question_id = a.c_id AND b.c_right = '1' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd2 = $database->LoadObjectList();
		
		$query = "SELECT b.c_id FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$data['quest_id']."' AND b.c_question_id = a.c_id AND b.c_right <> '1' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd3 = $database->LoadObjectList();
		
		$ans_array = explode(',',$data['answer']);
		if ((count($ddd2) || count($ddd3)) && count($ddd)) {
			$data['is_correct'] = 1;
			foreach ($ddd2 as $right_row) {
				if (!in_array($right_row->c_id, $ans_array)) { $data['is_correct'] = 0; }
			}
			foreach ($ddd3 as $not_right_row) {
				if (in_array($not_right_row->c_id, $ans_array)) { $data['is_correct'] = 0; }
			}
		}
		
		return $data;
	}
	
	public function onReviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$choice_data[0]->title_true = $data['q_data']->c_title_true;
		$choice_data[0]->title_false = $data['q_data']->c_title_false;
		
		foreach($choice_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($choice_data[$t]->text, 'quiz_t_choice', 'c_choice', $choice_data[$t]->value);
		}

		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();

		$choice_data[0]->overal = '';
		if($past_this) {
			$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
			$database->setQuery($query);
			$choice_this_one = $database->LoadResult();
				
			for($i=0;$i<count($choice_data);$i++) {					
				$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
				$database->setQuery($query);
				$choice_this = $database->LoadResult();
				$temp_stat = round(($choice_this*100)/$choice_this_one);
				
				$choice_data[$i]->statistic_true = ' '.$temp_stat.'%';
				$choice_data[$i]->statistic_false = ' '.(100 - $temp_stat).'%';
			}
			$choice_data[0]->overal = JText::_('COM_QUIZ_RST_PANSW')." ".$past_this." ".JText::_('COM_QUIZ_RST_PANSW_TIMES');
		}

		$query = "SELECT * FROM #__quiz_t_choice AS c LEFT JOIN #__quiz_r_student_choice AS sc"
			. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$data['sid']."'"
			. "\n WHERE c.c_question_id = '".$data['q_data']->c_id."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();

		foreach($tmp as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_choice'], 'quiz_t_choice', 'c_choice', $tmp[$t]['c_choice_id']);
		}

		$answer = '';
		if(is_array($tmp))
		foreach($tmp as $t) {
			if($t['c_choice_id']) {
				$answer .= $t['c_choice']." - ".$data['q_data']->c_title_true."; ";
			} else  {
				$answer .= $t['c_choice']." - ".$data['q_data']->c_title_false."; ";
			}
		}
		
		$choice_data[0]->answer = $answer;

		$qhtml = JoomlaQuiz_template_class::JQ_createReview($choice_data, $data);
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>'. $qhtml .'</form></div>]]></quest_data_user>' . "\n";
		return $data;		
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT *, c.c_id AS id FROM (#__quiz_t_choice AS c, #__quiz_t_question AS q) LEFT JOIN #__quiz_r_student_choice AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$data['id']."'"
		. "\n WHERE c.c_question_id = '".$data['qid']."' AND q.c_id = c.c_question_id ORDER BY c.ordering";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
			
		foreach($tmp as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_choice'], 'quiz_t_choice', 'c_choice', $tmp[$t]['id']);
		}
		$data['info']['c_choice'] = $tmp;
			
		$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id = ".$data['qid']." ";
		$database->SetQuery( $query );
		$data['info']['c_point'] += $database->LoadResult();
		
		return true;
	}
	
	public function onGetPdf(&$data){

		//$data['pdf']->SetFont('freesans');
		$fontFamily = $data['pdf']->getFontFamily();
		
		$data['pdf']->Ln();
		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', true);
		$str = "  ".JText::_('COM_QUIZ_PDF_ANSWER');
		$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);

		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', false);
		$data['pdf']->Ln();
				
		for($j=0,$k='A';$j < count($data['data']['c_choice']);$j++,$k++) {
			if($data['data']['c_choice'][$j]['c_choice_id']) {
				$data['answer'] .= $k." ";
			}
					
			if ($data['data']['c_choice'][$j]['c_right']) {
				$correct_answer .= $k." ";
			}
					
			$data['pdf']->Ln();
			$data['pdf']->setFont($fontFamily);
			//$data['pdf']->setStyle('b', true);
			$str = "  $k.";
			$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);

			$data['pdf']->setFont($fontFamily);
			//$data['pdf']->setStyle('b', false);
			$str = $data['data']['c_choice'][$j]['c_choice'] . ' - ' . ($data['data']['c_choice'][$j]['c_choice_id']? $data['data']['c_choice'][$j]['c_title_true']: $data['data']['c_choice'][$j]['c_title_false']);
			$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
		}
				
		return $data['pdf'];		
	}
	
	public function onSendEmail(&$data){
	
		$data['str'] .= "  ".JText::_('COM_QUIZ_PDF_ANSWER')." \n";
		for($j=0,$k='A';$j < count($data['data']['c_choice']);$j++,$k++) {

			$data['str'] .= "$k. ".$data['data']['c_choice'][$j]['c_choice']. ' - '. ($data['data']['c_choice'][$j]['c_choice_id']? $data['data']['c_choice'][$j]['c_title_true']: $data['data']['c_choice'][$j]['c_title_false']) ."\n";
		}
		$data['str'] .= "<hr />";
		return $data['str'];
	}
	
	public function onGetStatistic(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		$past_this += 0.0000000000001;
		$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
		$database->setQuery($query);
		$choice_this_one = $database->LoadResult();
		
		for($i=0;$i<count($choice_data);$i++)
		{
			$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
			$database->setQuery($query);
			$choice_this = $database->LoadResult();
			$temp_stat = round(($choice_this*100)/$past_this);
			
			$choice_data[$i]->statistic1 = $temp_stat.'%';
			$choice_data[$i]->statistic2 = (intval($past_this)?(100 - $temp_stat):0).'%';
			$choice_data[$i]->count = (int)$past_this;
		}		
		$data['question']->choice_data = $choice_data;		
		return $data['question'];	
	}

	public function onStatisticContent(&$data){
		
		$data['question']->c_title_true = $data['question']->c_title_true? $data['question']->c_title_true: JText::_('COM_QUIZ_SIMPLE_TRUE');
		$data['question']->c_title_false = $data['question']->c_title_false? $data['question']->c_title_false: JText::_('COM_QUIZ_SIMPLE_FALSE');
		if (isset($data['question']->choice_data) && is_array($data['question']->choice_data))
		foreach($data['question']->choice_data as $cdata){
			?>
			<tr>
				<td><?php echo $cdata->text?></td>
				<td align="center"><?php echo $cdata->count?></td>
				<td><?php echo ($cdata->c_right? '<font color="#00CC00">'.$data['question']->c_title_true.'</font>':$data['question']->c_title_true).' - '.$cdata->statistic1?><br />
					<?php echo (!$cdata->c_right? '<font color="#00CC00">'.$data['question']->c_title_false.'</font>':$data['question']->c_title_false).' - '.$cdata->statistic2?>
				</td>
				<td><div style="width:100%; border:1px solid #cccccc;margin-bottom:3px;"><div style="height: 5px; width: <?php echo $cdata->statistic1+1;?>%; " class="jq_color_1"></div></div>
					<div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $cdata->statistic2+1;?>%;" class="jq_color_2"></div></div>
				</td>
			</tr>
			<?php
		}
		
	}

	//Administration part
	
	public function onGetAdminOptions($data)
	{
		$settings = JoomlaquizHelper::getSettings();
		$q_om_type = 10;
		$wysiwyg = (isset($settings->wysiwyg_options)) ? $settings->wysiwyg_options : 0;
		
		$db = JFactory::getDBO();
		$choices = array();
		$return = array();
		if($data['question_id']){
			$query = "SELECT * FROM #__quiz_t_choice WHERE c_question_id = '".$data['question_id']."' ORDER BY ordering";
			$db->SetQuery( $query );
			$choices = array();
			$choices = $db->LoadObjectList();
		}
		
		$return['choices'] = $choices;
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/mquestion/admin/options/mquestion.php");
		$options = ob_get_contents();
		ob_get_clean();
		
		return $options;
	}
	
	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');
		
		$db->setQuery("SELECT `c_random`, `c_partial`, `c_qform`, `c_title_true`, `c_title_false` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
		$row = $db->loadObject();
		
		$lists = array();
		$c_qform = array();
		$c_qform[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_RADIO_BUTTONS'));
		$c_qform[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_DROP_DOWN'));
		$c_qform = JHTML::_('select.genericlist', $c_qform, 'jform[c_qform]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_qform) ? intval( $row->c_qform ) : 0)); 
		$lists['c_qform']['input'] = $c_qform;
		$lists['c_qform']['label'] = JText::_('COM_JOOMLAQUIZ_DISPLAY_STYLE');
		
		$c_partial = array();
		$c_partial[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
		$c_partial[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
		$c_partial = JHTML::_('select.genericlist', $c_partial, 'jform[c_partial]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_partial) ? intval( $row->c_partial ) : 0)); 
		$lists['c_partial']['input'] = $c_partial;
		$lists['c_partial']['label'] = JText::_('COM_JOOMLAQUIZ_PARTIAL_SCORE');
		
		$c_random = array();
		$c_random[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
		$c_random[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
		$c_random = JHTML::_('select.genericlist', $c_random, 'jform[c_random]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_random) ? intval( $row->c_random ) : 0));
		$lists['c_random']['input'] = $c_random;
		$lists['c_random']['label'] = JText::_('COM_JOOMLAQUIZ_RANDOMIZE_ANSWERS');
		
		$c_title_true = (isset($row->c_title_true)) ? $row->c_title_true : '';
		$lists['c_title_true']['input'] = "<input type='text' size='30' name='c_title_true' value='".$c_title_true."' />";
		$lists['c_title_true']['label'] = JText::_('COM_JOOMLAQUIZ_TITLE_FOR_TRUE');
		
		$c_title_false = (isset($row->c_title_false)) ? $row->c_title_false : '';
		$lists['c_title_false']['input'] = "<input type='text' size='30' name='c_title_false' value='".$c_title_false."' />";
		$lists['c_title_false']['label'] = JText::_('COM_JOOMLAQUIZ_TITLE_FOR_FALSE');
		
		return $lists;
	}
	
	public function onGetAdminJavaScript(&$data){
		
		$settings = JoomlaquizHelper::getSettings();
		$q_om_type = 10;
		$wysiwyg = (isset($settings->wysiwyg_options)) ? $settings->wysiwyg_options : 0;
		
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/mquestion/admin/js/mquestion.js.php");
		$script = ob_get_contents();
		ob_get_clean();
		
		return $script;
	}
	
	public function onAdminIsFeedback(&$data){
		return true;
	}
	
	public function onAdminIsPoints(&$data){
		return true;
	}
	
	public function onAdminIsPenalty(&$data){
		return true;
	}
	
	public function onAdminIsReportName(){
		return true;
	}
	
	public function onAdminSaveOptions(&$data){
		
		$database = JFactory::getDBO();
		
		$database->setQuery("UPDATE #__quiz_t_question SET `c_qform` = '".$_POST['jform']['c_qform']."', `c_partial` = '".$_POST['jform']['c_partial']."', `c_random` = '".$_POST['jform']['c_random']."', `c_title_true` = '".$_POST['c_title_true']."', `c_title_false` = '".$_POST['c_title_false']."' WHERE c_id = '".$data['qid']."'");
		$database->execute();
		
		$field_order = 0;
		$ans_right = array();
		if (isset($_REQUEST['jq_checked'])) {
			foreach ($_REQUEST['jq_checked'] as $sss) {
				$ans_right[] = $sss;
			}
		}
		else
		$msg .= JText::_('COM_JOOMLAQUIZ_QUESTION_NOT_COMPLETE');
		if (isset($_POST['jq_hid_fields'])) {
			$mcounter = 0;
			$fids_arr = array();
			
			foreach ($_POST['jq_hid_fields'] as $f_row) {
					
					$new_field = new stdClass;
					if(intval($_POST['jq_hid_fields_ids'][$mcounter]))
						$new_field->c_id = intval($_POST['jq_hid_fields_ids'][$mcounter]);
						
					$new_field->c_question_id = $data['qid'];
					$new_field->c_choice = stripslashes($f_row);
					$new_field->c_incorrect_feed = stripslashes($_POST['jq_incorrect_feed'][$mcounter]);
					$new_field->c_right = $_POST['jq_checked'][$mcounter];
					
					$new_field->ordering = $field_order;
					$new_field->a_point = floatval($_POST['jq_a_points'][$mcounter]);
					$new_field->c_quiz_id	= intval($_POST['jform']['c_quiz_id']);
					$database->setQuery("SELECT COUNT(c_id) FROM #__quiz_t_choice WHERE c_id = '".$new_field->c_id."'");
					$exists = $database->loadResult();
					if($exists){
						$database->updateObject('#__quiz_t_choice', $new_field, 'c_id');
					} else {
						$database->insertObject('#__quiz_t_choice', $new_field);
						$new_field->c_id = $database->insertid();
					}
					$fids_arr[] = $new_field->c_id;					
					$field_order ++ ;
					$mcounter ++ ;
			}
			$fieldss = implode(',',$fids_arr);
			$query = "DELETE FROM #__quiz_t_choice WHERE c_question_id = '".$data['qid']."' AND c_id NOT IN (".$fieldss.")";
			$database->setQuery( $query );
			$database->execute();
			
		}
		else
		{
			$msg .= JText::_('COM_JOOMLAQUIZ_QUESTION_NOT_COMPLETE2');
			$query = "DELETE FROM #__quiz_t_choice WHERE c_question_id = '".$data['qid']."'";
			$database->setQuery( $query );
			$database->query();
		}
	}
	
	public function onGetAdminAddLists(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT c.*, sc.c_id as sc_id FROM #__quiz_t_choice as c LEFT JOIN #__quiz_r_student_choice as sc ON c.c_id = sc.c_choice_id"
		. "\n and sc.c_sq_id = '".$data['id']."'"
		. "\n WHERE c.c_question_id = '".$data['q_id']."'"
		. "\n ORDER BY c.ordering, c.c_id"
		;
		$database->SetQuery( $query );
		$answer = $database->LoadObjectList();
		
		$lists['answer'] = $answer;
		$lists['id'] = $data['id'];
				
		return $lists;
		
	}
	
	public function onGetAdminReportsHTML(&$data){
		$rows = $data['lists']['answer'];
		
		ob_start();
		?>
		<table class="table table-striped">
					<tr>
						<th class="title" width="100"><?php echo JText::_('COM_JOOMLAQUIZ_USER_CHOICE');?></th>
						<th class="title" width="100"><?php echo JText::_('COM_JOOMLAQUIZ_RIGHT_ANSWER');?></th>
						<th class="title"><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_OPTIONS');?></th>
					</tr>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="left" nowrap>
								<input type="radio" disabled <?php echo ($row->sc_id ? 'checked' : ''); ?>/><?php echo $data['lists']['title_true']; ?><br />
								<input type="radio" disabled <?php echo (!$row->sc_id ? 'checked' : ''); ?>/><?php echo $data['lists']['title_false']; ?>
							</td>
							<td align="left" nowrap>
								<input type="radio" disabled <?php echo ($row->c_right ? 'checked' : ''); ?>/><?php echo $data['lists']['title_true']; ?><br />
								<input type="radio" disabled <?php echo (!$row->c_right ? 'checked' : ''); ?>/><?php echo $data['lists']['title_false']; ?>
							</td>
							<td align="left">
								<?php echo $row->c_choice; ?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</table>
		<?php
		
		$content = ob_get_contents();
		ob_clean();
		return $content;
	}
	
	public function onGetAdminQuestionData(&$data){
	
		$database = JFactory::getDBO();
		
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
				
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		$past_this += 0.0000000000001;
		$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
		$database->setQuery($query);
		$choice_this_one = $database->LoadResult();
					
		for($i=0;$i<count($choice_data);$i++)
		{
			$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
			$database->setQuery($query);
			$choice_this = $database->LoadResult();
			$temp_stat = round(($choice_this*100)/$past_this);
						
			$choice_data[$i]->statistic1 = $temp_stat.'%';
			$choice_data[$i]->statistic2 = (intval($past_this)?(100 - $temp_stat):0).'%';
			$choice_data[$i]->count = (int)$past_this;
		}
			
		$data['question']->choice_data = $choice_data;
				
		return $data['question'];	
	}
	
	public function onGetAdminStatistic(&$data){
		$data['question']->c_title_true = $data['question']->c_title_true? $data['question']->c_title_true: 'True';
		$data['question']->c_title_false = $data['question']->c_title_false? $data['question']->c_title_false: 'False';
		if (is_array($data['question']->choice_data))
			foreach($data['question']->choice_data as $cdata){
				?>
				<tr>
					<td><?php echo $cdata->text?></td> 
					<td><?php echo $cdata->count?></td>
					<td><?php echo ($cdata->c_right? '<font color="#00CC00">'.$data['question']->c_title_true.'</font>':$data['question']->c_title_true).' - '.$cdata->statistic1?><br />
						<?php echo (!$cdata->c_right? '<font color="#00CC00">'.$data['question']->c_title_false.'</font>':$data['question']->c_title_false).' - '.$cdata->statistic2?>
					</td>
					<td><div style="width:100%; border:1px solid #cccccc;margin-bottom:3px;"><div style="height: 5px; width: <?php echo $cdata->statistic1+1;?>%; " class="jq_color_1"></div></div>
						<div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $cdata->statistic2+1;?>%;" class="jq_color_2"></div></div>
					</td>
				</tr>
				<?php												
			}
	}
	
	public function onGetAdminCsvData(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT `a`.`c_score` FROM `#__quiz_r_student_question` AS `a` WHERE `a`.`c_stu_quiz_id` = '".$data['result']->c_id."' AND `a`.`c_question_id` = '".$data['question']->c_id."'";
		$database->setQuery( $query );
		$score = $database->loadResult();
		if ($score != null)
			$data['answer'] = 'Score - '.$score;
		
		return $data['answer'];	
	}
}