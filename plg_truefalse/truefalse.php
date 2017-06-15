<?php
/**
* JoomlaQuiz True/False questions Plugin for Joomla
* @version $Id: choice.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage choice.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizTruefalse extends plgJoomlaquizQuestion
{
	var $name		= 'Truefalse';
	var $_name		= 'truefalse';
	
	public function onCreateQuestion(&$data) {
		
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, '0' as c_right, '0' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		foreach($choice_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($choice_data[$t]->text, 'quiz_t_choice', 'c_choice', $choice_data[$t]->value);
		}
						
		$i = 0;
		while ($i < count($choice_data)) {
			if ( ($choice_data[$i]->text == 'true') || ($choice_data[$i]->text == 'True') ) {
				$choice_data[$i]->text = JText::_('COM_QUIZ_SIMPLE_TRUE');
			} elseif ( ($choice_data[$i]->text == 'false') || ($choice_data[$i]->text == 'False') ) {
				$choice_data[$i]->text = JText::_('COM_QUIZ_SIMPLE_FALSE');
			}
			$i ++;
		}
						
		$query = "SELECT c_choice_id FROM #__quiz_t_choice AS c  LEFT JOIN #__quiz_r_student_choice AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$data['sid']."'"
		. "\n WHERE c.c_question_id = '".$data['q_data']->c_id."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$answers = $database->loadColumn();
		$answers = is_array($answers)? $answers: array();
		for($c = 0, $cn = count($choice_data); $c < $cn; $c++) {
			if (in_array($choice_data[$c]->value, $answers)) {
				$choice_data[$c]->c_right = 1;
			}
		}
		
		$data['q_data']->c_qform = 0;
		$data['q_data']->c_layout = 0;
		
		$qhtml = JoomlaQuiz_template_class::JQ_createQuestion($choice_data, $data);
		
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div style="width:100%;clear:both;" id="div_qoption'.$data['q_data']->c_id.'"><form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'>'.$qhtml.'</form></div>]]></quest_data_user>' . "\n";
		
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
	
	public function onFeedbackQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$choice_data[0]->score = $data['score'];
		
		foreach($choice_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($choice_data[$t]->text, 'quiz_t_choice', 'c_choice', $choice_data[$t]->value);
		}
			
		$i = 0;
		while ($i < count($choice_data)) {
			if ( ($choice_data[$i]->text == 'true') || ($choice_data[$i]->text == 'True') ) {
				$choice_data[$i]->text = JText::_('COM_QUIZ_SIMPLE_TRUE');
			} elseif ( ($choice_data[$i]->text == 'false') || ($choice_data[$i]->text == 'False') ) {
				$choice_data[$i]->text = JText::_('COM_QUIZ_SIMPLE_FALSE');
			}
			$i ++;
		}
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		if($past_this) {
			for($i=0;$i<count($choice_data);$i++)
			{
				$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
				$database->setQuery($query);
				$choice_this = $database->LoadResult();
				$choice_data[$i]->statistic = round(($choice_this*100)/$past_this).'%';
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
			$data['qoption'] = "\t" . $qhtml. "\n";
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
		
		$i = 0;
		while ($i < count($choice_data)) {
			if ( ($choice_data[$i]->text == 'true') || ($choice_data[$i]->text == 'True') ) {
				$choice_data[$i]->text = JText::_('COM_QUIZ_SIMPLE_TRUE');
			} elseif ( ($choice_data[$i]->text == 'false') || ($choice_data[$i]->text == 'False') ) {
				$choice_data[$i]->text = JText::_('COM_QUIZ_SIMPLE_FALSE');
			}
			$i ++;
		}
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		$choice_data[0]->overal = '';
		if($past_this){
			for($i=0; $i<count($choice_data); $i++) {
				$query = "SELECT count(*) FROM #__quiz_r_student_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
				$database->setQuery($query);
				$choice_this = $database->LoadResult();
				$choice_data[$i]->statistic = round(($choice_this*100)/$past_this).'%';
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
				$answer .= $t['c_choice']."; ";
			}				
		}	
		
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

		//$data['pdf']->SetFont('freesans');
		$fontFamily = $data['pdf']->getFontFamily();
		
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
		$database = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_choice WHERE c_question_id = '".$data['question_id']."' ORDER BY ordering";
		$database->SetQuery( $query );
		
		$choices = array();
		$choices = $database->LoadObjectList();
		$choice_true = 1;
		foreach ($choices as $eee) {
			if ((strtolower($eee->c_choice) == "false") && ($eee->c_right == 1)) {
				$choice_true = 0;
			}
		}
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/truefalse/admin/options/truefalse.php");
		$options = ob_get_contents();
		ob_get_clean();
		
		return $options;
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
		
		$query = "SELECT c_id, c_choice FROM #__quiz_t_choice WHERE c_question_id = '".$data['qid']."'";
		$database->setQuery( $query );
		$faltrue = $database->LoadObjectList();		
		$field_order = 0;
		$ans_right = JFactory::getApplication()->input->get('znach');
		$ans_true = 0;$ans_false = 0;
		if ($ans_right) { $ans_true = 1; } else { $ans_false = 1; }
		$new_field = new stdClass;
		if(count($faltrue))
		{
			if($faltrue[0]->c_choice == 'true')
				$new_field->c_id = $faltrue[0]->c_id;
			else
			if($faltrue[1]->c_choice == 'true')
				$new_field->c_id = $faltrue[1]->c_id;
		}
		$new_field->c_question_id = $data['qid'];
		$new_field->c_choice = "true";
		$new_field->c_right = $ans_true;
		$new_field->ordering = 1;
		$new_field->c_quiz_id	= intval($_POST['jform']['c_quiz_id']);
		$database->setQuery("SELECT COUNT(c_id) FROM #__quiz_t_choice WHERE c_id = '".$new_field->c_id."'");
		$exists = $database->loadResult();
		
		if($exists){
			$database->updateObject('#__quiz_t_choice', $new_field, 'c_id');
		} else {
			$database->insertObject('#__quiz_t_choice', $new_field);
		}
		
		$new_field = new stdClass;
		if(count($faltrue))
		{
			if($faltrue[0]->c_choice == 'false')
				$new_field->c_id = $faltrue[0]->c_id;
			else if($faltrue[1]->c_choice == 'false')
				$new_field->c_id = $faltrue[1]->c_id;
		}
		$new_field->c_question_id = $data['qid'];
		$new_field->c_choice = "false";
		$new_field->c_right = $ans_false;
		$new_field->ordering = 2;
		$database->setQuery("SELECT COUNT(c_id) FROM #__quiz_t_choice WHERE c_id = '".$new_field->c_id."'");
		$exists = $database->loadResult();
		
		if($exists){
			$database->updateObject('#__quiz_t_choice', $new_field, 'c_id');
		} else {
			$database->insertObject('#__quiz_t_choice', $new_field);
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
	
	public function onScoreByCategory(&$data){
		
		return true;
	}
}