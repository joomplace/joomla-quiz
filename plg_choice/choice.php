<?php
/**
* JoomlaQuiz Multiple Choice Plugin for Joomla
* @version $Id: choice.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage choice.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizChoice extends plgJoomlaquizQuestion
{
	var $name		= 'Choice';
	var $_name		= 'choice';
	
	public function onCreateQuestion(&$data) {
		
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, '0' as c_right, '0' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."'";
		if ($data['qrandom'])
			$query .=  "\n ORDER BY rand()";
		else
			$query .=  "\n ORDER BY ordering";
		
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
						
		foreach($choice_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($choice_data[$t]->text, 'quiz_t_choice', 'c_choice', $choice_data[$t]->value);
		}
						
		$query = "SELECT c_choice_id FROM #__quiz_t_choice AS c LEFT JOIN #__quiz_r_student_choice AS sc ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$data['sid']."' WHERE c.c_question_id = '".$data['q_data']->c_id."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$answers = $database->loadColumn();
		$answers = is_array($answers)? $answers: array();
		
		for($c = 0, $cn = count($choice_data); $c < $cn; $c++) {

			if (in_array($choice_data[$c]->value, $answers)) {
				$choice_data[$c]->c_right = 1;
			}

			$choice_data[$c]->text = JoomlaquizHelper::JQ_ShowText_WithFeatures($choice_data[$c]->text);
		}
		
		$qhtml = JoomlaQuiz_template_class::JQ_createQuestion($choice_data, $data);
		if ($data['q_data']->c_layout == 1) $data['ret_add_script'] .= "jq_jQuery('.jq_question_text_cont').css('width', 'auto');";

		if ($data['q_data']->c_qform && JoomlaquizHelper::jq_strpos($data['ret_add'], '{x}') !== false) {				
			$data['ret_add'] = '<form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'>'.str_replace('{x}', $qhtml, $data['ret_add']).'</form>';
			$data['ret_str'] .= (preg_match('/pretty_green/', $data['cur_template'])) ? "\t" . '<quest_data_user><![CDATA[<div id="div_qoption'.$data['q_data']->c_id.'" class="jq_quiz_align-wr"><div class="jq_quiz_align"><!-- x --></div></div>]]></quest_data_user>' . "\n": "\t" . '<quest_data_user><![CDATA[<div style="width:100%;clear:both;" id="div_qoption'.$data['q_data']->c_id.'"><!-- x --></div>]]></quest_data_user>' . "\n";
		} else {
			$data['ret_str'] .= (preg_match('/pretty_green/', $data['cur_template']) || preg_match('/pretty_blue/', $data['cur_template'])) ? "\t" . '<quest_data_user><![CDATA[<div id="div_qoption'.$data['q_data']->c_id.'" class="jq_quiz_align-wr"><div class="jq_quiz_align"><form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'>'.$qhtml.'</form></div></div>]]></quest_data_user>' . "\n": "\t" . '<quest_data_user><![CDATA[<div style="width:100%;clear:both;" id="div_qoption'.$data['q_data']->c_id.'"><form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'>'.$qhtml.'</form></div>]]></quest_data_user>' . "\n";
		}
		
		if((preg_match('/pretty_green/', $data['cur_template']) || preg_match('/pretty_blue/', $data['cur_template'])) && $data['q_data']->c_qform){
			$data['ret_add_script'] = "jq_jQuery(function() {jq_jQuery('.chzn-select').chosen()});";
		}
		
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
		
		$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$data['quest_id']."' and b.c_question_id = a.c_id and b.c_right = '1' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		
		$query = "SELECT b.a_point FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$data['quest_id']."' and b.c_question_id = a.c_id and b.c_id = '".$data['answer']."'  AND a.published = 1";
		$database->SetQuery( $query );
		$c_quest_score = $database->LoadResult();
		
		$data['c_all_attempts'] = 1;
		$data['is_avail'] = 1;
		if (count($ddd)) {
			if ($ddd[0]->c_id == $data['answer']) {
				$c_quest_score += $ddd[0]->c_point;
				$data['is_correct'] = 1;
			}
			
			if ($ddd[0]->c_attempts) {
				$data['c_all_attempts'] = $ddd[0]->c_attempts;
			}
		}
		
		if($data['qtype'] == 1) {									
			$query = "SELECT b.c_incorrect_feed FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$data['quest_id']."' and b.c_question_id = a.c_id and b.c_id = '".$data['answer']."' AND a.published = 1";
			$database->SetQuery( $query );
			$inc_ddd = $database->LoadObjectList();
			if (count($inc_ddd))
				$data['questtype1_answer_incorrect'] = htmlspecialchars(nl2br($inc_ddd[0]->c_incorrect_feed));
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
			
			$query = "INSERT INTO #__quiz_r_student_choice (c_sq_id, c_choice_id)"
			. "\n VALUES('".$c_sq_id."', '".$data['answer']."')";
			$database->SetQuery($query);
			$database->execute();
		}
		
		$data['score'] = $c_quest_score;
		
		return true;
	}
	
	public function onTotalScore(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id IN (".$data['qch_ids'].") AND c_right = 1";
		$database->SetQuery( $query );
		$data['max_score'] = $database->LoadResult();
		
		$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 1 AND c_quiz_id = '".$data['quiz_id']."' AND published = 1";
		$database->SetQuery( $query );
		$qch_ids_type_1 = $database->loadColumn();
		
		if(count($qch_ids_type_1)) {
			foreach($qch_ids_type_1 as $key => $c_quetion_id){
				$query = "SELECT b.c_right FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$c_quetion_id."' AND b.c_question_id = a.c_id AND a.published = 1";
				$database->SetQuery( $query );
				$c_choices = $database->loadColumn();
				
				if (!in_array(1, $c_choices)){
					$query = "SELECT MAX(a_point) FROM #__quiz_t_choice WHERE c_question_id = '".$c_quetion_id."' AND c_right = 0";
					$database->SetQuery( $query );
					$data['max_score'] += $database->LoadResult();
				}
			}
		}
		
		return true;
	}
	
	public function onScoreByCategory(&$data){
		
		$database = JFactory::getDBO();
		$database->setQuery("SELECT SUM(a_point) FROM #__quiz_t_choice WHERE `c_question_id` = '".$data['score_bycat']->c_id."' AND c_right = 1");
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
					
		if($past_this)
		{
			for($i=0;$i<count($choice_data);$i++)
			{
				$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
				$database->setQuery($query);
				$choice_this = $database->LoadResult();
				$choice_data[$i]->statistic = round(($choice_this*100)/$past_this).'%';

				$choice_data[$i]->text = JoomlaquizHelper::JQ_ShowText_WithFeatures($choice_data[$i]->text);
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
		
		$feedback_data = array();
		$feedback_data['choice_data'] = $choice_data;
		$feedback_data['uanswer'] = $uanswer;
		
		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($feedback_data, $data);
		
		if(preg_match('/pretty_green/', $data['cur_template'])){
			$data['qoption'] = "\t" .$qhtml . "\n";
		} else {
			$data['qoption'] = "\t" . '<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>'.$qhtml.'</form></div>' . "\n";
		}
		
		return $data['qoption'];
	}
	
	public function onNextPreviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$data['quest_id']."' and b.c_question_id = a.c_id and b.c_right = '1' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		if (count($ddd)) { if ($ddd[0]->c_id == $data['answer']) { $data['is_correct'] = 1; }}
		
		$query = "SELECT b.c_incorrect_feed FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$data['quest_id']."' and b.c_question_id = a.c_id and b.c_id = '".$data['answer']."' AND a.published = 1";
		$database->SetQuery( $query );
		$inc_ddd = $database->LoadObjectList();
		if (count($inc_ddd))
			$data['questtype1_answer_incorrect'] = htmlspecialchars(nl2br($inc_ddd[0]->c_incorrect_feed));
						
		return $data;
	}
	
	public function onReviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		foreach($choice_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($choice_data[$t]->text, 'quiz_t_choice', 'c_choice', $choice_data[$t]->value);
		}
					
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		$choice_data[0]->overal = '';
		if($past_this) {
			for($i=0;$i<count($choice_data);$i++) {
				$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
				$database->setQuery($query);
				$choice_this = $database->LoadResult();
				$choice_data[$i]->statistic = round(($choice_this*100)/$past_this).'%';

				$choice_data[$i]->text = JoomlaquizHelper::JQ_ShowText_WithFeatures($choice_data[$i]->text);
			}
			
			$choice_data[0]->overal = JText::_('COM_QUIZ_RST_PANSW')." ".$past_this." ".JText::_('COM_QUIZ_RST_PANSW_TIMES');
		}	
		
		$query = "SELECT * FROM #__quiz_t_choice AS c  LEFT JOIN #__quiz_r_student_choice AS sc"
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
				if(preg_match('/<P>(.*)<\/P>/', $t['c_choice'], $match)){
					$answer .= '<P>'.$match[1].";</P>";
				} else {$answer .= $t['c_choice'];}
			}				
		}
		
		$answer = JoomlaquizHelper::JQ_ShowText_WithFeatures($answer);				
		$choice_data[0]->answer = $answer;	

		$qhtml = JoomlaQuiz_template_class::JQ_createReview($choice_data, $data);
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>'.$qhtml.'</form></div>]]></quest_data_user>' . "\n";
		
		return $data;		
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT *, c.c_id AS id FROM #__quiz_t_choice AS c LEFT JOIN #__quiz_r_student_choice AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$data['id']."'"
		. "\n WHERE c.c_question_id = '".$data['qid']."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
		
		foreach($tmp as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_choice'], 'quiz_t_choice', 'c_choice', $tmp[$t]['id']);
		}
		$data['info']['c_choice'] = $tmp;
		$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id = ".$data['qid']." AND c_right = 1";
		$database->SetQuery( $query );
		$data['info']['c_point'] += $database->LoadResult();
		
		return true;
	}
	
	public function onGetPdf(&$data){
		
		for($j=0,$k='A';$j < count($data['data']['c_choice']);$j++,$k++) {
			if($data['data']['c_choice'][$j]['c_choice_id']) {
				$data['answer'] .= $k." ";
			}
					
			if ($data['data']['c_choice'][$j]['c_right']) {
				$correct_answer .= $k." ";
			}

			//$data['pdf']->SetFont('freesans');
			$fontFamily = $data['pdf']->getFontFamily();
					
			$data['pdf']->Ln();

			$data['pdf']->setFont($fontFamily);
			//$data['pdf']->setStyle('b', true);
			$str = "  $k.";
			$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);

			$data['pdf']->setFont($fontFamily);
			//$data['pdf']->setStyle('b', false);
			$str = $data['data']['c_choice'][$j]['c_choice'];
			$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
			
		}

		$data['pdf']->Ln();
		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', true);
		$str = '  '.JText::_('COM_QUIZ_PDF_ANSWER');
		$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', false);
		$str = $data['answer'];
		$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
		
		return $data['pdf'];		
	}
	
	public function onSendEmail(&$data){
	
		for($j=0,$k='A';$j < count($data['data']['c_choice']);$j++,$k++) {
			if($data['data']['c_choice'][$j]['c_choice_id']) $data['answer'] .= $k."&nbsp;";
			$data['str'] .= "$k. ".$data['data']['c_choice'][$j]['c_choice']."\n";
		}
		$data['str'] .= " ".JText::_('COM_QUIZ_PDF_ANSWER')." ".$data['answer']." \n";				
		
		return $data['str'];
	}
	
	public function onGetStatistic(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT c_id as value, c_choice as text, c_right FROM #__quiz_t_choice WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		$past_this += 0.0000000000001;
		
			for($i=0; $i<count($choice_data); $i++) {
				$query = "SELECT COUNT(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
				$database->setQuery($query);
				$choice_this = $database->LoadResult();
				$choice_data[$i]->statistic = round(($choice_this*100)/$past_this).'%';
				$choice_data[$i]->count = $choice_this;
			}
		
		$data['question']->choice_data = $choice_data;

		return $data['question'];		
	}

	public function onStatisticContent(&$data){
		
		$color = 1;
		if (isset($data['question']->choice_data) && is_array($data['question']->choice_data))
		foreach($data['question']->choice_data as $cdata){
			?>
			<tr>
				<td><?php echo $cdata->c_right? '<font color="#00CC00">'.$cdata->text.'</font>':$cdata->text?></td>
				<td align="center"><?php echo $cdata->count?></td>
				<td><?php echo $cdata->statistic?></td>
				<td><div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $cdata->statistic+1;?>%;" class="jq_color_<?php echo $color;?>"></div></div></td>
			</tr>
			<?php
			$color++;
			if ($color > 7) $color = 1;
		}
		
	}

	//Administration part
	
	public function onGetAdminOptions($data)
	{
		$settings = JoomlaquizHelper::getSettings();
		$q_om_type = 1;
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
		require_once(JPATH_SITE."/plugins/joomlaquiz/choice/admin/options/choice.php");
		$options = ob_get_contents();
		ob_get_clean();
		
		return $options;
	}
	
	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');
		
		$db->setQuery("SELECT `c_qform`, `c_layout`, `c_random` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
		$row = $db->loadObject();
		
		$lists = array();
		$c_qform = array();
		$c_qform[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_RADIO_BUTTONS'));
		$c_qform[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_DROP_DOWN'));
		$c_qform = JHTML::_('select.genericlist', $c_qform, 'jform[c_qform]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_qform) ? intval( $row->c_qform ) : 0)); 
		$lists['c_qform']['input'] = $c_qform;
		$lists['c_qform']['label'] = '<label class="hasTooltip control-label" title data-original-title="'.JText::_('COM_JOOMLAQUIZ_IF_THE_DISPLAY').'" for="c_qform" id="c_qform-lbl" style="width:156px;">'.JText::_('COM_JOOMLAQUIZ_DISPLAY_STYLE').'</label>';
			
		$c_layout = array();
		$c_layout[] = JHTML::_('select.option', 0, JText::_('COM_JOOMLAQUIZ_VERTICAL'));
		$c_layout[] = JHTML::_('select.option', 1, JText::_('COM_JOOMLAQUIZ_HORIZONTAL'));
		$c_layout = JHTML::_('select.genericlist', $c_layout, 'jform[c_layout]', 'class="text_area" size="1" ', 'value', 'text', (isset($row->c_layout) ? intval( $row->c_layout ) : 0));
		$lists['c_layout']['input'] = $c_layout;
		$lists['c_layout']['label'] = JText::_('COM_JOOMLAQUIZ_QUESTION_OPTIONS_LAYOUT');
		
		$c_random = array();
		$c_random[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
		$c_random[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
		$c_random = JHTML::_('select.genericlist', $c_random, 'jform[c_random]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_layout) ? intval( $row->c_random ) : 0));
		$lists['c_random']['input'] = $c_random;
		$lists['c_random']['label'] = JText::_('COM_JOOMLAQUIZ_RANDOMIZE_ANSWERS');
		
		return $lists;
	}
	
	public function onGetAdminJavaScript(&$data){
		
		$settings = JoomlaquizHelper::getSettings();
		$q_om_type = 1;
		$wysiwyg = (isset($settings->wysiwyg_options)) ? $settings->wysiwyg_options : 0;
		$question_id = $data['question_id'];
		
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/choice/admin/js/choice.js.php");
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
		
		$database->setQuery("UPDATE #__quiz_t_question SET `c_qform` = '".$_POST['jform']['c_qform']."', `c_layout` = '".$_POST['jform']['c_layout']."', `c_random` = '".$_POST['jform']['c_random']."' WHERE c_id = '".$data['qid']."'");
		$database->execute();
		
		$field_order = 0;
		$ans_right = array();
		if (isset($_REQUEST['jq_checked'])) {
			foreach ($_REQUEST['jq_checked'] as $sss) {
				$ans_right[] = $sss;
			}
		}
        else{
            $msg .= JText::_('COM_JOOMLAQUIZ_QUESTION_NOT_COMPLETE');
        }

        if(JFactory::getApplication()->input->get('task')=='save2copy'){
            $_POST['jq_hid_fields_ids'] = array_map(function($el){
                return '';
            }, $_POST['jq_hid_fields_ids']);
        }
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
					
					$new_field->c_right = in_array(($field_order+ 1), $ans_right)?1:0;
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
		<table class="table table-striped" style="width:50%">
			<tr>
				<th class="title"><?php echo JText::_('COM_JOOMLAQUIZ_USER_CHOICE');?></th>
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
					<?php if ($row->sc_id) { ?>
					<img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/tick.png"  border="0" alt="User choice" />
					<?php } ?>
				</td>
				<td align="center">
					<?php if ($row->c_right) { ?>
					<img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/tick.png"  border="0" alt="User choice" />
					<?php } ?>
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
		$query = "SELECT c_id as value, c_choice as text, c_right FROM #__quiz_t_choice WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
				
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
				
		$past_this += 0.0000000000001;
				
		for($i=0; $i<count($choice_data); $i++) {
			$query = "SELECT COUNT(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
			$database->setQuery($query);
			$choice_this = $database->LoadResult();
			$choice_data[$i]->statistic = round(($choice_this*100)/$past_this).'%';
			$choice_data[$i]->count = $choice_this;
		}
				
		$data['question']->choice_data = $choice_data;
		$question = $data['question'];
		
		return $question;	
	}
	
	public function onGetAdminStatistic(&$data){
		$color = 1;
		if (is_array($data['question']->choice_data))
			foreach($data['question']->choice_data as $cdata){
		?>
		<tr>
			<td><?php echo $cdata->c_right? '<font color="#00CC00">'.$cdata->text.'</font>':$cdata->text?></td> 
			<td><?php echo $cdata->count?></td>
			<td><?php echo $cdata->statistic?></td>
			<td><div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $cdata->statistic+1;?>%;" class="jq_color_<?php echo $color;?>"></div></div></td>
		</tr>
		<?php
			$color++;
			if ($color > 7) $color = 1;
		}
	}
	
	public function onGetAdminCsvData(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT `b`.`c_choice_id` FROM `#__quiz_r_student_question` AS `a`, `#__quiz_r_student_choice` AS `b` WHERE `a`.`c_stu_quiz_id` = '".$data['result']->c_id."' AND `a`.`c_question_id` = '".$data['question']->c_id."' AND `a`.`c_id` = `b`.`c_sq_id` ";
		$database->setQuery( $query );
		$stu_choices = $database->loadColumn();								
		foreach($data['choices'] as $choice) {	
			if ( in_array($choice->c_id, $stu_choices) ) {
				$data['answer'] .= $choice->number.',';
				if ($choice->c_incorrect_feed)
					$data['feedback'] .= $choice->c_incorrect_feed.';';
			}
		}
		if ($data['answer'] != '')
			$data['answer'] = JoomlaquizHelper::jq_substr($data['answer'], 0, strlen($data['answer'])-1);
			
		return $data['answer'];	
	}
}