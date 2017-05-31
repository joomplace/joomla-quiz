<?php
/**
* JoomlaQuiz Matching Drag&Drop questions Plugin for Joomla
* @version $Id: choice.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage choice.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizDragdrop extends plgJoomlaquizQuestion
{
	var $name		= 'Dragdrop';
	var $_name		= 'dragdrop';
	
	public function onCreateQuestion(&$data) {
		
		$database = JFactory::getDBO();
		$query = "SELECT *, c_right_text as c_val FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."'";
						
		$query .=  "\n ORDER BY ordering";
		$database->SetQuery( $query );
		$match_data = $database->LoadObjectList();
		foreach($match_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($match_data[$t]->c_val, 'quiz_t_matching', 'c_right_text', $match_data[$t]->c_id);
			JoomlaquizHelper::JQ_GetJoomFish($match_data[$t]->c_right_text, 'quiz_t_matching', 'c_right_text', $match_data[$t]->c_id);
			JoomlaquizHelper::JQ_GetJoomFish($match_data[$t]->c_left_text, 'quiz_t_matching', 'c_left_text', $match_data[$t]->c_id);
		}
						
		$shuffle_match = $match_data;
		if ($data['qrandom']) shuffle($shuffle_match);
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div id="div_qoption'.$data['q_data']->c_id.'">';
		$data['ret_add_script'] = 'var is_dragable'.$data['q_data']->c_id.' = true;'
		. 'kol_drag_elems'.$data['q_data']->c_id.' = '.count( $match_data ).';'."\n"
		. 'drag_array'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n".' coord_left'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n".' coord_top'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n"
		. 'ids_in_cont'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n".'cont_for_ids'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n".' answ_ids'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.'); '."\n"
		. 'cont_index = 0;'."\n".' last_drag_id = 0;'."\n".' last_drag_id_drag = 0; '."\n";
		$data['ret_str'] .= ''
		. '<div style=\'width:100%;text-align:center;position:relative;\'><table id=\'quest_table\' align=\'center\' style=\'position:relative;\'>';
						
		$query = "SELECT c_matching_id, c_sel_text FROM #__quiz_t_matching AS m LEFT JOIN #__quiz_r_student_matching AS sm"
		. "\n ON m.c_id = sm.c_matching_id AND sm.c_sq_id = '".$data['sid']."' WHERE m.c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$answers = $database->LoadObjectList();
		$ret_answer  = '';
		$ans_count = 0;
		for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
			$data['ret_str'] .= '<tr><td align=\'center\'><div id=\'cdiv'.$data['q_data']->c_id.'_'.($i+1).'\' class=\'jq_cont_drag_div\'>'
			. stripslashes($match_data[$i]->c_left_text) . '</div></td><td><div id=\'ddiv'.$data['q_data']->c_id.'_'.($i+1).'\'  class=\'jq_draggable_div\' style=\'top:0px;left:0px;\'>'
			. stripslashes($shuffle_match[$i]->c_right_text) . '</div></td></tr>';
							
			for ($j=0, $m = count( $match_data ); $j < $m; $j++ ) {
				if ($answers[$j]->c_matching_id == $match_data[$i]->c_id) {								
					$found = false;
					for($ii=0, $nn = count($shuffle_match); $ii<$nn; $ii++) {
						if (md5(intval($shuffle_match[$ii]->c_id).'answer') == $answers[$j]->c_sel_text) {										
							$ret_answer .= '<rfield_id>'.($ii+1).'</rfield_id>';
							$found = true;
							break;
						 }
					}
					if ($found) {
						$ret_answer .= '<lfield_id>'.($i+1).'</lfield_id>';
						$ans_count++;
					}
					break;
				}
			}
		}
		$data['ret_str'] .= '</table></div>' . "\n" ;
		for ($i=0, $n = count( $shuffle_match ); $i < $n; $i++ ) {
			$data['ret_add_script'] .= 'answ_ids'.$data['q_data']->c_id.'['.($i + 1).'] = \''.md5(intval($shuffle_match[$i]->c_id).'answer').'\'; '."\n";
		}			
						
		$data['ret_str'] .= '<form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'></form></div>]]></quest_data_user>' . "\n";
		$data['ret_str'] .= '<ans_count>'.$ans_count.'</ans_count>'.$ret_answer;
		
		return $data;
	}
	
	public function onSaveQuestion(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT a.c_point, a.c_attempts FROM #__quiz_t_question as a WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		
		$query = "SELECT b.c_id, b.c_left_text, b.c_right_text, b.a_points FROM #__quiz_t_question as a, #__quiz_t_matching as b WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1 and b.c_question_id = a.c_id ORDER BY b.ordering";
		$database->SetQuery( $query );
		$ddd2 = $database->LoadObjectList();
		
		$c_quest_score = 0;
		$data['c_all_attempts'] = 1;
		$data['is_avail'] = 1;
		$answer = urldecode($data['answer']);
		$ans_array = explode('```',$answer);
		if (count($ddd2) && count($ddd)) {							
			$data['is_correct'] = 1; 
			$rr_num = 0;
			foreach ($ddd2 as $right_row) {	
				if (md5(intval($right_row->c_id).'answer') != $ans_array[$rr_num]) {				
					$data['is_correct'] = 0;
				} else {
					$c_quest_score += $right_row->a_points;
				}
				$rr_num ++;
			}
			if ($data['is_correct'])
				$c_quest_score += $ddd[0]->c_point;
				
			if ($ddd[0]->c_attempts) {
				$data['c_all_attempts'] = $ddd[0]->c_attempts; }
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
				$query = "DELETE FROM #__quiz_r_student_matching WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
				$database->SetQuery( $query );
				$database->execute();
			}
		}
		if ($data['is_avail']) {
			if ($data['c_quest_cur_attempt'] && $data['c_penalty']) {
				if (((100-$data['c_penalty']*$data['c_quest_cur_attempt'])/100) < 0)
					$c_quest_score = 0;								
				else 
					$c_quest_score = $c_quest_score * ((100-$data['c_penalty']*$data['c_quest_cur_attempt'])/100) ;
			}

			$query = "INSERT INTO #__quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, is_correct)"
			. "\n VALUES('".$data['stu_quiz_id']."', '".$data['quest_id']."', '".$c_quest_score."', '".($data['c_quest_cur_attempt'] + 1)."', '".$data['is_correct']."')";
			$database->SetQuery($query);
			$database->execute();
			$c_sq_id = $database->insertid();
			$i = 0;
			while ($i < count($ddd2)) {
				$query = "INSERT INTO #__quiz_r_student_matching (c_sq_id, c_matching_id, c_sel_text)"
				. "\n VALUES('".$c_sq_id."', '".$ddd2[$i]->c_id."', '".$ans_array[$i]."')";
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
		$query = "SELECT c_id FROM #__quiz_t_question WHERE (c_type = 4 OR c_type = 5) AND c_id IN (".$data['qch_ids'].") AND published = 1";
		$database->SetQuery( $query );
		$qch_ids_type_45 = $database->loadColumn();
		
		if(count($qch_ids_type_45)) {
			$query = "SELECT SUM(a_points) FROM #__quiz_t_matching WHERE c_question_id IN (".implode(',', $qch_ids_type_45).")";
			$database->SetQuery( $query );
			$data['max_score'] += $database->LoadResult();
		}
		return $data['max_score'];
	}
	
	public function onScoreByCategory(&$data){
		
		$database = JFactory::getDBO();
		$database->setQuery("SELECT SUM(a_points) FROM #__quiz_t_matching WHERE `c_question_id` = '".$data['score_bycat']->c_id."'");
		$data['score'] = $database->loadResult();
		return $data['score'];
	}
	
	public function onFeedbackQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT *, c_right_text as c_val FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."'"
		. "\n ORDER BY ordering";
		$database->SetQuery( $query );				
		$match_data = $database->LoadObjectList();
		foreach($match_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($match_data[$t]->c_val, 'quiz_t_matching', 'c_right_text', $match_data[$t]->c_id);
			JoomlaquizHelper::JQ_GetJoomFish($match_data[$t]->c_right_text, 'quiz_t_matching', 'c_right_text', $match_data[$t]->c_id);
			JoomlaquizHelper::JQ_GetJoomFish($match_data[$t]->c_left_text, 'quiz_t_matching', 'c_left_text', $match_data[$t]->c_id);
		}
		
		$shuffle_match = $match_data;
		
		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$sid = $database->loadResult( );			

		$query = "SELECT *, m.c_id AS id FROM #__quiz_t_matching AS m LEFT JOIN #__quiz_r_student_matching AS sm"
		. "\n ON m.c_id = sm.c_matching_id AND sm.c_sq_id = '".$sid."' WHERE m.c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$qdata = $database->LoadAssocList();
		
		$qdata[0]['score'] = $data['score'];
		
		foreach($qdata as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($qdata[$t]['c_right_text'], 'quiz_t_matching', 'c_right_text', $qdata[$t]['id']);
			JoomlaquizHelper::JQ_GetJoomFish($qdata[$t]['c_left_text'], 'quiz_t_matching', 'c_left_text', $qdata[$t]['id']);
		}
			
		$query = "SELECT c_id, c_right_text FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$tmp2 = $database->LoadObjectList();
		foreach($tmp2 as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp2[$t]->c_right_text, 'quiz_t_matching', 'c_right_text', $tmp2[$t]->c_id);
		}
		for($i=0, $n=count($qdata); $i<$n; $i++) {
			foreach($tmp2 as $t2) {				
				if (md5(intval($t2->c_id).'answer') == $qdata[$i]['c_sel_text']) {
					$qdata[$i]['c_sel_text']= $t2->c_right_text;
				}
			}			
		}		

		$query = "SELECT count(*)"
		. "\n FROM #__quiz_r_student_question as sp LEFT JOIN #__quiz_t_question as q ON (sp.c_question_id = q.c_id AND q.published = 1) WHERE q.c_id='".$data['q_data']->c_id."'";
		
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		if($past_this) {
			$query = "SELECT count(*)"
				. "\n FROM #__quiz_r_student_question as sp LEFT JOIN #__quiz_t_question as q ON (sp.c_question_id = q.c_id AND q.published = 1) WHERE q.c_id='".$data['q_data']->c_id."' AND sp.c_score = q.c_point";
				
			$database->setQuery($query);
			$right_this = $database->LoadResult();
			$qdata[0]['past_this'] = $past_this;
			$qdata[0]['rht_proc'] = round($right_this*100/$past_this);
		}
		$feedback_data = array();
		$feedback_data['qdata'] = $qdata;
		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($feedback_data, $data);
		
		if(preg_match('/pretty_green/', $data['cur_template'])){
			$data['qoption'] = "\t" . $qhtml. "\n";
		} else {
			$data['qoption'] = '<div><form  onsubmit=\'javascript: return false;\' name=\'quest_form\'>'.$qhtml.'</form></div>' . "\n";
		}
		return $data['qoption'];
	}
	
	public function onNextPreviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT a.c_point, a.c_attempts FROM #__quiz_t_question as a WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		
		$query = "SELECT b.c_id, b.c_left_text, b.c_right_text FROM #__quiz_t_question as a, #__quiz_t_matching as b WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1 and b.c_question_id = a.c_id ORDER BY b.ordering";
		$database->SetQuery( $query );
		$ddd2 = $database->LoadObjectList(); 
		$answer = urldecode($data['answer']);
		
		$ans_array = explode('```', $answer);
		if (count($ddd2) && count($ddd)) {
			$is_correct = 1; $rr_num = 0;
			foreach ($ddd2 as $right_row) {
				if (md5(intval($right_row->c_id).'answer') != $ans_array[$rr_num]) {
					$is_correct = 0; 
				}
				$rr_num ++;
			}
		}
		
		return $data;
	}
	
	public function onReviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT *, c_right_text as c_val FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."'"
		. "\n ORDER BY ordering";
		$database->SetQuery( $query );
		$match_data = $database->LoadObjectList();
		
		foreach($match_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($match_data[$t]->c_val, 'quiz_t_matching', 'c_right_text', $match_data[$t]->c_id);
			JoomlaquizHelper::JQ_GetJoomFish($match_data[$t]->c_right_text, 'quiz_t_matching', 'c_right_text', $match_data[$t]->c_id);
			JoomlaquizHelper::JQ_GetJoomFish($match_data[$t]->c_left_text, 'quiz_t_matching', 'c_left_text', $match_data[$t]->c_id);
		} 
		
		$shuffle_match = $match_data;
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div>' . "\n"
		. '<div style=\'width:100%;text-align:center\'><table class=\'jq_mchoice_overal\' id=\'quest_table\' align="center">';
		for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
			$data['ret_str'] .= '<tr><td><div id=\'cdiv_'.($i+1).'\' class=\'jq_cont_drag_div\'>'
			. stripslashes($match_data[$i]->c_left_text) . '</div></td><td><div id=\'ddiv_'.($i+1).'\' class=\'jq_draggable_stop_div\'>'
			. stripslashes($shuffle_match[$i]->c_right_text) . '</div></td></tr>';
		}
		
		$query = "SELECT count(*)"
		. "\n FROM #__quiz_r_student_question as sp LEFT JOIN #__quiz_t_question as q ON (sp.c_question_id = q.c_id AND q.published = 1) WHERE q.c_id='".$data['q_data']->c_id."'";
		
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		if($past_this)
		{
			$query = "SELECT count(*)"
				. "\n FROM #__quiz_r_student_question as sp LEFT JOIN #__quiz_t_question as q ON (sp.c_question_id = q.c_id AND q.published = 1) WHERE q.c_id='".$data['q_data']->c_id."' AND sp.is_correct = 1";
				
			$database->setQuery($query);
			$right_this = $database->LoadResult();
			$rht_proc = round($right_this*100/$past_this);
		}
		
		$query = "SELECT *, m.c_id AS id FROM #__quiz_t_matching AS m LEFT JOIN #__quiz_r_student_matching AS sm"
		. "\n ON m.c_id = sm.c_matching_id AND sm.c_sq_id = '".$data['sid']."' WHERE m.c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
		
		foreach($tmp as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_right_text'], 'quiz_t_matching', 'c_right_text', $tmp[$t]['id']);
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_left_text'], 'quiz_t_matching', 'c_left_text', $tmp[$t]['id']);
		}
		
		$query = "SELECT c_id, c_right_text FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$tmp2 = $database->LoadObjectList();
		foreach($tmp2 as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp2[$t]->c_right_text, 'quiz_t_matching', 'c_right_text', $tmp2[$t]->c_id);
		} 

		for($i=0, $n=count($tmp); $i<$n; $i++) {
			foreach($tmp2 as $t2) {				
				if (md5(intval($t2->c_id).'answer') == $tmp[$i]['c_sel_text']) {
					$tmp[$i]['c_sel_text']= $t2->c_right_text;
				}
			}			
		}		

		$answer = '<br/>';		
		$answer .= '<table width="100%" id=\'quest_table\'><tr class="sectiontableheader"><td colspan="3"><strong>'.JText::_('COM_QUIZ_ANSWER').'</strong></td></tr>';
		if(is_array($tmp))
		foreach($tmp as $t) {
			$answer .= '<tr class="sectiontableentry1"><td style="padding-left: 10px;">'
			. stripslashes($t['c_left_text']) . '</td><td style="padding-left: 10px;">'
			. stripslashes($t['c_sel_text']) . '</td><td width="40%"></td></tr>';						
		}				
		$answer .= '</table>';				
		
		$data['ret_add_script'] = 'var is_dragable'.$data['q_data']->c_id.' = true;'
		. 'kol_drag_elems'.$data['q_data']->c_id.' = '.count( $match_data ).';'."\n"
		. 'drag_array'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n".' coord_left'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n".' coord_top'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n"
		. 'ids_in_cont'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n".'cont_for_ids'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.');'."\n".' answ_ids'.$data['q_data']->c_id.' = new Array(kol_drag_elems'.$data['q_data']->c_id.'); '."\n"
		. 'cont_index = 0;'."\n".' last_drag_id = 0;'."\n".' last_drag_id_drag = 0; '."\n";
		$data['ret_str'] .= '</table><table width="100%" align="left"><tr><td colspan="2" class="review_statistic">'.JText::_('COM_QUIZ_RST_PPAST').' '.$past_this.' '.JText::_('COM_QUIZ_RST_PPAST_TIMES').', '.$rht_proc.'% '.JText::_('COM_QUIZ_RST_ARIGHT').$answer.'</td></tr>';
		$data['ret_str'] .= '</table></div>' . "\n";
		$data['ret_str'] .= '<form onsubmit=\'javascript: return false;\' name=\'quest_form\'></form></div>]]></quest_data_user>' . "\n";
		return $data;		
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT *, m.c_id AS id FROM #__quiz_t_matching AS m LEFT JOIN #__quiz_r_student_matching AS sm"
		. "\n ON m.c_id = sm.c_matching_id AND sm.c_sq_id = '".$data['id']."' WHERE m.c_question_id = '".$data['qid']."'";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
		foreach($tmp as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_sel_text'], 'quiz_t_matching', 'c_right_text', $tmp[$t]['id']);
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_right_text'], 'quiz_t_matching', 'c_right_text', $tmp[$t]['id']);
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_left_text'], 'quiz_t_matching', 'c_left_text', $tmp[$t]['id']);
		}
			
		$query = "SELECT c_id, c_right_text FROM #__quiz_t_matching WHERE c_question_id = '".$data['qid']."'";
		$database->SetQuery( $query );
		$tmp2 = $database->LoadObjectList();
		foreach($tmp2 as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp2[$t]->c_right_text, 'quiz_t_matching', 'c_right_text', $tmp2[$t]->c_id);
		}
			
		for($i=0, $n=count($tmp); $i<$n; $i++) {
			foreach($tmp2 as $t2) {				
				if (md5(intval($t2->c_id).'answer') == $tmp[$i]['c_sel_text']) {
					$tmp[$i]['c_sel_text']= $t2->c_right_text;
				}
			}			
		}		
		$data['info']['c_matching'] = $tmp;
		
		$query = "SELECT SUM(`a_points`) FROM `#__quiz_t_matching` WHERE `c_question_id` = ".$data['qid'];
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
				
		for($j=0,$k='A';$j < count($data['data']['c_matching']);$j++,$k++) {			
			$data['pdf']->Ln();
			$data['pdf']->setFont($fontFamily);
			//$data['pdf']->setStyle('b', true);
			$str = "  $k.";
			$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);

			$data['pdf']->setFont($fontFamily);
			//$data['pdf']->setStyle('b', false);
			$str = $data['data']['c_matching'][$j]['c_left_text']." - ".$data['data']['c_matching'][$j]['c_sel_text']."";				
			$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
		}
		$data['pdf']->Ln();
				
		return $data['pdf'];		
	}
	
	public function onSendEmail(&$data){
	
		$data['str'] .= "  ".JText::_('COM_QUIZ_PDF_ANSWER')." \n";
		for($j=0,$k='A';$j < count($data['data']['c_matching']);$j++,$k++) {
			$data['str'] .= "  $k. ".$data['data']['c_matching'][$j]['c_left_text']." ";
			$data['str'] .= "  ".$data['data']['c_matching'][$j]['c_sel_text']."\n";
		}
		$data['str'] .= " ";		
		return $data['str'];
	}
	
	public function onGetStatistic(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT *, c_right_text as c_val FROM #__quiz_t_matching WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );				
		$match_data = $database->LoadObjectList();
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		$past_this += 0.0000000000001;
		
		for($i=0; $i<count($match_data); $i++) {
			$match_data[$i]->match_data = array();
			
			for($j=0; $j<count($match_data); $j++) {
				$query = "SELECT COUNT(*) FROM #__quiz_r_student_matching AS a, #__quiz_r_student_question AS b WHERE b.c_question_id = '".$data['question']->c_id."' AND b.c_id=a.c_sq_id AND  a.c_matching_id  = '".$match_data[$i]->c_id."' AND a.c_sel_text = '".md5($match_data[$j]->c_id.'answer')."'";
				$database->setQuery($query);
				
				$choice_this = $database->LoadResult();
				$match_data[$i]->match_data[] = array('c_right_text'=>$match_data[$j]->c_right_text, 'statistic'=>round(($choice_this*100)/$past_this).'%', 'count'=>$choice_this, 'c_right'=>$match_data[$i]->c_right_text==$match_data[$j]->c_right_text);
			}
			
		}
		
		$data['question']->match_data = $match_data;
		
		return $data['question'];	
	}

	public function onStatisticContent(&$data){
		
		if (isset($data['question']->match_data) && is_array($data['question']->match_data))
		foreach($data['question']->match_data as $mdata) {?>
			<tr>
				<td colspan="4"><?php echo $mdata->c_left_text.'<br/>'; ?>
					<table>
					<?php
					$color = 1;
					if (is_array($mdata->match_data))
					foreach($mdata->match_data as $sdata){?>
						<tr>
							<td width="400">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $sdata['c_right']? '<font color="#00CC00">'.$sdata['c_right_text'].'</font>':$sdata['c_right_text']?></td>
							<td align="center" width="50"><?php echo $sdata['count'];?></td>
							<td width="100"><?php echo $sdata['statistic'];?></td>
							<td width="300"><div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo ($sdata['statistic']+1)?>%;" class="jq_color_<?php echo $color;?>">&nbsp;</div></div></td>
						</tr>
						<?php
						$color++;
						if ($color > 7) $color = 1;
					} ?>
					</table>
				</td>
			</tr>
		<?php
		}		
	}

	//Administration part
	
	public function onGetAdminOptions($data)
	{
		$q_om_type = 4;
		
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_matching WHERE c_question_id = '".$data['question_id']."' ORDER BY ordering";
		$db->SetQuery( $query );
		$matching = array();
		$matching = $db->LoadObjectList();
		
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/dragdrop/admin/options/dragdrop.php");
		$options = ob_get_contents();
		ob_get_clean();
		
		return $options;
	}
	
	public function onGetAdminJavaScript(&$data){
	
		$q_om_type = 4;
		
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/dragdrop/admin/js/dragdrop.js.php");
		$script = ob_get_contents();
		ob_get_clean();
		
		return $script;
		
	}
	
	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');
		
		$db->setQuery("SELECT `c_random` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
		$row = $db->loadObject();
		
		$lists = array();		
		$c_random = array();
		$c_random[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
		$c_random[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
		$c_random = JHTML::_('select.genericlist', $c_random, 'jform[c_random]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_random) ? intval( $row->c_random ) : 0));
		$lists['c_random']['input'] = $c_random;
		$lists['c_random']['label'] = JText::_('COM_JOOMLAQUIZ_RANDOMIZE_ANSWERS');
		
		return $lists;
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
		
		$database->setQuery("UPDATE #__quiz_t_question SET `c_random` = '".$_POST['jform']['c_random']."' WHERE c_id = '".$data['qid']."'");
		$database->execute();
		
		$field_order = 0;
		$mcounter = 0;
		$fids_arr = array();		
		if (isset($_POST['jq_hid_fields_left'])) {
			foreach ($_POST['jq_hid_fields_left'] as $f_row) {					
					$new_field = new stdClass;
					if(intval($_POST['jq_hid_fields_ids'][$mcounter]))
					$new_field->c_id = intval($_POST['jq_hid_fields_ids'][$mcounter]);
					
					$new_field->c_question_id = $data['qid'];
					$new_field->c_left_text = stripslashes($f_row);
					$new_field->c_right_text = (isset($_POST['jq_hid_fields_right'][$field_order])?stripslashes($_POST['jq_hid_fields_right'][$field_order]):'');
					$new_field->ordering = $field_order;
					$new_field->c_quiz_id	= intval($_POST['jform']['c_quiz_id']);
					$new_field->a_points	= floatval((isset($_POST['jq_hid_fields_points'][$field_order])?stripslashes($_POST['jq_hid_fields_points'][$field_order]):''));
					$database->setQuery("SELECT COUNT(c_id) FROM #__quiz_t_matching WHERE c_id = '".$new_field->c_id."'");
					$exists = $database->loadResult();
					if($exists){
						$database->updateObject('#__quiz_t_matching', $new_field, 'c_id');
					} else {
						$database->insertObject('#__quiz_t_matching', $new_field);
						$new_field->c_id = $database->insertid();
					}
					
					$fids_arr[] = $new_field->c_id;					
					$field_order ++ ;
					$mcounter ++ ;					
			}
			$fieldss = implode(',',$fids_arr);
			$query = "DELETE FROM #__quiz_t_matching WHERE c_question_id = '".$data['qid']."' AND c_id NOT IN (".$fieldss.")";
			$database->setQuery( $query );
			$database->execute();
		}
		else
		{
			$query = "DELETE FROM #__quiz_t_matching WHERE c_question_id = '".$data['qid']."'";
			$database->setQuery( $query );
			$database->execute();
			$msg .= JText::_('COM_JOOMLAQUIZ_QUESTION_NOT_COMPLETE2');		
		}		
	}
	
	public function onGetAdminAddLists(&$data){
		$database = JFactory::getDBO();
		
		$query = "SELECT m.*, m2.c_right_text AS `c_sel_text` FROM #__quiz_t_matching as m LEFT JOIN #__quiz_r_student_matching as sm"
		. "\n ON m.c_id = sm.c_matching_id and sm.c_sq_id = '".$data['id']."'"
		. "\n LEFT JOIN #__quiz_t_matching AS m2 ON MD5(CONCAT(m2.c_id, 'answer')) = sm.c_sel_text "
		. "\n WHERE m.c_question_id = '".$data['q_id']."'"
		. "\n ORDER BY m.ordering, m.c_id"
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
				<th class="title" colspan="2"><?php echo JText::_('COM_JOOMLAQUIZ_USER_CHOICE');?></th>
				<th class="title"><?php echo JText::_('COM_JOOMLAQUIZ_RIGHT_ANSWER');?></th>
				<th class="title"><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_OPTIONS');?></th>
			</tr>
			<?php
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
					<?php if ($row->c_sel_text == $row->c_right_text) { ?>
					<img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/tick.png"  border="0" alt="User choice" />
					<?php } ?>
				</td>
				<td align="left">
					<?php echo $row->c_sel_text; ?>
				</td>
				<td align="left">
					<?php echo $row->c_right_text; ?>
				</td>
				<td align="left">
					<?php echo $row->c_left_text; ?>
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
		
		$query = "SELECT *, c_right_text as c_val FROM #__quiz_t_matching WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );				
		$match_data = $database->LoadObjectList();
				
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		$past_this += 0.0000000000001;
				
		for($i=0; $i<count($match_data); $i++) {
			$match_data[$i]->match_data = array();
						
			for($j=0; $j<count($match_data); $j++) {
				$query = "SELECT COUNT(*) FROM #__quiz_r_student_matching AS a, #__quiz_r_student_question AS b WHERE b.c_question_id = '".$data['question']->c_id."' AND b.c_id=a.c_sq_id AND  a.c_matching_id  = '".$match_data[$i]->c_id."' AND a.c_sel_text = '".md5($match_data[$j]->c_id.'answer')."'";
				$database->setQuery($query);
							
				$choice_this = $database->LoadResult();
				$match_data[$i]->match_data[] = array('c_right_text'=>$match_data[$j]->c_right_text, 'statistic'=>round(($choice_this*100)/$past_this).'%', 'count'=>$choice_this, 'c_right'=>$match_data[$i]->c_right_text==$match_data[$j]->c_right_text);
			}
						
		}
				
		$data['question']->match_data = $match_data;
		$question = $data['question'];
		
		return $question;
	}
	
	public function onGetAdminStatistic(&$data){
		if (is_array($data['question']->match_data))
			foreach($data['question']->match_data as $mdata) {?>
				<tr>
					<td colspan="4"><?php echo $mdata->c_left_text.'<br/>'; ?>
						<table>
						<?php 
						$color = 1;
						if (is_array($mdata->match_data))
							foreach($mdata->match_data as $sdata){?>
								<tr>
									<td width="400">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $sdata['c_right']? '<font color="#00CC00">'.$sdata['c_right_text'].'</font>':$sdata['c_right_text']?></td>
									<td width="50"><?php echo $sdata['count'];?></td>
									<td width="100"><?php echo $sdata['statistic'];?></td>
									<td width="300"><div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo ($sdata['statistic']+1)?>%;" class="jq_color_<?php echo $color;?>">&nbsp;</div></div></td>
								</tr>
						<?php 
						$color++; 
						if ($color > 7) $color = 1;
						} ?>
						</table>
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