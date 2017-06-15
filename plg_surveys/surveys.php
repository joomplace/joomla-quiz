<?php
/**
* JoomlaQuiz Surveys Plugin for Joomla
* @version $Id: surveys.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage surveys.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizSurveys extends plgJoomlaquizQuestion
{
	var $name		= 'Surveys';
	var $_name		= 'surveys';
	
	public function onCreateQuestion(&$data) {
		
		$database = JFactory::getDBO();
		$query = "SELECT c_answer FROM #__quiz_r_student_survey WHERE c_sq_id='".$data['sid']."'";
		$database->setQuery($query);
		$answer = $database->LoadResult();	
		
		$data['css_class'] = $data['q_data']->c_image;
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'>' . "\n";
		$qhtml = JoomlaQuiz_template_class::JQ_createQuestion($answer, $data);// 'Survey' - question type
		$data['ret_str'] .= $qhtml . "\n" . "\t" . '</form><div id="div_qoption'.$data['q_data']->c_id.'"></div>]]></quest_data_user>' . "\n";
		
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

		$answer = trim( ($data['answer']));//decode_unicode_url
		if (count($ddd)) {
			if ($answer) {
				$data['is_correct'] = 1;
				$c_quest_score = $ddd[0]->c_point;
			}
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
				$query = "DELETE FROM #__quiz_r_student_survey WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
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

			$query = "INSERT INTO #__quiz_r_student_survey (c_sq_id, c_answer)"
			. "\n VALUES('".$c_sq_id."', ". $database->Quote( $answer ) .")";
			$database->SetQuery($query);
			$database->execute();
		}
		
		$data['score'] = $c_quest_score;
		
		return true;
	}
	
	public function onTotalScore(&$data){
				
		return true;
	}
	
	public function onScoreByCategory(&$data){
		
		return true;
	}
	
	public function onFeedbackQuestion(&$data){
		
		$database = JFactory::getDBO();
		$data['qoption'] = "\t" . '<form  onsubmit=\'javascript: return false;\' name=\'quest_form\'>' . "\n";
		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$sid = $database->loadResult( );			

		$query = "SELECT c_answer FROM #__quiz_r_student_survey AS h "
		. "\n WHERE h.c_sq_id = '".$sid."' ";
		$database->SetQuery( $query );
		$answer = $database->LoadResult();
		
		$query = "SELECT remark FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$remark = $database->loadResult( );
		
		$feedback_data = array();
		$feedback_data['answer'] = $answer;
		
		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($feedback_data, $data);
		$data['qoption'] .= $qhtml.'<br/><strong>'.JText::_('COM_JQ_REMARK').'</strong>'.$remark."<br/>\n" . "\t" . '</form>' . "\n";
		return $data['qoption'];
	}
	
	public function onNextPreviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT a.c_point, a.c_attempts FROM #__quiz_t_question as a WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		
		$answer = trim(urldecode($data['answer']));
		if (count($ddd)) { if ($answer) { $data['is_correct'] = 1; }}
		
		return $data;
	}
	
	public function onReviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$sid = $database->loadResult( );			

		$query = "SELECT c_answer FROM #__quiz_r_student_survey AS h "
		. "\n WHERE h.c_sq_id = '".$sid."' ";
		$database->SetQuery( $query );
		$answer = $database->LoadResult();
		
		$query = "SELECT remark FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$remark = $database->loadResult( );
		
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<form onsubmit=\'javascript: return false;\' name=\'quest_form\'>' . "\n";
		
		$data['css_class'] = $data['q_data']->c_image;
		$qhtml = JoomlaQuiz_template_class::JQ_createReview($answer, $data);
		$data['ret_str'] .= $qhtml.'<br/><strong>'.JText::_('COM_JQ_REMARK').'</strong>'.$remark . "\n" . "\t" . '</form>]]></quest_data_user>' . "\n";
		return $data;		
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "select * from #__quiz_r_student_survey where c_sq_id='".$data['id']."'";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
		$data['info']['c_survey'] = $tmp[0];
		
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
		$str = $data['data']['c_survey']['c_answer'];
		$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
				
		return $data['pdf'];		
	}
	
	public function onSendEmail(&$data){
	
		$data['str'] .= "  ".JText::_('COM_QUIZ_PDF_ANSWER')." ".$data['data']['c_survey']['c_answer']." \n";	
		return $data['str'];
	}
	
	public function onGetStatistic(&$data){
		return true;		
	}

	public function onStatisticContent(&$data){
		return true;		
	}

	//Administration part
	
	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');
		
		$db->setQuery("SELECT `c_image`, `c_manual` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
		$row = $db->loadObject();
		
		$lists = array();
		$c_image = (isset($row->c_image)) ? $row->c_image : '';
		
		$lists['c_image']['input'] = "<input type='text' size='30' name='c_image' value='".$c_image."' />";
		$lists['c_image']['label'] = JText::_('COM_JOOMLAQUIZ_CUSTOMCSS_CLASS');
		
		$c_manual = array();
		$c_manual[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
		$c_manual[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
		$c_manual = JHTML::_('select.genericlist', $c_manual, 'jform[c_manual]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_manual) ? intval( $row->c_manual ) : 0));
		$lists['c_manual']['input'] = $c_manual;
		$lists['c_manual']['label'] = JText::_('COM_JOOMLAQUIZ_MANUALGRADING');
		
		return $lists;
	}
	
	public function onAdminIsFeedback(&$data){
		return false;
	}
	
	public function onAdminIsPoints(&$data){
		return true;
	}
	
	public function onAdminIsPenalty(&$data){
		return true;
	}
	
	public function onAdminIsReportName(){
		return false;
	}
	
	public function onAdminSaveOptions(&$data){
		
		$database = JFactory::getDBO();
		
		$database->setQuery("UPDATE #__quiz_t_question SET `c_image` = '".$_POST['c_image']."', `c_manual` = '".$_POST['jform']['c_manual']."' WHERE c_id = '".$data['qid']."'");
		$database->execute();
	}
	
	public function onGetAdminAddLists(&$data){
		
		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
		
		$query = "SELECT q.c_type, q.c_id,q.c_image, sq.c_stu_quiz_id, q.c_question, c_title_true, c_title_false, sq.c_score, sq.`remark`, sq.reviewed FROM #__quiz_t_question as q, #__quiz_r_student_question as sq"
		. "\n WHERE q.c_id = sq.c_question_id and sq.c_id = '".$data['id']."' AND q.published = 1"
		;
		$database->SetQuery( $query );
		$q_data = $database->LoadObjectList();

		$query = "SELECT * FROM #__quiz_r_student_survey WHERE c_sq_id = '".$data['id']."'";
		$database->SetQuery( $query );
		$answer = $database->LoadObjectList();
		
		if (!count($answer)) { $answer = array(); $answer[0] = new stdClass; $answer[0]->c_answer = ''; }
		$lists['id'] = $data['id'];
		$lists['answer'] = $answer;

		if (isset($_POST['c_score']) && !$q_data[0]->reviewed){
			$remark = JRequest::getVar( 'remark', '', 'post', 'string', JREQUEST_ALLOWRAW );			

			$c_score = (float)$_POST['c_score'];
			$query = "UPDATE #__quiz_r_student_question SET c_score = '".$c_score."', `remark` = ".$database->Quote($remark).", reviewed = 1 WHERE c_id = '".$data['id']."'";
			$database->SetQuery( $query );
			$database->execute();
					
			$query = "SELECT * FROM #__quiz_r_student_quiz WHERE c_id = '".$q_data[0]->c_stu_quiz_id."'";
			$database->SetQuery( $query );
			$student_quiz = $database->LoadObjectList();
			$c_passed = ($student_quiz[0]->c_total_score+($c_score - $q_data[0]->c_score)) >= $student_quiz[0]->c_passing_score ? 1: 0;
					
			$query = "UPDATE #__quiz_r_student_quiz SET `c_total_score` = `c_total_score` + '".($c_score-$q_data[0]->c_score)."', `c_passed` = '".$c_passed."' WHERE c_id = '".$q_data[0]->c_stu_quiz_id."'";
			$database->SetQuery( $query );
			$database->execute();
					
			$q_data[0]->c_score = $c_score;
			$lists['remark'] = $remark;
			$lists['reviewed'] = 1;
			$lists['stu_id'] = $q_data[0]->c_stu_quiz_id;
					
			$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$student_quiz[0]->c_quiz_id."'";
			$database->SetQuery( $query );
			$quiz = $database->LoadObjectList();
					
			if ($quiz[0]->c_email_to) {
						
				$from = $mainframe->getCfg('mailfrom');
				$fromname = $mainframe->getCfg('fromname');
												
				$email_address = '';
				if ($quiz[0]->c_email_to == 2) {							
					$email_address 	= $student_quiz[0]->user_email;
					$name = '';
				} 
						
				if ($quiz[0]->c_email_to != 2 || !$email_address){
					$query = "SELECT u.email, u.username, u.name, q.c_email_to, q.c_language, sq.unique_id "
					. "\n FROM #__quiz_r_student_quiz sq, #__quiz_t_quiz q LEFT JOIN #__users u ON  q.c_user_id = u.id"
					. "\n WHERE sq.c_id = '".$q_data[0]->c_stu_quiz_id."' AND sq.c_quiz_id = q.c_id";
					$database->setQuery( $query );
					$rows = $database->loadObjectList();
					$email_address = $rows[0]->email;
					$name = $rows[0]->name;
				}
						
				if ($email_address){
					$subject = JText::_('COM_JOOMLAQUIZ_USER_RESULT_EMAIL_SUBJECT') ;
					$message = sprintf(JText::_('COM_JOOMLAQUIZ_USER_RESULT_EMAIL'), $name, $quiz[0]->c_title, JURI::root().'index.php?option=com_joomlaquiz&view=results&layout=stu_report&cid='.$q_data[0]->c_stu_quiz_id, JURI::root());
					
					$jmail = new JMail();
					$jmail->sendMail($from, $fromname, trim($email_address), $subject, $message, 1, NULL, NULL, NULL, NULL, NULL); 
				}
			}
		}
		
		$lists['c_score'] = $q_data[0]->c_score;
		
		return $lists;		
	}
	
	public function onGetAdminReportsHTML(&$data){
		$rows = $data['lists']['answer'];
		
		ob_start();
		?>
		<table class="adminlist">
			<tr>
				<th class="title"><?php echo JText::_('COM_JOOMLAQUIZ_USER_ANSWER');?></th>
			</tr>
					
			<tr class="row1">
				<td align="left">
					<?php echo $rows[0]->c_answer; ?>
				</td>
			</tr>
			<?php if ($data['lists']['reviewed']) {?>
			<tr class="row1">
				<td align="left">
					<strong><?php echo JText::_('COM_JOOMLAQUIZ_POINTS');?></strong> <?php echo $data['lists']['c_score'];?>							
				</td>
			</tr>
			<tr class="row1">
				<td align="left">
					<strong><?php echo JText::_('COM_JOOMLAQUIZ_REMARK');?></strong> <br /><?php echo stripslashes($data['lists']['remark']) ?>								
				</td>
			</tr>
			<?php } else {?>
			<tr class="row1">
				<td align="left">
					<strong><?php echo JText::_('COM_JOOMLAQUIZ_POINTS');?></strong> <input name="c_score" value="<?php echo $data['lists']['c_score'];?>" type="text" class="inputbox" /> &nbsp; <input type="button" value="Apply" onclick="javascript: document.adminForm.id.value='<?php echo $data['lists']['id']; ?>'; document.adminForm.stu_id.value='<?php echo $data['lists']['qid']; ?>'; submitbutton('quest_reportA');" />			
				</td>
			</tr>
			<tr class="row1">
				<td align="left">
					<strong><?php echo JText::_('COM_JOOMLAQUIZ_REMARK');?></strong> <br />
						<?php
						$editor = JFactory::getEditor();
						// parameters : areaname, content, hidden field, width, height, rows, cols
						echo $editor->display( 'remark', $data['lists']['remark'], 'remark', '100%;', '250', '75', '40' ) ; 
						?>							
				</td>
			</tr>
			<?php }?>	
		</table>
		<input type="hidden" name="stu_id" value="<?php echo $data['lists']['stu_id'];?>" />
		<?php
		
		$content = ob_get_contents();
		ob_clean();
		return $content;
	}
	
	public function onGetAdminQuestionData(&$data){
			return;
	}
	
	public function onGetAdminStatistic(&$data){
		return;
	}
	
	public function onGetAdminCsvData(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT `b`.`c_answer` FROM `#__quiz_r_student_question` AS `a`, `#__quiz_r_student_survey` AS `b` WHERE `a`.`c_stu_quiz_id` = '".$data['result']->c_id."' AND `a`.`c_question_id` = '".$data['question']->c_id."' AND `a`.`c_id` = `b`.`c_sq_id` ";
		$database->setQuery( $query );
		$data['answer'] = $database->loadResult();
			
		return $data['answer'];	
	}
}