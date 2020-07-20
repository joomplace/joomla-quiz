<?php
/**
* JoomlaQuiz Jigsaw Puzzle Plugin for Joomla
* @version $Id: puzzle.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage puzzle.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizPuzzle extends plgJoomlaquizQuestion
{
	var $name		= 'Puzzle';
	var $_name		= 'puzzle';
	
	public function onCreateQuestion(&$data)
    {
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div style="width:100%;clear:both;"><form onsubmit=\'javascript: return false;\' name=\'quest_form\' id=\'quest_form\'></form></div>]]></quest_data_user>' . "\n";
		
		$c_width = 200;
		$c_height = 200;
		
		if(file_exists(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image)){
			$img_data = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image);
			$c_width = $img_data[0] * 2 + 20;
			$c_height = $img_data[1] + 150;
		}

        $data['ret_add_script'] .= "var puzzleWidth = document.documentElement.clientWidth * 0.8 < ".$c_width." ? document.documentElement.clientWidth * 0.8 : ".$c_width.";
            var puzzleHeight = Math.ceil(puzzleWidth / (". $c_width / $c_height ."));
            options = {handler:'iframe', size:{x: puzzleWidth, y: puzzleHeight}, closable: true, closeBtn: false};";
		
		return $data;
	}
	
	public function onAjaxPlugin($data)
    {
		$app = JFactory::getApplication();
		$input = $app->input;
		$database = JFactory::getDBO();
		
		switch($data['plg_task']) {
			case 'show':
				require_once(JPATH_SITE.'/plugins/joomlaquiz/puzzle/html/puzzle.php');
				$app->close();
			    break;
			case 'getdata':
				$qid = $input->getInt('qid', 0);
				$stu_quiz_id = $input->getInt('stu_quiz_id', 0);
				
				$database->setQuery("SELECT * FROM #__quiz_t_question WHERE `c_id` = '".$qid."'");
				$q_data = $database->loadObject();
				
				$database->setQuery("SELECT `c_pieces` FROM #__quiz_t_puzzle WHERE `c_question_id` = '".$qid."'");
				$pieces = $database->loadResult();
				
				$database->setQuery("SELECT `c_id`, `c_attempts` FROM `#__quiz_r_student_question` WHERE `c_stu_quiz_id` = '".$stu_quiz_id."' AND `c_question_id` = '".$qid."'");
				$student_data = $database->loadObject();
				
				$xml_attempts = '<c_attempts>1</c_attempts>';
				if($student_data->c_id){
					$q_data->c_attempts = ($q_data->c_attempts) ? $q_data->c_attempts : 1;
					if((int)$student_data->c_attempts < (int)$q_data->c_attempts){
						$database->setQuery("UPDATE `#__quiz_r_student_question` SET `c_attempts` = `c_attempts` + 1 WHERE `c_stu_quiz_id` = '".$stu_quiz_id."' AND `c_question_id` = '".$qid."'");
						$database->query();
					} else {
						$xml_attempts = '<c_attempts>0</c_attempts>';
					}
				}
				
				@header ('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
				@header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				@header ('Cache-Control: no-cache, must-revalidate');
				@header ('Pragma: no-cache');
				@header ('Content-Type: text/xml; charset="utf-8"');
				echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
				echo '<response>' . "\n";
				echo '<c_quiz_id>'.$q_data->c_quiz_id.'</c_quiz_id>' . "\n";
				echo '<c_quest_text><![CDATA['.$q_data->c_question.']]></c_quest_text>' . "\n";
				echo '<c_image><![CDATA['.$q_data->c_image.']]></c_image>' . "\n";
				echo '<c_point>'.$q_data->c_point.'</c_point>' . "\n";
				echo '<quest_time>'.(($q_data->c_timer) ? $q_data->c_timer : $pieces*1.5*10).'</quest_time>' . "\n";
				echo '<puzzle_difficulty>'.$pieces.'</puzzle_difficulty>' . "\n";
				echo $xml_attempts. "\n";
				echo '</response>' . "\n";

                $app->close();
			    break;
			case 'addpoints':
				$stu_quiz_id = $input->getInt('stu_quiz_id', 0);
				$quest_id = $input->getInt('quest_id', 0);
				$quiz_id = $input->getInt('quiz_id', 0);
				$action = $input->get('action', '');
				$piece = $input->getInt('piece', 0);
				$ltime = $input->getInt('ltime', 0);

				$database->setQuery("SELECT `c_point`, `c_timer` FROM #__quiz_t_question WHERE `c_id` = '".$quest_id."' AND `c_quiz_id` = '".$quiz_id."'");
				$q_data = $database->loadAssoc();

                $database->setQuery("SELECT `c_pieces` FROM #__quiz_t_puzzle WHERE `c_question_id` = '".$quest_id."'");
                $pieces = $database->loadResult();

				$point = (!$action) ? $q_data['c_point'] : 0;
				$all_time = ($q_data['c_timer']) ? $q_data['c_timer'] : $pieces*1.5*10;
				
				$database->setQuery("SELECT COUNT(c_id) FROM #__quiz_r_student_question WHERE `c_stu_quiz_id` = '".$stu_quiz_id."' AND `c_question_id` = '".$quest_id."'");
				$exists = $database->loadResult();
				if(!$exists){
					$database->setQuery("INSERT INTO #__quiz_r_student_question (`c_id`, `c_stu_quiz_id`, `c_question_id`, `c_score`, `c_attempts`, `is_correct`, `remark`, `reviewed`) VALUES ('', '".$stu_quiz_id."', '".$quest_id."', '".$point."', 1, 1, '', 0)");
					$database->execute();
					$sid = $database->insertid();
				} else {
					$database->setQuery("UPDATE #__quiz_r_student_question SET c_score = c_score + ".$point." WHERE `c_stu_quiz_id` = '".$stu_quiz_id."' AND `c_question_id` = '".$quest_id."'");
					$database->execute();
					
					$database->setQuery("SELECT `c_id` FROM #__quiz_r_student_question WHERE `c_stu_quiz_id` = '".$stu_quiz_id."' AND `c_question_id` = '".$quest_id."'");
					$sid = $database->loadResult();
				}
				
				if(!$action){
					$c_elapsed_time = $all_time - $ltime;
					$database->setQuery("INSERT INTO `#__quiz_r_student_puzzle` (`c_id`, `c_sq_id`, `c_piece`, `c_elapsed_time`) VALUES ('', '".$sid."', '".$piece."', '".$c_elapsed_time."')");
					$database->execute();
				}
				
				$data['score'] = $point;

				$dispatcher	= JDispatcher::getInstance();
				JPluginHelper::importPlugin('system');
				$dispatcher->trigger('onJQuizAnswerSubmitted', array (&$data));
				
				$app->close();
			    break;
		}
		
	}
	
	public function onTotalScore(&$data){
		
		$data['max_score'] = 0;
		$database = JFactory::getDBO();
		$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 11 AND c_id IN (".$data['qch_ids'].")";
		$database->SetQuery( $query );
		$qch_ids_type_11 = $database->loadColumn();

		if(!empty($qch_ids_type_11)) {
			foreach($qch_ids_type_11 as $c_question_id){
				
				$database->setQuery("SELECT `c_pieces` FROM #__quiz_t_puzzle WHERE `c_question_id` = '".$c_question_id."'");
				$c_pieces = $database->loadResult();

				$database->setQuery("SELECT `c_point` FROM #__quiz_t_question WHERE `c_id` = '".$c_question_id."'");
				$c_point = $database->loadResult();
		
				$score = ($c_pieces * $c_pieces) * $c_point;
				$data['max_score'] += $score;
			}
		}
		
		return true;
	}
	
	public function onScoreByCategory(&$data){
		
		$database = JFactory::getDBO();
		$database->setQuery("SELECT `c_pieces` FROM #__quiz_t_puzzle WHERE `c_question_id` = '".$data['score_bycat']->c_id."'");
		$c_pieces = $database->loadResult();

		$database->setQuery("SELECT `c_point` FROM #__quiz_t_question WHERE `c_id` = '".$data['score_bycat']->c_id."'");
		$c_point = $database->loadResult();
		
		$data['score'] = ($c_pieces * $c_pieces) * $c_point;
		
		return true;
	}
	
	public function onFeedbackQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id FROM #__quiz_r_student_question WHERE c_stu_quiz_id= '".$data['stu_quiz_id']."' AND `c_question_id` = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$c_id = $database->LoadResult();
		
		$query = "SELECT `c_piece` FROM #__quiz_r_student_puzzle WHERE c_sq_id= '".$c_id."'";
		$database->SetQuery( $query );
		$puzzle_data = $database->loadColumn();
		
		$query = "SELECT `c_pieces` FROM #__quiz_t_puzzle WHERE c_question_id= '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$difficulty = $database->LoadResult();
		
		$qdata = array();
		$query = "SELECT SUM(`c_elapsed_time`) FROM #__quiz_r_student_puzzle WHERE c_sq_id= '".$c_id."'";
		$database->SetQuery( $query );
		$qdata[0]['elapsed_time'] = $database->LoadResult();
		$qdata[0]['score'] = $data['score'];
		$qdata[0]['q_data'] = $data['q_data'];
		$qdata[0]['difficulty'] = $difficulty;
		
		$data['qdata'] = $qdata;
		
		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($puzzle_data, $data);
		
		if(preg_match("/pretty_green/", $data['cur_template'])){
			$data['qoption'] = "\t" . $qhtml. "\n";
		} else {
			$data['qoption'] = "\t" . '<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>'. $qhtml .'</form></div>' . "\n";
		}
		return $data['qoption'];
	}
	
	public function onReviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id FROM #__quiz_r_student_question WHERE c_stu_quiz_id= '".$data['stu_quiz_id']."' AND `c_question_id` = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$c_id = $database->LoadResult();
	
		$query = "SELECT `c_piece` FROM #__quiz_r_student_puzzle WHERE c_sq_id= '".$c_id."'";
		$database->SetQuery( $query );
		$puzzle_data = $database->LoadColumn();
	
		$query = "SELECT `c_pieces` FROM #__quiz_t_puzzle WHERE c_question_id= '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$difficulty = $database->LoadResult();
	
		$qdata = array();
		$qdata[0]['q_data'] = $data['q_data'];
		$qdata[0]['difficulty'] = $difficulty;
		
		$data['qdata'] = $qdata;
		$qhtml = JoomlaQuiz_template_class::JQ_createReview($puzzle_data, $data);
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>'. $qhtml .'</form></div>]]></quest_data_user>' . "\n";		
		return $data;		
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT `c_score` FROM #__quiz_r_student_question"
		. "\n WHERE c_question_id = '".$data['qid']."' AND c_id = '".$data['id']."'";
		$database->SetQuery( $query );
		$data['info']['c_score'] = $database->LoadResult();
		
		$database->setQuery("SELECT `c_pieces` FROM #__quiz_t_puzzle WHERE `c_question_id` = '".$data['qid']."'");
		$c_pieces = $database->loadResult();

		$database->setQuery("SELECT `c_point` FROM #__quiz_t_question WHERE `c_id` = '".$data['qid']."'");
		$c_point = $database->loadResult();
		$data['info']['c_point'] = ($c_pieces * $c_pieces) * $c_point;
		return true;
	}
	
	public function onGetPdf(&$data){

		//$data['pdf']->SetFont('freesans');
		$fontFamily = $data['pdf']->getFontFamily();
		
		if($data['data']['c_score']) 
			$answer = $data['data']['c_score'];
		else 
			$answer = 0;
					
		$data['pdf']->Ln();
		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', true);
		$str = "  Scores:";
		$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);

		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', false);
		$str = $answer;
		$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
				
		return $data['pdf'];		
	}
	
	public function onSendEmail(&$data){
		
		if($data['data']['c_score']) $answer = $data['data']['c_score'];
		else $answer = 0;
		$data['str'] .= "  Scores: ".$answer."\n";		
		return $data['str'];
	}
	
	//Administration part
	
	public function onCreateDatabase(&$data){
		
		$db = JFactory::getDBO();
		
		$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 11");
		$exists = $db->loadResult();
		
		if (!$exists) {
			$db->setQuery("INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (11, 'Jigsaw Puzzle', 'puzzle');");
			$db->query();
			
			$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_t_puzzle` (
			  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `c_question_id` int(11) NOT NULL,
			  `c_pieces` int(11) NOT NULL DEFAULT '4',
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;");
			$db->query();
			
			$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_r_student_puzzle` (
			  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `c_sq_id` int(11) unsigned NOT NULL,
			  `c_piece` int(10) NOT NULL,
			  `c_elapsed_time` int(10) NOT NULL,
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;");
			$db->query();
			
			$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 12 OR `c_id` = 13");
			$pzl_exists = $db->loadResult();
			
			if(!$pzl_exists){
				$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_width` INT( 10 ) NOT NULL DEFAULT '150';");
				$db->query();
			}
			
			$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 12");
			$pzl_exists = $db->loadResult();
			
			if(!$pzl_exists){
				$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_timer` INT( 10 ) NOT NULL;");
				$db->query();
			}
		}
	}

	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');
		
		$db->setQuery("SELECT `c_width`, `c_timer` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
		$row = $db->loadObject();
		
		$db->setQuery("SELECT `c_pieces`  FROM #__quiz_t_puzzle WHERE `c_question_id` = '".$c_id."'");
		$c_pieces = $db->loadResult();
		
		$lists = array();

        $lists['c_pieces']['input'] = '<input class="required" type="text" size="35" name="c_pieces" value="'.(isset($c_pieces) ? $c_pieces : '').'">';
        $lists['c_pieces']['label'] = 'Puzzle Difficulty (In pieces): *';
        $lists['c_pieces']['label_title'] = 'Puzzle Difficulty';
        $lists['c_pieces']['label_description'] = 'Define the number of puzzle pieces on one side (the value will be squared)';

        $lists['c_width']['input'] = '<input type="text" size="35" name="c_width" value="'.(isset($row->c_width) ? $row->c_width : '').'">';
		$lists['c_width']['label'] = 'Image Width:';
		
		$lists['c_timer']['input'] = '<input type="text" size="35" name="c_timer" value="'.(isset($row->c_timer) ? $row->c_timer : '').'">';
		$lists['c_timer']['label'] = 'Time Limit:';
		
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
		require_once(JPATH_SITE."/plugins/joomlaquiz/puzzle/admin/tabs/puzzle.php");
		$tab_content1 = ob_get_contents();
		ob_get_clean();
		
		$tabs[0]['label'] = '<li><a href="#question-image" data-toggle="tab">'.JText::_('COM_JOOMLAQUIZ_QUESTION_IMAGE').'</a></li>';
		$tabs[0]['content'] = $tab_content1;
		
		return $tabs;
	}
	
	public function onAdminSaveOptions(&$data){
        $jinput = JFactory::getApplication()->input;

        if($jinput->get('task') == 'copy_quizzes') {
            return true;
        }

		$database = JFactory::getDBO();
        $database->setQuery("UPDATE #__quiz_t_question SET `c_image` = '".$jinput->get('c_image','')."', `c_width` = '".$jinput->get('c_width','', 'INT')."', `c_timer` = '".$jinput->get('c_timer','', 'INT')."' WHERE c_id = '".$data['qid']."'");
		$database->execute();
		
		$query = "SELECT c_id FROM #__quiz_t_puzzle WHERE c_question_id = '".$data['qid']."'";
		$database->setQuery( $query );
		$cid = $database->LoadResult();
		
		$new_field = new stdClass;
		$new_field->c_id = ($cid) ? $cid : '';
		$new_field->c_question_id = $data['qid'];
		$new_field->c_pieces = $jinput->get('c_pieces',4,'INT');
		
		$database->setQuery("SELECT COUNT(c_id) FROM #__quiz_t_puzzle WHERE c_id = '".$new_field->c_id."'");
		$exists = $database->loadResult();
		if($exists){
			$database->updateObject('#__quiz_t_puzzle', $new_field, 'c_id');
		} else {
			$database->insertObject('#__quiz_t_puzzle', $new_field);
			$new_field->c_id = $database->insertid();
		}
	}
	
	public function onGetAdminCsvData(&$data){
		
		$database = JFactory::getDBO();
		
		$data['answer'] = '';
		$query = "SELECT `a`.`c_score` FROM `#__quiz_r_student_question` AS `a` WHERE `a`.`c_stu_quiz_id` = '{$data['result']->c_id}' AND `a`.`c_question_id` = '{$data['question']->c_id}'";
		$database->setQuery( $query );
		$score = $database->loadResult();
		if ($score != null)
			$data['answer'] = 'Score - '.$score;
			
		return $data['answer'];	
	}
}