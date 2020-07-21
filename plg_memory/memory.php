<?php
/**
* JoomlaQuiz Memory Plugin for Joomla
* @version $Id: memory.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage memory.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizMemory extends plgJoomlaquizQuestion
{
	var $name		= 'Memory';
	var $_name		= 'memory';
	
	public function onCreateQuestion(&$data) {
		
		$database = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_memory WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );
		$memory_data = $database->LoadObjectList();
		$qhtml = '';
		$check_pairs = false;
		$cc = 0;				
		$summ_pairs = 0;
		$cols = $data['q_data']->c_column;
		if(!empty($memory_data)){
			foreach($memory_data as $mem){
				$summ_pairs += $mem->a_pairs;
			}
		}
		
		$sq = $summ_pairs * 2;
		$rows = ($cols) ? ceil($sq / $cols) : 1;
		
		if($summ_pairs){
			$cc = $cols;
			$check_pairs = ($sq % (($cols * $rows)) == 0 ) ? true : false;
		}
		
		if($cols && $cc && $check_pairs){
			$qhtml = '<table id="memory_tbl" cellpadding="3" cellspacing="3">';
			for($r=0; $r<=$rows-1; $r++){
				$qhtml .= '<tr>';
				for($c = 0; $c <= $cols-1; $c++){
					$qhtml .= '<td align="center">';
					$qhtml .= '<a href="javascript:void(0);" onclick="javascript:showimage('.(($cc*$r)+$c).')" style="outline:none;" >';
                    //$qhtml .= '<img src="'.JURI::root().'images/joomlaquiz/images/memory/'.$data['q_data']->c_img_cover.'" name="img'.(($cc*$r)+$c).'" border="0" width="'.$data['q_data']->c_width.'" height="'.$data['q_data']->c_height.'" style="'.($data['q_data']->c_width?'width:'.$data['q_data']->c_width.'px;':'').' '.($data['q_data']->c_height?'height:'.$data['q_data']->c_height.'px;':'').'">';
                    $qhtml .= '<img src="'.JURI::root().'images/joomlaquiz/images/memory/'.$data['q_data']->c_img_cover.'" name="img'.(($cc*$r)+$c).'" border="0" style="'.($data['q_data']->c_width ? 'width:'.$data['q_data']->c_width.'px;' : ($data['q_data']->c_height ? 'height:'.$data['q_data']->c_height.'px;' : '')).'">';
                    $qhtml .= '</a></td>';
				}
				$qhtml .= '</tr>';
			}
			$qhtml .= '</table>';
			$data['ret_add_script'] = '';
			$data['ret_add_script'] .= 'pics = new Array();';
			$data['ret_add_script'] .= 'm_ids = new Array();';
			$data['ret_add_script'] .= 'user = new Array();';
			$data['ret_add_script'] .= 'oktoclick = true;';
			$data['ret_add_script'] .= 'finished = 0;';
			$data['ret_add_script'] .= 'ctr = 0;';
			$ii = 1;
			foreach($memory_data as $mem){
				for($mc = 1; $mc <= $mem->a_pairs; $mc++){
					$data['ret_add_script'] .= 'pics['.$ii.'] = "'.$mem->c_img.'";';
					$data['ret_add_script'] .= 'm_ids['.$ii.'] = "'.$mem->m_id.'";';
					$ii++;
				}
			}
			$maps = array();
			for($j=1; $j<=$summ_pairs; $j++){
				$maps[] = $j.','.$j;
			}
			$map = implode(',', $maps);
			$data['ret_add_script'] .= 'map= new Array('.$map.');';
			$data['ret_add_script'] .= 'for (var im = 0; im <= '.(($cols * $rows) - 1).' ;im++) { user[im] = 0;}';
		} else {
			$qhtml .= '<div>Incorrect settings!</div>';
		}
		
		$database->setQuery("SELECT `c_timer` FROM #__quiz_t_question WHERE `c_id` = '".$data['q_data']->c_id."'");
		$quest_limit_time = $database->loadResult();
		
		$data['ret_str'] .= "\t" . '<quest_limit_time>'.$quest_limit_time.'</quest_limit_time>';
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div style="width:100%;clear:both;"><form onsubmit=\'javascript: return false;\' name=\'quest_form\' id=\'quest_form\'>'.$qhtml.'</form></div>]]></quest_data_user>' . "\n";
		$data['ret_str'] .= "\t" . '<count_pairs>'.$summ_pairs.'</count_pairs>' . "\n";
		$data['ret_str'] .= "\t" . '<c_img_cover>'.$data['q_data']->c_img_cover.'</c_img_cover>' . "\n";
		
		return $data['ret_str'];
	}
	
	public function onPointsForAnswer(&$data){
		$database = JFactory::getDBO();
		
		$query = "SELECT SUM(a_points) FROM #__quiz_t_memory WHERE c_question_id = '".$data['q_data']->c_id."'";
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
			case 'add_points':
				$quiz_id = JFactory::getApplication()->input->get('quiz_id');
				$quest_id = JFactory::getApplication()->input->get('quest_id');
				$m_id = JFactory::getApplication()->input->get('m_id');
				$stu_quiz_id = JFactory::getApplication()->input->get('stu_quiz_id');
				$action = JFactory::getApplication()->input->get('action', '');
				$mem_time = JFactory::getApplication()->input->get('mem_time');
				
				$c_score = 0;
				
				$database->setQuery("SELECT `c_penalty`, `c_sec_penalty` FROM #__quiz_t_question WHERE `c_id` = '".$quest_id."'");
				$mem_data = $database->loadAssoc();
				
				if (!$action){
					$database->setQuery("SELECT `a_points` FROM `#__quiz_t_memory` WHERE `m_id` = '".$m_id."' AND `c_question_id` = '".$quest_id."'");
					$a_points = $database->loadResult();
				} else {
					$a_points = 0;
				}
				
				$penalty = 0;
				$take_time = ($mem_time) ? $mem_time : 0;
				if($take_time > $mem_data["c_sec_penalty"]){
					$penalty = ($mem_data["c_sec_penalty"]) ? round($take_time/$mem_data["c_sec_penalty"]) * $mem_data["c_penalty"] : 0;
				}			
				$a_points = $a_points - $penalty;
				
				if($a_points < 0){
					$a_points = 0;
				}
					
				$database->setQuery("SELECT COUNT(*) FROM `#__quiz_r_student_question` WHERE `c_stu_quiz_id` = '".$stu_quiz_id."' AND `c_question_id` = '".$quest_id."'");
				$exists = $database->loadResult();
					
				if(!$exists){
					$query = "INSERT INTO #__quiz_r_student_question (`c_stu_quiz_id`, `c_question_id`, `c_score`, `c_attempts`, `is_correct`)"
							. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$a_points."', 1, 1)";
					$database->SetQuery($query);
					$database->query();
				} else {
					$database->setQuery("SELECT `c_score` FROM `#__quiz_r_student_question` WHERE `c_stu_quiz_id` = '".$stu_quiz_id."' AND `c_question_id` = '".$quest_id."'");
					$c_score = $database->loadResult();
					$c_score += $a_points;
					
					$attempt_set = "";
					if($action){
						$attempt_set = ", `c_attempts` = `c_attempts` + 1";
					}
					
					$database->setQuery("UPDATE `#__quiz_r_student_question` SET `c_score` = '".$c_score."'".$attempt_set." WHERE `c_stu_quiz_id` = '".$stu_quiz_id."' AND `c_question_id` = '".$quest_id."'");
					$database->query();
				}
				
				$query = "INSERT INTO #__quiz_r_student_memory(`c_id`, `c_sq_id`, `c_mid`, `c_elapsed_time`)"
							. "\n VALUES('', '".$stu_quiz_id."', '".$m_id."', '".$take_time."')";
				$database->SetQuery($query);
				$database->query();
				
				$data['score'] = $c_score;

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
				echo '<t_score>'.$c_score.'</t_score>' . "\n";
				echo '<c_score>'.$a_points.'</c_score>' . "\n";
				echo '<p_score>'.$penalty.'</p_score>' . "\n";
				echo '</response>' . "\n";
				
				die;
			break;
		}
		
	}
	
	public function onTotalScore(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT SUM(a_points) FROM #__quiz_t_memory WHERE c_question_id IN (".$data['qch_ids'].")";
		$database->SetQuery( $query );
		$data['max_score'] += $database->LoadResult();
		
		return true;
	}
	
	public function onScoreByCategory(&$data){
		
		$database = JFactory::getDBO();
		$database->setQuery("SELECT SUM(a_points) FROM #__quiz_t_memory WHERE `c_question_id` = '".$data['score_bycat']->c_id."'");
		$data['score'] = $database->loadResult();
		
		return true;
	}
	
	public function onFeedbackQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_memory WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );				
		$memory_data = $database->LoadObjectList();
		
		$query = "SELECT c_mid FROM #__quiz_r_student_memory WHERE c_sq_id= '".$data['stu_quiz_id']."'";
		$database->SetQuery( $query );
		$udata = $database->LoadColumn();
		
		$query = "SELECT `c_elapsed_time` FROM #__quiz_r_student_memory WHERE c_sq_id= '".$data['stu_quiz_id']."'";
		$database->SetQuery( $query );
		$c_elapsed_times = $database->LoadColumn();
		
		$feedback_data = array();
		$feedback_data['memory_data'] = $memory_data;
		$feedback_data['udata'] = $udata;
		$feedback_data['c_elapsed_times'] = $c_elapsed_times;
		
		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($feedback_data, $data);
		if(preg_match("/pretty_green/", $data['cur_template'])){
			$data['qoption'] = "\t" . $qhtml. "\n";
		} else {
			$data['qoption'] = '<div><form  onsubmit=\'javascript: return false;\' name=\'quest_form\'>'.$qhtml.'</form></div>' . "\n";
		}
		return $data['qoption'];
	}
		
	public function onReviewQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_memory WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );				
		$memory_data = $database->LoadObjectList();
	
		$query = "SELECT c_mid FROM #__quiz_r_student_memory WHERE c_sq_id= '".$data['stu_quiz_id']."'";
		$database->SetQuery( $query );
		$udata = $database->LoadColumn();
		
		$query = "SELECT c_elapsed_time FROM #__quiz_r_student_memory WHERE c_sq_id= '".$data['stu_quiz_id']."'";
		$database->SetQuery( $query );
		$c_elapsed_times = $database->LoadColumn();
		
		$review_data = array();
		$review_data['memory_data'] = $memory_data;
		$review_data['udata'] = $udata;
		$review_data['c_elapsed_times'] = $c_elapsed_times;
		
		$qhtml = JoomlaQuiz_template_class::JQ_createReview($review_data, $data);
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<div><form onsubmit=\'javascript: return false;\' name=\'quest_form\'>'. $qhtml .'</form></div>]]></quest_data_user>' . "\n";
		return $data;		
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT `c_score` FROM #__quiz_r_student_question"
		. "\n WHERE c_question_id = '".$data['qid']."' AND c_id = '".$data['id']."'";
		$database->SetQuery( $query );
		$data['info']['c_score'] = $database->LoadResult();
		
		$database->setQuery("SELECT SUM(`a_points`) FROM #__quiz_t_memory WHERE `c_question_id` = '".$data['qid']."'");
		$data['info']['c_point'] = $database->loadResult();
		
		return true;
	}
	
	public function onGetPdf(&$data){
		
		if($data['data']['c_score']) 
			$answer = $data['data']['c_score'];
		else 
			$answer = 0;

		//$data['pdf']->SetFont('freesans');
		$fontFamily = $data['pdf']->getFontFamily();
					
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
	
	public function onGetStatistic(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT * FROM #__quiz_t_memory WHERE c_question_id = '".$data['question']->c_id."'";
		$database->SetQuery( $query );
		$memory_data = $database->LoadObjectList();
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		$past_this += 0.0000000000001;
		
		for($i=0; $i<count($memory_data); $i++) {
			$query = "SELECT COUNT(*) FROM #__quiz_r_student_memory as mem, #__quiz_r_student_question as qst WHERE mem.c_mid = '".$memory_data[$i]->m_id."' AND mem.c_sq_id=qst.c_stu_quiz_id AND qst.c_question_id='".$data['question']->c_id."'";
			$database->setQuery($query);
			$memory_this = $database->LoadResult();
			$memory_data[$i]->statistic = round(($memory_this*100)/$past_this).'%';
			$memory_data[$i]->count = $memory_this;
		}
						
		$data['question']->memory_data = $memory_data;
		return $data['question'];
		
	}
	
	public function onStatisticContent(&$data){
		
		$color = 1;									
		if (is_array($data['question']->memory_data))
		
			foreach($data['question']->memory_data as $mdata) {?>
			<tr>
				<td width="400"><?php echo '<img src="'.JURI::root().'images/joomlaquiz/images/memory/'.$mdata->c_img.'" height="50"/>';?></td>
				<td width="50"><?php echo $mdata->count;?></td>
				<td width="100"><?php echo $mdata->statistic;?></td>
				<td width="300">
					<div style="width:100%; border:1px solid #cccccc;">
						<div style="height: 5px; width: <?php echo ($mdata->statistic+1)?>%;" class="jq_color_<?php echo $color;?>">&nbsp;</div>
					</div>
				</td>
			</tr>
			<?php 
				$color++; 
				if ($color > 7) $color = 1;
			}
	}
	
	//Administration part
	
	public function onCreateDatabase(&$data){
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 13");
		$exists = $db->loadResult();
		
		if (!$exists) {
			if (!JFolder::exists(JPATH_SITE . '/images/joomlaquiz/images/memory') ) {
				JFolder::create( JPATH_SITE . '/images/joomlaquiz/images/memory');
			}
			
			if (!JFile::exists(JPATH_SITE . '/images/joomlaquiz/images/memory/tnnophoto.jpg')) {
				JFile::copy(JPATH_SITE . '/plugins/joomlaquiz/memory/admin/images/tnnophoto.jpg', JPATH_SITE . '/images/joomlaquiz/images/memory/tnnophoto.jpg');		
			}
			
			$db->setQuery("INSERT INTO #__quiz_t_qtypes (c_id, c_qtype, c_type) VALUES (13, 'Memory', 'memory');");
			$db->query();
			
			$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_t_memory` (
			  `m_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `c_question_id` int(11) NOT NULL,
			  `a_points` float NOT NULL,
			  `c_img` varchar(50) NOT NULL,
			  `a_pairs` int(10) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`m_id`)
			) DEFAULT CHARSET=utf8;");
			$db->query();
			
			$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_r_student_memory` (
			  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `c_sq_id` int(11) NOT NULL,
			  `c_mid` int(11) NOT NULL,
			  `c_elapsed_time` int( 11 ) NOT NULL,
			  PRIMARY KEY (`c_id`)
			) DEFAULT CHARSET=utf8;");
			$db->query();
			
			$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_column` INT( 11 ) NOT NULL DEFAULT '1';");
			$db->query();
			$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_img_cover` VARCHAR(50) NOT NULL DEFAULT 'tnnophoto.jpg';");
			$db->query();
			$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_sec_penalty` INT( 11 ) NOT NULL DEFAULT '0'");
			$db->query();
			
			$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 11 OR `c_id` = 12");
			$pzl_exists = $db->loadResult();
			
			if(!$pzl_exists){
				$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_width` INT( 10 ) NOT NULL DEFAULT '150';");
				$db->query();
			}
			
			$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` = 12");
			$pzl_exists = $db->loadResult();
			
			if(!$pzl_exists){
				$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD `c_height` INT( 10 ) NOT NULL DEFAULT '150';");
				$db->query();
			}
		}
	}
	
	public function onGetAdminOptions($data)
	{
		$q_om_type = 13;
		$database = JFactory::getDBO();
				
		$query = "SELECT * FROM #__quiz_t_question WHERE `c_id` = '".$data['question_id']."'";
		$database->SetQuery( $query );
		$row = $database->loadObject();
		
		if(empty($row)){
			$row = new stdClass;
		}
		
		$query = "SELECT * FROM #__quiz_t_memory WHERE c_question_id = '".$data['question_id']."'";
		$database->SetQuery( $query );
		
		$row->memory_data = array();
		$row->memory_data = $database->LoadObjectList();
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$memory_dir = JPATH_SITE.'/images/joomlaquiz/images/memory';
		
		if(!file_exists($memory_dir)){
			JFolder::create($memory_dir, 0757);
		}	
		$files = JFolder::files($memory_dir);
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
		
		$imagelist = JHTML::_('select.genericlist', $images, 'picture', ' class="inputbox" size="1" onchange="javascript:if (document.getElementById(\'picture\').options[selectedIndex].value != \'\') 
		{
			document.getElementById(\'imagelib\').src=\''.JURI::root().'images/joomlaquiz/images/memory/\' + document.getElementById(\'picture\').options[selectedIndex].value;
		} else {
			document.getElementById(\'imagelib\').src=\''.JURI::root().'images/joomlaquiz/images/memory/tnnophoto.jpg\';
		}"' ,'value', 'text', '' );
		$lists['imagelist'] = $imagelist;
		
		$imagelist_cover = JHTML::_('select.genericlist', $images, 'picture_cover', ' class="inputbox" size="1" onchange="javascript:if (document.getElementById(\'picture_cover\').options[selectedIndex].value != \'\') 
		{
			document.getElementById(\'imagelib_cover\').src=\''.JURI::root().'images/joomlaquiz/images/memory/\' + document.getElementById(\'picture_cover\').options[selectedIndex].value;
		} else {
			document.getElementById(\'imagelib_cover\').src=\''.JURI::root().'images/joomlaquiz/images/memory/tnnophoto.jpg\';
		}
		
		document.getElementById(\'c_img_cover\').value = document.getElementById(\'picture_cover\').options[selectedIndex].value;
		"' ,'value', 'text', '' );
		$lists['imagelist_cover'] = $imagelist_cover;
		
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/memory/admin/options/memory.php");
		$options = ob_get_contents();
		ob_get_clean();
		
		return $options;
	}
	
	public function onGetAdminJavaScript(&$data){
		
		$c_id = $data['question_id'];
		$q_om_type = 13;
		ob_start();
		require_once(JPATH_SITE."/plugins/joomlaquiz/memory/admin/js/memory.js.php");
		$script = ob_get_contents();
		ob_get_clean();
		
		return $script;
	}
	
	public function onGetAdminForm(&$data)
	{
		$db = JFactory::getDBO();
		$c_id = JFactory::getApplication()->input->get('c_id');
		
		$db->setQuery("SELECT `c_timer`, `c_sec_penalty`, `c_column`, `c_height`, `c_width` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
		$row = $db->loadObject();
		
		$lists = array();
		
		$lists['c_width']['input'] = '<input type="text" size="35" name="c_width" value="'.(isset($row->c_width) ? $row->c_width : '').'" required>';
		$lists['c_width']['label'] = 'Image Width: *';
		
		$lists['c_height']['input'] = '<input type="text" size="35" name="c_height" value="'.(isset($row->c_height) ? $row->c_height : '').'" required>';
		$lists['c_height']['label'] = 'Image Height: *';
		
		$lists['c_column']['input'] = '<input type="text" size="35" name="c_column" value="'.(isset($row->c_column) ? $row->c_column : '').'" required>';
		$lists['c_column']['label'] = 'Number of columns: *';
		
		$lists['c_sec_penalty']['input'] = '<input type="text" size="35" name="c_sec_penalty" value="'.(isset($row->c_sec_penalty) ? $row->c_sec_penalty : '').'">';
		$lists['c_sec_penalty']['label'] = 'Seconds of penalty:';
		
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
	
	function _uploadResizeCropImg(){
		
		$mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
		$database = JFactory::getDBO();
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.path');

		$filedata = $jinput->files->get('Filedata', array(), 'array');
		
		$userfile2 = !empty($filedata['tmp_name']) ? $filedata['tmp_name'] : '';
		$userfile_name = !empty($filedata['name']) ? $filedata['name'] : '';
		$qid = $jinput->getInt('c_id', 0);
		
		if (!empty($filedata)) {
			$base_Dir = JPATH_SITE."/images/joomlaquiz/images/memory";
			$filename = explode(".", $userfile_name);
			
			if (preg_match("/[^0-9a-zA-Z_\-]/", $filename[0])) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_FILE_MUST')."'); window.history.go(-1);</script>\n";
				die();
			}
		
			if (JFile::exists($base_Dir.'/'.$userfile_name)) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_IMAGE').$userfile_name.JText::_('COM_JOOMLAQUIZ_ALREADY_EXISTS')."'); window.history.go(-1);</script>\n";
				die();
			}
		
			if ((strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".gif")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".jpg")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".png")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".bmp")) ) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_ACCEPTED_FILES')."'); window.history.go(-1);</script>\n";
				die();
			}
			
			if (!JFile::move($filedata['tmp_name'],$base_Dir.'/'.$filedata['name']) || !JPath::setPermissions($base_Dir.'/'.$filedata['name'])) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_UPLOAD_OF').$userfile_name.JText::_('COM_JOOMLAQUIZ_FAILED')."'); window.history.go(-1);</script>\n";
				die();
			} else {
				
				$database->setQuery("SELECT `c_width` FROM `#__quiz_t_question` WHERE `c_id` = '".$qid."'");
				$width = $database->loadResult();
				$width = ($width) ? $width : 150;
				
				require_once(JPATH_SITE.'/administrator/components/com_joomlaquiz/assets/image.class.php');
				
				$image = new SimpleImage();
				$image->load($base_Dir.'/'.$filedata['name']);
				$image->resizeToWidth($width);
				
				$image->save($base_Dir.'/'.$filedata['name']);
				
				$javascript = '<script type="text/javascript">alert("'.JText::_('COM_JOOMLAQUIZ_UPLOAD_OF').$filedata['name'].JText::_('COM_JOOMLAQUIZ_TO').$base_Dir.JText::_('COM_JOOMLAQUIZ_SUCCESSFUL').'"); var image = parent.document.getElementById("picture"); image.options[image.options.length] = new Option("'.$filedata['name'].'", "'.$filedata['name'].'");
				var image_cover = parent.document.getElementById("picture_cover"); image_cover.options[image_cover.options.length] = new Option("'.$filedata['name'].'", "'.$filedata['name'].'");
				parent.jQuery(\'#picture\').trigger(\'liszt:updated\');
				parent.jQuery(\'#picture_cover\').trigger(\'liszt:updated\');
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

        if($jinput->get('task') == 'copy_quizzes') {
            return true;
        }

        $jform = $jinput->get('jform', array(), 'ARRAY');
		$plg_task = $jinput->get('plgtask', '');
        $database = JFactory::getDBO();
		
		if($plg_task == 'upload_resize_crop_img'){
			$this->_uploadResizeCropImg();
			die;
		}

		$database->setQuery("UPDATE #__quiz_t_question SET `c_sec_penalty` = '".$jinput->getInt('c_sec_penalty', 0)."', `c_height` = '".$jinput->getInt('c_height', 0)."', `c_width` = '".$jinput->getInt('c_width', 0)."', c_timer = '".$jinput->getInt('c_timer', 0)."', `c_column` = '".$jinput->getInt('c_column', 0)."', `c_img_cover` = '".$jinput->get('c_img_cover', '')."' WHERE c_id = '".$data['qid']."'");
		$database->execute();
		
		$mcounter = 0;
		$fids_arr = array();

        $jq_hid_fields        = $jinput->get('jq_hid_fields', array(), 'ARRAY');
        $jq_hid_fields_ids    = $jinput->get('jq_hid_fields_ids', array(), 'ARRAY');
        $jq_hid_fields_points = $jinput->get('jq_hid_fields_points', array(), 'ARRAY');
        $jq_hid_fields_pairs  = $jinput->get('jq_hid_fields_pairs', array(), 'ARRAY');

		if (!empty($jq_hid_fields)) {
			foreach ($jq_hid_fields as $field_order => $f_row) {
					$new_field = new stdClass;
					if(intval($jq_hid_fields_ids[$mcounter])) {
                        $new_field->m_id = intval($jq_hid_fields_ids[$mcounter]);
                    }
					$new_field->c_question_id = $data['qid'];
					$new_field->c_img = stripslashes($f_row);
					$new_field->a_points	= floatval(!empty($jq_hid_fields_points[$field_order]) ? stripslashes($jq_hid_fields_points[$field_order]) : '');
					$new_field->a_pairs	= floatval(!empty($jq_hid_fields_pairs[$field_order]) ? stripslashes($jq_hid_fields_pairs[$field_order]) : '');

					$database->setQuery("SELECT COUNT(m_id) FROM #__quiz_t_memory WHERE m_id = '".$new_field->m_id."'");
					$exists = $database->loadResult();
					if($exists){
						$database->updateObject('#__quiz_t_memory', $new_field, 'm_id');
					} else {
						$database->insertObject('#__quiz_t_memory', $new_field);
						$new_field->m_id = $database->insertid();
					}
					$fids_arr[] = $new_field->m_id;					
					$mcounter ++ ;					
			}
			$fieldss = implode(',',$fids_arr);
			$query = "DELETE FROM #__quiz_t_memory WHERE c_question_id = '".$data['qid']."' AND m_id NOT IN (".$fieldss.")";
			$database->setQuery( $query );
			$database->query();
		}
		else
		{	
			$query = "DELETE FROM #__quiz_t_memory WHERE c_question_id = '".$data['qid']."'";
			$database->setQuery( $query );
			$database->query();
			$msg .= JText::_('COM_JOOMLAQUIZ_QUESTION_NOT_COMPLETE2');		
		}
		
		return true;
	}
	
	public function onGetAdminAddLists(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT DISTINCT m.* FROM #__quiz_r_student_question as sq LEFT JOIN #__quiz_t_memory as m"
			. "\n ON m.c_question_id = sq.c_question_id and sq.c_id = '".$data['id']."'"
			. "\n LEFT JOIN #__quiz_r_student_memory as sm ON sm.c_sq_id = sq.c_stu_quiz_id"
			. "\n WHERE m.c_question_id = '".$data['q_id']."'"
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
			<th class="title"><?php echo JText::_('COM_JOOMLAQUIZ_RIGHT_ANSWER');?></th>
			
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="left" width="75px">
					<img src="<?php echo JURI::root();?>images/joomlaquiz/images/memory/<?php echo $row->c_img;?>" width="100"/>
					&nbsp;&nbsp;
					<img src="<?php echo JURI::root();?>images/joomlaquiz/images/memory/<?php echo $row->c_img;?>" width="100"/>
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
		$query = "SELECT * FROM #__quiz_t_memory WHERE c_question_id = '".$data['question']->c_id."'";
		$database->SetQuery( $query );
		$memory_data = $database->LoadObjectList();
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		
		$past_this += 0.0000000000001;
		
		for($i=0; $i<count($memory_data); $i++) {
			$query = "SELECT COUNT(*) FROM #__quiz_r_student_memory as mem, #__quiz_r_student_question as qst WHERE mem.c_mid = '".$memory_data[$i]->m_id."' AND mem.c_sq_id=qst.c_stu_quiz_id AND qst.c_question_id='".$data['question']->c_id."'";
			$database->setQuery($query);
			$memory_this = $database->LoadResult();
			$memory_data[$i]->statistic = round(($memory_this*100)/$past_this).'%';
			$memory_data[$i]->count = $memory_this;
		}
						
		$data['question']->memory_data = $memory_data;
		return $data['question'];
	}
	
	public function onGetAdminStatistic(&$data){
		
		$color = 1;									
		if (is_array($data['question']->memory_data))
		
			foreach($data['question']->memory_data as $mdata) {?>
			<tr>
				<td width="400"><?php echo '<img src="'.JURI::root().'images/joomlaquiz/images/memory/'.$mdata->c_img.'" height="50"/>';?></td>
				<td width="50"><?php echo $mdata->count;?></td>
				<td width="100"><?php echo $mdata->statistic;?></td>
				<td width="300">
					<div style="width:100%; border:1px solid #cccccc;">
						<div style="height: 5px; width: <?php echo ($mdata->statistic+1)?>%;" class="jq_color_<?php echo $color;?>">&nbsp;</div>
					</div>
				</td>
			</tr>
			<?php 
				$color++; 
				if ($color > 7) $color = 1;
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