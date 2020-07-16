<?php
/**
* JoomlaQuiz Image Match Plugin for Joomla
* @version $Id: imgmatch.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage imgmatch.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizImgmatch extends plgJoomlaquizQuestion
{
	var $name		= 'Imgmatch';
	var $_name		= 'imgmatch';

	public function onCreateQuestion(&$data) {

		$database = JFactory::getDBO();
		$script_arr = array();
		$dd_c = 1;
		$query = "SELECT * FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."'";
		$query .=  "\n ORDER BY ordering";
		$database->SetQuery( $query );
		$match_data = $database->LoadObjectList();

		$shuffle_match = $match_data;
		if ($data['qrandom']) shuffle($shuffle_match);

		$database->setQuery("SELECT `c_timer` FROM #__quiz_t_question WHERE `c_id` = '".$data['q_data']->c_id."'");
		$quest_limit_time = $database->loadResult();

		$data['ret_str'] .= "\t" . '<quest_limit_time>'.$quest_limit_time.'</quest_limit_time>';

		if(!$data['q_data']->c_width){
            $data['q_data']->c_width = 100;
        }
        if(!$data['q_data']->c_height){
            $data['q_data']->c_height = 100;
        }
        $ratio = round($data['q_data']->c_width / $data['q_data']->c_height, 5);

        $data['ret_str'] .= "\t" . '<img_ratio>'.$ratio.'</img_ratio>';
        $data['ret_str'] .= "\t" . '<img_width>'.$data['q_data']->c_width.'</img_width>';
        $data['ret_str'] .= "\t" . '<img_height>'.$data['q_data']->c_height.'</img_height>';

        $data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div class="imagematch-answers"><table id="dd_table" style="border-collapse:separate;">';

		if(!empty($match_data)){
			foreach($match_data as $ii => $dd_data){
				$data['ret_str'] .= "\t" .'<tr>';
				$data['ret_str'] .= "\t" .'<td style="padding:10px 0;">';
				$data['ret_str'] .= "\t" .'<div style="float:left;width:'.$data['q_data']->c_width.'px;height:'.$data['q_data']->c_height.'px;background-repeat: no-repeat;background-size:contain;background-image:url('.JURI::root().'images/joomlaquiz/images/resize/'.$dd_data->c_left_text.');"><input type="hidden" class="jq_left_text" name="lefts[]" value="'.$dd_data->c_id.'***'.$dd_data->c_left_text.'" /></div>';
				$data['ret_str'] .= "\t" .'</td>';
				$data['ret_str'] .= "\t" .'<td id="gw_'.($dd_c).'" width="'.$data['q_data']->c_width.'" height="'.$data['q_data']->c_height.'" style="padding:10px 0;">';
                $data['ret_str'] .= "\t" .'<div class="imagematch-target" style="width:'.$data['q_data']->c_width.'px;">&nbsp;</div>';
				$data['ret_str'] .= "\t" .'<input type="hidden" class="jq_complete" name="complete[]" value="false" /></td>';
				$data['ret_str'] .= "\t" .'<td id="gw_'.($dd_c+1).'" width="'.$data['q_data']->c_width.'" height="'.$data['q_data']->c_height.'" style="padding:10px 0;">';

				$data['ret_str'] .= "\t" .'<div class="groupItem" style="background-repeat: no-repeat;background-size:contain;cursor:move;float:left;width:'.$data['q_data']->c_width.'px;height:'.$data['q_data']->c_height.'px;background-image:url('.JURI::root().'images/joomlaquiz/images/resize/'.$shuffle_match[$ii]->c_right_text.');"><div class="headerItem" style="width:'.$data['q_data']->c_width.'px;height:'.$data['q_data']->c_height.'px;" draggable="true"><input type="hidden" class="jq_right_text" name="rights[]" value="'.$shuffle_match[$ii]->c_right_text.'" /><!--x--></div></div>';
				$data['ret_str'] .= "\t" .'</td>';
				$data['ret_str'] .= "\t" .'</tr>';
				array_push($script_arr,'"#gw_'.$dd_c.'"');
				array_push($script_arr,'"#gw_'.($dd_c+1).'"');
				$dd_c = $dd_c + 2;
			}
		}

		$data['ret_add_script'] = 'els = ['.implode(',', $script_arr).'];';

		$data['ret_str'] .= "\t" . '</table></div>';
		$data['ret_str'] .= '<form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'></form></div>]]></quest_data_user>' . "\n";

		return $data['ret_str'];
	}

	public function onPointsForAnswer(&$data){
		$database = JFactory::getDBO();

		$query = "SELECT SUM(a_points) FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$tmp_pointz = $database->LoadResult();
		if(floatval($tmp_pointz))
			$data['q_data']->c_point = $data['q_data']->c_point.' - '.(floatval($tmp_pointz) + $data['q_data']->c_point);

		return $data['q_data'];
	}

	public function onSaveQuestion(&$data){

		$database = JFactory::getDBO();
		$database->setQuery("SELECT `c_timer` FROM `#__quiz_t_question` WHERE `c_id` = '".$data['quest_id']."'");
		$c_timer = $database->loadResult();
		$c_elapsed_time = ($c_timer && $data['timer']) ? $c_timer - $data['timer'] : 0;

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

		if (!empty($ddd2) && !empty($ddd)) {
			$data['is_correct'] = 1;
			$rr_num = 0;
			foreach ($ddd2 as $right_row) {
				$ans = explode('|||', $ans_array[$rr_num]);
				$ans_left = explode('***', $ans[0]);

				if ($right_row->c_id == $ans_left[0]) {
					if($right_row->c_right_text != $ans[1]){
						$data['is_correct'] = 0;
					} else {
						$c_quest_score += $right_row->a_points;
					}
				} else {

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
		if (!empty($c_tmp)) {
			$data['c_quest_cur_attempt'] = $c_tmp[0]->c_attempts;
			if ($data['c_quest_cur_attempt'] >= $data['c_all_attempts']) {
				$data['is_avail'] = 0;
				$is_no_attempts = 1;
			}
			if ($data['is_avail']) {
				$query = "DELETE FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' and c_question_id = '".$data['quest_id']."'";
				$database->SetQuery( $query );
				$database->query();
				$query = "DELETE FROM #__quiz_r_student_matching WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
				$database->SetQuery( $query );
				$database->query();
			}
		}
		if ($data['is_avail']) {
			if ($data['c_quest_cur_attempt'] && $data['c_penalty']) {
				if (((100-$data['c_penalty']*$data['c_quest_cur_attempt'])/100) < 0)
					$c_quest_score = 0;
				else
					$c_quest_score = $c_quest_score * ((100-$data['c_penalty']*$data['c_quest_cur_attempt'])/100) ;
			}

			$query = "INSERT INTO #__quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, is_correct, c_elapsed_time)"
			. "\n VALUES('".$data['stu_quiz_id']."', '".$data['quest_id']."', '".$c_quest_score."', '".($data['c_quest_cur_attempt'] + 1)."', '".$data['is_correct']."', '".$c_elapsed_time."')";
			$database->SetQuery($query);
			$database->query();
			$c_sq_id = $database->insertid();
			$i = 0;
			while ($i < count($ddd2)) {
				$query = "INSERT INTO #__quiz_r_student_matching (c_sq_id, c_matching_id, c_sel_text)"
				. "\n VALUES('".$c_sq_id."', '".$ddd2[$i]->c_id."', '".base64_encode($ans_array[$i])."')";
				$database->SetQuery($query);
				$database->query();
				$i ++;
			}
		}

		$data['score'] = $c_quest_score;

		return true;
	}

	public function onTotalScore(&$data){

		$database = JFactory::getDBO();
		$query = "SELECT `c_id` FROM #__quiz_t_question WHERE `c_type` = 12 AND `c_id` IN (".$data['qch_ids'].") AND published = 1";
		$database->SetQuery( $query );
		$qch_ids_type_12 = $database->LoadColumn();

		if(!empty($qch_ids_type_12)) {
			$query = "SELECT SUM(a_points) FROM #__quiz_t_matching WHERE c_question_id IN (".implode(',', $qch_ids_type_12).")";
			$database->SetQuery( $query );
			$data['max_score'] += $database->LoadResult();
		}

		return true;
	}

	public function onScoreByCategory(&$data){

		$database = JFactory::getDBO();
		$database->setQuery("SELECT SUM(a_points) FROM #__quiz_t_matching WHERE `c_question_id` = '".$data['score_bycat']->c_id."'");
		$data['score'] = $database->loadResult();

		return true;
	}

	public function onFeedbackQuestion(&$data){

		$database = JFactory::getDBO();
		$user_ans = '';
		$query = "SELECT * FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."'"
		. "\n ORDER BY ordering";
		$database->SetQuery( $query );
		$match_data = $database->LoadObjectList();

		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$sid = $database->loadResult( );

		$query = "SELECT *, m.c_id AS id FROM #__quiz_t_matching AS m LEFT JOIN #__quiz_r_student_matching AS sm"
		. "\n ON m.c_id = sm.c_matching_id AND sm.c_sq_id = '".$sid."' WHERE m.c_question_id = '".$data['q_data']->c_id."' ORDER BY m.ordering";
		$database->SetQuery( $query );
		$qdata = $database->LoadAssocList();

		$qdata[0]['score'] = $data['score'];

		$query = "SELECT c_id, c_right_text, c_left_text FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$tmp2 = $database->LoadObjectList();

		for($i=0, $n=count($qdata); $i<$n; $i++) {
				if ($tmp2[$i]->c_id.'***'.$tmp2[$i]->c_left_text.'|||'.$tmp2[$i]->c_right_text == base64_decode($qdata[$i]['c_sel_text'])) {
					$qdata[$i]['c_correct'] = 1;
					$qdata[$i]['c_sel_text']= $tmp2[$i]->c_right_text;
				} else {
					$qdata[$i]['c_correct'] = 0;
					$user_ans = base64_decode($qdata[$i]['c_sel_text']);
					$ans = explode('|||', $user_ans);
					$qdata[$i]['c_sel_text'] = $ans[1];
				}
		}

		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($qdata, $data);
		if(preg_match("/pretty_green/", $data['cur_template'])){
			$data['qoption'] = "\t" . $qhtml. "\n";
		} else {
			$data['qoption'] = '<div><form  onsubmit=\'javascript: return false;\' name=\'quest_form\'>'.$qhtml.'</form></div>' . "\n";
		}
		return $data['qoption'];
	}

	public function onNextPreview($data){
		$database = JFactory::getDBO();

		$database->setQuery("SELECT c_id, c_left_text, c_right_text FROM #__quiz_t_matching WHERE c_question_id = '".$data['quest_id']."'");
		$images_match = $database->loadObjectList();

		$match_str = '';
		$match_array = array();
		if(!empty($images_match)){
			foreach($images_match as $image_match){
				$match_array[] = $image_match->c_id."***".$image_match->c_left_text."|||".$image_match->c_right_text;
			}

			$match_str = implode('```', $match_array);
		}

		$data['is_correct'] = ($match_str == $answer) ? 1 : 0;
		return true;
	}

	public function onReviewQuestion(&$data){

		$database = JFactory::getDBO();
		$user_ans = '';
		$query = "SELECT * FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."'"
		. "\n ORDER BY ordering";
		$database->SetQuery( $query );
		$match_data = $database->LoadObjectList();

		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$sid = $database->loadResult( );

		$query = "SELECT *, m.c_id AS id FROM #__quiz_t_matching AS m LEFT JOIN #__quiz_r_student_matching AS sm"
		. "\n ON m.c_id = sm.c_matching_id AND sm.c_sq_id = '".$sid."' WHERE m.c_question_id = '".$data['q_data']->c_id."' ORDER BY m.ordering";
		$database->SetQuery( $query );
		$qdata = $database->LoadAssocList();

		$query = "SELECT c_id, c_right_text, c_left_text FROM #__quiz_t_matching WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$tmp2 = $database->LoadObjectList();

		for($i=0, $n=count($qdata); $i<$n; $i++) {
				if ($tmp2[$i]->c_id.'***'.$tmp2[$i]->c_left_text.'|||'.$tmp2[$i]->c_right_text == base64_decode($qdata[$i]['c_sel_text'])) {
					$qdata[$i]['c_correct'] = 1;
					$qdata[$i]['c_sel_text']= $tmp2[$i]->c_right_text;
				} else {
					$qdata[$i]['c_correct'] = 0;
					$user_ans = base64_decode($qdata[$i]['c_sel_text']);
					$ans = explode('|||', $user_ans);
					$qdata[$i]['c_sel_text'] = $ans[1];
				}
		}

		$qhtml = JoomlaQuiz_template_class::JQ_createReview($qdata, $data);
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>'. $qhtml .'</form></div>]]></quest_data_user>' . "\n";
		return $data;
	}

	public function onGetResult(&$data){

		$database = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_question AS q, #__quiz_t_matching AS m"
		. "\n WHERE q.c_id = '".$data['qid']."' AND q.c_id = m.c_question_id AND q.published = 1";
		$database->SetQuery( $query );
		$data['info']['c_imgmatch'] = $database->LoadRow();
		$query = "select * from #__quiz_r_student_matching where c_sq_id='".$data['id']."'";
		$database->SetQuery( $query );
		$tmp = $database->LoadRow();
		while(list($key,$value) = each($tmp)) {
			$data['info']['c_imgmatch'][$key] = $value;
		}

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
		$str = " Scores:";
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
					$query = "SELECT COUNT(*) FROM #__quiz_r_student_matching AS a, #__quiz_r_student_question AS b WHERE b.c_question_id = '".$data['question']->c_id."' AND b.c_id=a.c_sq_id AND  a.c_matching_id  = '".$match_data[$i]->c_id."' AND a.c_sel_text = '".base64_encode($match_data[$j]->c_id."***".$match_data[$j]->c_left_text."|||".$match_data[$j]->c_right_text)."'";
					$database->setQuery($query);

					$choice_this = $database->LoadResult();

					$match_data[$i]->match_data[] = array('c_right_text'=>$match_data[$j]->c_right_text, 'statistic'=>round(($choice_this*100)/$past_this).'%', 'count'=>$choice_this, 'c_right'=>$match_data[$i]->c_right_text==$match_data[$j]->c_right_text);
				}
		}

		$data['question']->match_data = $match_data;
		return $data['question'];

	}

	public function onStatisticContent(&$data){

		if (is_array($data['question']->match_data))
		foreach($data['question']->match_data as $mdata) {?>
			<tr>
				<td colspan="4"><?php echo '<img src="'.JURI::root().'images/joomlaquiz/images/resize/'.$mdata->c_left_text.'" height="50"/><br/>'; ?>
					<table>
					<?php
					$color = 1;
					if (is_array($mdata->match_data))
					foreach($mdata->match_data as $sdata){?>
						<tr>
							<td width="400">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $sdata['c_right']? '<img src="'.JURI::root().'images/joomlaquiz/images/resize/'.$sdata['c_right_text'].'" height="50" style="border: 2px solid green"/>':'<img src="'.JURI::root().'images/joomlaquiz/images/resize/'.$sdata['c_right_text'].'" height="50" style="border: 2px solid red"/>'?></td>
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

	//Administration part

	public function onCreateDatabase(&$data){

		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		$db = JFactory::getDBO();
		$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 12");
		$exists = $db->loadResult();

		if (!$exists) {
			if (!JFolder::exists(JPATH_SITE . '/images/joomlaquiz/images/resize') ) {
				JFolder::create( JPATH_SITE . '/images/joomlaquiz/images/resize');
			}

			if (!JFile::exists(JPATH_SITE . '/images/joomlaquiz/images/resize/tnnophoto.jpg')) {
				JFile::copy(JPATH_SITE . '/plugins/joomlaquiz/imgmatch/admin/images/tnnophoto.jpg', JPATH_SITE . '/images/joomlaquiz/images/resize/tnnophoto.jpg');
			}

			$db->setQuery("INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (12, 'Image Match', 'imgmatch');");
			$db->query();

			$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_t_matching` (
			`c_id` int(10) unsigned NOT NULL auto_increment,
			`c_question_id` int(10) unsigned NOT NULL default '0',
			`c_left_text` text NOT NULL,
			`c_right_text` text NOT NULL,
			`ordering` int(11) NOT NULL default '0',
			`c_quiz_id` int(11) NOT NULL default '0',
			`a_points` FLOAT( 11 ) NOT NULL default '0',
			PRIMARY KEY  (`c_id`),
			KEY `c_question_id` (`c_question_id`) ) DEFAULT CHARSET=utf8;");
			$db->query();

			$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_r_student_matching` (
			`c_id` int(10) unsigned NOT NULL auto_increment,
			`c_sq_id` int(10) unsigned NOT NULL default '0',
			`c_sel_text` text NOT NULL,
			`c_matching_id` int(10) unsigned NOT NULL default '0',
			PRIMARY KEY  (`c_id`),
			KEY `c_sq_id` (`c_sq_id`),
			KEY `c_matching_id` (`c_matching_id`) ) DEFAULT CHARSET=utf8;");
			$db->query();

			$db->setQuery("ALTER TABLE `#__quiz_r_student_question` ADD `c_elapsed_time` INT( 10 ) NOT NULL;");
			$db->query();
			$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 11 OR `c_id` = 13");
			$pzl_exists = $db->loadResult();

			if(!$pzl_exists){
				$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_width` INT( 10 ) NOT NULL DEFAULT '150';");
				$db->query();
			}

			$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 11");
			$pzl_exists = $db->loadResult();

			if(!$pzl_exists){
				$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_timer` INT( 10 ) NOT NULL;");
				$db->query();
			}

			$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 13");
			$pzl_exists = $db->loadResult();

			if(!$pzl_exists){
				$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_height` INT( 10 ) NOT NULL DEFAULT '150';");
				$db->query();
			}
		}
	}

	public function onGetAdminOptions($data)
	{
		$database = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_matching WHERE c_question_id = '".$data['question_id']."' ORDER BY ordering";
		$database->SetQuery( $query );

		$row = new stdClass;
		$row->matching = array();
		$row->matching = $database->LoadObjectList();

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

        if (!JFolder::exists(JPATH_SITE . '/images/joomlaquiz/images/resize') ) {
            JFolder::create( JPATH_SITE . '/images/joomlaquiz/images/resize');
        }

        if (!JFile::exists(JPATH_SITE . '/images/joomlaquiz/images/resize/tnnophoto.jpg')) {
            JFile::copy(JPATH_SITE . '/plugins/joomlaquiz/imgmatch/admin/images/tnnophoto.jpg', JPATH_SITE . '/images/joomlaquiz/images/resize/tnnophoto.jpg');
        }

		$resize_dir = JPATH_SITE.'/images/joomlaquiz/images/resize';
		if(!file_exists($resize_dir)){
			JFolder::create($resize_dir, 0757);
		}
		$files = JFolder::files($resize_dir);
		$tmp_files = array();
		if(!empty($files))
		{
			foreach($files as $file){
				if($file == 'tnnophoto.jpg') continue;
				$tmp_files[$file] = $file;
			}
		}
		$files = $tmp_files;
		$images = array_merge(array(JHTML::_('select.option', '', '-- Select picture --' )), $files);

		$imagelist_left = JHTML::_('select.genericlist', $images, 'picture_left', ' class="inputbox" size="1" onchange="javascript:if (document.getElementById(\'picture_left\').options[selectedIndex].value != \'\') 
		{
			document.getElementById(\'imagelib_left\').src=\''.JURI::root().'images/joomlaquiz/images/resize/\' + document.getElementById(\'picture_left\').options[selectedIndex].value;
		} else {
			document.getElementById(\'imagelib_left\').src=\''.JURI::root().'images/joomlaquiz/images/resize/tnnophoto.jpg\';
		}"' ,'value', 'text', '' );
		$imagelist_right = JHTML::_('select.genericlist', $images, 'picture_right', ' class="inputbox" size="1" onchange="javascript:if (document.getElementById(\'picture_right\').options[selectedIndex].value != \'\') 
		{
			document.getElementById(\'imagelib_right\').src=\''.JURI::root().'images/joomlaquiz/images/resize/\' + document.getElementById(\'picture_right\').options[selectedIndex].value
		} else {
			document.getElementById(\'imagelib_right\').src=\''.JURI::root().'images/joomlaquiz/images/resize/tnnophoto.jpg\';
		}"' ,'value', 'text', '' );
		$lists = array();
		$lists['imagelist_left'] = $imagelist_left;
		$lists['imagelist_right'] = $imagelist_right;

		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/imgmatch/admin/options/imgmatch.php");
		$options = ob_get_contents();
		ob_get_clean();

		return $options;
	}

	public function onGetAdminJavaScript(&$data){

		$c_id = $data['question_id'];
		$q_om_type = 12;
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/imgmatch/admin/js/imgmatch.js.php");
		$script = ob_get_contents();
		ob_get_clean();

		return $script;
	}

	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');

		$db->setQuery("SELECT `c_random`, `c_height`, `c_width`, `c_timer` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
		$row = $db->loadObject();

		$lists = array();

		$c_random = array();
		$c_random[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
		$c_random[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
		$c_random = JHTML::_('select.genericlist', $c_random, 'jform[c_random]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_random) ? intval( $row->c_random ) : 0));
		$lists['c_random']['input'] = $c_random;
		$lists['c_random']['label'] = JText::_('COM_JOOMLAQUIZ_RANDOMIZE_ANSWERS');

		$lists['c_width']['input'] = '<input type="text" size="35" name="c_width" value="'.(isset($row->c_width) ? $row->c_width : '').'">';
		$lists['c_width']['label'] = 'Image Width:';

		$lists['c_height']['input'] = '<input type="text" size="35" name="c_height" value="'.(isset($row->c_height) ? $row->c_height : '').'">';
		$lists['c_height']['label'] = 'Image Height:';

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

	function _uploadResizeImg(){
		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
		$jinput = $mainframe->input;
        $user_files = $jinput->files->get('Filedata', array(), 'array');

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.path');

		$userfile2 = (!empty($user_files['tmp_name']) ? $user_files['tmp_name'] : "");
		$userfile_name = (!empty($user_files['name']) ? $user_files['name'] : "");
		$qid = JFactory::getApplication()->input->get('c_id');

		if (!empty($user_files)) {
			$base_Dir = JPATH_SITE."/images/joomlaquiz/images/resize";
			$filename = explode(".", $userfile_name);

			if (preg_match("/[^0-9a-zA-Z_]/", $filename[0])) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_FILE_MUST')."'); window.history.go(-1);</script>\n";
				die();
			}

			if (JFile::exists($base_Dir.'/'.$userfile_name)) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_IMAGE').$userfile_name.JText::_('COM_JOOMLAQUIZ_ALREADY_EXISTS')."'); window.history.go(-1);</script>\n";
				die();
			}

			if ((strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".gif")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".jpg")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".jpeg")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".png")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".bmp")) ) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_ACCEPTED_FILES')."'); window.history.go(-1);</script>\n";
				die();
			}

			if (!JFile::move($user_files['tmp_name'],$base_Dir.'/'.$user_files['name']) || !JPath::setPermissions($base_Dir.'/'.$user_files['name'])) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_UPLOAD_OF').$userfile_name.JText::_('COM_JOOMLAQUIZ_FAILED')."'); window.history.go(-1);</script>\n";
				die();
			} else {
				require_once(JPATH_SITE.'/administrator/components/com_joomlaquiz/assets/image.class.php');

				$database->setQuery("SELECT `c_height` FROM `#__quiz_t_question` WHERE `c_id` = '".$qid."'");
				$height = $database->loadResult();
				$height = ($height) ? $height : 150;

				$image = new SimpleImage();
				$image->load($base_Dir.'/'.$user_files['name']);

				$image->resizeToHeight($height);
				$image->save($base_Dir.'/'.$user_files['name']);

				$javascript = '<script type="text/javascript">alert("'.JText::_('COM_JOOMLAQUIZ_UPLOAD_OF').$user_files['name'].JText::_('COM_JOOMLAQUIZ_TO').$base_Dir.JText::_('COM_JOOMLAQUIZ_SUCCESSFUL').'"); var image_left = parent.document.getElementById("picture_left"); image_left.options[image_left.options.length] = new Option("'.$user_files['name'].'", "'.$user_files['name'].'");
				parent.jQuery(\'#picture_left\').trigger(\'liszt:updated\');
				var image_right = parent.document.getElementById("picture_right"); image_right.options[image_right.options.length] = new Option("'.$user_files['name'].'", "'.$user_files['name'].'");
				parent.jQuery(\'#picture_right\').trigger(\'liszt:updated\');
				
				</script>';

				echo $javascript;
				$mainframe->close();
			}

			return true;
		}

		return false;
	}

	public function onAdminSaveOptions(&$data){
		$jinput = JFactory::getApplication()->input;
		$jform_data = $jinput->get('jform', array(), 'ARRAY');
		$jq_hid_fields_ids = $jinput->get('jq_hid_fields_ids', array(), 'ARRAY');
		$jq_hid_fields_points = $jinput->get('jq_hid_fields_points', array(), 'ARRAY');
		$jq_hid_fields_right = $jinput->get('jq_hid_fields_right', array(), 'ARRAY');
		$jq_hid_fields_left = $jinput->get('jq_hid_fields_left', array(), 'ARRAY');

        if($jinput->get('task') == 'copy_quizzes') {
            return true;
        }

		$database = JFactory::getDBO();

		$plg_task = JFactory::getApplication()->input->get('plgtask', '');
		if($plg_task == 'upload_resize_img'){
			$this->_uploadResizeImg();
			die;
		}

		$database->setQuery("UPDATE #__quiz_t_question SET `c_height` = '".$jinput->get('c_height',0, 'ALNUM')."',  `c_width` = '".$jinput->get('c_width',0, 'ALNUM')."', `c_random` = '".$jform_data['c_random']."', c_timer = '".$jinput->get('c_timer',0, 'ALNUM')."' WHERE c_id = '".$data['qid']."'");
		$database->execute();

		$field_order = 0;
		$mcounter = 0;
		$fids_arr = array();
		if (!empty($jq_hid_fields_left)) {
			foreach ($jq_hid_fields_left as $f_row) {
					$new_field = new stdClass;
					if(intval($jq_hid_fields_ids[$mcounter]))
					$new_field->c_id = intval($jq_hid_fields_ids[$mcounter]);

					$new_field->c_question_id = $data['qid'];
					$new_field->c_left_text = stripslashes($f_row);
					$new_field->c_right_text = (!empty($jq_hid_fields_right[$field_order])?stripslashes($jq_hid_fields_right[$field_order]):'');
					$new_field->ordering = $field_order;
					$new_field->c_quiz_id	= intval($jform_data['c_quiz_id']);
					$new_field->a_points	= floatval((!empty($jq_hid_fields_points[$field_order])?stripslashes($jq_hid_fields_points[$field_order]):''));
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
		$query = "SELECT m.*, sm.c_sel_text FROM #__quiz_t_matching as m LEFT JOIN #__quiz_r_student_matching as sm"
			. "\n ON m.c_id = sm.c_matching_id and sm.c_sq_id = '".$data['id']."'"
			. "\n WHERE m.c_question_id = '".$data['q_id']."'"
			. "\n ORDER BY m.ordering, m.c_id"
		;

		$database->SetQuery( $query );
		$answer = $database->LoadObjectList();
		$lists['id'] = $data['id'];
		$lists['qid'] = $data['q_id'];
		$lists['answer'] = $answer;

		return $lists;

	}

	public function onGetAdminReportsHTML(&$data){
		$rows = $data['lists']['answer'];

		ob_start();
		?>
		<table class="adminlist">
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
				<td align="center" width="75px">
					<?php
					$c_sel_text = base64_decode($row->c_sel_text);
					$c_right = explode('|||', $c_sel_text);
					$c_right = $c_right[1];
					if ($c_right == $row->c_right_text) { ?>
						<img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/tick.png"  border="0" alt="User choice" />
					<?php } ?>
				</td>
				<td align="left">
					<img src="<?php echo JURI::root();?>images/joomlaquiz/images/resize/<?php echo $row->c_sel_text; ?>" height="100" />
				</td>
				<td align="left">
					<img src="<?php echo JURI::root();?>images/joomlaquiz/images/resize/<?php echo $row->c_right_text; ?>" height="100" />
				</td>
				<td align="left">
					<img src="<?php echo JURI::root();?>images/joomlaquiz/images/resize/<?php echo $row->c_left_text; ?>" height="100" />
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
					$query = "SELECT COUNT(*) FROM #__quiz_r_student_matching AS a, #__quiz_r_student_question AS b WHERE b.c_question_id = '".$data['question']->c_id."' AND b.c_id=a.c_sq_id AND  a.c_matching_id  = '".$match_data[$i]->c_id."' AND a.c_sel_text = '".base64_encode($match_data[$j]->c_id."***".$match_data[$j]->c_left_text."|||".$match_data[$j]->c_right_text)."'";
					$database->setQuery($query);

					$choice_this = $database->LoadResult();

					$match_data[$i]->match_data[] = array('c_right_text'=>$match_data[$j]->c_right_text, 'statistic'=>round(($choice_this*100)/$past_this).'%', 'count'=>$choice_this, 'c_right'=>$match_data[$i]->c_right_text==$match_data[$j]->c_right_text);
				}

		}

		$data['question']->match_data = $match_data;
		return $data['question'];
	}

	public function onGetAdminStatistic(&$data){

		if (is_array($data['question']->match_data))
		foreach($data['question']->match_data as $mdata) {?>
			<tr>
				<td colspan="4"><?php echo '<img src="'.JURI::root().'images/joomlaquiz/images/resize/'.$mdata->c_left_text.'" height="50"/><br/>'; ?>
					<table>
					<?php
					$color = 1;
					if (is_array($mdata->match_data))
					foreach($mdata->match_data as $sdata){?>
						<tr>
							<td width="400">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $sdata['c_right']? '<img src="'.JURI::root().'images/joomlaquiz/images/resize/'.$sdata['c_right_text'].'" height="50" style="border: 2px solid green"/>':'<img src="'.JURI::root().'images/joomlaquiz/images/resize/'.$sdata['c_right_text'].'" height="50" style="border: 2px solid red"/>'?></td>
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

		$data['answer'] = '';
		$query = "SELECT `a`.`c_score` FROM `#__quiz_r_student_question` AS `a` WHERE `a`.`c_stu_quiz_id` = '{$data['result']->c_id}' AND `a`.`c_question_id` = '{$data['question']->c_id}'";
		$database->setQuery( $query );
		$score = $database->loadResult();
		if ($score != null)
			$data['answer'] = 'Score - '.$score;

		return $data['answer'];
	}
}
