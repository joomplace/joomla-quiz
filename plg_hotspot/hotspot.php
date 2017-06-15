<?php
/**
* JoomlaQuiz Hotspot Plugin for Joomla
* @version $Id: hotspot.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage hotspot.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizHotspot extends plgJoomlaquizQuestion
{
	var $name		= 'Hotspot';
	var $_name		= 'hotspot';
	
	public function onCreateQuestion(&$data) {
		
		$database = JFactory::getDBO();
		$query = "SELECT c_select_x, c_select_y FROM `#__quiz_r_student_hotspot` WHERE `c_sq_id` ='".$data['sid']."'";
		$database->setQuery($query);
		$hotspot = $database->LoadAssoc();			
		
		$database->setQuery("SELECT `c_paths` FROM `#__quiz_t_ext_hotspot` WHERE `c_question_id` = '".$data['q_data']->c_id."'");
		$hs_data = $database->loadResult();
		$data['hs_data_array'] = json_decode($hs_data);
		
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div id="foo"></div>';
		$data['ret_str'] .= ']]></quest_data_user>' . "\n";
		$data['ret_add_script'] = JoomlaQuiz_template_class::JQ_createQuestion($hotspot, $data);	

		return $data;
	}
	
	public function onSaveQuestion(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT a.c_point, a.c_attempts FROM `#__quiz_t_question` as a WHERE a.`c_id` = '".$data['quest_id']."' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();

		$c_quest_score = 0;
		$data['c_all_attempts'] = 1;
		$data['is_avail'] = 1;
		if (count($ddd)) {
			$ans_array = explode(',', $data['answer']);

			if ($ans_array[2]) {
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
				$query = "DELETE FROM #__quiz_r_student_hotspot WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
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
			$query = "INSERT INTO #__quiz_r_student_hotspot (c_sq_id, c_select_x, c_select_y)"
				. "\n VALUES('".$c_sq_id."', '".(isset($ans_array[0])?$ans_array[0]:0)."', '".(isset($ans_array[1])?$ans_array[1]:0)."')";
			$database->SetQuery($query);
			$database->execute();
		}

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
		$query = "SELECT * FROM `#__quiz_t_ext_hotspot` WHERE `c_question_id` = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$hotspot_data = $database->LoadObjectList();
		
		$query = "SELECT `c_id` FROM `#__quiz_r_student_question` AS sq WHERE `c_stu_quiz_id` = '".$data['stu_quiz_id']."' AND `c_question_id` = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$sid = $database->loadResult( );			

		$query = "SELECT * FROM `#__quiz_r_student_hotspot` AS h "
		. "\n WHERE h.c_sq_id = '".$sid."' ";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
		
		$paths_array = json_decode($hotspot_data[0]->c_paths);
										
		$query = "SELECT count(*) FROM `#__quiz_r_student_question` WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		$query = "SELECT count(*) FROM `#__quiz_r_student_question` WHERE `c_question_id` = '".$data['q_data']->c_id."' AND `c_score`!=0";
		$database->setQuery($query);
		$right_this = $database->LoadResult();
		$rht_proc = round($right_this*100/$past_this);
		
		$qdata = array();
		
		list($width, $height, $type, $attr) = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image);
		$qdata['quest_id'] = $data['q_data']->c_id;
		$qdata['c_paths'] = $paths_array;
		$qdata['c_select_x'] = @$tmp[0]['c_select_x'];
		$qdata['c_select_y'] = @$tmp[0]['c_select_y'];
		$qdata['c_image'] = $data['q_data']->c_image;
		$qdata['width'] = $width;
		$qdata['height'] = $height;
		
		$qdata['past_this'] = $past_this;
		$qdata['rht_proc'] = $rht_proc;
		$qdata['score'] = $data['score'];
		
		$feedback_data = array();
		$feedback_data['qdata'] = $qdata;
		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($feedback_data, $data);
		
		$data['qoption'] = "\t" . '<div>'.$qhtml.'</div>'. "\n";
		return $data['qoption'];
	}
	
	public function onNextPreviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT a.`c_point`, a.`c_attempts` FROM `#__quiz_t_question` as a WHERE a.c_id = '".$data['quest_id']."' AND a.`published` = 1";
        $database->SetQuery( $query );
        $ddd = $database->LoadObjectList();
		if (count($ddd)) {
			$ans_array = explode(',',$data['answer']);
            if ($ans_array[2]) {
              $data['is_correct'] = 1;
            }
		}

		return $data;
	}
	
	public function onReviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$stu_quiz_id = JFactory::getApplication()->input->get('stu_quiz_id');
						
		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$stu_quiz_id."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$sid = $database->loadResult( );
		
		$query = "SELECT c_select_x, c_select_y FROM #__quiz_r_student_hotspot WHERE c_sq_id='".$sid."'";
		$database->setQuery($query);
		$hotspot = $database->LoadAssoc();
		
		$query = "SELECT * FROM #__quiz_t_ext_hotspot WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$hotspot_data = $database->LoadObjectList();
		
		$paths_array = json_decode($hotspot_data[0]->c_paths);
							
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['q_data']->c_id."' AND c_score!=0";
		$database->setQuery($query);
		$right_this = $database->LoadResult();
		$rht_proc = round($right_this*100/$past_this);
		
		$qdata = array();
		
		list($width, $height, $type, $attr) = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image);
		$qdata['c_image'] = $data['q_data']->c_image;
		$qdata['width'] = $width;
		$qdata['height'] = $height;
		$qdata['quest_id'] = $data['q_data']->c_id;
		$qdata['c_paths'] = $paths_array;
		
		$qdata['past_this'] = $past_this;
		$qdata['rht_proc'] = $rht_proc;
		
		$qdata['c_select_x'] = $hotspot['c_select_x'];
		$qdata['c_select_y'] = $hotspot['c_select_y'];
		
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div>';
		$data['ret_str'] .= JoomlaQuiz_template_class::JQ_createReview($qdata, $data);					
		$data['ret_str'] .= '</div>]]></quest_data_user>' . "\n";
		return $data;		
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_question AS q, #__quiz_t_hotspot AS h"
		. "\n WHERE q.c_id = '".$data['qid']."' AND q.c_id = h.c_question_id AND q.published = 1";
		$database->SetQuery( $query );
		$data['info']['c_hotspot'] = $database->LoadRow();
		$query = "select * from #__quiz_r_student_hotspot where c_sq_id='".$data['id']."'";
		$database->SetQuery( $query );
		$tmp = $database->LoadRow();
		while(list($key,$value) = each($tmp)) {
			$data['info']['c_hotspot'][$key] = $value;
		}
		
		return true;
	}
	
	public function onGetPdf(&$data){

		//$data['pdf']->SetFont('freesans');
		$fontFamily = $data['pdf']->getFontFamily();
		
		if($data['data']['is_correct']) 
			$answer = JText::_('COM_QUIZ_PDF_RIGHT');
		else 
			$answer = ' '.JText::_('COM_QUIZ_PDF_WRONG');
					
		$data['pdf']->Ln();
		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', true);
		$str = "  ".JText::_('COM_QUIZ_PDF_ANSWER');
		$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);

		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', false);
		$str = $answer;
		$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
				
		return $data['pdf'];		
	}
	
	public function onSendEmail(&$data){
	
		if($data['c_score']) $answer = JText::_('COM_QUIZ_PDF_RIGHT');
		else $answer = ' '.JText::_('COM_QUIZ_PDF_WRONG');
		$data['str'] .= "  ".JText::_('COM_QUIZ_PDF_ANSWER')." ".$answer."\n";		
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
	
	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');
		
		$lists = array();
		
		return $lists;
	}
	
	public function onGetAdminTabs(&$data){
		
		$db = JFactory::getDBO();
		$tabs = array();
		
		$query = "SELECT `c_image`, `c_id` FROM #__quiz_t_question WHERE c_id = '".$data['question_id']."'";
		$db->SetQuery( $query );
		$row = $db->LoadObject();
		
		$lists = array();
		$directory = '/images/joomlaquiz/images';

		$javascript = "onchange=\"javascript:if (document.adminForm.c_image.options[document.adminForm.c_image.selectedIndex].value!='') {"
		. " document.imagelib.src='..$directory/' + document.adminForm.c_image.options[document.adminForm.c_image.selectedIndex].value; } else {"
		. " document.imagelib.src='".JURI::root()."administrator/components/com_joomlaquiz/assets/images/blank.png'}\""; 
		$lists['images'] = JHTML::_('list.images', 'c_image', ((isset($row->c_image)) ? $row->c_image : ''), $javascript, $directory, 'bmp|gif|jpg|png|BMP|GIF|JPG|PNG');
		
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/hotspot/admin/tabs/hotspot.php");
		$tab_content1 = ob_get_contents();
		ob_get_clean();
		
		$tabs[0]['label'] = '<li><a href="#question-image" data-toggle="tab">'.JText::_('COM_JOOMLAQUIZ_QUESTION_IMAGE').'</a></li>';
		$tabs[0]['content'] = $tab_content1;
		
		return $tabs;
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
		return false;
	}
	
	public function onAdminSaveOptions(&$data){
		
		$database = JFactory::getDBO();
		
		$database->setQuery("UPDATE #__quiz_t_question SET `c_image` = '".$_POST['c_image']."' WHERE c_id = '".$data['qid']."'");
		$database->execute();
	}
	
	public function onGetAdminAddLists(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT * FROM #__quiz_r_student_hotspot WHERE c_sq_id = '".$data['id']."'";
		$database->SetQuery( $query );
		$answer = $database->LoadObjectList();
		if (!count($answer)) { $answer = array(); $answer[0]->c_select_x = 0; $answer[0]->c_select_y = 0; }
		
		$lists['answer'] = $answer;
		$lists['id'] = $data['id'];
		
		$query = "SELECT * FROM #__quiz_t_hotspot WHERE c_question_id = '".$data['q_id']."'";
		$database->SetQuery( $query );
		$hotspot = $database->LoadObjectList();
		if (!count($hotspot)) { $hotspot = array(); $hotspot[0]->c_start_x = 0; $hotspot[0]->c_start_y = 0; $hotspot[0]->c_width = 0; $hotspot[0]->c_height = 0; }
				
		$lists['hotspot'] = $hotspot[0];
		
		return $lists;
	}
	
	public function onGetAdminReportsHTML(&$data){
		$rows = $data['lists']['answer'];
		
		ob_start();
		?>
		<table><tr><td align="center">
			<div style="text-align:left;">
			<div id="div_hotspot_rec" style="background-color:#FFFFFF; z-index:1001; <?php if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) { echo "filter:alpha(opacity=50);";}?> -moz-opacity:.50; opacity:.50; border:1px solid #000000; position:relative; left:<?php echo $data['lists']['hotspot']->c_start_x?>px; top:<?php echo ($data['lists']['hotspot']->c_start_y+$data['lists']['hotspot']->c_height + 12)?>px; width:<?php echo $data['lists']['hotspot']->c_width?>px; height:<?php echo $data['lists']['hotspot']->c_height?>px; ">
			<img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/blank.png" border="0" width="1" height="1">
			</div>
			
			<div style='position:relative; z-index:1000; top:<?php echo ($rows[0]->c_select_y + 6)?>px; left:<?php echo ($rows[0]->c_select_x - 6)?>px'>
				<img src='<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/hs_round.png' width='12' height='12'>
			</div>
				<img style='position:relative; z-index:999;' src='<?php echo JURI::root();?>images/joomlaquiz/images/<?php echo $data['lists']['image']?>'>
			</div></td></tr></table>
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
		$score = $database->loadResult();
		if ($score != null)
			$data['answer'] = 'Score - '.$score;
			
		return $data['answer'];	
	}
}