<?php
/**
* JoomlaQuiz Dalliclick Plugin for Joomla
* @version $Id: dalliclick.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage dalliclick.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizDalliclick extends plgJoomlaquizQuestion
{
	var $name		= 'Dalliclick';
	var $_name		= 'dalliclick';
	
	public function onCreateQuestion(&$data)
    {
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, '0' as c_right, '0' as c_review FROM #__quiz_t_dalliclick WHERE c_question_id = '".$data['q_data']->c_id."'";
		if ($data['qrandom']) {
            $query .= "\n ORDER BY rand()";
        } else {
            $query .= "\n ORDER BY ordering";
        }
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$width = 184; //based on the screen width 320px
		
		$img_data = array();
		if(file_exists(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image)) {
			$img_data = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image);
		}

		if(!empty($img_data[1])) {
            $ratio = $img_data[0]/$img_data[1];
			if($img_data[0] >= $width) {
                $w = $img_data[0];
            } else {
                $w = $width;
            }
            $h = round($w / $ratio);
		} else {
            $ratio = 1;
            $w = $width;
            $h = $w * $ratio;
        }

		$dc_img = base64_encode(JURI::root()."images/joomlaquiz/images/".$data['q_data']->c_image);

        $qhtml = ($data['q_data']->c_image) ?
            "<div class='dc_layout'>
                <div class='dc_cover_container'>
                    <div class='cover' style='width:".$w."px; height:".$h."px;'>
                        <div class='pause'><!--x--></div>
                        <canvas id='dc_image' class='dc_image' width='".$w."' height='".$h."'></canvas>
                        <input type='hidden' value='".$dc_img."' id='imgSrc' />
                    </div>
                </div> \n"
            : "";

		$qhtml .= JoomlaQuiz_template_class::JQ_createQuestion($choice_data, $data);
		$qhtml .= "</div>";
		$qhtml .= "<div class='dc_time'>".JText::_('COM_QUIZ_RES_MES_TIME')."</div>";

		if ($data['q_data']->c_layout == 1) {
		    $data['ret_add_script'] .= "jq_jQuery('.jq_question_text_cont').css('width', 'auto');";
        }

		$data['ret_add_script'] .= "";

		if ($data['q_data']->c_qform && JoomlaquizHelper::jq_strpos($data['ret_add'], '{x}') !== false) {
			$data['ret_add'] = '<form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'>'.str_replace('{x}', $qhtml, $data['ret_add']).'</form>';
			$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div style="width:100%;clear:both;" id="div_qoption'.$data['q_data']->c_id.'"><!-- x --></div>]]></quest_data_user>' . "\n";
		} else {
			$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div style="width:100%;clear:both;" id="div_qoption'.$data['q_data']->c_id.'"><form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'>'.$qhtml.'</form></div>]]></quest_data_user>' . "\n";
		}

        $data['ret_str'] .= "\t" . '<img_ratio>'.$ratio.'</img_ratio>' . "\n";
		$data['ret_str'] .= "\t" . '<img_width>'.$w.'</img_width>' . "\n";
		$data['ret_str'] .= "\t" . '<img_height>'.$h.'</img_height>' . "\n";
		if(!$data['q_data']->sq_delayed) {
            $data['q_data']->sq_delayed = 1;
        }
		$data['ret_str'] .= "\t" . '<sq_delayed>'.$data['q_data']->sq_delayed.'</sq_delayed>' . "\n";

		return $data['ret_str'];
	}
	
	public function onPointsForAnswer(&$data){
		$database = JFactory::getDBO();
		
		$query = "SELECT SUM(a_point) FROM #__quiz_t_dalliclick WHERE c_question_id = '".$data['q_data']->c_id."' AND c_right = 1";
		$database->SetQuery( $query );
		$tmp_pointz = $database->LoadResult();
		if(floatval($tmp_pointz))
			$data['q_data']->c_point = $data['q_data']->c_point.' - '.(floatval($tmp_pointz) + $data['q_data']->c_point);
		
		return $data['q_data'];
	}
	
	public function onAjaxPlugin($data){
		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		switch($data['plg_task']){
			case 'check_right':
			
				$quiz_id = JFactory::getApplication()->input->get('quiz_id');
				$quest_id = JFactory::getApplication()->input->get('quest_id');
				$answer = JFactory::getApplication()->input->get('check_id');
				$o_sq = JFactory::getApplication()->input->get('o_sq');
				$stu_quiz_id = JFactory::getApplication()->input->get('stu_quiz_id');
				$elapsed_time = JFactory::getApplication()->input->get('elapsed_time');
				
				$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__quiz_t_question as a, #__quiz_t_dalliclick as b WHERE a.c_id = '".$quest_id."' and b.c_question_id = a.c_id and b.c_right = '1' AND a.published = 1";
				$database->SetQuery( $query );
				$ddd = $database->LoadObjectList();
				$query = "SELECT b.a_point FROM #__quiz_t_question as a, #__quiz_t_dalliclick as b WHERE a.c_id = '".$quest_id."' and b.c_question_id = a.c_id and b.c_id = '".$answer."'  AND a.published = 1";
				$database->SetQuery( $query );
				$c_quest_score = $database->LoadResult();
				
				$database->setQuery("SELECT `c_penalty` FROM `#__quiz_t_question` WHERE `c_id` = '".$quest_id."' AND `c_quiz_id` = '".$quiz_id."'");
				$c_penalty = $database->loadResult();
				
				$c_all_attempts = 1;
				$is_avail = 1;
				$is_correct = 0;
				
				if (!empty($ddd)) {
					if ($ddd[0]->c_id == $answer) {
						$c_quest_score = $c_quest_score - $o_sq * $c_penalty;
						$is_correct = 1;
					}
					
					if ($ddd[0]->c_attempts) {
								$c_all_attempts = $ddd[0]->c_attempts;
					}
				}
				
				if($o_sq == 25){
					$c_quest_score = 0;
				}
				
				$query = "SELECT b.c_incorrect_feed FROM #__quiz_t_question as a, #__quiz_t_dalliclick as b WHERE a.c_id = '".$quest_id."' and b.c_question_id = a.c_id and b.c_id = '".$answer."' AND a.published = 1";
				$database->SetQuery( $query );
				$inc_ddd = $database->LoadObjectList();
				if (!empty($inc_ddd))
					$questtype1_answer_incorrect = htmlspecialchars(nl2br($inc_ddd[0]->c_incorrect_feed));
				
				$c_quest_cur_attempt = 0;
				$query = "SELECT c_id, c_attempts FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
				$database->SetQuery( $query );
				$c_tmp = $database->LoadObjectList();
				if (!empty($c_tmp)) {
					$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
					if ($c_quest_cur_attempt >= $c_all_attempts) {
						$is_avail = 0;
						$is_no_attempts = 1;
					}
					if ($is_avail) {
						$query = "DELETE FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
						$database->SetQuery( $query );
						$database->query();
						$query = "DELETE FROM #__quiz_r_student_dalliclick WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
						$database->SetQuery( $query );
						$database->query();
					}
				}
				
				if ($is_avail) {
					$query = "INSERT INTO #__quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, is_correct)"
							. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$is_correct."')";
					$database->SetQuery($query);
					$database->query();
					$c_sq_id = $database->insertid();
							
					$query = "INSERT INTO #__quiz_r_student_dalliclick (c_sq_id, c_choice_id, c_elapsed_time)"
					. "\n VALUES('".$c_sq_id."', '".$answer."', '".$elapsed_time."')";
					$database->SetQuery($query);
					$database->query();
				}

				
				$data['score'] = $c_quest_score;

				$dispatcher	= JDispatcher::getInstance();
				JPluginHelper::importPlugin('system');
				$dispatcher->trigger('onJQuizAnswerSubmitted', array (&$data));
				
				@header ('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
				@header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				@header ('Cache-Control: no-cache, must-revalidate');
				@header ('Pragma: no-cache');
				@header ('Content-Type: text/xml; charset="utf-8"');
				echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
				echo '<response>' . "\n";
				echo '<c_score>'.$c_quest_score.'</c_score>' . "\n";
				echo '<is_correct>'.$is_correct.'</is_correct>' . "\n";
				echo '</response>' . "\n";
				
				die;
			break;
		}
	}
	
	public function onTotalScore(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT SUM(a_point) FROM #__quiz_t_dalliclick WHERE c_question_id IN (".$data['qch_ids'].") AND c_right = 1";
		$database->SetQuery( $query );
		$data['max_score'] += $database->LoadResult();
		
		$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 11 AND c_id IN (".$data['qch_ids'].")";
		$database->SetQuery( $query );
		$qch_ids_type_14 = $database->loadColumn();

		if(!empty($qch_ids_type_14)) {
			$query = "SELECT SUM(a_point) FROM #__quiz_t_dalliclick WHERE c_question_id IN (".implode(',', $qch_ids_type_14).") AND c_right = 0";
			$database->SetQuery( $query );
			$data['max_score'] += $database->LoadResult();
		}
		
		return true;
	}
	
	public function onScoreByCategory(&$data){
		
		$database = JFactory::getDBO();
		$database->setQuery("SELECT SUM(a_point) FROM #__quiz_t_dalliclick WHERE `c_question_id` = '".$data['score_bycat']->c_id."' AND c_right = 1");
		$data['score'] = $database->loadResult();
		
		return true;
	}
	
	public function onFeedbackQuestion(&$data)
    {
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_dalliclick WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$choice_data[0]->score = $data['score'];

		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$sid = $database->loadResult( );
		
		$query = "SELECT * FROM #__quiz_t_dalliclick AS c  LEFT JOIN #__quiz_r_student_dalliclick AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$sid."'"
		. "\n WHERE c.c_question_id = '".$data['q_data']->c_id."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();

		$uanswer = array();
		if(!empty($tmp) && is_array($tmp)) {
            foreach ($tmp as $t) {
                if ($t['c_choice_id']) {
                    $uanswer['c_choice_id'][] = $t['c_choice_id'];
                    $uanswer['c_elapsed_time'][] = $t['c_elapsed_time'];
                }
            }
        }

        $width = 184; //based on the screen width 320px

        $img_data = array();
        if(file_exists(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image)) {
            $img_data = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image);
        }

        if(!empty($img_data[1])) {
            $ratio = $img_data[0]/$img_data[1];
            if($img_data[0] >= $width) {
                $w = $img_data[0];
            } else {
                $w = $width;
            }
            $h = round($w / $ratio);
        } else {
            $ratio = 1;
            $w = $width;
            $h = $w * $ratio;
        }
		
		$feedback_data = array();
		$feedback_data['choice_data'] = $choice_data;
		$feedback_data['uanswer'] = $uanswer;
		$feedback_data['c_image'] = $data['q_data']->c_image;
		$feedback_data['w'] = $w;
		$feedback_data['h'] = $h;
		
		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($feedback_data, $data);

		if(preg_match("/pretty_green/", $data['cur_template'])) {
			$data['qoption'] = "\t" . $qhtml. "\n";
		} else {
			$data['qoption'] = "\t" . '<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>'.$qhtml.'</form></div>' . "\n";
		}

		return $data['qoption'];
	}
		
	public function onReviewQuestion(&$data)
    {
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_dalliclick WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$sid = $database->loadResult( );
		
		$query = "SELECT * FROM #__quiz_t_dalliclick AS c  LEFT JOIN #__quiz_r_student_dalliclick AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$sid."'"
		. "\n WHERE c.c_question_id = '".$data['q_data']->c_id."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
			
		$uanswer = array();
		if(!empty($tmp) && is_array($tmp)) {
            foreach ($tmp as $t) {
                if ($t['c_choice_id']) {
                    $uanswer['c_choice_id'][] = $t['c_choice_id'];
                    $uanswer['c_elapsed_time'][] = $t['c_elapsed_time'];
                }
            }
        }

        $width = 184; //based on the screen width 320px

        $img_data = array();
        if(file_exists(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image)) {
            $img_data = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image);
        }

        if(!empty($img_data[1])) {
            $ratio = $img_data[0]/$img_data[1];
            if($img_data[0] >= $width) {
                $w = $img_data[0];
            } else {
                $w = $width;
            }
            $h = round($w / $ratio);
        } else {
            $ratio = 1;
            $w = $width;
            $h = $w * $ratio;
        }

		$review_data = array();
		$review_data['choice_data'] = $choice_data;
		$review_data['uanswer'] = $uanswer;
		$review_data['c_image'] = $data['q_data']->c_image;
		$review_data['w'] = $w;
		$review_data['h'] = $h;
		
		$qhtml = JoomlaQuiz_template_class::JQ_createReview($review_data, $data);

		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>'. $qhtml .'</form></div>]]></quest_data_user>' . "\n";

		return $data;
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT *, c.c_id AS id FROM #__quiz_t_dalliclick AS c LEFT JOIN #__quiz_r_student_dalliclick AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$data['id']."'"
		. "\n WHERE c.c_question_id = '".$data['qid']."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();

		$data['info']['c_choice'] = $tmp;
		$query = "SELECT SUM(a_point) FROM #__quiz_t_dalliclick WHERE c_question_id = ".$data['qid']." AND c_right = 1";
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
			$str = $data['c_choice'][$j]['c_choice'];
			$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
	
		}

		$data['pdf']->Ln();
		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', true);
		$str = '  '.JText::_('COM_QUIZ_PDF_ANSWER');
		//$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
        $data['pdf']->writeHTML('<b>'.$str.'</b>', false);
		$data['pdf']->setFont($fontFamily);
		//$data['pdf']->setStyle('b', false);
		$str = $data['answer'];
		$data['pdf']->Write(5, $data['pdf_doc']->cleanText($str), '', 0);
				
		return $data['pdf'];		
	}
	
	public function onSendEmail(&$data){
		
		for($j=0,$k='A';$j < count($data['data']['c_choice']);$j++,$k++) {
			if($data['data']['c_choice'][$j]['c_choice_id']) $answer .= $k."&nbsp;";
			$data['str'] .= "$k. ".$data['data']['c_choice'][$j]['c_choice']."\n";
		}
		$data['str'] .= " ".JText::_('COM_QUIZ_PDF_ANSWER')." $answer \n";	
		return $data['str'];
	}
	
	public function onGetStatistic(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right FROM #__quiz_t_dalliclick WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		$past_this += 0.0000000000001;
		
			for($i=0; $i<count($choice_data); $i++) {
				$query = "SELECT COUNT(*) FROM #__quiz_r_student_dalliclick as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
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
	
	//Administration part
	
	public function onCreateDatabase(&$data){
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 14");
		$exists = $db->loadResult();
		
		if (!$exists) {			
			$db->setQuery("INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (14, 'Dalliclick', 'dalliclick');");
			$db->query();
			
			$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_t_dalliclick` (
			  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `c_choice` text NOT NULL,
			  `c_right` char(1) NOT NULL,
			  `c_question_id` int(10) NOT NULL,
			  `ordering` int(11) NOT NULL,
			  `c_incorrect_feed` text NOT NULL,
			  `a_point` float NOT NULL,
			  `c_quiz_id` int(11) NOT NULL,
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;");
			$db->query();
			
			$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_r_student_dalliclick` (
			  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `c_sq_id` int(10) NOT NULL,
			  `c_choice_id` int(10) NOT NULL,
			  `c_elapsed_time` int(10) NOT NULL,
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;");
			$db->query();
			
			$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `sq_delayed` INT( 5 ) NOT NULL DEFAULT '1';");
			$db->query();
		}
	}
	
	public function onGetAdminOptions($data)
	{
        $q_om_type = 14;
        $wysiwyg = JComponentHelper::getParams('com_joomlaquiz')->get('wysiwyg_options', true);

        $database = JFactory::getDBO();
		
		$row = new stdClass;
		$row->choices = array();
		$return = array();
		
		if($data['question_id']){
			$query = "SELECT * FROM #__quiz_t_dalliclick WHERE c_question_id = '".$data['question_id']."' ORDER BY ordering";
			$database->SetQuery( $query );
			$row->choices = $database->LoadObjectList();		
		}
		
		$return['choices'] = $row->choices;
		
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/dalliclick/admin/options/dalliclick.php");
		$options = ob_get_contents();
		ob_get_clean();
		
		return $options;
	}
	
	public function onGetAdminJavaScript(&$data){

		$q_om_type = 14;
        $wysiwyg = JComponentHelper::getParams('com_joomlaquiz')->get('wysiwyg_options', true);
        $question_id = $data['question_id'];
		
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/dalliclick/admin/js/dalliclick.js.php");
		$script = ob_get_contents();
		ob_get_clean();
		
		return $script;
	}
	
	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');
		
		$db->setQuery("SELECT `sq_delayed` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
		$row = $db->loadObject();
		
		$lists = array();
		
		$lists['sq_delayed']['input'] = '<input type="text" size="35" name="sq_delayed" value="'.(isset($row->sq_delayed) ? $row->sq_delayed : '').'">';
		$lists['sq_delayed']['label'] = 'Pieces uncovering interval (sec):';
				
		return $lists;
	}
	
	public function onAdminIsFeedback(&$data){
		return false;
	}
	
	public function onAdminIsPoints(&$data){
		return false;
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
		require_once(JPATH_SITE."/plugins/joomlaquiz/dalliclick/admin/tabs/dalliclick.php");
		$tab_content1 = ob_get_contents();
		ob_get_clean();
		
		$tabs[0]['label'] = '<li><a href="#question-image" data-toggle="tab">'.JText::_('COM_JOOMLAQUIZ_QUESTION_IMAGE').'</a></li>';
		$tabs[0]['content'] = $tab_content1;
		
		return $tabs;
	}
	
	public function onAdminSaveOptions(&$data)
    {
        $jinput = JFactory::getApplication()->input;
        $jform = $jinput->get('jform', array(), 'ARRAY');

        if($jinput->get('task') == 'copy_quizzes') {
            return true;
        }

		$database = JFactory::getDBO();
		$database->setQuery("UPDATE #__quiz_t_question SET `sq_delayed` = '".$jinput->getInt('sq_delayed')."', `c_image` = '".$jinput->get('c_image')."' WHERE c_id = '".$data['qid']."'");
		$database->execute();

		$field_order = 0;
		$ans_right = array();
        $jq_checked = $jinput->get('jq_checked', array(), 'ARRAY');
		if (!empty($jq_checked)) {
			foreach ($jq_checked as $sss) {
				$ans_right[] = $sss;
			}
		}
		else {
            $msg .= JText::_('COM_JOOMLAQUIZ_QUESTION_NOT_COMPLETE');
        }

        $jq_hid_fields = $jinput->get('jq_hid_fields', array(), 'ARRAY');
        $jq_hid_fields_ids = $jinput->get('jq_hid_fields_ids', array(), 'ARRAY');
        $jq_incorrect_feed = $jinput->get('jq_incorrect_feed', array(), 'ARRAY');
        $jq_a_points = $jinput->get('jq_a_points', array(), 'ARRAY');

		if (!empty($jq_hid_fields)) {
			$mcounter = 0;
			$fids_arr = array();
			foreach ($jq_hid_fields as $f_row) {
					
					$new_field = new stdClass;
					if(intval($jq_hid_fields_ids[$mcounter])) {
                        $new_field->c_id = intval($jq_hid_fields_ids[$mcounter]);
                    }
					$new_field->c_question_id = $data['qid'];
					$new_field->c_choice = stripslashes($f_row);
					$new_field->c_incorrect_feed = !empty($jq_incorrect_feed[$mcounter]) ? stripslashes($jq_incorrect_feed[$mcounter]) : '';
					
					$new_field->c_right = in_array(($field_order+ 1), $ans_right)?1:0;
					$new_field->ordering = $field_order;
					$new_field->a_point = floatval($jq_a_points[$mcounter]);
					$new_field->c_quiz_id	= intval($jform['c_quiz_id']);
					
					$database->setQuery("SELECT COUNT(c_id) FROM #__quiz_t_dalliclick WHERE c_id = '".$new_field->c_id."'");
					$exists = $database->loadResult();
					if($exists){
						$database->updateObject('#__quiz_t_dalliclick', $new_field, 'c_id');
					} else {
						$database->insertObject('#__quiz_t_dalliclick', $new_field);
						$new_field->c_id = $database->insertid();
					}
					
					$fids_arr[] = $new_field->c_id;					
					$field_order ++ ;
					$mcounter ++ ;
			}
			$fieldss = implode(',',$fids_arr);
			$query = "DELETE FROM #__quiz_t_dalliclick WHERE c_question_id = '".$data['qid']."' AND c_id NOT IN (".$fieldss.")";
			$database->setQuery( $query );
			$database->execute();
			
		}
		else
		{
			$msg .= JText::_('COM_JOOMLAQUIZ_QUESTION_NOT_COMPLETE2');
			$query = "DELETE FROM #__quiz_t_dalliclick WHERE c_question_id = '".$data['qid']."'";
			$database->setQuery( $query );
			$database->query();
		}
		
		return true;
	}
	
	public function onGetAdminAddLists(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c.*, sc.c_id as sc_id FROM #__quiz_t_dalliclick as c LEFT JOIN #__quiz_r_student_dalliclick as sc ON c.c_id = sc.c_choice_id"
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
		$query = "SELECT c_id as value, c_choice as text, c_right FROM #__quiz_t_dalliclick WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		$past_this += 0.0000000000001;
		
			for($i=0; $i<count($choice_data); $i++) {
				$query = "SELECT COUNT(*) FROM #__quiz_r_student_dalliclick as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
				$database->setQuery($query);
				$choice_this = $database->LoadResult();
				$choice_data[$i]->statistic = round(($choice_this*100)/$past_this).'%';
				$choice_data[$i]->count = $choice_this;
			}
		
		$data['question']->choice_data = $choice_data;
		return $data['question'];
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
		
		$query = "SELECT `b`.`c_choice_id` FROM `#__quiz_r_student_question` AS `a`, `#__quiz_r_student_dalliclick` AS `b` WHERE `a`.`c_stu_quiz_id` = '".$data['result']->c_id."' AND `a`.`c_question_id` = '".$data['question']->c_id."' AND `a`.`c_id` = `b`.`c_sq_id` ";
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
