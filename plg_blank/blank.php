<?php
/**
* JoomlaQuiz Fill in the blank Plugin for Joomla
* @version $Id: blank.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage blank.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizBlank extends plgJoomlaquizQuestion
{
	var $name		= 'Blank';
	var $_name		= 'blank';
	
	public function onCreateQuestion(&$data) {
		
		if ($data['q_data']->c_immediate)  $data['im_check'] = 1;
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div id="div_qoption'.$data['q_data']->c_id.'"><!-- x --></div>]]></quest_data_user>' . "\n";
		
		return $data;
	}
	
	public function onSaveQuestion(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT a.c_point, a.c_attempts FROM #__quiz_t_question as a WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		
		$c_quest_score = 0;
		$data['c_all_attempts'] = 1;
		$data['is_avail'] = 1;
		$blank_fbd = '';
		$blank_fbd_count = 0;
		
		$answer =  rawurldecode(stripslashes($data['answer']));

		$ans_array = explode('```',$answer);
		$is_correct_q = 0;
		$query = "SELECT c_id FROM #__quiz_t_blank  WHERE c_question_id = '".$data['quest_id']."' ORDER BY ordering";
		$database->SetQuery( $query );
		$blnk_cid = $database->loadColumn();
		foreach($ans_array as &$ans){
			$ans = (object) array('text'=>$ans,'result'=>0);
		}
		
		for($z = 0; $z<count($blnk_cid); $z++){
			$query = "SELECT c.c_id, c.c_text, c.regexp, b.points AS a_points, b.gtype FROM #__quiz_t_question as a, #__quiz_t_blank as b, #__quiz_t_text as c WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1 and b.c_question_id = a.c_id and c.c_blank_id = b.c_id and b.c_id = '".$blnk_cid[$z]."'";
			$database->SetQuery( $query );
			$ddd2 = $database->LoadObjectList();							
			if (count($ddd2) && count($ddd)) {
				$is_correct_b = $is_correct_q;
				$is_correct_t = 0;

				foreach ($ddd2 as $right_row) {
					JoomlaquizHelper::JQ_GetJoomFish($right_row->c_text, 'quiz_t_text', 'c_text', $right_row->c_id);

					if ($right_row->gtype && @$ddd[0]->c_qform == 0) {
						if ($right_row->regexp) {
							if (isset($ans_array[$z]->text) && preg_match($right_row->c_text, $ans_array[$z]->text)) { //utf8: ubral strlower								
								$is_correct_t++;
								$c_quest_score += $right_row->a_points;
								$ans_array[$z]->result = 1;
							}
						} else {											
							if (isset($ans_array[$z]->text) && strpos($ans_array[$z]->text, $right_row->c_text) !== false) {
								$is_correct_t++;
								$c_quest_score += $right_row->a_points;
								$ans_array[$z]->result = 1;
							}
						}
					} else {
						if ($right_row->c_text == '[empty]'){
							if (isset($ans_array[$z]->text) && !$ans_array[$z]->text) {
								$is_correct_q++;
								$c_quest_score += $right_row->a_points;
								$ans_array[$z]->result = 1;
							}
						} else { 
							if ($right_row->regexp) {	
								if (isset($ans_array[$z]->text) && preg_match ($right_row->c_text, $ans_array[$z]->text)) { //utf8: ubral strlower								
									$is_correct_q++;
									$c_quest_score += $right_row->a_points;
									$ans_array[$z]->result = 1;
								}
							} else {
								if (isset($ans_array[$z]->text) && ($right_row->c_text) == ($ans_array[$z]->text)) { //utf8: ubral strlower								
									$is_correct_q++;
									$c_quest_score += $right_row->a_points;
									$ans_array[$z]->result = 1;
								}
							}
						}
					}

				}
				if ($ddd[0]->c_attempts) {
					$data['c_all_attempts'] = $ddd[0]->c_attempts; 
				}
				
				if ($ddd2[0]->gtype && count($ddd2) == $is_correct_t)
					$is_correct_q++;
					
				if ($is_correct_b == $is_correct_q){
					$blank_fbd .= '<quest_blank_id><![CDATA['.$blnk_cid[$z].']]></quest_blank_id>' . "\n". "\t" . '<is_correct><![CDATA[0]]></is_correct>';
					$blank_fbd_count++;
				} else {
					$blank_fbd .= '<quest_blank_id><![CDATA['.$blnk_cid[$z].']]></quest_blank_id>' . "\n". "\t" . '<is_correct><![CDATA[1]]></is_correct>';
					$blank_fbd_count++;
				}
			}
		}	
		if($is_correct_q >= count($blnk_cid)) {$data['is_correct'] = 1; $c_quest_score += $ddd[0]->c_point;}
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
				$query = "DELETE FROM #__quiz_r_student_blank WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
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
			for($z = 0; $z<count($ans_array); $z++){
				if($ans_array[$z]->text){
					$query = "INSERT INTO #__quiz_r_student_blank (c_sq_id, c_answer, is_correct)"
					. "\n VALUES('".$c_sq_id."', ". $database->Quote( $ans_array[$z]->text ) .", ". $database->Quote( $ans_array[$z]->result ) .")";
					$database->SetQuery($query);
					$database->execute();
				}
			}	
		}
		
		$data['score'] = $c_quest_score;
		
		return true;
	}
	
	public function onTotalScore(&$data){
		
		$data['max_score'] = 0;
		$database = JFactory::getDBO();
		$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 6 AND c_id IN (".$data['qch_ids'].")";
		$database->SetQuery( $query );
		$qch_ids_type_6 = $database->loadColumn();

		if(count($qch_ids_type_6)) {
			$query = "SELECT SUM(points) FROM #__quiz_t_blank WHERE c_question_id IN (".implode(',', $qch_ids_type_6).") AND gtype = 0";
			$database->SetQuery( $query );
			$data['max_score'] += $database->LoadResult();
			
			$query = "SELECT SUM(b.points) FROM #__quiz_t_blank AS b, #__quiz_t_text AS t WHERE b.c_question_id IN (".implode(',', $qch_ids_type_6).") AND b.gtype = 1 AND t.c_blank_id = b.c_id ";
			$database->SetQuery( $query );
			$data['max_score'] += $database->LoadResult();
		}
		
		return true;
	}
	
	public function onScoreByCategory(&$data){
		
		$database = JFactory::getDBO();
		$database->setQuery("SELECT SUM(points) FROM #__quiz_t_blank WHERE `c_question_id` = '".$data['score_bycat']->c_id."'");
		$data['score'] = $database->loadResult();
		
		return true;
	}
	
	public function onFeedbackQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT t.c_id, t.c_text FROM #__quiz_t_blank as b, #__quiz_t_text as t"
		. "\n WHERE b.c_question_id = '".$data['q_data']->c_id."' AND t.c_blank_id = b.c_id"
		. "\n ORDER BY t.ordering";
		$database->SetQuery( $query );
		$blank_data = $database->LoadObjectList();
		foreach($blank_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($blank_data[$t]->c_text, 'quiz_t_text', 'c_text', $blank_data[$t]->c_id);
		}
		$data['qoption'] = "\t" . '<div><form  onsubmit=\'javascript: return false;\' name=\'quest_form\'>' . "\n";
				
		$query = "SELECT count(*)"
		. "\n FROM #__quiz_r_student_question as sp LEFT JOIN #__quiz_t_question as q ON (sp.c_question_id = q.c_id AND q.published = 1) WHERE q.c_id='".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$all_this = $database->LoadResult();
		$blank_overal = '';
		if($all_this)
		{
			$query = "SELECT count(*)"
			. "\n FROM #__quiz_r_student_question as sp LEFT JOIN #__quiz_t_question as q ON (sp.c_question_id = q.c_id AND q.published = 1) WHERE q.c_id='".$data['q_data']->c_id."' AND sp.is_correct";
			$database->setQuery($query);
			$right_this = $database->LoadResult();
			$blank_overal = "<br /><div class='review_statistic'>".JText::_('COM_QUIZ_RST_PPAST')." ".$all_this." ".JText::_('COM_QUIZ_RST_PPAST_TIMES').", ".round($right_this*100/$all_this)."% ".JText::_('COM_QUIZ_RST_ARIGHT')."</div>";
		}
		$blank_overal .= "<br /><strong>".JText::_('COM_QUIZ_RES_MES_SCORE')." ".$data['score']."</strong><br />" . "\n";

		$data['qoption'] .= $blank_overal .  "\n" . "\t" . '</form></div>' . "\n";
		return $data['qoption'];
	}
	
	public function onNextPreviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT a.c_point, a.c_attempts FROM #__quiz_t_question as a WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		
		$query = "SELECT c.c_text FROM #__quiz_t_question as a, #__quiz_t_blank as b, #__quiz_t_text as c WHERE a.c_id = '".$data['quest_id']."' and b.c_question_id = a.c_id and c.c_blank_id = b.c_id AND a.published = 1";
		$database->SetQuery( $query );
		$ddd2 = $database->LoadObjectList();
		
		$answer = rawurldecode($data['answer']);
		$ans_array = explode('```',$answer);
		
		$is_correct_q = 0;
		$query = "SELECT c_id FROM #__quiz_t_blank  WHERE c_question_id = '".$data['quest_id']."' ORDER BY ordering";
		$database->SetQuery( $query );
		$blnk_cid = $database->loadColumn();
		
		for($z = 0; $z<count($blnk_cid); $z++){
			$query = "SELECT c.c_id, c.c_text, c.regexp, b.points AS a_points FROM #__quiz_t_question as a, #__quiz_t_blank as b, #__quiz_t_text as c WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1 and b.c_question_id = a.c_id and c.c_blank_id = b.c_id and b.c_id = '".$blnk_cid[$z]."'";
			
			$database->SetQuery( $query );
			$ddd2 = $database->LoadObjectList();
			
			if (count($ddd2) && count($ddd)) {
				foreach ($ddd2 as $right_row) {
					JoomlaquizHelper::JQ_GetJoomFish($right_row->c_text, 'quiz_t_text', 'c_text', $right_row->c_id);
					if ($right_row->regexp) {	
						if (isset($ans_array[$z]) && preg_match ($right_row->c_text, $ans_array[$z])) { //utf8: ubral strlower								
							$is_correct_q++;
							$c_quest_score += $right_row->a_points;
						}
					} else {
						if (isset($ans_array[$z]) && ($right_row->c_text) == ($ans_array[$z])) { //utf8: ubral strlower								
							$is_correct_q++;
							$c_quest_score += $right_row->a_points;
						}
					}
				}
				if ($ddd[0]->c_attempts) {
					$c_all_attempts = $ddd[0]->c_attempts; }
			}
		}
		
		if($is_correct_q == count($blnk_cid)) {$data['is_correct'] = 1; }
		
		return $data;
	}
	
	public function onReviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT t.c_id, t.c_text FROM #__quiz_t_blank as b, #__quiz_t_text as t"
		. "\n WHERE b.c_question_id = '".$data['q_data']->c_id."' AND t.c_blank_id = b.c_id"
		. "\n ORDER BY t.ordering";
		$database->SetQuery( $query );
		$blank_data = $database->LoadObjectList();
		foreach($blank_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($blank_data[$t]->c_text, 'quiz_t_text', 'c_text', $blank_data[$t]->c_id);
		}
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>' . "\n";
		$qhtml = '';
		
		$query = "SELECT count(*)"
		. "\n FROM #__quiz_r_student_question as sp LEFT JOIN #__quiz_t_question as q ON (sp.c_question_id = q.c_id AND q.published = 1) WHERE q.c_id='".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$all_this = $database->LoadResult();
		$blank_overal = '';
		if($all_this)
		{
			$query = "SELECT count(*)"
			. "\n FROM #__quiz_r_student_question as sp LEFT JOIN #__quiz_t_question as q ON (sp.c_question_id = q.c_id AND q.published = 1) WHERE q.c_id='".$data['q_data']->c_id."' AND sp.is_correct";
			$database->setQuery($query);
			$right_this = $database->LoadResult();
			$blank_overal = "<div class='review_statistic'>".JText::_('COM_QUIZ_RST_PPAST')." ".$all_this." ".JText::_('COM_QUIZ_RST_PPAST_TIMES').", ".round($right_this*100/$all_this)."% ".JText::_('COM_QUIZ_RST_ARIGHT')."</div>";
		}
		$query = "SELECT * FROM #__quiz_t_blank WHERE c_question_id=".$data['q_data']->c_id." ORDER BY ordering";
		$database->setQuery($query);
		$blnk = $database->loadObjectList();
		$qhtml = '';
		for($i=0;$i<count($blnk);$i++){												
			if(!substr_count(strtolower($data['q_data']->c_question),"{blank".($blnk[$i]->ordering + 1)."}")){
				$query = "SELECT c_text FROM #__quiz_t_text WHERE c_blank_id = ".$blnk[$i]->c_id." ORDER BY ordering";
				$database->setQuery($query);
				
				$qdata = array();
				$qdata['c_text'] = implode(', ',$database->loadColumn());
				$qdata['color'] = '#cc0000';
				
				$qhtml .= JoomlaQuiz_template_class::JQ_createReview($qdata, $data).'<br />';							
			}	
		}
	
		$query = "SELECT * FROM #__quiz_r_student_blank WHERE c_sq_id = '".$data['sid']."'";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();

		$answer = '<table width="100%" class="jq_mchoice_overal" id=\'quest_table\'><tr class="sectiontableheader"><td colspan="3" align="left"><strong>'.JText::_('COM_QUIZ_ANSWER').'</strong></td></tr>';
		$answer .= '<tr class="sectiontableentry1"><td align="left" style="padding-left: 10px;">';
		if(is_array($tmp))
		foreach($tmp as $t) {
			$answer .= $t['c_answer']."; ";
			
		}			
		$answer .= '</td></tr></table>';	

		$data['ret_str'] .= $qhtml . $blank_overal. $answer . "\n" . "\t" . '</form></div>]]></quest_data_user>' . "\n";
		return $data;		
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_r_student_blank WHERE c_sq_id = '".$data['id']."'";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
		$data['info']['c_blank'] = $tmp;
		
		$query = "SELECT SUM(`points`) FROM `#__quiz_t_blank` WHERE `c_question_id` = ".$data['qid'];
		$database->SetQuery( $query );
		$data['info']['c_point'] += $database->LoadResult();
		
		$query = "SELECT a.c_id, a.c_text FROM #__quiz_t_text AS a, #__quiz_t_blank as b WHERE b.c_question_id = '".$data['qid']."' AND a.c_blank_id = b.c_id ORDER BY a.c_blank_id, a.ordering ";
		$database->SetQuery( $query );
		$tmp3 = $database->LoadObjectList();
		$tmp = array();
		foreach($tmp3 as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp3[$t]->c_text, 'quiz_t_text', 'c_text', $tmp3[$t]->c_id);
			$tmp[] = $tmp3[$t]->c_text;
		}
		$data['info']['answers'] = @implode('; ', $tmp);

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
		for($s=0;$s<count($data['data']['c_blank']);$s++){
			$data['pdf']->setFont($fontFamily);
			//$data['pdf']->setStyle('b', false);
			$str = $data['data']['c_blank'][$s]['c_answer'].'; ';
			$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
		}
				
		return $data['pdf'];		
	}
	
	public function onSendEmail(&$data){
	
		$data['str'] .= "<br>&nbsp;&nbsp;<strong>".JText::_('COM_QUIZ_PDF_ANSWER')."</strong>";
		for($s=0;$s<count($data['data']['c_blank']);$s++){
			$data['str'] .= $data['data']['c_blank'][$s]['c_answer']."; ";
		}		
		return $data['str'];
	}
	
	public function onGetStatistic(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		$past_this += 0.0000000000001;
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_score > 0 AND c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$correct = $database->LoadResult();				
		
		$data['question']->correct =round(($correct*100)/($past_this)).'%';
		$data['question']->correct_c = intval($correct);
		
		$data['question']->incorrect = (intval($past_this)?(100-$data['question']->correct):0).'%';
		$data['question']->incorrect_c = intval($past_this-$correct);
		
		return $data['question'];	
	}

	public function onStatisticContent(&$data){
		
		?>
		<tr>
			<td><font color="#00CC00"><?php echo JText::_('COM_STATISTICS_CORRECT');?></font></td>
			<td><?php echo $data['question']->correct_c?></td>
			<td><?php echo $data['question']->correct;?></td>
			<td><div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $data['question']->correct+1;?>%;" class="jq_color_1"></div></div></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_STATISTICS_INCORRECT');?></td>
			<td><?php echo $data['question']->incorrect_c?></td>
			<td><?php echo $data['question']->incorrect;?></td>
			<td><div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $data['question']->incorrect+1;?>%;" class="jq_color_2"></div></div></td>
		</tr>
		<?php
	}

	//Administration part
	public function onGetAdminOptions($data)
	{
		$q_om_type = 6;
		
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_blank  WHERE c_question_id = '".$data['question_id']."' ORDER BY ordering";
		$db->SetQuery( $query );
		$blank_data = array();
		$blank_data = $db->LoadObjectList();
			
		$query = "SELECT * FROM #__quiz_t_faketext  WHERE c_quest_id = '".$data['question_id']."' ORDER BY c_id";
		$db->SetQuery( $query );
		$fake_data = array();
		$fake_data = $db->LoadObjectList();
				
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/blank/admin/options/blank.php");
		$options = ob_get_contents();
		ob_get_clean();
		
		return $options;
	}
	
	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');
		
		$db->setQuery("SELECT `c_qform`, `c_image` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
		$row = $db->loadObject();
		
		$lists = array();
		$c_qform = array();
		$c_qform[] = JHTML::_('select.option', 0, JText::_('COM_JOOMLAQUIZ_DEFAULT'));
		$c_qform[] = JHTML::_('select.option', 1, JText::_('COM_JOOMLAQUIZ_DRAGGABLE'));
		$c_qform = JHTML::_('select.genericlist', $c_qform, 'c_qform', 'class="text_area" size="1" ', 'value', 'text', (isset($row->c_qform)) ? intval( $row->c_qform ) : 0);
		$lists['c_qform']['input'] = $c_qform;
		$lists['c_qform']['label'] = JText::_('COM_JOOMLAQUIZ_DISPLAY_STYLE');
		
		$c_image = (isset($row->c_image)) ? $row->c_image : '';
		$lists['c_image']['input'] = "<input type='text' size='30' name='c_image' value='".$c_image."' />";
		$lists['c_image']['label'] = JText::_('COM_JOOMLAQUIZ_CUSTOMCSS_CLASS');
		
		return $lists;
	}
	
	public function onGetAdminJavaScript(&$data){
		
		$q_om_type = 6;
		
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_blank  WHERE c_question_id = '".$data['question_id']."' ORDER BY ordering";
		$db->SetQuery( $query );
		$blank_data = array();
		$blank_data = $db->LoadObjectList();
		
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/blank/admin/js/blank.js.php");
		$script = ob_get_contents();
		ob_get_clean();
		
		return $script;
	}
	
	public function onGetAdminFeedbackFields($data){
		$db = JFactory::getDBO();
		
		$db->setQuery("SELECT `c_immediate` FROM #__quiz_t_question WHERE `c_id` = '".$data['question_id']."'");
		$row = $db->loadObject();
		
		$lists = array();		
		$c_immediate = array();
		$c_immediate[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
		$c_immediate[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
		$c_immediate = JHTML::_('select.genericlist', $c_immediate, 'jform[c_immediate]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_immediate) ? intval( $row->c_immediate ) : 0));
		
		$lists['c_immediate']['input'] = $c_immediate;
		$lists['c_immediate']['label'] = JText::_('COM_JOOMLAQUIZ_IMMEDIATE_FEEDBACK');
		
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
		
		$database->setQuery("UPDATE #__quiz_t_question SET `c_immediate` = '".$_POST['jform']['c_immediate']."', `c_qform` = '".$_POST['c_qform']."', `c_image` = '".$_POST['c_image']."' WHERE c_id = '".$data['qid']."'");
		$database->execute();
		
		if(isset($_POST['blnk_arr']))
		{
            if(JFactory::getApplication()->input->get('task')=='save2copy') {
                $_POST['blnk_arr_id'] = array_map(function ($id) {
                    return 0;
                }, $_POST['blnk_arr_id']);
            }
			$ord = 0;
			$blank_arr = array();
			foreach ($_POST['blnk_arr'] as $blnk_n) {	
				$database->SetQuery("SELECT c_id FROM #__quiz_t_blank WHERE c_id = '".(isset($_POST['blnk_arr_id'][$ord])?($_POST['blnk_arr_id'][$ord]):0)."'");
				$bid = $database->LoadResult();
				
				$new_points = floatval(@$_POST['jq_hid_points_'.$blnk_n]);
				$new_css_class = (@$_POST['jq_hid_css_'.$blnk_n]);
				$new_gtype = intval(@$_POST['jq_hid_gtype_'.$blnk_n][0]);

				if (!$bid) {					
					$database->SetQuery("INSERT INTO #__quiz_t_blank (c_question_id,ordering, points, css_class, gtype) VALUES('".$data['qid']."', '".$ord."', '".$new_points."', ".$database->Quote($new_css_class).", ".$new_gtype.")");
					$database->query();
					$bid = $database->insertid();
				} else {
					$database->SetQuery("UPDATE #__quiz_t_blank SET `points` = '".$new_points."', `gtype` = '".$new_gtype."' , `css_class` = ".$database->Quote($new_css_class).", `ordering` = '".$ord."' WHERE c_id='".$bid."'");
					$database->execute();
				}
				$ord++;
				$blank_arr[] = $bid;
				
				$field_order = 0;
				$mcounter = 0;
				$fids_arr = array();
				if (isset($_POST['jq_hid_fields_'.$blnk_n])) {
                    if(JFactory::getApplication()->input->get('task')=='save2copy') {
                        $_POST['jq_hid_fields_ids_' . $blnk_n]
                            = array_map(function ($id) {
                            return 0;
                        }, $_POST['jq_hid_fields_ids_' . $blnk_n]);
                    }
					foreach ($_POST['jq_hid_fields_'.$blnk_n] as $br=>$f_row) {
						$new_field = new stdClass;
						if(intval($_POST['jq_hid_fields_ids_'.$blnk_n][$mcounter])){
                            $new_field->c_id = intval($_POST['jq_hid_fields_ids_'.$blnk_n][$mcounter]);
                        }
						$new_field->c_blank_id = $bid;
						$new_field->c_text = (stripslashes($f_row));
						$new_field->ordering = $field_order;
						$new_field->regexp = intval(@$_POST['jq_hid_regexp_'.$blnk_n][$br]);

						$database->setQuery("SELECT COUNT(c_id) FROM #__quiz_t_text WHERE c_id = '".$new_field->c_id."'");
						$exists = $database->loadResult();
						if($exists){
							$database->updateObject('#__quiz_t_text', $new_field, 'c_id');
						} else {
							$database->insertObject('#__quiz_t_text', $new_field);
							$new_field->c_id = $database->insertid();
						}
						$fids_arr[] = $new_field->c_id;
						$field_order ++ ;
						$mcounter ++ ;
					}
					$fieldss = implode(',',$fids_arr);
					$query = "DELETE FROM #__quiz_t_text WHERE c_blank_id = '".$bid."' AND c_id NOT IN (".$fieldss.")";
					$database->setQuery( $query );
					$database->query();
				}
				else {
					$msg = JText::_('COM_JOOMLAQUIZ_QUESTION_NOT_COMPLETE2');
				}
			}	
			$fieldss = implode(',',$blank_arr);
			$query = "SELECT c_id FROM #__quiz_t_blank WHERE c_question_id = '".$data['qid']."' AND c_id NOT IN (".$fieldss.")";
			$database->setQuery( $query );
			$for_del = $database->loadColumn();
			$for_dels = implode(',',$for_del);
	
			$query = "DELETE FROM #__quiz_t_blank WHERE c_question_id = '".$data['qid']."' AND c_id NOT IN (".$fieldss.")";
			$database->setQuery( $query );
			$database->execute();
			
			if($for_dels){
				$query = "DELETE FROM #__quiz_t_text WHERE c_blank_id IN (".$for_dels.")";
				$database->setQuery( $query );
				$database->execute();
			}
			
			$query = "DELETE FROM #__quiz_t_faketext WHERE c_quest_id = '".$data['qid']."'";
			$database->setQuery( $query );
			$database->execute();
			
			$jq_hid_fakes = JFactory::getApplication()->input->get('jq_hid_fake', '', array());
			foreach($jq_hid_fakes as $jq_hid_fake){
				$query = "INSERT INTO #__quiz_t_faketext SET c_quest_id = '".$data['qid']."', c_text = ".$database->Quote($jq_hid_fake);
				$database->setQuery( $query );
				$database->execute();
			}
		}
	}
	
	public function onGetAdminAddLists(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT * FROM #__quiz_r_student_blank WHERE c_sq_id = '".$data['id']."' ORDER BY c_id";
		$database->SetQuery( $query );
		$answer = $database->LoadObjectList();

		if (!count($answer)) { $answer = array(); $answer[0]->c_answer = ''; }
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
				<th class="title" colspan="2"><?php echo JText::_('COM_JOOMLAQUIZ_USER_ANSWER');?></th>
			</tr>
			<?php
				for($j=0;$j<count($rows);$j++){
			?>
			<tr class="row1">
				<td align="left" width="100">
					<?php echo JText::_('COM_JOOMLAQUIZ_BLANK');?><?php echo $j+1; ?>
				</td>
				<td align="left">
					<?php echo $rows[$j]->c_answer; ?>
				</td>
			</tr>
			<?php } ?>
		</table>
		<?php
		
		$content = ob_get_contents();
		ob_clean();
		return $content;
	}
	
	public function onGetAdminQuestionData(&$data){
	
		$database = JFactory::getDBO();
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
				
		$past_this += 0.0000000000001;
				
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_score > 0 AND c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$correct = $database->LoadResult();				
				
		$data['question']->correct =round(($correct*100)/($past_this)).'%';
		$data['question']->correct_c = intval($correct);
				
		$data['question']->incorrect = (intval($past_this)?(100-$data['question']->correct):0).'%';
		$data['question']->incorrect_c = intval($past_this-$correct);
		
		return $data['question'];	
	}
	
	public function onGetAdminStatistic(&$data){
		?>
		<tr>
			<td><font color="#00CC00"><?php echo JText::_('COM_JOOMLAQUIZ_CORRECT');?></font></td> 
			<td><?php echo $data['question']->correct_c?></td>
			<td><?php echo $data['question']->correct;?></td>
			<td><div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $data['question']->correct+1;?>%;" class="jq_color_1"></div></div></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JOOMLAQUIZ_INCORRECT');?></td> 
			<td><?php echo $data['question']->incorrect_c?></td>
			<td><?php echo $data['question']->incorrect;?></td>
			<td><div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $data['question']->incorrect+1;?>%;" class="jq_color_2"></div></div></td>
		</tr>
		<?php
	}
	
	public function onGetAdminCsvData(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT `a`.`c_score` FROM `#__quiz_r_student_question` AS `a` WHERE `a`.`c_stu_quiz_id` = '".$data['result']->c_id."' AND `a`.`c_question_id` = '".$data['question']->c_id."'";
		$database->setQuery( $query );
		$data['score'] = $database->loadResult();
		if ($data['score'] != null)
			$data['answer'] = 'Score - '.$data['score'];
		
		return $data['answer'];
	}
}