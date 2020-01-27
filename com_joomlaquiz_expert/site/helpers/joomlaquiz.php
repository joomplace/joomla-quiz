<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

/**
 * Joomlaquiz Deluxe component helper.
 */
class JoomlaquizHelper
{
		public static function getResultsByCategories($start_id){
			
			$appsLib = JqAppPlugins::getInstance();
			$database = JFactory::getDBO();
			
			$query = "SELECT qtq.c_id, qtq.c_type, qtq.c_ques_cat as q_cat, qtq.c_point as fuly_score, qrs.c_score as us_score FROM #__quiz_r_student_question as qrs, #__quiz_t_question as qtq WHERE qrs.c_question_id = qtq.c_id AND qrs.c_stu_quiz_id = '".$start_id."' AND qtq.published = 1";
			$database->SetQuery( $query );
			$score_bycat = $database->LoadObjectList();
			
			$quest_cats = array();
			foreach($score_bycat as $cat){
				$quest_cats[] =  $cat->q_cat;
			}
			
			$query = "SELECT id AS qc_id,title AS qc_category FROM #__categories WHERE `extension` = 'com_joomlaquiz.questions' AND `id` IN (".str_replace(',',implode($database->q(','),$quest_cats),$database->q(',')).") ORDER BY `lft` ASC";
			$database->SetQuery( $query );
			$quest_cats = $database->LoadObjectList();
								
			$q_cate[0][0] = 'Other';
			$q_cate[0][1] = 0;
			$q_cate[0][2] = 0;
			for($i=0;$i<count($quest_cats);$i++)
			{
				$q_cate[$quest_cats[$i]->qc_id][0] = $quest_cats[$i]->qc_category;
				$q_cate[$quest_cats[$i]->qc_id][1] = 0;
				$q_cate[$quest_cats[$i]->qc_id][2] = 0;
			}
									
			for($i=0;$i<count($score_bycat);$i++)
			{
				if(isset($score_bycat[$i])){
					$score = 0;
					$points = $score_bycat[$i]->fuly_score;
					$type = JoomlaquizHelper::getQuestionType($score_bycat[$i]->c_type);
					
					$data = array();
					$data['quest_type'] = $type;
					$data['score_bycat'] = $score_bycat[$i];
					$data['score'] = 0;
					
					$database->setQuery("SELECT `enabled` FROM `#__extensions` WHERE folder = 'joomlaquiz' AND type = 'plugin' AND element = '".$type."'");
					$enabled = $database->loadResult();
					
					if($enabled){
						$appsLib->triggerEvent( 'onScoreByCategory' , $data );
						$full_score = $data['score'] + $points;
					}
					@$q_cate[$score_bycat[$i]->q_cat][1] += $score_bycat[$i]->us_score;
					@$q_cate[$score_bycat[$i]->q_cat][2] += $full_score;
				}
			}
			
			return $q_cate;
		}
		
		public static function isJoomfish()
		{
			if (!defined('_JQ_JF_LANG')) {
				$lang_tag = explode('-',JFactory::getLanguage()->getTag());
				define('_JQ_JF_LANG', $lang_tag[0] );
			}
			
			return true;
		}
		
		public static function JQ_GetJoomFish(&$original, $table='', $field='', $id = 0) {

			$original = $original;
			
		}
		
		public static function jq_substr($str, $start, $length=null) {
			if (function_exists('mb_substr')) {
				if ($length!==null)
					return mb_substr($str, $start, $length);
				else
					return mb_substr($str, $start);
			} else {
				if ($length!==null)
					return substr($str, $start, $length);
				else
					return substr($str, $start);
			}
		}
		
		public static function jq_strpos($haystack, $needle, $offset=null) {
			if (function_exists('mb_strpos')) {
				if ($offset!==null)
					return mb_strpos($haystack, $needle, $offset);
				else
					return mb_strpos($haystack, $needle);
			} else {
				if ($offset!==null)
					return strpos($haystack, $needle, $offset);
				else
					return strpos($haystack, $needle);
			}
		}
		
		public static function getQuestionType($new_qtype_id){
			
			$db = JFactory::getDBO();
			$reg_types = array(); $type = '';
			
			$db->setQuery("SELECT `c_id`, `c_type` FROM #__quiz_t_qtypes");
			$reg_types = $db->loadObjectList();
			
			if(!empty($reg_types)){
				foreach($reg_types as $reg_type){
					$reg_type = (array) $reg_type;
					if($reg_type['c_id'] == $new_qtype_id){
						$type = $reg_type['c_type'];
						break;
					}
				}
			}
			
			return $type;
		}
		
        public static function getVersion() 
        {
			$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/joomlaquiz.xml');
			return (string)$xml->version;
        }
		
		public static function loadAddonsFunctions($type, $class_prefix, $subpath, $is_suffix = true)
		{
			$class_suffix = '';
			if(file_exists(JPATH_SITE.'/plugins/joomlaquiz/'.$subpath.'/'.$type.'.php')){
				$class_suffix = ($is_suffix) ? ucfirst($type) : '';
				if(!class_exists($class_prefix.$class_suffix)){
					require_once(JPATH_SITE.'/plugins/joomlaquiz/'.$subpath.'/'.$type.'.php');
				}
			} else {
				return false;
			}
			
			return $class_suffix;
		}
		
		public static function poweredByHTML(){
			$content = '';
			if(JComponentHelper::getParams('com_joomlaquiz')->get('jq_show_dev_info', 0)) {
				$word = 'component';
				if (intval(md5(JPATH_SITE.'quiz')) % 2 == 0) $word = 'extension';
				$content = '<br/><div style="text-align:center;">Powered by <span title="JoomPlace"><a target="_blank" title="JoomPlace" href="http://www.joomplace.com/">Joomla '.$word.'</a></span> JoomlaQuiz Deluxe Software.</div>';
			}
			return $content;
		}
		
		public static function jq_UTF8string_check($string) {
			
			return preg_match('%(?:
				[\xC2-\xDF][\x80-\xBF]
				|\xE0[\xA0-\xBF][\x80-\xBF]
				|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
				|\xED[\x80-\x9F][\x80-\xBF]
				|\xF0[\x90-\xBF][\x80-\xBF]{2}
				|[\xF1-\xF3][\x80-\xBF]{3}
				|\xF4[\x80-\x8F][\x80-\xBF]{2}
				)+%xs', $string);
		}

		public static function jq_string_jq_substr($str, $offset, $length = NULL) {
			if (JoomlaquizHelper::jq_UTF8string_check($str)) {
				return JoomlaquizHelper::jq_UTF8string_jq_substr($str, $offset, $length);
			} else {
				return JoomlaquizHelper::jq_substr($str, $offset, $length);
			}
		}


		public static function decode_unicode_url($str)
		{
		  $res = '';

		  $i = 0;
		  $max = strlen($str) - 6;
		  while ($i <= $max)
		  {
			$character = $str[$i];
			if ($character == '%' && $str[$i + 1] == 'u')
			{
			  $value = hexdec(JoomlaquizHelper::jq_substr($str, $i + 2, 4));
			  $i += 6;

			  if ($value < 0x0080) // 1 byte: 0xxxxxxx
				$character = chr($value);
			  else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
				$character =
					chr((($value & 0x07c0) >> 6) | 0xc0)
				  . chr(($value & 0x3f) | 0x80);
			  else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
				$character =
					chr((($value & 0xf000) >> 12) | 0xe0)
				  . chr((($value & 0x0fc0) >> 6) | 0x80)
				  . chr(($value & 0x3f) | 0x80);
			}
			else
			  $i++;

			$res .= $character;
		  }

		  return $res . JoomlaquizHelper::jq_substr($str, $i);
		}

		public static function jq_UTF8string_jq_substr($str, $offset, $length = NULL) {
			
			if ( $offset >= 0 && $length >= 0 ) {

				if ( $length === NULL ) {
					$length = '*';
				} else {
					if ( !preg_match('/^[0-9]+$/', $length) ) {
						trigger_error(JText::_('COM_QUIZ_UTF8_EXPECTS_3_PARAMS'), E_USER_WARNING);
						return '';//FALSE;
					}

					$strlen = strlen(utf8_decode($str));
					if ( $offset > $strlen ) {
						return '';
					}

					if ( ( $offset + $length ) > $strlen ) {
					   $length = '*';
					} else {
						$length = '{'.$length.'}';
					}
				}

				if ( !preg_match('/^[0-9]+$/', $offset) ) {
					trigger_error(JText::_('COM_QUIZ_UTF8_EXPECTS_2_PARAMS'), E_USER_WARNING);
					return '';//FALSE;
				}

				$pattern = '/^.{'.$offset.'}(.'.$length.')/us';

				preg_match($pattern, $str, $matches);

				if ( isset($matches[1]) ) {
					return $matches[1];
				}

				return '';//FALSE;

			} else {

				// Handle negatives using different, slower technique
				// From: http://www.php.net/manual/en/function.substr.php#44838
				preg_match_all('/./u', $str, $ar);
				if( $length !== NULL ) {
					return join('',array_slice($ar[0],$offset,$length));
				} else {
					return join('',array_slice($ar[0],$offset));
				}
			}
		}
		
		public static function JQ_load_template($template_name){
			if ( file_exists(JPATH_SITE . '/components/com_joomlaquiz/views/templates/view.html.php') ) {
				if(!class_exists('JoomlaquizViewTemplates')){
					require_once(JPATH_SITE . '/components/com_joomlaquiz/views/templates/view.html.php');
					$view = new JoomlaquizViewTemplates($template_name);
				}
			}
		}
		
		public static function Blnk_replace_answers($qdata) {
		
			$database = JFactory::getDBO();
			$n=1;
			$query = "SELECT `c_id` FROM `#__quiz_t_blank` WHERE `c_question_id` = ".$qdata->c_id;
			$database->setQuery($query);
			$blanks = (array)$database->loadColumn();

            $query = "( SELECT c_id, c_text FROM #__quiz_t_text WHERE c_blank_id  IN ('".implode("','", $blanks)."') ) UNION (SELECT c_id, c_text FROM #__quiz_t_faketext WHERE c_quest_id = ".$qdata->c_id.") ORDER BY rand()";
            $database->setQuery($query);
            $answers = $database->loadColumn(1);
			
			srand ((float)microtime()*1000000);
			shuffle ($answers);
			$html = '';
			if (!empty($answers)) {
				$html = '<div style="clear:both;"></div>';
				foreach($answers as $answer){
					if ($answer != '[empty]')
					$html .= '<div class="jq_draggable_answer '.$qdata->c_image.'" xid="dd_blk_id_'.$n++.'" draggable="true">'.$answer.'</div>';
				}
				$html .= '<div style="clear:both;"></div>';
			}
			
			return str_replace('{answers}', $html, $qdata->c_question);
		}
		
		public static function Blnk_replace_quest($q_id, $q_text, $stu_quiz_id=0, $c_qform=0){
			$database = JFactory::getDBO();
			$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$stu_quiz_id."' AND c_question_id = '".$q_id."'";
			$database->SetQuery( $query );
			$sid = $database->loadResult( );
			
			$query = "SELECT c_answer FROM #__quiz_r_student_blank WHERE c_sq_id = '".$sid."' ORDER BY c_id";
			$database->SetQuery( $query );
			$answers = $database->loadColumn();			

			$query = "SELECT * FROM #__quiz_t_blank WHERE c_question_id=".$q_id;
			$database->setQuery($query);
			$blnk = $database->loadObjectList();
			for($i=0;$i<count($blnk);$i++){
				$replacement = JoomlaQuiz_template_class::JQ_createBlank(($i + 1), (isset($answers[$i])?$answers[$i]:''), $blnk[$i]->css_class, $blnk[$i]->c_id, $c_qform, $q_id);
				if(function_exists('str_ireplace')){
					$q_text = str_ireplace("{Blank".($i + 1)."}", $replacement, $q_text);
				}else{
					$q_text = str_replace("{Blank".($i + 1)."}", $replacement, $q_text);
					$q_text = str_replace("{blank".($i + 1)."}", $replacement, $q_text);
				}
			}

            return '<div style="overflow:auto;" class="jq_blank_wrap">'.$q_text.'</div>';
		}
		
		public static function Blnk_replace_quest_fdb($q_id, $q_text, $stu_quiz_id){
			
			$tag = JFactory::getLanguage()->getTag();
			$lang = JFactory::getLanguage();
			$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);

			$database = JFactory::getDBO();
			
			$query = "SELECT * FROM #__quiz_t_blank WHERE c_question_id=".$q_id." ORDER BY ordering";
			$database->setQuery($query);
			$blnk = $database->loadObjectList();
			
			$query = "SELECT * FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$stu_quiz_id."' AND c_question_id = '".$q_id."'";
			$database->SetQuery( $query );
			$rsq = $database->loadObject( );
						
			$query = "SELECT * FROM #__quiz_r_student_blank WHERE c_sq_id = '".$rsq->c_id."' ORDER BY c_id ";
			$database->SetQuery( $query );
			$tmp = $database->LoadAssocList();
			$q_text = str_replace("{blank", "{Blank", $q_text);			
			for($i=0;$i<count($blnk);$i++){
				if(!$blnk[$i]->ordering)
					$blnk[$i]->ordering = $i;
				$query = "SELECT c_id, c_text FROM #__quiz_t_text WHERE c_blank_id = ".$blnk[$i]->c_id." ORDER BY ordering";
				$database->setQuery($query);
				$tmp2 = $database->loadObjectList();
				$c_texts = array();
				foreach($tmp2 as $t=>$cd) {		
					JoomlaquizHelper::JQ_GetJoomFish($tmp2[$t]->c_text, 'quiz_t_text', 'c_text', $tmp2[$t]->c_id);
					$c_texts[] = $tmp2[$t]->c_text;
				}
			
				$replacement = JoomlaQuiz_template_class::JQ_createBlank_fdb($c_texts, (isset($tmp[$i]['c_answer'])? $tmp[$i]['c_answer']: ''), 'red', $tmp[$i]['is_correct']);
				
				if(function_exists('str_ireplace')){
					$q_text = str_ireplace("{Blank".($blnk[$i]->ordering + 1)."}", $replacement, $q_text);
					$q_text = str_ireplace("{blank".($blnk[$i]->ordering + 1)."}", $replacement, $q_text);
				}else{
					$q_text = str_replace("{Blank".($blnk[$i]->ordering + 1)."}", $replacement, $q_text);
					$q_text = str_replace("{blank".($blnk[$i]->ordering + 1)."}", $replacement, $q_text);
				}
			}
			return $q_text;
		}
		
		public static function Blnk_replace_quest_review($q_id, $q_text){
			
			$database = JFactory::getDBO();

			$query = "SELECT * FROM #__quiz_t_blank WHERE c_question_id=".$q_id;
			$database->setQuery($query);
			$blnk = $database->loadObjectList();
			for($i=0;$i<count($blnk);$i++)
			{
                $c_texts = array();

				$query = "SELECT c_id, c_text FROM #__quiz_t_text WHERE c_blank_id = ".$blnk[$i]->c_id." ORDER BY ordering ";
				$database->setQuery($query);
				$tmp2 = $database->loadObjectList();
				foreach($tmp2 as $t=>$cd) {
					$c_texts[] = $tmp2[$t]->c_text;
				}

                $query = "SELECT `c_id`, `c_text` FROM `#__quiz_t_faketext` WHERE `c_quest_id` = ".(int)$q_id;
                $database->setQuery($query);
                $tmp3 = $database->loadObjectList();
                foreach($tmp3 as $t=>$cd) {
                    $c_texts[] = $tmp3[$t]->c_text;
                }

				$replacement = JoomlaQuiz_template_class::JQ_createBlank_review(implode(', ',$c_texts));		
				
				if(function_exists('str_ireplace')){
					$q_text = str_ireplace("{Blank".($blnk[$i]->ordering + 1)."}", $replacement, $q_text);
				}else{
					$q_text = str_replace("{Blank".($blnk[$i]->ordering + 1)."}", $replacement, $q_text);
					$q_text = str_replace("{blank".($blnk[$i]->ordering + 1)."}", $replacement, $q_text);
				}
			}
			return $q_text;
		}
		
		public static function JQ_GetItemId(){
			static $jqItemid = -1;
			
			$ret = '';
			if($jqItemid == -1)
			{	
				global $Itemid;
				if(JFactory::getApplication()->input->get('Itemid') != 0) {
				  $Itemid = $jqItemid = JFactory::getApplication()->input->get('Itemid');
				  $ret = '&Itemid='.$jqItemid;
				}else{
				  $Itemid = $jqItemid = 0;
				}
			} elseif ($jqItemid > 0) {
				$ret = '&Itemid='.$jqItemid;
			}

			return $ret;
		}
		
		public static function JQ_Email($sid, $email_to) {
			
			$database = JFactory::getDBO();

			$query = "SELECT q.c_title AS quiz_id FROM #__quiz_t_quiz AS q, #__quiz_r_student_quiz AS sq WHERE sq.c_id = '".$sid."' AND sq.c_quiz_id = q.c_id";
			$database->SetQuery( $query );
			$info = $database->LoadAssocList();
			$info = $info[0];
			
			if (!preg_match("/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$/i", $email_to)) {
				return false;
			}
						
			if(!class_exists('JoomlaquizModelPrintresult')){
				require_once(JPATH_SITE.'/components/com_joomlaquiz/models/printresult.php');
			}
			$str = JoomlaquizModelPrintresult::JQ_PrintResultForMail($sid);
			
			$email = $email_to;
			$subject = JText::_('COM_QUIZ_RESULTS').'('.$info['quiz_id'].')';
			$message = html_entity_decode($str, ENT_QUOTES);
			
			$config = new JConfig();
			$mailfrom = $config->mailfrom;
			$fromname = $config->fromname;
			
			if ($mailfrom != "" && $fromname != "") {
				$adminName2 = $fromname;
				$adminEmail2 = $mailfrom;
			} else {
				
				$query = "SELECT u.`name`, u.`email`"
				. "\n FROM #__users as u"
				. "\n LEFT JOIN `#__user_usergroup_map` as um ON um.`user_id` = u.`id`"
				. "\n LEFT JOIN `#__usergroups` as ug ON ug.`id` = um.`group_id`"
				. "\n WHERE ug.`title` = 'Super Users'"
				;
				$database->setQuery( $query );
				$rows = $database->loadObjectList();
				$row2 			= $rows[0];
				$adminName2 	= $row2->name;
				$adminEmail2 	= $row2->email;
			}
			
			$jmail = JFactory::getMailer();
			return $jmail->sendMail($adminEmail2, $adminName2, $email, $subject, $message, 1, null, null, null, null, null);
		}
		
		public static function JQ_ShowText_WithFeatures($text, $force_compatibility = false) {
			
			jimport( 'joomla.html.parameter' );
			
			// Black list of mambots:
			$banned_bots = array();
			$row = new stdclass();
			
			$row->id = null;
			$row->text = $text;
			$row->introtext = '';
			$params = new JInput();
			$new_text = $text;

			$dispatcher	= JDispatcher::getInstance();

			JPluginHelper::importPlugin('content');
			$results = $dispatcher->trigger('onContentPrepare', array ('com_joomlaquiz', &$row, &$params, 0));
			$results = $dispatcher->trigger('onPrepareContent', array (& $row, & $params, 0));
			$new_text = $row->text;
		 
			return $new_text;
		}
		
		public static function isQuizAttepmts($quiz_id, $lid=0, $rel_id=0, $order_id=0, &$msg) {
		
			$my = JFactory::getUser();
			$database = JFactory::getDBO();
			
			if (!$quiz_id)
				return false;
			
			$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '{$quiz_id}'";
			$database->setQuery($query);
			$quiz = $database->loadObjectList();
			
			if (!isset($quiz[0])) 
				return false;
			$quiz = $quiz[0];
			
			$unique_pass_str = '';
            $cookieQuizupi = JFactory::getApplication()->input->cookie->get('quizupi');
			if (!$my->id && !empty($cookieQuizupi[$quiz_id])) {
				$unique_pass_id = $cookieQuizupi[$quiz_id];
				$unique_pass_str = " `unique_pass_id` = '".$unique_pass_id."' AND `c_lid` = 0 AND `c_rel_id` = 0 ";	
			} elseif ($lid && $my->id) {
				$unique_pass_str = " `c_student_id` = '".$my->id."' AND `c_lid` = '".$lid."' ";
			} elseif ($my->id) {
				$unique_pass_str = " `c_student_id` = '".$my->id."' ";
			}
			
			if (!$unique_pass_str) {	
				return true;
			}
			
			JPluginHelper::importPlugin('content');
			$dispatcher = JEventDispatcher::getInstance();
            $result_event = $dispatcher->trigger('onQuizCustomFieldsFromUser');
            $cust_params = '{}';
            if($result_event && !empty($result_event)){
                $cust_params = $result_event[0];
            }
			
			//stand alone quiz	or  free learn path
			if (!$order_id && !$rel_id) {
				if ($quiz->c_number_times) {			
					//no period, just check number of tries	
					$query = "SELECT COUNT(*) FROM #__quiz_r_student_quiz WHERE `c_quiz_id` = '".$quiz_id."' ". ($quiz->c_allow_continue ? ' AND c_finished = 1 ': ''). " AND {$unique_pass_str} AND `params` = ".$database->quote($cust_params)." ";
					$database->SetQuery( $query );
					$number_times_passed = (int)$database->loadResult();
					
					if ($number_times_passed < $quiz->c_number_times) {
						return true;
					} elseif ($quiz->c_min_after) {
						$query = "SELECT `c_date_time` FROM #__quiz_r_student_quiz WHERE `c_quiz_id` = '".$quiz_id."' ". ($quiz->c_allow_continue ? ' AND c_finished = 1 ': ''). " AND {$unique_pass_str} ORDER BY `c_id` DESC LIMIT ".$quiz->c_number_times;
						$database->SetQuery( $query );
						$user_tries = $database->loadColumn();
						$last_try_date = strtotime($user_tries[count($user_tries)-1]);
					
						if ($last_try_date + ($quiz->c_min_after*60) > strtotime(JFactory::getDate()) ){
							if ($quiz->c_once_per_day && date('d', $last_try_date) != date('d')) {
								return true;
							}
							$msg = $last_try_date + ($quiz->c_min_after*60) - time();
							return false;
						} else {
							return true;
						}
					} else {
						return false;
					}			
				} else {
					return true;
				}		
			} else {
			    //product
				if ($order_id < 1000000000) {
					$query = "SELECT qp.*"
					. "\n FROM #__virtuemart_orders AS vm_o"
					. "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
					. "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
					. "\n WHERE vm_o.virtuemart_user_id = {$my->id} AND vm_o.virtuemart_order_id = $order_id AND qp.id = $rel_id AND vm_o.order_status IN ('C')"
					;
				} else {
					$query = "SELECT qp.*"
					. "\n FROM `#__quiz_payments` AS p"
					. "\n INNER JOIN `#__quiz_products` AS qp ON qp.pid = p.pid"
					. "\n WHERE p.user_id = {$my->id} AND p.id = '".($order_id-1000000000)."' AND qp.id = '{$rel_id}' AND p.status IN ('Confirmed') "
					;
				}
				$database->SetQuery( $query );
				$rel_check = $database->loadObjectList();
				if(empty($rel_check)) {
					return false;
				}

				$query = "SELECT attempts FROM `#__quiz_products` WHERE `id` = '{$rel_id}' ";		
				$database->SetQuery( $query );
				$product_params_attempts = (int)$database->loadResult();

                if (!$product_params_attempts) {
                    return true;
                }

				$product_quantity = 1;
				if ($order_id < 1000000000) {
					$query = "SELECT vm_oi.product_quantity"
					. "\n FROM #__virtuemart_orders AS vm_o"
					. "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
					. "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
					. "\n WHERE vm_o.virtuemart_user_id = {$my->id} AND vm_o.virtuemart_order_id = $order_id AND qp.id = $rel_id AND vm_o.order_status IN ('C')"
					;
					$database->SetQuery( $query );
					$product_quantity = ($database->loadResult()) ? (int)$database->loadResult() : 1;
				}

				if($rel_check[0]->type == 'l') {
                    //$query = "SELECT attempts FROM #__quiz_lpath_stage WHERE uid = '{$my->id}' AND oid = '{$order_id}' AND rel_id = '{$rel_id}' AND lpid = '{$rel_check[0]->rel_id}' AND qid = '{$quiz_id}'";
                    $productUsedAttempts = JoomlaquizHelper::getProductUsedAttempts($rel_id, $product_quantity);
                    if(isset($productUsedAttempts['quizzes']['left'][$quiz_id]) && $productUsedAttempts['quizzes']['left'][$quiz_id]) {
                        return true;
                    }

				} else {
					$query = "SELECT attempts FROM #__quiz_products_stat WHERE uid = '{$my->id}' AND oid = '{$order_id}' AND qp_id = '{$rel_id}' ";
                    $database->SetQuery( $query );
                    $products_stats_attempts = (int)$database->loadResult();

                    if ($products_stats_attempts < $product_params_attempts * $product_quantity){
                        return true;
                    }
				}

			}
			
			return false;
		}
		
		public static function JQ_checkPackage($package_id, $rel_id, $vm=1) {
		
			$database = JFactory::getDBO();
			$my = JFactory::getUser();
			$mainframe = JFactory::getApplication();

			$rel_id = intval($rel_id);
			$package_id = intval($package_id);
			$quiz_params = array();
			$quiz_params[0] = new stdClass;
				
			if (!$rel_id || !$package_id) {
				$quiz_params[0]->error = 1;
				$quiz_params[0]->message = '';
				return $quiz_params[0];
			}		
		
			if ($vm) {
				$query = "SELECT qp.*"
				. "\n FROM #__virtuemart_orders AS vm_o"
				. "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
				. "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
				. "\n WHERE vm_o.virtuemart_user_id = {$my->id} AND vm_o.virtuemart_order_id = $package_id AND qp.id = $rel_id AND vm_o.order_status IN ('C')"
				;
			} else {
				$query = "SELECT qp.*"
				. "\n FROM `#__quiz_payments` AS p"
				. "\n INNER JOIN `#__quiz_products` AS qp ON qp.pid = p.pid"
				. "\n WHERE p.user_id = {$my->id} AND p.id = '".($package_id-1000000000)."' AND qp.id = '{$rel_id}' AND p.status IN ('Confirmed') "
				;
			}
			$database->SetQuery( $query );
			$rel_check = $database->loadObjectList();
			if(empty($rel_check)) {
				$quiz_params[0]->error = 1;
				$quiz_params[0]->message = '<p align="left">'.JText::_('COM_QUIZ_LPATH_NOT_AVAILABLE').'</p>';
				return $quiz_params[0];
			}
		
			$product_data = $rel_check[0];
					
			$products_stat = array();
			$query = "SELECT *"
			. "\n FROM #__quiz_products_stat"
			. "\n WHERE uid = '{$my->id}' AND qp_id = '{$rel_id}' "
			. "\n AND oid = '$package_id' "
			;
			$database->SetQuery( $query );
			$products_stat = $database->loadObjectList('qp_id');
			
			//Check for xdays, period
			if($product_data->xdays) {
				if(!empty($products_stat) && array_key_exists($rel_id, $products_stat)) {
					$confirm_date = strtotime($products_stat[$rel_id]->xdays_start);
				} else {
					if ($vm) {
						$query = "SELECT UNIX_TIMESTAMP(order_history.created_on) "
							. "\n FROM #__virtuemart_order_histories AS order_history"
							. "\n INNER JOIN #__virtuemart_order_items AS order_item ON order_item.virtuemart_order_id = order_history.virtuemart_order_id"
							. "\n WHERE order_history.order_status_code = 'C' AND order_item.virtuemart_order_id = $package_id AND order_item.virtuemart_product_id = '{$product_data->pid}'"
							. "\n ORDER BY order_history.created_on DESC"
							. "\n LIMIT 1"
							;
					} else {
						$query = "SELECT UNIX_TIMESTAMP(p.confirmed_time) "
								. "\n FROM #__quiz_payments AS p"
								. "\n WHERE p.id = '".($package_id-1000000000)."' AND p.status = 'Confirmed' AND  p.pid = '{$product_data->pid}'"
								. "\n ORDER BY p.confirmed_time DESC"
								. "\n LIMIT 1"
								;
					}
					$database->setQuery($query);
					$confirm_date = $database->loadResult();
				}
				
				if($confirm_date) {
					$ts_day_end = $confirm_date + $product_data->xdays*24*60*60;
					if(strtotime(JFactory::getDate()) > $ts_day_end) {
						$quiz_params[0]->error = 1;
						$quiz_params[0]->message = '<p align="left">'.JText::_('COM_ACCESS_EXPIRED').'</p>';
						return $quiz_params[0];
					}
				} else {
					$quiz_params[0]->error = 1;
					$quiz_params[0]->message = '<p align="left">'.($product_data->type == 'l' ? JText::_('COM_LPATH_NOT_AVAILABLE') : JText::_('COM_QUIZ_NOT_AVAILABLE')).'</p>';
					return $quiz_params[0];
				}
		
			} else if (($product_data->period_start && $product_data->period_start != '0000-00-00')
					|| ($product_data->period_end && $product_data->period_end != '0000-00-00')) {
				
				if(!empty($products_stat) && array_key_exists($rel_id, $products_stat)) {
					$product_data->period_start = $products_stat[$rel_id]->period_start;
					$product_data->period_end = $products_stat[$rel_id]->period_end;
				}	
				
				$ts_start = null;
				if($product_data->period_start && $product_data->period_start != '0000-00-00') {
					$ts_start = strtotime($product_data->period_start . ' 00:00:00');
				}
		
				$ts_end = null;
				if($product_data->period_end && $product_data->period_end != '0000-00-00') {
					$ts_end = strtotime($product_data->period_end . ' 23:59:59');
				}
				$ts = strtotime(JFactory::getDate());
				if(($ts_start && $ts_start > $ts) || ($ts_end && $ts_end < $ts)) {
					$quiz_params[0]->error = 1;
					$quiz_params[0]->message = '<p align="left">'.JText::_('COM_ACCESS_EXPIRED').'</p>';
					return $quiz_params[0];
				}
			}
			
			//Check attempts
            if((int)$product_data->attempts){
                $product_quantity = 1;
                if($vm){
                    $query = "SELECT vm_oi.product_quantity"
                    . "\n FROM #__virtuemart_orders AS vm_o"
                    . "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
                    . "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
                    . "\n WHERE vm_o.virtuemart_user_id = {$my->id} AND vm_o.virtuemart_order_id = ".$package_id." AND qp.id = $rel_id AND vm_o.order_status IN ('C')"
                    ;
                    $database->SetQuery( $query );
                    $product_quantity = ($database->loadResult()) ? (int)$database->loadResult() : 1;
                }

                if($product_data->type == 'q') {
                    $attempts = (!empty($products_stat) && array_key_exists($rel_id, $products_stat) && $products_stat[$rel_id]->attempts ? $products_stat[$rel_id]->attempts : 0);
                    if ($product_data->attempts && ($product_data->attempts * $product_quantity) <= $attempts) {
                        $quiz_params[0]->error = 1;
                        $quiz_params[0]->message = '<p align="left">' . JText::_('COM_ACCESS_EXPIRED') . '</p>';
                        return $quiz_params[0];
                    }
                }
                else if($product_data->type == 'l'){
                    $query = "SELECT * FROM `#__quiz_lpath_stage` 
                              WHERE `uid` = {$my->id} AND `rel_id` = {$rel_id}  AND `lpid` = {$rel_check[0]->rel_id} 
                              AND `type` = 'q' AND oid = '{$package_id}' ";
                    $database->SetQuery( $query );
                    $quiz_lpath_stage = $database->loadObjectList();

                    $yet_attempts = true;
                    if($quiz_lpath_stage && is_array($quiz_lpath_stage) && !empty($quiz_lpath_stage)){
                        for($i=0; $i<count($quiz_lpath_stage); $i++){
                            if($quiz_lpath_stage[$i]->attempts >= ($product_data->attempts * $product_quantity)){
                                $yet_attempts = false;
                            }
                        }
                    }
                    else {
                        if($product_data->attempts * $product_quantity < 1){
                            $yet_attempts = false;
                        }
                    }

                    if(!$yet_attempts){
                        $quiz_params[0]->error = 1;
                        $quiz_params[0]->message = '<p align="left">' . JText::_('COM_ACCESS_EXPIRED') . '</p>';
                        return $quiz_params[0];
                    }
                }
            }
			
			if($rel_check[0]->type == 'q') {
				$mainframe->redirect(JRoute::_(JURI::root()."index.php?option=com_joomlaquiz&view=quiz&package_id={$package_id}&rel_id={$rel_id}&quiz_id={$rel_check[0]->rel_id}&force=1"));
			}
			else if($rel_check[0]->type == 'l') {
				$lpath_id = $rel_check[0]->rel_id;
                return $lpath_id;
			}
		}
		
		public static function getTotalScore($qch_ids, $quiz_id){
			
			jimport('joomla.filesystem.folder');

			$qch_ids = $qch_ids ? $qch_ids : 0;

			$database = JFactory::getDBO();
			$query = $database->getQuery(true);

			//$query = "SELECT SUM(c_point) FROM #__quiz_t_question WHERE c_id IN (".$qch_ids.") AND published = 1 AND c_type <> 11";
			$query->select ('SUM(c_point)')
				->from($database->qn('#__quiz_t_question'))
				//->where("`c_id` IN (".$database->qn($qch_ids).")")
				->where("`c_id` IN (".$qch_ids.")")
				->where($database->qn('published')." = 1")
				->where($database->qn('c_type')." <> 11");

			$database->SetQuery( $query );
			$max_score = $database->LoadResult();
			
			$appsLib = JqAppPlugins::getInstance();
			$plugins = $appsLib->loadApplications();
						
			$folders = JFolder::folders(JPATH_SITE.'/plugins/joomlaquiz/', '.', false, false);
			if(!empty($folders)){
				foreach($folders as $folder){
					
					$data = array();
					$data['quest_type'] = $folder;
					$data['qch_ids'] = $qch_ids;
					$data['quiz_id'] = $quiz_id;
					$data['max_score'] = 0;
					
					$database->setQuery("SELECT `enabled` FROM `#__extensions` WHERE folder = 'joomlaquiz' AND type = 'plugin' AND element = '".$folder."'");
					$enabled = $database->loadResult();
					
					if($enabled){
						$appsLib->triggerEvent( 'onTotalScore' , $data );
						$max_score += $data['max_score'];
					}
				}
			}
			
			return $max_score;			
		}
		
		public static function getJavascriptFunctions(){
			jimport('joomla.filesystem.folder');
			$paths = array();
			$folders = JFolder::folders(JPATH_SITE.'/plugins/joomlaquiz/', '.', false, false);
			if(!empty($folders)){
				foreach($folders as $folder){
					if(JFolder::exists(JPATH_SITE.'/plugins/joomlaquiz/'.$folder.'/js/functions/')){
						$func_files = JFolder::files(JPATH_SITE.'/plugins/joomlaquiz/'.$folder.'/js/functions/', '.', false, false, array('index.html'));
						if(!empty($func_files)){
							foreach($func_files as $func_file){
								$paths[] = JURI::root().'plugins/joomlaquiz/'.$folder.'/js/functions/'.$func_file;
							}
						}
					}
				}
			}
			return $paths;
		}
		
		public static function getJavascriptIncludes($dir='includes')
		{
			jimport('joomla.filesystem.folder');
			
			$folders = JFolder::folders(JPATH_SITE.'/plugins/joomlaquiz/', '.', false, false);
			if(!empty($folders)){
				foreach($folders as $folder){
					if(JFolder::exists(JPATH_SITE.'/plugins/joomlaquiz/'.$folder.'/js/'.$dir.'/')){
						$include_files = JFolder::files(JPATH_SITE.'/plugins/joomlaquiz/'.$folder.'/js/'.$dir.'/', '.', false, false, array('index.html'));
						
						if(!empty($include_files)){
							foreach($include_files as $include_file){
								echo "\n";
								include(JPATH_SITE.'/plugins/joomlaquiz/'.$folder.'/js/'.$dir.'/'.$include_file);
							}
						} else {
							echo "";
						}
					}
				}
			}
		}

        /*
         * for Learning Paths
         *
         * Product's attempts: counting the number of passes of each quiz included in the LP.
         * Each quiz from LP can be completed as many times as indicated in the product.
         */
		public static function getProductUsedAttempts($product_id, $product_quantity=1)
        {
            $usedAttempts = array('product'=>array(), 'quizzes'=>array('used'=>array(), 'left'=>array()));
            $user = JFactory::getUser();

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            //$query->clear();
            $query->select('*')
                ->from($db->qn('#__quiz_products'))
                ->where($db->qn('id') .'='. $db->q($product_id));
            $db->setQuery($query);
            $product = $db->loadObject();

            if(empty($product->attempts)){
                return $usedAttempts;
            }

            $usedAttempts['product']['total'] = $product->attempts;

            if($product->type == 'l'){
                $query->clear();
                $query->select($db->qn('qid'))
                    ->from($db->qn('#__quiz_lpath_quiz'))
                    ->where($db->qn('lid') .'='. $db->q($product->rel_id))
                    ->where($db->qn('type') .'='. $db->q('q'));
                $db->setQuery($query);
                $quizzes_in_lp = $db->loadColumn();

                $usedAttempts['product']['is_expired'] = true;

                if(!empty($quizzes_in_lp)) {
                    foreach ($quizzes_in_lp as $quiz_in_lp){
                        $query->clear();
                        $query->select('COUNT(`c_id`)')
                            ->from($db->qn('#__quiz_r_student_quiz', 'qrsq'))
                            ->where($db->qn('qrsq.c_student_id') .'='. $db->q($user->id))
                            ->where($db->qn('qrsq.c_rel_id') .'='. $db->q($product->id))
                            ->where($db->qn('qrsq.c_quiz_id') .'='. $db->q($quiz_in_lp));
                        $db->setQuery($query);
                        $quizz_attempts = (int)$db->loadResult();

                        $usedAttempts['quizzes']['used'][$quiz_in_lp] = $quizz_attempts;
                        $usedAttempts['quizzes']['left'][$quiz_in_lp] = ($usedAttempts['product']['total'] * $product_quantity) - $quizz_attempts;
                        if((int)$usedAttempts['quizzes']['left'][$quiz_in_lp] > 0){
                            $usedAttempts['product']['is_expired'] =  false;
                        }
                    }

                }
            }

            return $usedAttempts;
        }

}