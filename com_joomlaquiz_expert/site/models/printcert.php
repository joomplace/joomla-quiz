<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');



jimport('joomla.application.component.modellist');
/**
 * Print Certificate Model.
 *
 */
class JoomlaquizModelPrintcert extends JModelList
{	
	protected $rtl = false;

	public function JQ_printCertificate(){
		
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$stu_quiz_id = intval( JFactory::getApplication()->input->get('stu_quiz_id', 0 ) );
		$user_unique_id = JFactory::getApplication()->input->get('user_unique_id', '', 'STRING');
		$unique_pass_id = JFactory::getApplication()->input->get('unique_pass_id', '', 'STRING');
		
		$query = "SELECT SUM(squ.c_score) as user_score, ch.q_chain, sq.params, sq.user_name, sq.user_email, sq.user_surname, sq.c_passed, sq.c_student_id, sq.c_total_score, sq.c_date_time, sq.c_total_time, sq.unique_id, sq.unique_pass_id, sq.c_max_score as c_full_score, qtq.c_title, qtq.c_certificate, qtq.c_id"
		. "\n FROM #__quiz_r_student_quiz AS sq"
		. "\n LEFT JOIN #__quiz_t_quiz as qtq ON qtq.c_id = sq.c_quiz_id"
		. "\n LEFT JOIN #__quiz_r_student_question as squ ON sq.c_id = squ.c_stu_quiz_id"
		. "\n LEFT JOIN `#__quiz_q_chain` AS ch ON ch.s_unique_id = sq.unique_id"
		. "\n WHERE sq.c_id = '".$stu_quiz_id."' and qtq.c_id = sq.c_quiz_id"
		. "\n GROUP BY squ.c_stu_quiz_id";
		$database->SetQuery( $query );
		$stu_quiz = $database->LoadObjectList();
		
		if (!empty($stu_quiz)) {
			$stu_quiz = $stu_quiz[0];
            if ( (($my->id == $stu_quiz->c_student_id || $unique_pass_id == $stu_quiz->unique_pass_id) || $my->authorise('core.managefe','com_joomlaquiz')) && ($user_unique_id == $stu_quiz->unique_id) ) {
				if ($stu_quiz->c_passed != 1) {
					echo JText::_('COM_QUIZ_MES_NOTPASSED'); die();
				}
				if (!$stu_quiz->c_certificate) {
					echo JText::_('COM_QUIZ_MES_NOTAVAIL'); die();
				}
				
				if(file_exists(JPATH_SITE.'/components/com_comprofiler/comprofiler.php')){
					$query = "SELECT `name` FROM `#__comprofiler_fields` WHERE  `name` NOT IN ( 'password', 'onlinestatus', 'formatname', 'connections', 'forumrank', 'forumposts', 'forumkarma', 'forumsignature', 'forumview', 'forumorder') AND `table` LIKE '%comprofiler'";
					$database->SetQuery( $query );
					$cb_fields = $database->loadColumn();
					
					$ftmp = array();
					$conf = new JConfig();
					if(!empty($cb_fields)){
						foreach ($cb_fields as $cb_field) {
							$query = "SELECT * FROM `information_schema`.`columns` WHERE `table_schema` = '".$conf->db."' AND `table_name` = '".$conf->dbprefix."comprofiler' AND column_name = '".$cb_field."'";
							$database->setQuery($query);
							$f = $database->loadObjectList();

							if(!empty($f)){
								$ftmp[] = $cb_field;
							}
						}
					}
					$cb_fields = $ftmp;
					
					if (!empty($cb_fields)) {
						$query = "SELECT `".implode('`,`', $cb_fields)."` FROM #__comprofiler WHERE user_id= ".$stu_quiz->c_student_id;
						$database->SetQuery( $query );				
						$cb_data = $database->loadObjectList();	
						$cb_data = @$cb_data[0];			
					} 
				}
				
				$database->SetQuery("SELECT * FROM #__quiz_certificates WHERE id = '".$stu_quiz->c_certificate."'");
				$certif = $database->LoadObjectList();
				$certif = $certif[0];
				
				$loadFile = JPATH_SITE . "/images/joomlaquiz/images/" . $certif->cert_file;
				$im_fullsize = getimagesize($loadFile);
				if ($im_fullsize[2] == 1) {
					$im = imagecreatefromgif($loadFile); }
				elseif ($im_fullsize[2] == 2) {
					$im = imagecreatefromjpeg($loadFile); }
				elseif ($im_fullsize[2] == 3) {
					$im = imagecreatefrompng($loadFile); }
				else { die();}
				$white = imagecolorallocate($im, 255, 255, 255);
				$grey = imagecolorallocate($im, 128, 128, 128);
				$black = imagecolorallocate($im, 0, 0, 0);
				$font_size = $certif->text_size;
				$font_x = $certif->text_x;
				$font_y = $certif->text_y;
				$inform = array();
				$query = "SELECT u.name, u.username from #__quiz_r_student_quiz sq, #__users u";
				$query .= " WHERE sq.c_id = '".$stu_quiz_id."' AND sq.c_student_id=u.id";
				$database->SetQuery($query);
				$inform = $database->LoadObjectList();			
				if (!empty($inform)) {
					if ($inform[0]->name != '') {
						$u_name = $inform[0]->name;
					} 
					if ($inform[0]->username != '') {
						$u_usrname = $inform[0]->username;
					}
				} else {
					$u_usrname = JText::_('COM_QUIZ_USERNAME_ANONYMOUS');
					if($stu_quiz->user_name != ''){
						$u_name = $stu_quiz->user_name;
					} else {
						$u_name = JText::_('COM_QUIZ_USERNAME_ANONYMOUS');
					}
				}

				$u_surname = '';
				$u_email = '';
				if($stu_quiz->user_surname != ''){
					$u_surname = $stu_quiz->user_surname;
				}
				if($stu_quiz->user_email != ''){
					$u_email = $stu_quiz->user_email;
				}
				
				/*$full_score = 0;
				$qids = str_replace('*', ",", $stu_quiz->q_chain);
				$full_score = JoomlaquizHelper::getTotalScore($qids, $stu_quiz->c_id);
				$stu_quiz->c_full_score = ($full_score) ? $full_score : $stu_quiz->c_full_score;*/

				$sc_procent = ($stu_quiz->c_full_score != 0) ? number_format(($stu_quiz->user_score * 100) / $stu_quiz->c_full_score, 2, '.', ' ') : 0;
				$font_text = $certif->crtf_text;
				$font_text = JHtml::_('content.prepare',$this->revUni($font_text),$stu_quiz,'');
				$font_text = str_replace("#unique_code#", $this->revUni(base_convert(JText::_('COM_JOOMLAQUIZ_SHORTCODE_ADJUSTER').$stu_quiz->c_id.''.$stu_quiz->c_student_id.''.$stu_quiz->user_score, 10, 36)), $font_text);
				$font_text = str_replace("#name#", $this->revUni($u_name), $font_text);
				$font_text = str_replace("#surname#", $this->revUni($u_surname), $font_text);
				$font_text = str_replace("#email#", $this->revUni($u_email), $font_text);
				$font_text = str_replace("#username#",$this->revUni($u_usrname), $font_text);

				$reg_answer_replace = number_format($stu_quiz->user_score, 2, '.', ' ') .
                    ' ' . JText::_('COM_QUIZ_CERT_OUT_OF') . ' ' .
                    number_format($stu_quiz->c_full_score, 2, '.', ' ') .
                    ' (' . $sc_procent . JText::_('COM_QUIZ_CERT_PERCENT_SYMBOL') . ')';
                //$font_text = str_replace("#reg_answer#",JText::_('COM_QUIZ_CERT_TOTAL')." ".$sc_procent." ".JText::_('COM_QUIZ_CERT_PERCENT'), $font_text);
                $font_text = str_replace("#reg_answer#",JText::_('COM_QUIZ_CERT_TOTAL')." ".$reg_answer_replace, $font_text);

                $font_text = str_replace("#stu_points#",$stu_quiz->c_total_score, $font_text);
				$font_text = str_replace("#course#",$this->revUni($stu_quiz->c_title), $font_text);
				$stu_datetime = strtotime($stu_quiz->c_date_time) + $stu_quiz->c_total_time;

				if (!empty($cb_fields)) {
					foreach($cb_fields as $cb_field) {	
						if ($cb_data && isset($cb_data->$cb_field))
							$font_text = str_replace("#{$cb_field}#", $cb_data->$cb_field, $font_text);
						else 
							$font_text = str_replace("#{$cb_field}#", '', $font_text);
					}
				}
				
				$str_format = 'Y-m-d';
				$str_format_pre = '';
				$first_pos = JoomlaquizHelper::jq_strpos( $font_text,'#date');
				while( $first_pos !== false ){
					if ($first_pos !== false) {
						$first_str = JoomlaquizHelper::jq_substr($font_text, $first_pos+5, strlen($font_text) - $first_pos - 5);
						$sec_pos = JoomlaquizHelper::jq_strpos( $first_str,'#');

						if($sec_pos === false){
							echo JText::_('COM_JOOMLAQUIZ_CLOSE_END_TAG_PLEASE');
							die;
						}

						$str_format = JoomlaquizHelper::jq_substr($first_str, 0, $sec_pos);
						$str_format_pre = $str_format;					
						if ($str_format) {
							if (JoomlaquizHelper::jq_substr($str_format,0,1) == '(') {
								$str_format = JoomlaquizHelper::jq_substr($str_format,1);
							}
							if (JoomlaquizHelper::jq_substr($str_format,-1) == ')') {
								$str_format = JoomlaquizHelper::jq_substr($str_format,0,-1);
							}
						}					
					}
					if (!$str_format) { $str_format = 'Y-m-d';}
					$date = $stu_datetime;
					$format = $str_format;
					$font_text = str_replace('#date'.$str_format_pre.'#', JHTML::_('date', $date , $format), $font_text);
					
					$first_pos = JoomlaquizHelper::jq_strpos( $font_text,'#date');
				}
				$font_text = str_replace('#date#', date('Y-m-d', $stu_datetime), $font_text);
			
				$font = JPATH_SITE . "/media/".(isset($certif->text_font)? $certif->text_font: 'arial.ttf');
				$text_array = explode("\n",$font_text);
				$count_lines = count($text_array);
				$text_lines_xlefts = array();
				$text_lines_xrights = array();
				$text_lines_heights = array();
				for ($i = 0; $i< $count_lines; $i++) {
					$font_box = imagettfbbox($font_size, 0, $font, $text_array[$i]);
					$text_lines_xlefts[$i] = $font_box[0];
					$text_lines_xrights[$i] = $font_box[2];
					$text_lines_heights[$i] = $font_box[1]-$font_box[7];
					if ($text_lines_heights[$i] < $font_size) { $text_lines_heights[$i] = $font_size; }
				}
				$min_x = 0;
				$max_x = 0;
				$max_w = 0;
				for ($i = 0; $i< $count_lines; $i++) {
					if ($min_x > $text_lines_xlefts[$i]) $min_x = $text_lines_xlefts[$i];
					if ($max_x < $text_lines_xrights[$i]) $max_x = $text_lines_xrights[$i];
					if ($max_w < ($text_lines_xrights[$i]-$text_lines_xlefts[$i])) $max_w = ($text_lines_xrights[$i] - $text_lines_xlefts[$i]);
				}

				$allow_shadow = ($certif->crtf_shadow == 1);

				foreach($text_array as $arr){
					$arr = $this->revUni($arr);
				}
				
				switch(intval($certif->crtf_align)) {
					case 1:
						for ($i = 0; $i< $count_lines; $i++) {
							$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
							$ad = intval(($max_w - $cur_w)/2) - intval($max_w/2);
							if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
							imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
							$font_y = $font_y + $text_lines_heights[$i] + 3;
						}
					break;
					case 2:		
						for ($i = 0; $i< $count_lines; $i++) {
							$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
							$ad = 0;
							if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
							imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
							$font_y = $font_y + $text_lines_heights[$i] + 3;
						}

					break;
					default:
						for ($i = 0; $i< $count_lines; $i++) {
							$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
							$ad = intval($max_w - $cur_w) - intval($max_w);
							if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
							imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
							$font_y = $font_y + $text_lines_heights[$i] + 3;
						}

					break;
				}

				$query = "SELECT * FROM #__quiz_cert_fields WHERE cert_id = '{$certif->id}' ORDER BY c_id";
				$database->setQuery($query);
				$fields = $database->loadObjectList();

				$ad = 0;		
				if (is_array($fields) && !empty($fields)) {
					foreach($fields as $field){
					
						$field->f_text = JHtml::_('content.prepare',$this->revUni($field->f_text),$stu_quiz,'');
						$field->f_text = str_replace("#unique_code#", $this->revUni(base_convert(JText::_('COM_JOOMLAQUIZ_SHORTCODE_ADJUSTER').$stu_quiz->c_id.''.$stu_quiz->c_student_id.''.$stu_quiz->user_score, 10, 36)), $field->f_text);
						$field->f_text = str_replace("#name#", $this->revUni($u_name), $field->f_text);
						$field->f_text = str_replace("#surname#", $this->revUni($u_surname), $field->f_text);
						$field->f_text = str_replace("#email#", $u_email, $field->f_text);
						$field->f_text = str_replace("#username#",$this->revUni($u_usrname), $field->f_text);

                        //$field->f_text = str_replace("#reg_answer#",JText::_('COM_QUIZ_CERT_TOTAL')." ".$sc_procent." ".JText::_('COM_QUIZ_CERT_PERCENT'), $field->f_text);
                        $field->f_text = str_replace("#reg_answer#",JText::_('COM_QUIZ_CERT_TOTAL')." ".$reg_answer_replace, $field->f_text);

						$field->f_text = str_replace("#points#",JText::_('COM_QUIZ_CERT_TOTAL')." ".$stu_quiz->user_score." ".JText::_('COM_QUIZ_CERT_OUT_OF')." ".number_format($stu_quiz->c_full_score, 0, '.', '')." ".JText::_('COM_QUIZ_CERT_POINTS'), $field->f_text);
                                                $field->f_text = str_replace("#stu_points#",$stu_quiz->c_total_score, $field->f_text);
						$field->f_text = str_replace("#course#",$this->revUni($stu_quiz->c_title), $field->f_text);

						if (!empty($cb_fields)) {
							foreach($cb_fields as $cb_field) {	
								if ($cb_data && isset($cb_data->$cb_field))
									$field->f_text = str_replace("#{$cb_field}#", $cb_data->$cb_field, $field->f_text);
								else 
									$field->f_text = str_replace("#{$cb_field}#", '', $field->f_text);
							}
						}
						
						if (JoomlaquizHelper::jq_strpos($field->f_text, '#date') !== false) {
							$str_format = 'Y-m-d';
							$str_format_pre = '';
							$font_text = $field->f_text;
							$first_pos = JoomlaquizHelper::jq_strpos( $font_text,'#date');
							while($first_pos !== false ){
								if ($first_pos !== false) {
									$first_str = JoomlaquizHelper::jq_substr($font_text, $first_pos+5, strlen($font_text) - $first_pos - 5);
									$sec_pos = JoomlaquizHelper::jq_strpos( $first_str,'#');
									if($sec_pos === false){
										echo JText::_('COM_JOOMLAQUIZ_CLOSE_END_TAG_PLEASE');
										die;
									}
									$str_format = JoomlaquizHelper::jq_substr($first_str, 0, $sec_pos);
									$str_format_pre = $str_format;
									
									if ($str_format) {
										if (JoomlaquizHelper::jq_substr($str_format,0,1) == '(') {
											$str_format = JoomlaquizHelper::jq_substr($str_format,1);
										}
										if (JoomlaquizHelper::jq_substr($str_format,-1) == ')') {
											$str_format = JoomlaquizHelper::jq_substr($str_format,0,-1);
										}
									}
									
								}
								if (!$str_format) { $str_format = 'Y-m-d';}
								$date = $stu_datetime;
								$format = $str_format;
								$font_text = str_replace('#date'.$str_format_pre.'#', JHTML::_('date', $date , $format), $font_text);
								$first_pos = JoomlaquizHelper::jq_strpos( $font_text,'#date');
							}
							$font_text = str_replace('#date#', date('Y-m-d', $stu_datetime), $font_text);
							
							$field->f_text = $font_text;
						}
			
						$font = JPATH_SITE . "/media/".(isset($field->font)? $field->font: 'arial.ttf');
						if($field->text_x_center){
                            $box_text =imagettfbbox($field->text_h, 0,$font,$field->f_text);
                            if ($field->shadow)
                            {
                                imagettftext($im, $field->text_h, 0,  $im_fullsize[0]/2 - ($box_text[2]/2) + $ad+2,
                                    $field->text_y+2,$grey, $font, $field->f_text);
                            }
                            imagettftext($im, $field->text_h, 0,  $im_fullsize[0]/2 - ($box_text[2]/2) , $field->text_y,
                                $black, $font,
                                $field->f_text);
                        }
						else
                        {
                            if ($field->shadow)
                            {imagettftext($im, $field->text_h, 0,  $field->text_x + $ad+2, $field->text_y+2,
                                    $grey, $font, $field->f_text);
                            }
						imagettftext($im, $field->text_h, 0,  $field->text_x + $ad, $field->text_y, $black, $font, $field->f_text);
					}
				}
				}

				if (preg_match('~Opera(/| )([0-9].[0-9]{1,2})~', $_SERVER['HTTP_USER_AGENT'])) {
					$UserBrowser = "Opera";
				}
				elseif (preg_match('~MSIE ([0-9].[0-9]{1,2})~', $_SERVER['HTTP_USER_AGENT'])) {
					$UserBrowser = "IE";
				} else {
					$UserBrowser = '';
				}
				$file_name = 'Certificate.png';
				header('Content-Type: image/png');
				header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				if ($UserBrowser == 'IE') {
					if(JComponentHelper::getParams('com_joomlaquiz')->get('download_certificate')){
						header('Content-Disposition: attachment; filename="' . $file_name . '";');
					}else{
						header('Content-Disposition: inline; filename="' . $file_name . '";');
					}
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
				} else {
					if(JComponentHelper::getParams('com_joomlaquiz')->get('download_certificate')){
						header('Content-Disposition: attachment; filename="' . $file_name . '";');
					}else{
						header('Content-Disposition: inline; filename="' . $file_name . '";');
					}
					header('Pragma: no-cache');
				}
				@ob_end_clean();
				imagepng($im);
				imagedestroy($im);
				exit;
			}
		}
		
		echo JText::_('COM_QUIZ_MES_NOTAVAIL');
	}
	
	function revUni($text) { 
    
		if($this->rtl==true){
			$wordsArray = explode(" ", $text); 

			$rtlCompleteText=''; 
			for ($i = sizeOf($wordsArray); $i > -1; $i = $i-1) { 

				//$lettersArray = explode("|", str_replace(";|", ";", $wordsArray[$i])); 
				$lettersArray = $wordsArray[$i]; 
				
				if(true){
					$rtlWord=''; 
					for ($k = strlen($lettersArray); $k > -1; $k = $k-1) { 
						if (strlen($lettersArray[$k]) > 0) { // make sure its full unicode letter 
							$rtlWord = $rtlWord."".$lettersArray[$k]; 
							echo $rtlWord."<br>";
						} 
					}
				}else{
					$rtlWord=$lettersArray; 
				}
				$rtlCompleteText = $rtlCompleteText." ".$rtlWord; 
				
			} 
			$text = $rtlCompleteText;
        }
        return $text; 
    }
	
	/*
		to use this function to match need to be reconstructed.
	*/
	function write_multiline_text($image, $font_size, $color, $font, $text, $start_x, $start_y, $max_width) { 
		//split the string 
		//build new string word for word 
		//check everytime you add a word if string still fits 
		//otherwise, remove last word, post current string and start fresh on a new line 
		$words = explode(" ", $text); 
		$string = ""; 
		$tmp_string = ""; 

		for($i = 0; $i < count($words); $i++) { 
			$tmp_string .= $words[$i]." "; 

			//check size of string 
			$dim = imagettfbbox($font_size, 0, $font, $tmp_string); 

			if($dim[4] < ($max_width - $start_x)) { 
				$string = $tmp_string; 
				$curr_width = $dim[4];
			} else { 
				$i--; 
				$tmp_string = ""; 
				$start_xx = $start_x + round(($max_width - $curr_width - $start_x) / 2);        
				imagettftext($image, $font_size, 0, $start_xx, $start_y, $color, $font, $string); 

				$string = ""; 
				$start_y += abs($dim[5]) * 2; 
				$curr_width = 0;
			} 
		} 

		$start_xx = $start_x + round(($max_width - $dim[4] - $start_x) / 2);        
		imagettftext($image, $font_size, 0, $start_xx, $start_y, $color, $font, $string);
	}
}
