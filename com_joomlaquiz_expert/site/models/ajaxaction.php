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
 * Ajax Action Model.
 *
 */ 
 
require_once( JPATH_ROOT .'/components/com_joomlaquiz/libraries/apps.php' ); 

class JoomlaquizModelAjaxaction extends JModelList
{	
	public function JQ_analizeAjaxRequest(){
	
		$jq_task = JFactory::getApplication()->input->get('ajax_task', '');
		JFactory::getDocument()->setType('xml');
		$this->JQ_process_ajax($jq_task);
		exit();
	}
	
	public function JQ_process_ajax($jq_task){
		@ob_start();
		$jq_ret_str = '';

        switch ($jq_task) {
			case 'start':			$jq_ret_str = $this->JQ_StartQuiz();			break;
			case 'next':			$jq_ret_str = $this->JQ_NextQuestion();	    	break;
			case 'nextFinish':		$jq_ret_str = $this->JQ_NextQuestionFinish();	break;
			case 'finish_stop':		$jq_ret_str = $this->JQ_FinishQuiz();		    break;
			case 'email_results':	$jq_ret_str = $this->JQ_emailResults();	    	break;
			case 'review_start':	$jq_ret_str = $this->JQ_StartReview();		    break;
			case 'review_next':		$jq_ret_str = $this->JQ_NextReview();		    break;
			case 'preview_quest':	$jq_ret_str = $this->JQ_QuestPreview();	    	break;
			case 'next_preview':	$jq_ret_str = $this->JQ_NextPreview();		    break;
			case 'goto_quest':		$jq_ret_str = $this->JQ_SeekQuestion();	    	break;
			case 'showpage':		$jq_ret_str = $this->JQ_ShowPage();		    	break;
			case 'prev':			$jq_ret_str = $this->JQ_PrevQuestion();	    	break;
			case 'check_blank':		$jq_ret_str = $this->JQ_CheckBlank();	        break;
			case 'ajax_plugin':		$jq_ret_str = $this->JQ_ajaxPlugin();	        break;
			
			default:		
			break;
		}
		
		$jq_ret_str = JHtml::_('content.prepare',$jq_ret_str);
		
		$regex  = '#href="index.php\?([^"]*)#m';
		$jq_ret_str = preg_replace_callback($regex, function ($matches) {return 'href="'.JRoute::_(str_replace('href="','',$matches[0]));}, $jq_ret_str);
		
		echo "\n"."some notices :)";
        $debug_str = @ob_get_contents();

        @ob_end_clean();
		@ob_end_clean();
		if ($jq_ret_str != "") {
			@header ('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
			@header ('Last-Modified: ' . JHtml::_('date',time(),'D, d M Y H:i:s') . ' GMT');
			@header ('Cache-Control: no-cache, must-revalidate');
			@header ('Pragma: no-cache');
			@header ('Content-Type: text/xml; charset="utf-8"');
			echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
			echo '<response>' . "\n";
			echo $jq_ret_str;
			echo "\t" . '<debug><![CDATA['.$debug_str.']]></debug>' . "\n";
			echo '</response>' . "\n";
		} else {
			@header ('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
			@header ('Last-Modified: ' . JHtml::_('date',time(),'D, d M Y H:i:s') . ' GMT');
			@header ('Cache-Control: no-cache, must-revalidate');
			@header ('Pragma: no-cache');
			@header ('Content-Type: text/xml; charset="utf-8"');
			echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
			echo '<response>' . "\n";
			echo "\t" . '<task>failed</task>' . "\n";
			echo "\t" . '<info>boom</info>' . "\n";
			echo "\t" . '<debug><![CDATA['.$debug_str.']]></debug>' . "\n";
			echo '</response>' . "\n";
		}
	}
	
	public function JQ_ajaxPlugin()
	{
		$plg_task = JFactory::getApplication()->input->get('plg_task');
		$quest_type = JFactory::getApplication()->input->get('quest_type');
		
		$appsLib = JqAppPlugins::getInstance();
		$plugins = $appsLib->loadApplications();
		
		$data = array();
		$data['quest_type'] = $quest_type;
		$data['plg_task'] = $plg_task;
		
		$appsLib->triggerEvent( 'onAjaxPlugin' , $data );
		die;
	}
	
	protected function userHasAccess($quiz, $user = null){
		
		if(!is_object($quiz)){		
		    $database = JFactory::getDbo();
			$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".(int)$quiz."'";
			$database->SetQuery ($query );
			$quiz = $database->LoadObject();
		}
		
		if($user===null){
			$user = JFactory::getUser();
		}else{
			if(!is_object($user)){
				$user = JFactory::getUser($user);
			}
		}
		
		if(
			!$quiz->published 
			|| 
			(!$user->authorise('core.view', 'com_joomlaquiz.quiz.'.$quiz->c_id) /* c_guest must be excluded */&& (!$user->id && !$quiz->c_guest))
		){ 
			return false; 
		}else{
			return true;
		}
	}
	
	public function JQ_StartQuiz() {
		
		$database = JFactory::getDBO(); 
		$my = JFactory::getUser();
		
		$ret_str = '';
		
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );

        $session = JFactory::getSession();

        $session_lid = $session->get('quiz_lid');
		$lid = intval(empty($session_lid) ? 0 : $session_lid);

        $session_rel_id = $session->get('quiz_rel_id');
        $rel_id = intval(empty($session_rel_id) ? 0 : $session_rel_id);

        $session_package_id = $session->get('quiz_package_id');
        $package_id = intval(empty($session_package_id) ? 0 : $session_package_id);
		
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		
		if (!empty($quiz)) { $quiz = $quiz[0];
		} else { return $ret_str; }
		
		if(!$this->userHasAccess($quiz, $my)){
			return $ret_str;
		}

		if ($quiz_id) {
			
			$msg = '';
			if (!JoomlaquizHelper::isQuizAttepmts($quiz_id, $lid, $rel_id, $package_id, $msg))
				return '';
			
			$user_unique_id = md5(uniqid(rand(), true));
			
			$unique_pass_id = '';
            $cookieQuizupi = JFactory::getApplication()->input->cookie->get('quizupi');
			if (!empty($cookieQuizupi[$quiz_id])) {
				$unique_pass_id = $cookieQuizupi[$quiz_id];
			} elseif ($my->id) {
				$query = "SELECT unique_pass_id FROM #__quiz_r_student_quiz WHERE c_quiz_id = '".$quiz_id."' AND `c_order_id` = '".$package_id."' AND c_rel_id = '".$rel_id."' AND c_lid = '".$lid."' AND c_order_id = '".$package_id."' AND c_student_id = '".$my->id."' ORDER BY c_id DESC";
				$database->SetQuery( $query );
				$unique_pass_id = $database->LoadResult();			
			}
			
			if (!$unique_pass_id) {
				$unique_pass_id = md5(uniqid(rand(), true));
			}

            JFactory::getApplication()->input->cookie->set("quizupi[$quiz_id]", $unique_pass_id, 0);
			
			$stu_quiz_id = 0;
			$old_quiz = false;
			if ($my->id && $quiz->c_allow_continue) {
				$query = "SELECT c_id FROM #__quiz_r_student_quiz WHERE c_student_id = '{$my->id}' AND c_rel_id = '$rel_id' AND c_lid = '$lid' AND c_order_id = '$package_id' AND c_quiz_id = '$quiz_id' AND c_finished = 0 ORDER BY c_id DESC";
				$database->SetQuery($query);
				$stu_quiz_id = $database->loadResult();
			}
			
			if (!$stu_quiz_id) {

				if ($my->id) {
					$user_name = $my->username;
					$user_email = $my->email;
				} else {
					$user_name = addslashes(JFactory::getApplication()->input->getString('uname', ''));
					$user_surname = addslashes(JFactory::getApplication()->input->getString('usurname', ''));
					$user_email = addslashes(JFactory::getApplication()->input->getString('uemail', ''));
				}

				JPluginHelper::importPlugin('content');
				$dispatcher = JEventDispatcher::getInstance();
				list($cust_params) = $dispatcher->trigger('onQuizCustomFieldsRetrieve');
				if(!$cust_params) $cust_params = '{}';

                /*
                 * @imoortant: do not use JDate, as this will cause double time transform
                 */
                $quiz_time = date( 'Y-m-d H:i:s');
				$query = "INSERT INTO #__quiz_r_student_quiz (c_order_id, c_rel_id, c_lid, c_quiz_id, c_student_id, c_total_score, c_total_time, c_date_time, c_passed, unique_id, unique_pass_id, c_finished, user_email, user_name, user_surname, params)"
			. "\n VALUES('".$package_id."', '".$rel_id."', '".$lid."', '".$quiz_id."', '".$my->id."', '0', '0', NOW(), '0', '".$user_unique_id."', '".$unique_pass_id."', 0, '".$user_email."', '".$user_name."', '".$user_surname."', ".$database->quote($cust_params).")";
				$database->SetQuery($query);
				$database->query();
				$stu_quiz_id = $database->insertid();

				if($rel_id && $my->id) {
					if ($package_id < 1000000000) {
						$query = "SELECT qp.*, vm_oh.created_on AS xdays_start "
							. "\n FROM #__virtuemart_orders AS vm_o"
							. "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
							. "\n LEFT JOIN #__virtuemart_order_histories AS vm_oh ON vm_oi.virtuemart_order_id = vm_oh.virtuemart_order_id AND vm_oh.order_status_code IN ('C')"
							. "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
							. "\n WHERE vm_o.virtuemart_user_id = " . $my->id . "  AND vm_o.virtuemart_order_id = $package_id AND vm_o.order_status IN ('C') AND qp.id = " . $rel_id
							;
					} else {
						$query = "SELECT qp.*, p.confirmed_time AS xdays_start "
						. "\n FROM `#__quiz_payments` AS p"
						. "\n INNER JOIN `#__quiz_products` AS qp ON qp.pid = p.pid"
						. "\n WHERE p.user_id = {$my->id} AND p.id = '".($package_id-1000000000)."' AND qp.id = '{$rel_id}' AND p.status IN ('Confirmed') "
						;
					}
					
					$database->SetQuery( $query );
					$rel_check = $database->loadObjectList();
					if(empty($rel_check)) {
						echo '<p align="left">'.JText::_('COM_QUIZ_LPATH_NOT_AVAILABLE').'</p>';
						return '';
					}
					
					if($rel_check[0]->type == 'l') {
						$query = "SELECT * FROM `#__quiz_lpath` WHERE `id` = '{$rel_check[0]->rel_id}' AND published = 1";
						$database->SetQuery( $query );
						$lpath = $database->loadObjectList();
						if(!empty($lpath)) {
							$lpath = $lpath[0];
						} else {
							return '';
						}

						$query = "DELETE FROM `#__quiz_lpath_stage`"
						. "\n WHERE uid = {$my->id} AND rel_id = {$rel_id}  AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$quiz_id} AND oid = '{$package_id}'"
						;
						$database->SetQuery( $query );
						$database->execute();
						
						$query = "INSERT INTO `#__quiz_lpath_stage`"
						. "\n SET uid = {$my->id}, oid = '{$package_id}', rel_id = {$rel_id}, lpid = {$lpath->id}, `type` = 'q', qid = {$quiz_id}, stage = 0"
						;
						$database->SetQuery( $query );
						$database->execute();

					}
					
					$query = "SELECT `id` FROM #__quiz_products_stat WHERE `uid` = $my->id AND `qp_id` = $rel_id AND oid = '{$package_id}'";
					$database->SetQuery( $query );
					if(!$database->loadResult()) {
						$query = "INSERT INTO #__quiz_products_stat"
						. "\n SET uid = {$my->id}, `qp_id` = {$rel_id}, `xdays_start` = '{$rel_check[0]->xdays_start}',"
						. "\n `period_start` = '{$rel_check[0]->period_start}', `period_end` = '{$rel_check[0]->period_end}', oid = '{$package_id}', `attempts` = 0";
						$database->SetQuery( $query );
						$database->execute();
					}
					
					if(!$quiz->c_allow_continue){
						$query = "UPDATE #__quiz_products_stat SET `attempts` = `attempts`+1 WHERE uid = $my->id AND `qp_id` = $rel_id AND oid = $package_id ";
						$database->SetQuery( $query );
						$database->execute();
					}
					
				} else if ($lid && $my->id) {
					$query = "SELECT * FROM `#__quiz_lpath` WHERE `id` = '{$lid}' AND published = 1";
					$database->SetQuery( $query );
					$lpath = $database->loadObjectList();
					if(!empty($lpath)) {
						$lpath = $lpath[0];
					} else {
						return '';
					}

					$query = "DELETE FROM `#__quiz_lpath_stage`"
						. "\n WHERE uid = {$my->id} AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$quiz_id} AND oid = 0 AND rel_id = 0 "
						;
					$database->SetQuery( $query );
					$database->execute();
					
					$query = "INSERT INTO `#__quiz_lpath_stage`"
						. "\n SET uid = {$my->id}, lpid = {$lpath->id}, `type` = 'q', qid = {$quiz_id}, stage = 0, oid = 0, rel_id = 0 "
						;
					$database->SetQuery( $query );
					$database->execute();

				}			
			} else { 
				$query = "SELECT unique_id  FROM #__quiz_r_student_quiz WHERE c_id = '$stu_quiz_id'";
				$database->SetQuery($query);
				$user_unique_id = $database->loadResult();
				
				$old_quiz = true;
			}

            $query = "SELECT q.* FROM #__quiz_t_question as q LEFT JOIN `#__quiz_t_qtypes` as `b` ON b.c_id = q.c_type LEFT JOIN `#__extensions` as `e` ON (CONVERT (e.element USING utf8) COLLATE utf8_unicode_ci) = b.c_type WHERE q.c_quiz_id = '".$quiz_id."' AND q.published = 1 AND e.folder = 'joomlaquiz' AND e.type = 'plugin' AND e.enabled = 1 ORDER BY q.ordering, q.c_id";
            $database->SetQuery($query);
			$q_data = $database->LoadObjectList();

			$q_data = $this->checkFirstQuestion($q_data);

			//---- pools ---------//
			switch($quiz->c_pool)
			{
				case '1':	$query = "SELECT q_count FROM #__quiz_pool WHERE q_id = '".$quiz_id."' LIMIT 1";
							$database->SetQuery($query);
							$pool_rand = $database->LoadResult();
							if( $pool_rand )
							{
                                $query = "SELECT q.* FROM #__quiz_t_question as q LEFT JOIN `#__quiz_t_qtypes` as `b` ON b.c_id = q.c_type LEFT JOIN `#__extensions` as `e` ON (CONVERT (e.element USING utf8) COLLATE utf8_unicode_ci) = b.c_type WHERE q.c_quiz_id = 0 AND q.published = 1 AND e.folder = 'joomlaquiz' AND e.type = 'plugin' AND e.enabled = 1 ORDER BY rand()";
                                $database->SetQuery($query);
								$pool_data = $database->LoadObjectList();
								for($i=0;$i<$pool_rand;$i++)
								{
									if(isset($pool_data[$i]))
										$q_data[count($q_data)] = $pool_data[$i];
								}
								
							}
							break;
							
				case '2':	$query = "SELECT * FROM #__quiz_pool WHERE q_id = '".$quiz_id."'";
							$database->SetQuery($query);
							$poolcat_data = $database->LoadObjectList();
							if (!empty($poolcat_data))
							{
								foreach( $poolcat_data as $dapool )
								{
									if( $dapool->q_count )
										{
                                            $query = "SELECT q.* FROM #__quiz_t_question as q LEFT JOIN `#__quiz_t_qtypes` as `b` ON b.c_id = q.c_type LEFT JOIN `#__extensions` as `e` ON (CONVERT (e.element USING utf8) COLLATE utf8_unicode_ci) = b.c_type WHERE q.c_quiz_id = '0' AND q.published = 1 AND q.c_ques_cat = '".$dapool->q_cat."' AND e.folder = 'joomlaquiz' AND e.type = 'plugin' AND e.enabled = 1 ORDER BY rand()";
                                            $database->SetQuery($query);
											$pool_data = $database->LoadObjectList();
											for($i=0;$i<($dapool->q_count);$i++)
											{
												if(isset($pool_data[$i]))
													$q_data[count($q_data)] = $pool_data[$i];
											}
										}
								}
							}
				break;
				
				default:	break;
			}
			//-----/end pools-----//
			
			$ret_str .= "\t" . '<quiz_past_time>0</quiz_past_time>' . "\n";
			$ret_str .= "\t" . '<task>start</task>' . "\n";
			$ret_str .= "\t" . '<stu_quiz_id>'.$stu_quiz_id.'</stu_quiz_id>' . "\n";
			$ret_str .= "\t" . '<user_unique_id>'.$user_unique_id.'</user_unique_id>' . "\n";
					
			$kol_quests = count($q_data);
			
			if ($old_quiz) {
				$ret_str = '';

				$query = "SELECT c_date_time, NOW()  FROM #__quiz_r_student_quiz WHERE c_id = '$stu_quiz_id'";
				$database->SetQuery($query);
				list($quiz_time2, $quiz_time1) = $database->loadRow();
				
				$ret_str .= "\t" . '<quiz_past_time>'.intval(strtotime($quiz_time1)-strtotime($quiz_time2)).'</quiz_past_time>' . "\n";

				$ret_str .= "\t" . '<task>seek_quest</task>' . "\n";
				$ret_str .= "\t" . '<stu_quiz_id>'.$stu_quiz_id.'</stu_quiz_id>' . "\n";
				$ret_str .= "\t" . '<user_unique_id>'.$user_unique_id.'</user_unique_id>' . "\n";

				$query = "SELECT c_question_id FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '$stu_quiz_id' ORDER BY c_id DESC ";
				$database->SetQuery( $query );
				$last_id = (int)$database->LoadResult();
				$quest_num = 0;

				$query = "SELECT a.q_chain FROM #__quiz_q_chain AS a, #__quiz_r_student_quiz AS b"
					. "\n WHERE a.s_unique_id =  b.unique_id AND  b.c_id = '".$stu_quiz_id."'";
				$database->SetQuery($query);
				$qch_ids = $database->LoadResult();
				if($qch_ids) {					
					$qchids = explode('*',$qch_ids);
				}
					
				$kol_quests = count($qchids);

				if ($last_id) {			
					if ($stu_quiz_id && is_array($qchids) && count($qchids)) {
						$z = 1;
						foreach($qchids as $ii => $qchid) {
							if ($qchid == $last_id) {
								$last_id = isset($qchids[$ii+1])? $qchids[$ii+1]: $qchids[$ii];
								break;
							}
						}
					}
				} else {
					$last_id = $qchids[0];
				}

                $query = "SELECT q.* FROM #__quiz_t_question as q LEFT JOIN `#__quiz_t_qtypes` as `b` ON b.c_id = q.c_type LEFT JOIN `#__extensions` as `e` ON (CONVERT (e.element USING utf8) COLLATE utf8_unicode_ci) = b.c_type WHERE q.c_id IN ('".implode("','", $qchids)."') AND q.published = 1 AND e.folder = 'joomlaquiz' AND e.type = 'plugin' AND e.enabled = 1 ORDER BY q.ordering, q.c_id";
                $database->SetQuery($query);
				$q_data = $database->LoadObjectList();

				$q_data = $this->checkFirstQuestion($q_data);
				
				foreach($q_data as $ii => $qchid) {
					if ($qchid->c_id == $last_id) {
						$quest_num = $ii;
					}
				}

				$ret_str .= "\t" . '<quiz_count_quests>'.$kol_quests.'</quiz_count_quests>' . "\n";
				$ret_str .= $this->JQ_GetQuestData($q_data[$quest_num], $quiz_id, $stu_quiz_id);
				$ret_str .= $this->JQ_GetPanelData($quiz_id, $q_data, $stu_quiz_id);
			} else {
			
				$chain_str = '';
				$chin_nums = '';
				$quest_num = 0;
				if ($kol_quests > 0) {			
					if ($quiz->c_random) {
						// -- create random chain questions -----//					
						$numbers = range (0,($kol_quests - 1));
						srand ((float)microtime()*1000000);
						shuffle ($numbers);
						while (list (, $number) = each ($numbers)) {
							$chain_str .= $q_data[$number]->c_id."*";
							$chin_nums .= "{$number}*";
						}
						if(strlen($chain_str))
						{
							$chain_str = JoomlaquizHelper::jq_substr($chain_str,0,strlen($chain_str)-1);
							$chin_nums = JoomlaquizHelper::jq_substr($chin_nums,0,strlen($chin_nums)-1);
							$chain_arr = explode("*", $chin_nums);
							if($chain_arr[0])
								$quest_num = $chain_arr[0];
							$query = "INSERT INTO #__quiz_q_chain (quiz_id, user_id, q_chain, s_unique_id)"
							. "\n VALUES('".$quiz_id."', '".$my->id."','".$chain_str."', '".$user_unique_id."')";
							$database->SetQuery($query);
							$database->execute();	
						}
						
						// --- randomize and -----//
					}
					else
					{
						if(!empty($q_data))
						{
							foreach($q_data as $q_num)
							{
								$chain_str .= $q_num->c_id."*";
							}
							if(strlen($chain_str))
								{
									$chain_str = JoomlaquizHelper::jq_substr($chain_str,0,strlen($chain_str)-1);
									$query = "INSERT INTO #__quiz_q_chain (quiz_id, user_id, q_chain, s_unique_id)"
									. "\n VALUES('".$quiz_id."', '".$my->id."','".$chain_str."', '".$user_unique_id."')";
									$database->SetQuery($query);
									$database->execute();	
								}
						}
					}
					
					$ret_str .= "\t" . '<quiz_count_quests>'.$kol_quests.'</quiz_count_quests>' . "\n";			
					$ret_str .= $this->JQ_GetQuestData($q_data[$quest_num], $quiz_id, $stu_quiz_id);
					$ret_str .= $this->JQ_GetPanelData($quiz_id, $q_data, $stu_quiz_id);
					
				} else { $ret_str = ''; }
			}	
		}
			
		return $ret_str;
	}
	
	public function JQ_NextQuestion($user_time = 0) {
		
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$ret_str = '';
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		$timer = intval(JFactory::getApplication()->input->get('timer', 0 ) );
		
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		if (!empty($quiz)) {
			$quiz = $quiz[0];
		} else { return $ret_str; }

		$now = JHtml::_('date',time(), 'Y-m-d H:i:s');
		if(!$this->userHasAccess($quiz, $my)){
			return $ret_str;
		}

		$stu_quiz_id = JFactory::getApplication()->input->getInt( 'stu_quiz_id', 0 );
		$quest_ids = JFactory::getApplication()->input->get( 'quest_id', array(), 'ARRAY' );
		$answers = JFactory::getApplication()->input->get( 'answer', array(), 'ARRAY' );
		$user_unique_id = strval( JFactory::getApplication()->input->get( 'user_unique_id', '') );
		
		$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$quiz->c_skin."'";
		$database->SetQuery( $query );
		$cur_tmpl = $database->LoadResult();
				
		if ($cur_tmpl && ($quiz_id) && ($stu_quiz_id) && !empty($quest_ids)) {
			//'time is up' check
			if ($quiz->c_time_limit) {
                $query = "SELECT c_date_time, NOW() FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
                $database->SetQuery( $query );
                list($quiz_time2, $quiz_time1) = $database->loadRow();
                $quiz_time1a = strtotime($quiz_time1);
                $quiz_time2a = strtotime($quiz_time2);
				$user_time = $quiz_time1a - $quiz_time2a;
				if ($user_time > ($quiz->c_time_limit * 60)) {
					return $this->JQ_TimeIsUp($quiz, $stu_quiz_id);
				}
			}

			$query = "SELECT c_quiz_id, c_student_id, unique_id FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
			$database->SetQuery($query);
			$st_quiz_data = $database->LoadObjectList();

			$quiz_time = JHtml::_('date',time(), 'Y-m-d H:i:s');
			$start_quiz = 0;
			if (!empty($st_quiz_data)) {
				$start_quiz = $st_quiz_data[0]->c_quiz_id;
			} else { return $ret_str; }
			
			if ($user_unique_id != $st_quiz_data[0]->unique_id) { return ''; }
			
			if ($my->id != $st_quiz_data[0]->c_student_id) { return ''; }
			
			if ( ($quiz_id == $start_quiz) ) {
				$feedback_count = 0;
				$blank_fbd = '';
				$blank_fbd_count = 0;

				for($q=0, $qn = count($quest_ids); $q < $qn; $q++) {
					
					$quest_id = $quest_ids[$q];
					$answer = $answers[$q];

                    $qtype = $c_penalty = $limit_time = '';
                    $query = $database->getQuery(true);
                    $query->select($database->qn(array('c_type', 'c_penalty', 'c_time_limit')))
                        ->from($database->qn('#__quiz_t_question'))
                        ->where($database->qn('c_id') .'='. $database->q((int)$quest_id))
                        ->where($database->qn('published') .'='. $database->q(1));
                    $database->setQuery($query);
                    $resultQRQ = $database->loadObject();
                    if($resultQRQ){
                        $qtype = $resultQRQ->c_type;
                        $c_penalty = $resultQRQ->c_penalty;
                        $limit_time = $resultQRQ->c_time_limit;
                    }

                    $quiz_time1 = strtotime(JHtml::_('date',time(),'Y-m-d H:i:s'));
					$quiz_time2a = strtotime($quiz_time2);
					$user_time = $quiz_time1 - $quiz_time2a;
					if($limit_time != 0 && $user_time > $limit_time * 60) {
						$user_time = $limit_time * 60;      // what is it and why?
					}

					$is_avail = null;
					$is_correct = 0;
					$is_no_attempts = 0;
					$questtype1_answer_incorrect = '';
					$got_one_correct = false;
					
					$c_quest_cur_attempt = null;
					$c_all_attempts = null;
					
					$this->JQ_SaveAnswer($stu_quiz_id, $quest_id, $answer, $qtype, $c_penalty, $is_avail, $is_correct, $is_no_attempts, $questtype1_answer_incorrect, $got_one_correct, $c_quest_cur_attempt, $c_all_attempts, $timer);
					
					$j = 0;
						
					$is_avail = 1;
					if (($c_quest_cur_attempt + 1) >= $c_all_attempts) { 
						$is_avail = 0; 
					}
		
					$jq_language = array();
					$jq_language['COM_QUIZ_ANSWER_INCORRECT'] = ($quiz->c_wrong_message) ? htmlspecialchars($quiz->c_wrong_message): JText::_('COM_QUIZ_ANSWER_INCORRECT');
					$jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($quiz->c_right_message) ? htmlspecialchars($quiz->c_right_message): JText::_('COM_QUIZ_ANSWER_CORRECT');
								
					$query = "SELECT * FROM #__quiz_t_question WHERE c_id = '".intval($quest_id)."' AND published = 1";
					$database->SetQuery( $query );
					$question = $database->LoadObjectList();
			
					$jq_language['COM_QUIZ_ANSWER_INCORRECT'] = ($question[0]->c_wrong_message)?htmlspecialchars($question[0]->c_wrong_message):$jq_language['COM_QUIZ_ANSWER_INCORRECT'];
					$jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($question[0]->c_right_message) ? htmlspecialchars($question[0]->c_right_message):$jq_language['COM_QUIZ_ANSWER_CORRECT'];
					
					if ($question[0]->c_partially_message) $jq_language['COM_QUIZ_PARTIALLY_CORRECT'] = htmlspecialchars($question[0]->c_partially_message);
					elseif ($question[0]->c_wrong_message) $jq_language['COM_QUIZ_PARTIALLY_CORRECT'] = htmlspecialchars($question[0]->c_wrong_message);
					else $jq_language['COM_QUIZ_PARTIALLY_CORRECT'] = $jq_language['COM_QUIZ_ANSWER_INCORRECT'];
					
					
					if ($got_one_correct) {
						if ($question[0]->c_partially_message) $jq_language['COM_QUIZ_ANSWER_INCORRECT'] = htmlspecialchars($question[0]->c_partially_message);
					}
					
					$jq_language['COM_QUIZ_ANSWER_ACCEPTED'] = JText::_('COM_QUIZ_ANSWER_ACCEPTED');
					if(($question[0]->c_type==8)&&($question[0]->c_right_message)) 
						$jq_language['COM_QUIZ_ANSWER_ACCEPTED'] = htmlspecialchars($question[0]->c_right_message);					
		
					if ($cur_tmpl) {
						
						JoomlaquizHelper::JQ_load_template($cur_tmpl);
						$feedback_count++;
						$ret_str .= "\t" . '<feedback>' . "\n";

						$c_detailed_feedback = "";

						if (!$quiz->c_show_qfeedback && !$is_no_attempts && ($quiz->c_feedback && $question[0]->c_feedback )&& $qtype != 9 && !$question[0]->c_immediate) {
								$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['COM_QUIZ_ANSWER_ACCEPTED']:$jq_language['COM_QUIZ_ANSWER_CORRECT']): ($got_one_correct? $jq_language['COM_QUIZ_PARTIALLY_CORRECT']:$jq_language['COM_QUIZ_ANSWER_INCORRECT'])));
							if($qtype == 1)
							{
								//$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($questtype1_answer_incorrect)?$questtype1_answer_incorrect:$jq_language['COM_QUIZ_ANSWER_CORRECT']):(($questtype1_answer_incorrect)?($questtype1_answer_incorrect):($jq_language['COM_QUIZ_ANSWER_INCORRECT']))));
								$msg_html = ($is_correct)?$jq_language['COM_QUIZ_ANSWER_CORRECT']:$jq_language['COM_QUIZ_ANSWER_INCORRECT'];
								$msg_html .= ($questtype1_answer_incorrect)?'<br/>'.$questtype1_answer_incorrect:'';
								
								$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $msg_html );
							}
							$ret_str .= "\t" . '<quest_feedback>1</quest_feedback>' . "\n";
							$ret_str .= "\t" . '<quest_feedback_repl_func>0</quest_feedback_repl_func>' . "\n";
							if ($blank_fbd && $blank_fbd_count) {
								$ret_str .= "\t" .  $blank_fbd;
								$ret_str .= "\t" . '<blank_fbd_count>'.$blank_fbd_count.'</blank_fbd_count>' . "\n";
							}

						} else {
							$ret_str .= "\t" . '<quest_feedback>0</quest_feedback>' . "\n";
							$ret_str .= "\t" . '<quest_feedback_repl_func><![CDATA[jq_QuizContinue();]]></quest_feedback_repl_func>' . "\n";
							$msg_html = ' ';
						}

						if ($is_no_attempts == 1 && $answer) {
							$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', JText::_('COM_MES_NO_ATTEMPTS'));
							$is_correct = 0;
						}

						if(!$is_correct){
							$query = "SELECT c_detailed_feedback from #__quiz_t_question WHERE c_id = '".$quest_id."' AND published = 1";
							$database->SetQuery( $query );
							$c_detailed_feedback = '</br>'.$database->LoadResult();
						}						
				
						$ret_str .= "\t" . '<quiz_prev_correct>'.$is_correct.'</quiz_prev_correct>' . "\n";
						$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.$c_detailed_feedback.']]></quiz_message_box>' . "\n";
						$ret_str .= "\t" . '<quiz_allow_attempt>'.$is_avail.'</quiz_allow_attempt>' . "\n";
						$ret_str .= "\t" . '<feedback_quest_id>'.$quest_id.'</feedback_quest_id>' . "\n";
						$ret_str .= "\t" . '<feedback_quest_type>'.$qtype.'</feedback_quest_type>' . "\n";

						if($is_correct OR (!$is_correct &&  ($c_quest_cur_attempt + 1) >= $c_all_attempts) ){
							$ret_str .= "\t" . '<feedback_show_flag>1</feedback_show_flag>' . "\n";
						} else {
							$ret_str .= "\t" . '<feedback_show_flag>0</feedback_show_flag>' . "\n";
						}

						$ret_str .= "\t" . '</feedback>' . "\n";
					}

				}

				$ret_str .= "\t" . '<feedback_count>'.$feedback_count.'</feedback_count>' . "\n";
				
				// -- my chain ==//
				$query = "SELECT q_chain FROM #__quiz_q_chain WHERE s_unique_id = '".$user_unique_id."'";
				$database->SetQuery($query);
				$qch_ids = $database->LoadResult();
				
				if(!empty($qch_ids)) {
					$query = "SELECT c_question_id FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
					$database->SetQuery( $query );
					$q_ids = $database->loadColumn();

					if (empty($q_ids)) {
						$q_ids = array(0);
					}

					$qchids = explode('*',$qch_ids);// chain

                    $q_not_answer = array_diff($qchids, $q_ids);
                    if (!empty($q_not_answer)) {
                        $q_data = array();
                        $query = "SELECT * FROM #__quiz_t_question WHERE c_id = '".intval(array_shift($q_not_answer))."' AND published = 1 ";
                        $database->SetQuery( $query );
                        $q_data = $database->LoadObjectList();
                        $ret_str .= "\t" . '<task>next</task>' . "\n";
                        $ret_str .= $this->JQ_GetQuestData($q_data[0], $quiz_id, $stu_quiz_id);
                    } else {
                        $ret_str .= "\t" . '<task>finish</task>' . "\n";
                        if ($is_no_attempts == 1) {
                            $ret_str = "\t" . '<task>finish</task>' . "\n";
                        }

                        $query = "SELECT sum(c_score) FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
                        $database->SetQuery( $query );
                        $q_total_score = $database->LoadResult();

                        $query = "SELECT c_date_time, NOW() FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
                        $database->SetQuery( $query );
                        list($quiz_time2, $quiz_time1) = $database->loadRow();

                        $q_time_total = strtotime($quiz_time1) - strtotime($quiz_time2);

                        $query = "UPDATE #__quiz_r_student_quiz SET c_total_score = '".$q_total_score."', c_total_time = '".$q_time_total."' WHERE c_id = '".$stu_quiz_id."'";
                        $database->SetQuery($query);
                        $database->execute();
                    }
				}
			}
		}

        preg_match_all('~src\s*=\s*\"([^\/](?!\:\/\/)[^\"]*)\"~i', $ret_str, $preg);
        if (!empty($preg[1])) {
            foreach ($preg[1] as $p) {
                if (strpos($p, "http") === false) {
                    $search_arr = array('src ="' . $p, 'src= "' . $p, 'src = "' . $p, 'src="' . $p);
                    $ret_str = str_replace($search_arr, 'src="/' . $p, $ret_str);
                }
            }
        }

		return $ret_str;
	}
	
	function JQ_NextQuestionFinish() {
		
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$ret_str = '';
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );

		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		if (!empty($quiz)) {
			$quiz = $quiz[0];
		} else { return $ret_str; }

		$now = JHtml::_('date',time(), 'Y-m-d H:i:s');
		if(!$this->userHasAccess($quiz, $my)){
			return $ret_str;
		}

		$stu_quiz_id = intval( JFactory::getApplication()->input->get( 'stu_quiz_id', 0 ) );
		$quest_ids = JFactory::getApplication()->input->get( 'quest_id', array(), '' );
		$answers = JFactory::getApplication()->input->get( 'answer', array(), '' );
		$user_unique_id = strval( JFactory::getApplication()->input->get( 'user_unique_id', '') );

		$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$quiz->c_skin."'";
		$database->SetQuery( $query );
		$cur_tmpl = $database->LoadResult();

		if ($cur_tmpl && ($quiz_id) && ($stu_quiz_id) && is_array($quest_ids) && count($quest_ids)) {
			//'time is up' check
			if ($quiz->c_time_limit) {
				$user_time = 0;
                $query = "SELECT c_date_time, NOW() FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
                $database->SetQuery( $query );
                list($quiz_time2, $quiz_time1) = $database->loadRow();
                $quiz_time1a = strtotime($quiz_time1);
                $quiz_time2a = strtotime($quiz_time2);
                $user_time = $quiz_time1a - $quiz_time2a;
				if ($user_time > ($quiz->c_time_limit * 60)) {
					return $this->JQ_TimeIsUp($quiz, $stu_quiz_id);
				}
			}

			$query = "SELECT c_quiz_id, c_student_id, unique_id FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
			$database->SetQuery($query);
			$st_quiz_data = $database->LoadObjectList();

			$quiz_time =JHtml::_('date',time(), 'Y-m-d H:i:s');
			$start_quiz = 0;
			if (!empty($st_quiz_data)) {
				$start_quiz = $st_quiz_data[0]->c_quiz_id;
			} else { return $ret_str; }

			if ($user_unique_id != $st_quiz_data[0]->unique_id) { return ''; }

			if ($my->id != $st_quiz_data[0]->c_student_id) { return ''; }

			if ( ($quiz_id == $start_quiz) ) {
				$feedback_count = 0;
				$blank_fbd = '';
				$blank_fbd_count = 0;

				for($q=0, $qn = count($quest_ids); $q < $qn; $q++) {

					$quest_id = $quest_ids[$q];
					$answer = $answers[$q];

					// get question type
					$query = "SELECT c_type from #__quiz_t_question WHERE c_id = '".$quest_id."' AND published = 1";
					$database->SetQuery( $query );
					$qtype = $database->LoadResult();

					$query = "SELECT c_penalty from #__quiz_t_question WHERE c_id = '".$quest_id."' AND published = 1";
					$database->SetQuery( $query );
					$c_penalty = (int)$database->LoadResult();

					// insert results to the Database

					$is_avail = null;
					$is_correct = 0;
					$is_no_attempts = 0;
					$questtype1_answer_incorrect = '';
					$got_one_correct = false;

					$c_quest_cur_attempt = null;
					$c_all_attempts = null;

					$this->JQ_SaveAnswer($stu_quiz_id, $quest_id, $answer, $qtype, $c_penalty, $is_avail, $is_correct, $is_no_attempts, $questtype1_answer_incorrect, $got_one_correct, $c_quest_cur_attempt, $c_all_attempts);

					$j = 0;

					$is_avail = 1;
					if (($c_quest_cur_attempt + 1) >= $c_all_attempts) {
						$is_avail = 0;
					}

					$jq_language = array();
					$jq_language['COM_QUIZ_ANSWER_INCORRECT'] = ($quiz->c_wrong_message) ? htmlspecialchars($quiz->c_wrong_message): JText::_('COM_QUIZ_ANSWER_INCORRECT');
					$jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($quiz->c_right_message) ? htmlspecialchars($quiz->c_right_message): JText::_('COM_QUIZ_ANSWER_CORRECT');

					$query = "SELECT * FROM #__quiz_t_question WHERE c_id = '".intval($quest_id)."' AND published = 1";
					$database->SetQuery( $query );
					$question = $database->LoadObjectList();

					$jq_language['COM_QUIZ_ANSWER_INCORRECT'] = ($question[0]->c_wrong_message)?htmlspecialchars($question[0]->c_wrong_message):JText::_('COM_QUIZ_ANSWER_INCORRECT');
					$jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($question[0]->c_right_message) ? htmlspecialchars($question[0]->c_right_message):JText::_('COM_QUIZ_ANSWER_CORRECT');

					if ($question[0]->c_partially_message) $jq_language['COM_QUIZ_PARTIALLY_CORRECT'] = htmlspecialchars($question[0]->c_partially_message);
					elseif ($question[0]->c_wrong_message) $jq_language['COM_QUIZ_PARTIALLY_CORRECT'] = htmlspecialchars($question[0]->c_wrong_message);
					else $jq_language['COM_QUIZ_PARTIALLY_CORRECT'] = $jq_language['COM_QUIZ_ANSWER_INCORRECT'];


					if ($got_one_correct) {
						if ($question[0]->c_partially_message) $jq_language['COM_QUIZ_ANSWER_INCORRECT'] = htmlspecialchars($question[0]->c_partially_message);
					}

					$jq_language['COM_QUIZ_ANSWER_ACCEPTED'] = JText::_('COM_QUIZ_ANSWER_ACCEPTED');
					if(($question[0]->c_type==8)&&($question[0]->c_right_message))
						$jq_language['COM_QUIZ_ANSWER_ACCEPTED'] = htmlspecialchars($question[0]->c_right_message);
				}

				// -- my chain ==//
				$query = "SELECT q_chain FROM #__quiz_q_chain WHERE s_unique_id = '".$user_unique_id."'";
				$database->SetQuery($query);
				$qch_ids = $database->LoadResult();

				if($qch_ids) {
					$query = "SELECT c_question_id FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
					$database->SetQuery( $query );
					$q_ids = $database->loadColumn();

					if (empty($q_ids)) {
						$q_ids = array(0);
					}

					$quest_answer = count($q_ids);
					$quest_num = $quest_answer;
					$qchids = explode('*',$qch_ids);
					$q_total = count($qchids);

					$qnum = 0;

					for($i=$q_total-1; $i>0; $i--) {
						if (!in_array($qchids[$i], $q_ids)) {
							$qnum = $qchids[$i];
						}
						if (in_array($qchids[$i], $q_ids)) {
							break;
						}
					}

					if ($quiz->c_enable_skip == 1 && !$qnum){
						for($i=0; $i<$q_total; $i++) {
							if (!in_array($qchids[$i], $q_ids)) { /* $quest_ids*/
								$qnum = $qchids[$i];
								break;
							}
						}
					}

					$z = 0;
					$q_data = array();

					$query = "SELECT * FROM #__quiz_t_question WHERE c_id = '".intval($qnum)."' AND published = 1 ";
					$database->SetQuery( $query );
					$q_data = $database->LoadObjectList();
					$j = count($q_data)?(0):(-1);
				}
				
				// -- my chain ==//
				$ret_str .= "\t" . '<task>finish</task>' . "\n";
				if ($is_no_attempts == 1) {
					$ret_str = "\t" . '<task>finish</task>' . "\n";
				}

				$query = "SELECT sum(c_score) FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
				$database->SetQuery( $query );
				$q_total_score = $database->LoadResult();

				$query = "SELECT c_date_time, NOW() FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
				$database->SetQuery( $query );
                list($quiz_time2, $quiz_time1) = $database->loadRow();

				$q_time_total = strtotime($quiz_time1) - strtotime($quiz_time2);
				$query = "UPDATE #__quiz_r_student_quiz SET c_total_score = '".$q_total_score."', c_total_time = '".$q_time_total."' WHERE c_id = '".$stu_quiz_id."'";
				$database->SetQuery($query);
				$database->execute();
			}
		}

		return $ret_str;
	}
	
	public function JQ_FinishQuiz() {
		$appsLib = JqAppPlugins::getInstance();
		$plugins = $appsLib->loadApplications();
		
		$database = JFactory::getDBO(); 
		$my = JFactory::getUser();
		$ret_str = '';

        $session = JFactory::getSession();

        $session_lid = $session->get('quiz_lid');
        $lid = intval(empty($session_lid) ? 0 : $session_lid);

        $session_rel_id = $session->get('quiz_rel_id');
        $rel_id = intval(empty($session_rel_id) ? 0 : $session_rel_id);

        $session_package_id = $session->get('quiz_package_id');
        $package_id = intval(empty($session_package_id) ? 0 : $session_package_id);

		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		
		$result_mode = 0;
        $session_jq_result_mode = $session->get('jq_result_mode');
        $session_jq_result_mode_lid = $session->get('jq_result_mode_lid');
        $session_jq_result_mode_5 = $session->get('jq_result_mode_5');
        if(!empty($session_jq_result_mode)
            && is_array($session_jq_result_mode)
            && count($session_jq_result_mode) == 3
            && $session_jq_result_mode[0] == $rel_id
            && $session_jq_result_mode[1] == $quiz_id)
        {
            $result_mode = 1;
            $session->clear('jq_result_mode');
        } else if(!empty($session_jq_result_mode_lid)
            && is_array($session_jq_result_mode_lid)
            && count($session_jq_result_mode_lid) == 2
            && $session_jq_result_mode_lid[0] == $lid
            && $session_jq_result_mode_lid[1] == $quiz_id)
        {
            $result_mode = 2;
            $session->clear('jq_result_mode_lid');
        } elseif (!empty($session_jq_result_mode_5)
            && is_array($session_jq_result_mode_5)
            && count($session_jq_result_mode_5) == 2
            && $session_jq_result_mode_5[1] == $quiz_id)
        {
            $result_mode = 3;
            $session->clear('jq_result_mode_5');
        }

		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		if (!empty($quiz)) {
			$quiz = $quiz[0];
		} else { return $ret_str; }

        $session_share_id = $session->get('share_id');
        $share_id = intval(empty($session_share_id) ? 0 : $session_share_id);
		$is_share = false;
		
		if($share_id){
			$database->setQuery("SELECT COUNT(id) FROM `#__quiz_r_student_share` WHERE `id` = '".$share_id."'");
			$is_share = $database->loadResult();

            $session->clear('share_id');
            unset($session_share_id);
		}
		
		$false_share = false;
        if($my->authorise('core.managefe','com_joomlaquiz')){
			$is_share = 1;
			$false_share = true;
		}

		$now = JHtml::_('date',time(), 'Y-m-d H:i:s');
		if(!$this->userHasAccess($quiz, $my)){
			return $ret_str;
		}

		if ($quiz_id) {
			$stu_quiz_id = intval( JFactory::getApplication()->input->get( 'stu_quiz_id', 0 ) );
			
			$database->setQuery("SELECT `user_email`, `user_name` FROM `#__quiz_r_student_quiz` WHERE `c_id` = '".$stu_quiz_id."'");
			$stu_quiz_data = $database->loadObject();
			
			$user_unique_id = strval( JFactory::getApplication()->input->get( 'user_unique_id', '') );

			if($quiz->c_share_buttons){
				if($is_share && !$false_share){

					$database->setQuery("SELECT `c_stu_quiz_id` FROM `#__quiz_r_student_share` WHERE `id` = '".$share_id."'");
					$stu_quiz_id = $database->loadResult();

					$database->setQuery("SELECT `unique_id` FROM `#__quiz_r_student_quiz` WHERE `c_id` = '".$stu_quiz_id."'");
					$user_unique_id = $database->loadResult();

				} else {

					$c_share_id = md5($user_unique_id);

					$database->setQuery("SELECT COUNT(`id`) FROM `#__quiz_r_student_share` WHERE `c_share_id` = '".$c_share_id."'");
					$is_share_exists = $database->loadResult();

					if(!$is_share_exists){
						$database->setQuery("INSERT INTO `#__quiz_r_student_share` (`id`, `c_quiz_id`, `c_stu_quiz_id`, `c_user_id`, `c_share_id`) VALUES ('', '".$quiz_id."', '".$stu_quiz_id."', '".$my->id."', '".$c_share_id."')");
						$database->execute();

						$share_id = $database->insertid();
					} else {
						$database->setQuery("SELECT `id` FROM `#__quiz_r_student_share` WHERE `c_share_id` = '".$c_share_id."'");
						$share_id = $database->loadResult();
					}


					$domen = rtrim(JUri::root(), '/');

					$share_link = urlencode($domen.JRoute::_('index.php?option=com_joomlaquiz&view=quiz&quiz_id='.$quiz_id.JoomlaquizHelper::JQ_GetItemId()));
				}
			}

			if ($stu_quiz_id) {
				$query = "SELECT c_quiz_id, c_student_id, unique_id, c_rel_id, c_order_id, c_lid FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
				$database->SetQuery($query);
				$st_quiz_data = $database->LoadObjectList();

				$start_quiz = 0;
				if (!empty($st_quiz_data)) {
					$start_quiz = $st_quiz_data[0]->c_quiz_id;
				} else {
					return '';
				}
				if ($user_unique_id != $st_quiz_data[0]->unique_id && !$is_share) {
				    return '';
				}
    			if ($my->id != (int)$st_quiz_data[0]->c_student_id && !$is_share){
                    return '';
                }
				if ($start_quiz != $quiz_id) {
				    return '';
				}
				
				$lid = (int)$st_quiz_data[0]->c_lid;
				$rel_id = (int)$st_quiz_data[0]->c_rel_id;
				$package_id = (int)$st_quiz_data[0]->c_order_id;
				
				$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
				$database->SetQuery( $query );
				$quiz_info = $database->LoadObjectList();
				if (!empty($quiz_info)) { $quiz_info = $quiz_info[0]; }
				else { return ''; }
		
				$query = "SELECT SUM(c_score) FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
				$database->SetQuery( $query );
				$user_score = $database->LoadResult();
				
				if (!$user_score) $user_score = 0;
				
				$query = "SELECT q_chain FROM #__quiz_q_chain "
				. "\n WHERE s_unique_id = '".$user_unique_id."'";
				$database->SetQuery($query);
				$qch_ids = $database->LoadResult();

                if(empty($qch_ids)){
                    return '';
                }
				$qch_ids = str_replace('*',',', $qch_ids);
								
				$max_score = JoomlaquizHelper::getTotalScore($qch_ids, $quiz_id);

				$query = $database->getQuery(true);
				$query->select(1);
				$query->from($database->qn('#__quiz_t_question', 'q'));
				$query->from($database->qn('#__quiz_r_student_question', 'sq'));
				$query->where($database->qn('q.c_id').' IN ('.$qch_ids.')');
				$query->where($database->qn('q.published').' = '.$database->q('1'));
				$query->where($database->qn('q.c_manual').' = '.$database->q('1'));
				$query->where($database->qn('q.c_id').' = '.$database->qn('sq.c_question_id'));
				$query->where($database->qn('sq.c_stu_quiz_id').' = '.$database->q($stu_quiz_id));
				$query->where($database->qn('sq.reviewed').' = '.$database->q('0'));

				$database->SetQuery( $query );
				$c_manual = (int)$database->LoadResult();
				
				$nugno_score = ($quiz_info->c_passing_score * $max_score) / 100;

				$user_passed = 0;
				if (!$c_manual && ($user_score >= $nugno_score)) {
					$user_passed = 1; 
				}

				$query = "SELECT c_date_time, NOW() FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
				$database->SetQuery( $query );
                list($quiz_time2, $quiz_time1) = $database->loadRow();
				
				if(!$result_mode) {
					$database->setQuery("SELECT `c_time_limit` FROM `#__quiz_t_quiz` WHERE `c_id` = '".$quiz_id."'");
					$limit_time = $database->loadResult();
					$quiz_time2a = strtotime($quiz_time2);
                    $quiz_time1a = strtotime($quiz_time1);
					$user_time = $quiz_time1a - $quiz_time2a;
					if($limit_time != 0 && $user_time > $limit_time * 60) $user_time = $limit_time * 60;


					$query = "UPDATE #__quiz_r_student_quiz SET c_total_score = '".$user_score."', c_passed = '".$user_passed."', c_finished = '1', c_total_time = '".$user_time."', `c_passing_score`='{$nugno_score}', `c_max_score` = '{$max_score}' "
					. "\n WHERE c_id = '".$stu_quiz_id."' AND c_rel_id = '".$rel_id."' AND c_order_id = '".$package_id."' AND c_quiz_id = '".$quiz_id."' AND c_student_id = '".$my->id."'";
					$database->SetQuery( $query );
					$database->execute();
					
					if ($rel_id && $my->id) {

                        $query_user_id = $my->id;
                        if ($my->id != (int)$st_quiz_data[0]->c_student_id && $is_share){
                            $query_user_id = (int)$st_quiz_data[0]->c_student_id;
                        }

						if ($package_id < 1000000000) {
							$query = "SELECT qp.*"
							. "\n FROM #__virtuemart_orders AS vm_o"
							. "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
							. "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
							. "\n WHERE vm_o.virtuemart_user_id = {$query_user_id} AND vm_o.virtuemart_order_id = $package_id AND qp.id = $rel_id AND vm_o.order_status IN ('C')"
							;
						} else {
							$query = "SELECT qp.*"
							. "\n FROM `#__quiz_payments` AS p"
							. "\n INNER JOIN `#__quiz_products` AS qp ON qp.pid = p.pid"
							. "\n WHERE p.user_id = {$query_user_id} AND p.id = '".($package_id-1000000000)."' AND qp.id = '{$rel_id}' AND p.status IN ('Confirmed') "
							;
						}
						$database->SetQuery( $query );
						$rel_check = $database->loadObjectList();
						if(empty($rel_check)) {
							return '';
						}
						
						if($rel_check[0]->type == 'l') {
							$query = "SELECT * FROM `#__quiz_lpath` WHERE `id` = '{$rel_check[0]->rel_id}' AND published = 1";
							$database->SetQuery( $query );
							$lpath = $database->loadObjectList();
							if(!empty($lpath)) {
								$lpath = $lpath[0];
							} else {
								return '';
							}
						}
					}

					if($rel_id && $my->id && $user_passed) {	
						if($rel_check[0]->type == 'l') {							
							$query = "UPDATE `#__quiz_lpath_stage` SET `stage` = 1, attempts = attempts+1 "
								. "\n WHERE uid = {$my->id} AND rel_id = {$rel_id} AND oid = $package_id AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$quiz_id}"
							;
							$database->SetQuery( $query );
							$database->execute();
						}				
						
						if($quiz->c_allow_continue){					
							$query = "UPDATE #__quiz_products_stat SET `attempts` = `attempts`+1 WHERE uid = $my->id AND `qp_id` = $rel_id AND oid = $package_id ";
							$database->SetQuery( $query );
							$database->execute();
						}
					} else if($rel_id && $my->id && !$user_passed) {
						if($rel_check[0]->type == 'l') {			
							$query = "SELECT `stage` FROM `#__quiz_lpath_stage` WHERE uid = {$my->id} AND rel_id = {$rel_id} AND oid = $package_id AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$quiz_id}";
							$database->SetQuery( $query );
							$stage = (int)$database->loadResult();
											
							$query = "UPDATE `#__quiz_lpath_stage` SET `stage` = {$stage}, attempts = attempts+1 "
								. "\n WHERE uid = {$my->id} AND rel_id = {$rel_id} AND oid = $package_id AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$quiz_id}"
							;
							$database->SetQuery( $query );
							$database->execute();
						}
						if($rel_check[0]->type == 'q') {		
							if($quiz->c_allow_continue){
								$query = "UPDATE #__quiz_products_stat SET `attempts` = `attempts`+1 WHERE uid = $my->id AND `qp_id` = $rel_id AND oid = $package_id ";
								$database->SetQuery( $query );
								$database->execute();
							}
						}
					} else if(!$rel_id && $lid && $my->id && $user_passed) {
						$query = "SELECT * FROM `#__quiz_lpath` WHERE `id` = '{$lid}' AND published = 1";
						$database->SetQuery( $query );
						$lpath = $database->loadObjectList();
						if(!empty($lpath)) {
							$lpath = $lpath[0];
						} else {
							return '';
						}
						
						$query = "UPDATE `#__quiz_lpath_stage` SET `stage` = 1"
							. "\n WHERE uid = {$my->id} AND rel_id = 0 AND oid = 0 AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$quiz_id}"
							;
						$database->SetQuery( $query );
						$database->execute();
					}
				} else {
					$query = "SELECT c_total_time FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
					$database->SetQuery( $query );
					$user_time = $database->LoadResult();
				}
				
				$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$quiz->c_skin."'";
				$database->SetQuery( $query );
				$cur_tmpl = $database->LoadResult();
				if ($cur_tmpl) {
					
					JoomlaquizHelper::JQ_load_template($cur_tmpl);
					$ret_str .= "\t" . '<task>results</task>' . "\n";
				
					$c_resbycat = '';
					if($quiz_info->c_resbycat == 1)
					{
						$q_cate = JoomlaquizHelper::getResultsByCategories($stu_quiz_id);
						$i=0;
						foreach($q_cate as $curcat)
						{
							if($curcat[2] || $i){
								$percent = ($curcat[2]) ? number_format(($curcat[1]/$curcat[2]) * 100, 0, '.', ',') : 0;
								$c_resbycat .= "<div class='jq_cat_score'>".$curcat[0].': '.sprintf(JText::_('COM_QUIZ_RES_MES_SCORE_TPL'), $curcat[1], $curcat[2], $percent)."</div><br />";
							}
							$i++;
						}
					}
					
					$tot_min = floor($user_time / 60);
					$tot_sec = $user_time - $tot_min*60;
					$tot_time = str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT);
					$results_txt = JoomlaQuiz_template_class::JQ_show_results();
					
					if(file_exists(JPATH_SITE.'/components/com_alphauserpoints/helper.php')){
						$AUP_isset = require_once(JPATH_SITE.'/components/com_alphauserpoints/helper.php');
					} else {
						$AUP_isset = false;
					}
					if($AUP_isset) {     
						$result = AlphaUserPointsHelper::checkRuleEnabled('plgup_joomlaquizpoints');
						if ($result[0]->displaymsg == 1 && $result[0]->fixedpoints != 1) {
							$results_txt = str_replace('<!-- SYSTEM MESSAGE CONTAINER -->', sprintf(JText::_('COM_QUIZ_RES_MES_SCORE_MESS'), number_format($user_score, 2, '.', ' '), number_format($max_score, 2, '.', ' ')), $results_txt);
						} elseif ($result[0]->displaymsg == 1 && $result[0]->fixedpoints == 1) {
							$results_txt = str_replace('<!-- SYSTEM MESSAGE CONTAINER -->', $result[0]->msg, $results_txt);
						}
					}
					
					$user_score_percent = ($max_score) ? ($user_score/$max_score) * 100 : 0;
					$results_txt = str_replace('<!-- TOTAL USER SCORE -->', sprintf(JText::_('COM_QUIZ_RES_MES_SCORE_TPL'), number_format($user_score, 2, '.', ' '), number_format($max_score, 2, '.', ' '), number_format($user_score_percent, 2, '.', ' ')), $results_txt);
					$results_txt = str_replace('<!-- PASSING SCORE -->', sprintf(JText::_('COM_QUIZ_RES_MES_PAS_SCORE_TPL'), number_format($nugno_score,2 , '.', ' '), number_format($quiz_info->c_passing_score, 2, '.', ' ')), $results_txt);
					$results_txt = str_replace('<!-- SPENT TIME -->', $tot_time, $results_txt);

					if ($c_resbycat) {
						$results_txt = str_replace('<!-- SCORE BY CATEGORIES -->', $c_resbycat, $results_txt);
					} else {
						$p1 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- SCORE BY CATEGORIES BEGIN -->');
						$p2 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- SCORE BY CATEGORIES END -->');
						$results_txt = JoomlaquizHelper::jq_substr($results_txt, 0, $p1).JoomlaquizHelper::jq_substr($results_txt, $p2+32);
					}
					
					if ($c_manual || !$quiz_info->c_show_result) {
						$p1 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- MAIN RESULT PART BEGIN -->');
						$p2 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- MAIN RESULT PART END -->');
						if($p1 && $p2)	$results_txt = JoomlaquizHelper::jq_substr($results_txt, 0, $p1).JoomlaquizHelper::jq_substr($results_txt, $p2+29);
					}

					if((!$is_share || $false_share) && $quiz->c_share_buttons){
						$sshare_message = (JText::_('COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE_'.$quiz->c_id) && JText::_('COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE_'.$quiz->c_id)!='COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE_'.$quiz->c_id)?(JText::_('COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE_'.$quiz->c_id)):(JText::_('COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE'));
						$user_score_replaced = sprintf($sshare_message, number_format($user_score, 2, '.', ' '), number_format($max_score, 2, '.', ' ')).$quiz->c_title;

						$social_buttons = '<div id="jq_share"><ul>';
                        $social_buttons .= '<li><div class="jq_facebook" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u='.$share_link.'\', \'_blank\');"></div></li>';
                        $social_buttons .= '<li><div class="jq_twitter" onclick="window.open(\'http://twitter.com/share?text='.$user_score_replaced.'&url='.$share_link.'\', \'_blank\');"><!--x--></div></li>';
                        //$social_buttons .= '<li><div class="jq_linkedin" onclick="window.open(\'http://www.linkedin.com/shareArticle?mini=true&url='.$share_link.'&title='.$user_score_replaced.'\', \'_blank\');"><!--x--></div>';
						$social_buttons .= '<li><div class="jq_linkedin"<a class="linkedIn" href="javascript:void(0)" onclick="window.open( \'https://www.linkedin.com/sharing/share-offsite/?mini=true&amp;url='.$share_link.'\', \'sharer\', \'toolbar=0, status=0, width=626, height=436\');return false;" title="Linkedin" ></div></li>';
                        $social_buttons .= '</ul></div>';

						$results_txt = str_replace('<!-- SOCIAL BUTTONS -->', $social_buttons, $results_txt);
					}
					
					$jq_language = array();
					$fin_message = '';
					if ($user_passed) {
						if ($quiz_info->c_pass_message) {
							$jq_language['COM_QUIZ_USER_PASSES'] = $quiz_info->c_pass_message;
						} else {
							$jq_language['COM_QUIZ_USER_PASSES'] = JText::_('COM_QUIZ_USER_PASSES');
						}
					} else {
						if ($quiz_info->c_unpass_message) {
							$jq_language['COM_QUIZ_USER_FAILS'] = $quiz_info->c_unpass_message;
						} else {
							$jq_language['COM_QUIZ_USER_FAILS'] = JText::_('COM_QUIZ_USER_FAILS');
						}
						if ($c_manual) {
							$jq_language['COM_QUIZ_USER_FAILS'] = JText::_('COM_JQ_RESULT_SHOULD_BE_REVIEWED');
						}
					}
					$req_scoremes = '';

					if($quiz->c_feed_option) {
						if($quiz->c_feed_option == 1){
                            $score_perc = ($max_score) ? (round(($user_score/$max_score)*100)) : 0;
							$query = "SELECT fmessage FROM #__quiz_feed_option WHERE quiz_id = '".$quiz_id."' AND from_percent<=".$score_perc." AND to_percent>=".$score_perc."";
						}elseif($quiz->c_feed_option == 2){
							$query = "SELECT fmessage FROM #__quiz_feed_option WHERE quiz_id = '".$quiz_id."' AND from_percent<=".$user_score." AND to_percent>=".$user_score."";
						}
						$database->SetQuery( $query );
						$req_scoremes = $database->LoadResult();
						if($req_scoremes!='') {
							$fin_message = $req_scoremes;
						} else {
							$fin_message = ($user_passed? $jq_language['COM_QUIZ_USER_PASSES']: $jq_language['COM_QUIZ_USER_FAILS']);
						}
					}else{
						$fin_message = ($user_passed? $jq_language['COM_QUIZ_USER_PASSES']: $jq_language['COM_QUIZ_USER_FAILS']);
					}
					
					if (!$result_mode){
						$ret_str .= "\t" . '<quiz_redirect>'.intval($quiz->c_redirect_after).'</quiz_redirect>' . "\n";
						if ($quiz->c_redirect_linktype && $fin_message) {
							$ret_str .= "\t" . '<quiz_redirect_url><![CDATA['.$fin_message.']]></quiz_redirect_url>' . "\n";
							$fin_message = '';
						} else {
							$ret_str .= "\t" . '<quiz_redirect_url><![CDATA['.$quiz->c_redirect_link.']]></quiz_redirect_url>' . "\n";
						}
						$ret_str .= "\t" . '<quiz_redirect_delay>'.intval($quiz->c_redirect_delay).'</quiz_redirect_delay>' . "\n";
					} else {
						$ret_str .= "\t" . '<quiz_redirect>0</quiz_redirect>' . "\n";
						$ret_str .= "\t" . '<quiz_redirect_delay>0</quiz_redirect_delay>' . "\n";
						if ($quiz->c_redirect_linktype && $fin_message) { $fin_message = ''; }
					}
					
					
					if ($fin_message && (!$is_share || $false_share)) {
						$results_txt = str_replace('<!-- QUIZ FINAL MESSAGE -->', $fin_message, $results_txt);
					} else {
						$p1 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- FIN MESSAGE BEGIN -->');
						$p2 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- FIN MESSAGE END -->');
						$results_txt = JoomlaquizHelper::jq_substr($results_txt, 0, $p1).JoomlaquizHelper::jq_substr($results_txt, $p2+25);
					}

					$footer_ar = array();
					$footer_ar[0] = '';
					$footer_ar[1] = '';
					$footer_ar[2] = '';
					$footer_ar[3] = '';
					$footer_ar[4] = '';
					$footer_ar[5] = '';
					$footer_ar[6] = '';			
					
					$tmp = '';
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select('1')
						->from('#__quiz_r_student_quiz')
						->where($db->qn('c_passed').' = '.$db->q('1'))
						->where($db->qn('c_quiz_id').' = '.$db->q($quiz->c_id))
						->where($db->qn('c_student_id').' = '.$db->q($my->id));
					if($rel_id) {
						$query = 'SELECT * FROM #__quiz_products WHERE id = ' . $rel_id;
						$database->setQuery($query);
						$prod = $database->LoadObjectList();
						$prod = @$prod[0];
						
						if(!empty($prod)) {
							if(@$prod->type == 'l'){
								$footer_ar[4] = "<div class='jq_footer_link jq_lpath'><a href='" . JRoute::_("index.php?option=com_joomlaquiz&view=lpath&package_id={$package_id}&rel_id={$rel_id}" . JoomlaquizHelper::JQ_GetItemId()) . "'>".JText::_('COM_LPATH_QUIZZES_LIST')."</a></div>";
							}
							
							if($user_passed) {
								$l_id = $prod->rel_id;
								$query = "SELECT `type`, `qid`"
								. "\n FROM #__quiz_lpath_quiz"
								. "\n WHERE lid = $l_id AND `order` > (SELECT `order` FROM #__quiz_lpath_quiz WHERE lid = $l_id AND `type` = 'q' AND qid = $quiz_id)"
								. "\n ORDER BY `order`"
								. "\n LIMIT 1"
								;
								$database->SetQuery( $query );
								$next = $database->loadObjectList();
		
								if(!empty($next)) {
									$footer_ar[6] = "<div class='jq_footer_link jq_nquiz'><a href='"
										. JRoute::_("index.php?option=com_joomlaquiz&view=quiz&package_id={$package_id}&rel_id=" . $rel_id
										. ($next[0]->type == 'q' ? '&quiz_id=' : '&article_id=') . $next[0]->qid . JoomlaquizHelper::JQ_GetItemId())
										. "'>".JText::_('COM_LPATH_NEXT_QUIZ')."</a></div>"
										;
								}
							}
						}
						$tmp = '';
						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->select('1')
							->from('#__quiz_r_student_quiz')
							->where($db->qn('c_passed').' = '.$db->q('1'))
							->where($db->qn('c_quiz_id').' = '.$db->q($quiz->c_id))
							->where($db->qn('c_student_id').' = '.$db->q($my->id));
						if (JoomlaquizHelper::isQuizAttepmts($quiz_id, 0, $rel_id, $package_id, $tmp) && (!$quiz->one_time || !$db->setQuery($query)->loadResult())){
							$is_attempts = true;
							$footer_ar[5] = "<div class='jq_footer_link jq_try_again'><a href='".JRoute::_("index.php?option=com_joomlaquiz&view=quiz&package_id={$package_id}&rel_id={$rel_id}&quiz_id={$quiz_id}&force=1".JoomlaquizHelper::JQ_GetItemId())."'>".JText::_('COM_QUIZ_TRY_AGAIN')."</a></div>";
						}
					
					} else if($lid) {
						$footer_ar[4] = "<div class='jq_footer_link jq_lpath'><a href='" . JRoute::_("index.php?option=com_joomlaquiz&view=lpath&lpath_id={$lid}" . JoomlaquizHelper::JQ_GetItemId()) . "'>".JText::_('COM_LPATH_QUIZZES_LIST')."</a></div>";
						if($user_passed) {
							$query = "SELECT `type`, `qid`"
							. "\n FROM #__quiz_lpath_quiz"
							. "\n WHERE lid = '{$lid}' AND `order` > (SELECT `order` FROM #__quiz_lpath_quiz WHERE lid = '{$lid}' AND `type` = 'q' AND qid = '{$quiz_id}')"
							. "\n ORDER BY `order`"
							. "\n LIMIT 1"
							;
							$database->SetQuery( $query );
							$next = $database->loadObjectList();
		
							if(!empty($next)) {
								$footer_ar[6] = "<div class='jq_footer_link jq_nquiz'><a href='".JRoute::_('index.php?option=com_joomlaquiz&view=quiz&lid='.$lid.($next[0]->type == 'q' ? '&quiz_id=' : '&article_id=') . $next[0]->qid . JoomlaquizHelper::JQ_GetItemId())."'>".JText::_('COM_LPATH_NEXT_QUIZ')."</a></div>";
							}
						}
						
						$tmp = '';
						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->select('1')
							->from('#__quiz_r_student_quiz')
							->where($db->qn('c_passed').' = '.$db->q('1'))
							->where($db->qn('c_quiz_id').' = '.$db->q($quiz->c_id))
							->where($db->qn('c_student_id').' = '.$db->q($my->id));
						if (JoomlaquizHelper::isQuizAttepmts($quiz_id, $lid, 0, 0, $tmp) && (!$quiz->one_time || !$db->setQuery($query)->loadResult())){
							$is_attempts = true;
							$footer_ar[5] = "<div class='jq_footer_link jq_try_again'><a href='". JRoute::_("index.php?option=com_joomlaquiz&view=quiz&lid={$lid}&quiz_id={$quiz_id}&force=1" . JoomlaquizHelper::JQ_GetItemId()) . "'>".JText::_('COM_QUIZ_TRY_AGAIN')."</a></div>";
						}
					} elseif (JoomlaquizHelper::isQuizAttepmts($quiz_id, 0, 0, 0, $tmp)
						&& (!$quiz->one_time || !$db->setQuery($query)->loadResult())){
						$is_attempts = true;
                        $jinput = JFactory::getApplication()->input;
                        $reStartString = $jinput->getString('reStartString', false);
                        $href = ($reStartString) ? JRoute::_('index.php'.$reStartString.'force=1') : JRoute::_("index.php?option=com_joomlaquiz&view=quiz&quiz_id={$quiz_id}&force=1".JoomlaquizHelper::JQ_GetItemId());
						$footer_ar[6] = "<div class='jq_footer_link jq_try_again'><a href='".$href."'>".JText::_('COM_QUIZ_TRY_AGAIN')."</a></div>";
					}
					
					if ($result_mode == 3) {
						$footer_ar[7] = "<div class='jq_footer_link jq_backtoresults'><a href='".JRoute::_("index.php?option=com_joomlaquiz&view=results".JoomlaquizHelper::JQ_GetItemId())."'>".JText::_('COM_JQ_BACK_TO_RESULTS')."</a></div>";
					}

					
					$this->user = JFactory::getUser();
					$certAccessGranted = $this->user->authorise('core.certificate', 'com_joomlaquiz.quiz.'.$quiz->c_id);
                    $reviewAccessGranted = $this->user->authorise('core.review', 'com_joomlaquiz.quiz.'.$quiz->c_id);
					if ($quiz->c_certificate && !$c_manual && $user_passed && $certAccessGranted) {
					    $footer_ar[2] = "<div class='jq_footer_link jq_certificate'><a href='javascript:void(0)' onclick=\"window.open ('". JRoute::_( "index.php?option=com_joomlaquiz&task=printcert.get_certificate&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."")."','blank');\">".JText::_('COM_QUIZ_FIN_BTN_CERTIFICATE')."</a></div>";
					}
					if ($quiz->c_enable_print && !$c_manual) {
                        $footer_ar[1] = "<div class='jq_footer_link jq_print'><a href='javascript:void(0)' onclick=\"window.open ('".JRoute::_("index.php?option=com_joomlaquiz&task=printresult.get_pdf&lang="._JQ_JF_LANG."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."")."','blank');\">".JText::_('COM_FIN_BTN_PRINT')."</a></div>";
					}
					if ($quiz->c_email_to == 2) {
						$footer_ar[3] = "<div class='jq_footer_link jq_email'><a href='javascript:void(0)' onclick=\"jq_emailResults();\">".JText::_('COM_QUIZ_FIN_BTN_EMAIL')."</a></div>";
					}
					if($quiz->c_email_to == 1 /*&& !$c_manual*/){
						$user = JFactory::getUser($quiz->c_user_id);
						JoomlaquizHelper::JQ_Email($stu_quiz_id, $user->email);
					}
					if ($quiz->c_enable_review && ($reviewAccessGranted || $user_passed || !$is_attempts)) {
						$query = "UPDATE #__quiz_r_student_quiz SET allow_review = 1 WHERE c_id = '".$stu_quiz_id."' AND c_rel_id = '".$rel_id."' AND c_order_id = '".$package_id."' AND c_quiz_id = '".$quiz_id."' AND c_student_id = '".$my->id."'";
						$database->SetQuery( $query );
						$database->execute();
						$footer_ar[0] = "<div class='jq_footer_link jq_review'><a href='javascript:void(0)' onclick=\"jq_startReview();\">".JText::_('COM_QUIZ_FIN_BTN_REVIEW')."</a></div>";
					}
					if (strlen(implode('', $footer_ar)) > 7){

                        if($quiz->c_certificate && !$c_manual && $user_passed && !$certAccessGranted){
                            
                            $database->setQuery('SELECT c_quiz_certificate_access_message FROM #__quiz_t_quiz WHERE c_id='.$quiz->c_id);
                            $msgDbText = $database->loadResult();
                            if(!empty($msgDbText)){
                                $results_txt = str_replace('<!-- QUIZ CERTIFICATE MESSAGE -->', $msgDbText, $results_txt );
                            }
                            else{
                                $results_txt = str_replace('<!-- QUIZ CERTIFICATE MESSAGE -->', JText::_("COM_JOOMLAQUIZ_CERTIFICATE_MSG_FE"), $results_txt );
                            }
                        }

                        $results_txt = str_replace('<!-- QUIZ FOOTER LNKS -->', implode('', $footer_ar), $results_txt);


                    }else {
						$p1 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- QUIZ FOOTER BEGIN -->');
						$p2 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- QUIZ FOOTER END -->');
						$results_txt = JoomlaquizHelper::jq_substr($results_txt, 0, $p1).JoomlaquizHelper::jq_substr($results_txt, $p2+25);
					}
					
					$wait_time = '';
					$is_attempts = JoomlaquizHelper::isQuizAttepmts($quiz_id, 0, 0, 0, $wait_time);

					$c_statistic = '';	
					if ((!$result_mode || $result_mode == 3) && $quiz->c_statistic) {
						if($quiz->c_hide_feedback && !$user_passed && $is_attempts){
							$c_statistic = '';
						} else {
							$c_statistic = $this->JQ_GetStatistic($quiz->c_id, $stu_quiz_id, $quiz->c_show_qfeedback);
						}
					}
					
					if ((!$result_mode || $result_mode == 3) && $quiz->c_statistic && !$c_manual) {
						if($quiz->c_hide_feedback && !$user_passed && $is_attempts){
							$p1 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- QUIZ FINAL FEEDBACK BEGIN -->');
							$p2 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- QUIZ FINAL FEEDBACK END -->');
							$results_txt = JoomlaquizHelper::jq_substr($results_txt, 0, $p1).JoomlaquizHelper::jq_substr($results_txt, $p2+33);
						} else {
							$results_txt = str_replace('<!-- QUIZ FINAL FEEDBACK -->', $c_statistic, $results_txt);
						}
					} else {
						$p1 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- QUIZ FINAL FEEDBACK BEGIN -->');
						$p2 = JoomlaquizHelper::jq_strpos($results_txt, '<!-- QUIZ FINAL FEEDBACK END -->');
						$results_txt = JoomlaquizHelper::jq_substr($results_txt, 0, $p1).JoomlaquizHelper::jq_substr($results_txt, $p2+33);
					}

					$ret_str .= "\t" . '<quiz_results><![CDATA['.$results_txt.']]></quiz_results>' . "\n";
					
					$config = new JConfig();
					$mailfrom = $config->mailfrom;
					$sitename = $config->fromname;

					if (!$result_mode && $quiz->c_emails && $quiz->c_email_chk) {
                        if (($quiz->c_ifmanual && $c_manual) ||  !$quiz->c_ifmanual) {

                            $emails = explode(',', $quiz->c_emails);

                            if (is_array($emails) && !empty($emails)){
								$subject = '[QUIZ] '.JText::_('COM_QUIZ_AN_MAIL_SUBJECT').'('.$quiz->c_title.')';
								if ($my->id) {
									$message = JText::_('COM_QUIZ_MAIL_MESSAGE_USER').$my->name.' ('.$my->username.', '.$my->email.') '.JText::_('COM_QUIZ_MAIL_MESSAGE_HAS_FINISHED').' "'.$quiz->c_title.'"<br/>'."\n";
								} else {
									if($stu_quiz_data->user_name){
										$message = JText::_('COM_QUIZ_MAIL_MESSAGE_USER').$stu_quiz_data->user_name.' ('.$stu_quiz_data->user_email.') '.JText::_('COM_QUIZ_MAIL_MESSAGE_HAS_FINISHED').' "'.$quiz->c_title.'"<br/>'."\n";
									}else{
										$message = JText::_('COM_QUIZ_MAIL_MESSAGE_ANONYMOUS').' "'.$quiz->c_title.'"<br/>'."\n";
									}
								}
							
								$message .= "<br/>\n".JText::_('COM_QUIZ_HEADER_FIN_RESULTS')."<br/>\n";	
								
								$percent = ($max_score) ? number_format(($user_score/$max_score) * 100, 0, '.', ',') : 0;
								if(!$c_manual)
                                    $message .= JText::_('COM_QUIZ_RES_MES_SCORE2').'  '.sprintf(JText::_('COM_QUIZ_RES_MES_SCORE_TPL'), $user_score, $max_score, $percent)."<br/>\n";
                                else
                                    $message .= JText::_('COM_QUIZ_RES_MES_SCORE2').' not graded yet '."<br/>\n";
								$message .= JText::_('COM_QUIZ_RES_MES_PAS_SCORE').'  '.sprintf(JText::_('COM_QUIZ_RES_MES_PAS_SCORE_TPL'), $nugno_score, $quiz_info->c_passing_score)."<br/>\n";
								$message .= JText::_('COM_QUIZ_RES_MES_TIME').'  '.$tot_time."<br/>\n";

								if($req_scoremes!=''){
									$message .= "<br>\n<br>\n".$req_scoremes;
								} elseif ($c_manual) {
                                    $message .= "<br>\n<br>\n".JText::_('COM_JQ_RESULT_SHOULD_BE_REVIEWED2')."<br/>\n".'<a href="'.JURI::root().'administrator/index.php?option=com_joomlaquiz&view=results&layout=stu_report&cid='.$stu_quiz_id.'">'.JText::_('COM_JQ_CLICK_TO_REVIEW').'</a>';
                                } else {
									$message .= "<br>\n<br>\n".($user_passed?JText::_('COM_QUIZ_USER_PASSES2'):JText::_('COM_QUIZ_USER_FAILS2'));
								}

	                            $jmail = JFactory::getMailer();
	                            include_once __DIR__ . '/printresult.php';
	                            $results_model = new JoomlaquizModelPrintresult();
	                            $pdf = $results_model->generatePDF($stu_quiz_id);
	                            $pdf = $pdf->Output('results.pdf', 'S');
	                            $jmail->AddStringAttachment($pdf,'results.pdf');
								foreach($emails as $email){
									$jmail->clearAllRecipients();
								    $jmail->sendMail( $mailfrom, $sitename, trim($email), $subject, $message, 1, NULL, NULL, NULL, NULL, NULL);
								}
							}
						}
					}
					
					if (!$c_manual) {	
						if (file_exists(JPATH_ROOT. '/components/com_community/libraries/userpoints.php')) {
							include_once( JPATH_ROOT. '/components/com_community/libraries/userpoints.php');
							include_once( JPATH_ROOT. '/components/com_community/libraries/core.php');
							if ($user_passed) {
								CuserPoints::assignPoint("successfully.completed.quiz".$quiz->c_id);
							} else {
								CuserPoints::assignPoint("fail.quiz".$quiz->c_id);
							}						
						}
					
						$params = array();
						$params['start_id'] = $stu_quiz_id;
						$params['quiz_id'] = $quiz->c_id;
						$params['quiz_title'] = $quiz->c_title;
						$params['user_points'] = $user_score;
						$params['passing_points'] = $nugno_score;
						$params['total_points'] = $max_score;
						$params['passed'] = (int)$user_passed;
						$params['started'] = JHtml::_('date',strtotime($quiz_time2), 'Y-m-d H:i:s');
						$params['spent_time'] = $tot_time;
						$params['comment'] = JText::sprintf('COM_QUIZ_AUP_USER_COMMENT', $quiz->c_title );

						$dispatcher	= JDispatcher::getInstance();
						JPluginHelper::importPlugin('system');
						$dispatcher->trigger('onJQuizFinished', array (&$params));
					}
					
				}
			}
		}
		return $ret_str;
	}
	
	public function JQ_emailResults() {
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$ret_str = '';
		$result = false;
		$stu_quiz_id = intval( JFactory::getApplication()->input->get( 'stu_quiz_id', 0 ) );
		$ent_em = intval( JFactory::getApplication()->input->get( 'ent_em', 0 ) );
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		
		if (!empty($quiz)) { $quiz = $quiz[0];
		} else { return $ret_str; }
		$user_unique_id = strval( JFactory::getApplication()->input->get( 'user_unique_id', '') );
		$query = "SELECT * FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
		$database->SetQuery( $query );
		$stu_info = $database->LoadObjectList();
		
		if (!empty($stu_info)) {
			$stu_info = $stu_info[0];
			if ( ($user_unique_id == $stu_info->unique_id) && ($quiz_id == $stu_info->c_quiz_id) && ($my->id == $stu_info->c_student_id) ) {
				$query = "SELECT u.email, u.username, q.c_email_to, q.c_language, sq.unique_id "
				. "\n FROM #__quiz_r_student_quiz sq, #__quiz_t_quiz q LEFT JOIN #__users u ON  q.c_user_id = u.id"
				. "\n WHERE sq.c_id = '".$stu_quiz_id."' AND sq.c_quiz_id = q.c_id";
				$database->setQuery( $query );
				$rows = $database->loadObjectList();
				
				if (!empty($rows)) {
					if ($rows[0]->c_email_to) {
						$query = "SELECT q_chain FROM #__quiz_q_chain "
						. "\n WHERE s_unique_id = '".$rows[0]->unique_id."'";
						$database->SetQuery($query);
						$qch_ids = $database->LoadResult();
						$qch_ids = str_replace('*',',',$qch_ids);
													
						$query = "SELECT 1 FROM #__quiz_t_question AS q, #__quiz_r_student_question AS sq WHERE q.c_id IN (".$qch_ids.") AND q.published = 1 AND q.c_manual = 1 AND q.c_id = sq.c_question_id AND sq.c_stu_quiz_id = '".$stu_quiz_id."' AND reviewed = 0";

						$database->SetQuery( $query );
						$c_manual = (int)$database->LoadResult();
						
						$email_address = '';
						if ($rows[0]->c_email_to == 2) {
							$email_address 	= strval( $_REQUEST['email_address'] );
						} else {
							$email_address = $rows[0]->email;
						}
						
						if ($c_manual) {
							if (!preg_match("/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$/", $email_address)) {
								$result = false;
							} else {
								$query = "UPDATE #__quiz_r_student_quiz SET user_email = ".$database->Quote($email_address)." WHERE c_id = ".$stu_quiz_id;
								$database->SetQuery( $query );
								$database->execute();
								$result = 3;							
							}
						} else {
							$result = JoomlaquizHelper::JQ_Email($stu_quiz_id, $email_address);
						}
					}
				}
			}
		}
		
		$mail_tofr = ($quiz->c_email_to == 2 && $ent_em != 0 )?(JText::_('COM_QUIZ_EMAILTO_PROMPT')." <input class='inputbox' type='text' name='user_email' id='jq_user_email' /><input type=\"button\" onClick=\"jq_emailResultsUser();\" value=\"".JText::_('COM_QUIZ_EMAILTO_SEND_BUTTON')."\" />"):'';
		$ret_str .= "\t" . '<task>email_results</task>' . "\n";
		if ($result) {
			if ($result === 3)
				$ret_str .= "\t" . '<email_msg><![CDATA['.JText::_('COM_JQ_RESULT_SHOULD_BE_REVIEWED').']]></email_msg>' . "\n";
			else
				$ret_str .= "\t" . '<email_msg><![CDATA['.JText::_('COM_QUIZ_MES_EMAIL_OK').$mail_tofr.']]></email_msg>' . "\n";
		} else {
			if($ent_em)
				$ret_str .= "\t" . '<email_msg><![CDATA['.$mail_tofr.']]></email_msg>' . "\n";
			else	
				$ret_str .= "\t" . '<email_msg>'.JText::_('COM_QUIZ_MES_EMAIL_FAIL').'</email_msg>' . "\n";
		}
		return $ret_str;
	}
	
	public function JQ_StartReview() {
		
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$ret_str = '';
		$qcount = 0;
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		
		if (!empty($quiz)) {
			$quiz = $quiz[0];
		} else { return $ret_str; }
		
		$now = JHtml::_('date',time(), 'Y-m-d H:i:s');
		if(!$this->userHasAccess($quiz, $my)){
			return $ret_str;
		}

		if ($quiz_id) {
			$stu_quiz_id = intval( JFactory::getApplication()->input->get( 'stu_quiz_id', 0 ) );
			$user_unique_id = strval( JFactory::getApplication()->input->get( 'user_unique_id', '') );
			if ($stu_quiz_id) {
				$query = "SELECT c_quiz_id, c_student_id, unique_id, allow_review FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
				$database->SetQuery($query);
				$st_quiz_data = $database->LoadObjectList();

				$start_quiz = 0;
				if (!empty($st_quiz_data)) {
					$start_quiz = $st_quiz_data[0]->c_quiz_id;
				} else { return ''; }
				if ($user_unique_id != $st_quiz_data[0]->unique_id) { return ''; }
                if ($my->id != $st_quiz_data[0]->c_student_id  &&  !$my->authorise('core.managefe','com_joomlaquiz')) { return ''; }
				if ($start_quiz != $quiz_id) { return '';}
				if (!$st_quiz_data[0]->allow_review) { return ''; }

				if ($quiz->c_random) {
					
					// -- my chain ==//
					$query = "SELECT q_chain FROM #__quiz_q_chain "
					. "\n WHERE s_unique_id = '".$user_unique_id."'";
					$database->SetQuery($query);
					$qch_ids = $database->LoadResult();
					if($qch_ids)
					{
						$qchids = explode('*',$qch_ids);
					
						$z = 0;
						$q_data = array();
						if(!empty($qchids))
						foreach ($qchids as $q_ord)
						{
						$query = "SELECT * FROM #__quiz_t_question WHERE c_id = ".intval($q_ord)." AND published = 1 ";
							$database->SetQuery( $query );
							$q_data[$z] = $database->LoadObjectList();
							$q_data[$z] = $q_data[$z][0];
							$z++;
						}
					}	
					// -- my chain ==//
			}else
			{			
				$query = "SELECT * FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND published = 1 ORDER BY ordering, c_id";
				$database->SetQuery($query);
				$q_data = $database->LoadObjectList();
				
				$query = "SELECT q_chain FROM #__quiz_q_chain "
					. "\n WHERE s_unique_id = '".$user_unique_id."'";
				$database->SetQuery($query);
				$qch_id = $database->LoadResult();
				
				$qcount = substr_count($qch_id, '*') + 1;
				$qch_id = (int)$qch_id;
				//---- pools ---------//
					switch($quiz->c_pool)
					{
						case '1':	$query = "SELECT q_count FROM #__quiz_pool WHERE q_id = '".$quiz_id."' LIMIT 1";
									$database->SetQuery($query);
									$pool_rand = $database->LoadResult();
									if( $pool_rand ){
										$query = "SELECT * FROM #__quiz_t_question WHERE c_quiz_id = 0 AND published = 1 ".($qch_id > 0? " AND `c_id` = '".$qch_id."' ": '')." ORDER BY rand()";
										$database->SetQuery($query);
										$pool_data = $database->LoadObjectList();
										for($i=0;$i<$pool_rand;$i++){
											if(isset($pool_data[$i]))
											$q_data[count($q_data)] = $pool_data[$i];
										}
										
									}
									break;
									
						case '2':	$query = "SELECT * FROM #__quiz_pool WHERE q_id = '".$quiz_id."'";
									$database->SetQuery($query);
									$poolcat_data = $database->LoadObjectList();
									if (!empty($poolcat_data)){
										foreach( $poolcat_data as $dapool )
										{
											if( $dapool->q_count ){
												$query = "SELECT * FROM #__quiz_t_question WHERE c_quiz_id = '0' AND published = 1 ".($qch_id > 0? " AND `c_id` = '".$qch_id."' ": '')." AND c_ques_cat = '".$dapool->q_cat."' ORDER BY rand()";
												$database->SetQuery($query);
												$pool_data = $database->LoadObjectList();
												for($i=0;$i<($dapool->q_count);$i++)
												{
													if(isset($pool_data[$i]))
													$q_data[count($q_data)] = $pool_data[$i];
												}
											}
										}
									}
						break;
						
						default:	break;
					}
					//-----/end pools-----//
			}	
				
				$ret_str .= "\t" . '<task>review_start</task>' . "\n";
				if (!empty($q_data)) {
					$ret_str .= "\t" . '<quiz_count_quests>'.($qcount? $qcount: count($q_data)).'</quiz_count_quests>' . "\n";
                    
                    $str = $this->JQ_GetQuestData_review($q_data[0], $quiz_id, $stu_quiz_id);

                    $ret_add = $str;
                    $img_urls = array();
                    $pat_im = '/<img[^>]+src=([\'|\"])([^>]+)\1[^>]*>/iU';
                    $pat_url = '/^(http|https|ftp):\/\//i';
                    $out_arr = preg_split($pat_im, $ret_add);
                    if(preg_match_all($pat_im, $ret_add, $quest_images, PREG_SET_ORDER))
                    {
                        foreach($quest_images as $img_c => $quest_image){
                            $img_urls[$img_c] = @$quest_image[2];
                            if(preg_match($pat_url, $img_urls[$img_c], $url_match)){
                                $img_urls[$img_c] = '';
                            }
                        }
                    }

                    $out_html = "";
                    if(!empty($out_arr))
                    {
                        foreach($out_arr as $html_c => $html_peace){
                            if(count($out_arr) != $html_c && isset($img_urls[$html_c])){
                                if(!$img_urls[$html_c]){
                                    $out_html .= $html_peace.$quest_images[$html_c][0];
                                } else {
                                    $src_arr = explode($quest_images[$html_c][2], $quest_images[$html_c][0]);
                                    $img_tag = implode(JURI::root().$quest_images[$html_c][2], $src_arr);
                                    $out_html .= $html_peace.$img_tag;
                                }
                            } else {
                                $out_html .= $html_peace;
                            }
                        }
                    }

                    $ret_add = ($out_html) ? $out_html : $ret_add;

                    $ret_str .= $ret_add;
				} else { $ret_str = ''; }
			}
		}
		return $ret_str;
	}
	
	public function JQ_NextReview() {
	
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$ret_str = '';
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		if (!empty($quiz)) {
			$quiz = $quiz[0];
		} else { return $ret_str; }

		$now = JHtml::_('date',time(), 'Y-m-d H:i:s');
		if(!$this->userHasAccess($quiz, $my)){
			return $ret_str;
		}

		$stu_quiz_id = intval( JFactory::getApplication()->input->get( 'stu_quiz_id', 0 ) );
		$quest_ids =  JFactory::getApplication()->input->get( 'quest_id', array(), '') ;
		$user_unique_id = strval( JFactory::getApplication()->input->get( 'user_unique_id', '') );

		if (($quiz_id) && ($stu_quiz_id) && count($quest_ids)) {
			
			$query = "SELECT c_quiz_id, c_student_id, unique_id, allow_review FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
			$database->SetQuery($query);
			$st_quiz_data = $database->LoadObjectList();
			$start_quiz = 0;
			if (!empty($st_quiz_data)) {
				$start_quiz = $st_quiz_data[0]->c_quiz_id;
			} else { return $ret_str; }
			if ($user_unique_id != $st_quiz_data[0]->unique_id) { return ''; }
            if ($my->id != $st_quiz_data[0]->c_student_id && !$my->authorise('core.managefe','com_joomlaquiz')) { return ''; }
			if ($start_quiz != $quiz_id) { return '';}
			if (!$st_quiz_data[0]->allow_review) { return ''; }
			
			// -- my chain ==//
			$query = "SELECT q_chain FROM #__quiz_q_chain "
			. "\n WHERE s_unique_id = '".$user_unique_id."'";
			$database->SetQuery($query);
			$qch_ids = $database->LoadResult();
			if($qch_ids)
			{
				$quest_answer = 1;
				$qchids = explode('*',$qch_ids);
				foreach($quest_ids as $quest_id) {
				
					for($i=0;$i<count($qchids);$i++)
					{
						if(intval($qchids[$i]) == $quest_id)
						{
							$quest_answer = $i;
						}
					}
				}

				$q_total = count($qchids);
				$z = 0;
				$q_data = array();
				if(!empty($qchids))
				foreach ($qchids as $q_ord)
				{
					$query = "SELECT * FROM #__quiz_t_question WHERE c_id = ".intval($q_ord)." AND published = 1 ";
					$database->SetQuery( $query );
					$q_data[$z] = $database->LoadObjectList();
					$q_data[$z] = $q_data[$z][0];
					$z++;
				}
				$j = $quest_answer+1;

			}	
			// -- my chain ==//
	
			$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$quiz->c_skin."'";
			$database->SetQuery( $query );
			$cur_tmpl = $database->LoadResult();

			if ($cur_tmpl) {
				JoomlaquizHelper::JQ_load_template($cur_tmpl);
				if (isset($q_data[$j])) {
					$ret_str .= "\t" . '<task>review_next</task>' . "\n";				
					//$ret_str .= $this->JQ_GetQuestData_review($q_data[$j], $quiz_id, $stu_quiz_id);
                    $str = $this->JQ_GetQuestData_review($q_data[$j], $quiz_id, $stu_quiz_id);
                    $ret_add = $str;
                    $img_urls = array();
                    $pat_im = '/<img[^>]+src=([\'|\"])([^>]+)\1[^>]*>/iU';
                    $pat_url = '/^(http|https|ftp):\/\//i';
                    $out_arr = preg_split($pat_im, $ret_add);
                    if(preg_match_all($pat_im, $ret_add, $quest_images, PREG_SET_ORDER))
                    {
                        foreach($quest_images as $img_c => $quest_image){
                            $img_urls[$img_c] = @$quest_image[2];
                            if(preg_match($pat_url, $img_urls[$img_c], $url_match)){
                                $img_urls[$img_c] = '';
                            }
                        }
                    }

                    $out_html = "";
                    if(!empty($out_arr))
                    {
                        foreach($out_arr as $html_c => $html_peace){
                            if(count($out_arr) != $html_c && isset($img_urls[$html_c])){
                                if(!$img_urls[$html_c]){
                                    $out_html .= $html_peace.$quest_images[$html_c][0];
                                } else {
                                    $src_arr = explode($quest_images[$html_c][2], $quest_images[$html_c][0]);
                                    $img_tag = implode(JURI::root().$quest_images[$html_c][2], $src_arr);
                                    $out_html .= $html_peace.$img_tag;
                                }
                            } else {
                                $out_html .= $html_peace;
                            }
                        }
                    }

                    $ret_add = ($out_html) ? $out_html : $ret_add;

                    $ret_str .= $ret_add;
				} else {
					$ret_str .= "\t" . '<task>review_finish</task>' . "\n";
				}
			}
		}
		return $ret_str;
	}
	
	public function JQ_QuestPreview() {
		
		$database = JFactory::getDBO();
		
		$ret_str = '';
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		$quest_id = intval( JFactory::getApplication()->input->get('quest_id', 0 ) );
		$preview_id = strval( JFactory::getApplication()->input->get( 'preview_id', '' ) );
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		
		if (!empty($quiz)) {
			$quiz = $quiz[0];
		} else { 
			return $ret_str; 
		}	
		
		$query = "SELECT c_par_value FROM #__quiz_setup WHERE c_par_name = 'admin_preview'";
		$database->SetQuery( $query );
		$preview_code = $database->LoadResult();
		
		$query = "SELECT c_quiz_id FROM #__quiz_t_question WHERE c_id = '".$quest_id."'";
		$database->SetQuery( $query );
		$q_quiz = $database->LoadResult();
		
		if (($quiz_id == $q_quiz) && ($preview_id == $preview_code) && ($quest_id)) {				
			$query = "SELECT * FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND c_id = '".$quest_id."'";
			$database->SetQuery($query);
			$q_data = $database->LoadObjectList();
			$query = "SELECT count(*) FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
			$database->SetQuery($query);
			$q_data_count = $database->LoadResult();
			
			$jq_language = array();
			$jq_language['COM_QUIZ_ANSWER_INCORRECT'] =  ($quiz->c_wrong_message) ? htmlspecialchars($quiz->c_wrong_message) : JText::_('COM_QUIZ_ANSWER_INCORRECT');
			$jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($quiz->c_right_message) ? htmlspecialchars($quiz->c_right_message) : JText::_('COM_QUIZ_ANSWER_CORRECT');
				
			$query = "SELECT * FROM #__quiz_t_question WHERE c_id = '".intval($quest_id)."'";
			$database->SetQuery( $query );
			$question = $database->LoadObjectList();
			
			$qtype = $question[0]->c_type;
			$jq_language['COM_QUIZ_ANSWER_INCORRECT'] = ($question[0]->c_wrong_message) ? htmlspecialchars($question[0]->c_wrong_message) : JText::_('COM_QUIZ_ANSWER_INCORRECT');
			$jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($question[0]->c_right_message) ? htmlspecialchars($question[0]->c_right_message) : JText::_('COM_QUIZ_ANSWER_CORRECT');
			
			$jq_language['COM_QUIZ_ANSWER_ACCEPTED'] = (($question[0]->c_type==8)&&($question->c_right_message)) ? htmlspecialchars($question[0]->c_right_message) : JText::_('COM_QUIZ_ANSWER_ACCEPTED');  
				
			$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$quiz->c_skin."'";
			$database->SetQuery( $query );
			$cur_tmpl = $database->LoadResult();
			if ($cur_tmpl) {
				$is_correct = 1;//first preview - previous question not defined (always correct)				
				JoomlaquizHelper::JQ_load_template($cur_tmpl);
				if (!empty($q_data)) {
					$ret_str .= "\t" . '<task>quest_preview</task>' . "\n";
					$ret_str .= "\t" . '<quiz_count_quests>'.$q_data_count.'</quiz_count_quests>' . "\n";		
					$ret_str .= "\t" . '<quiz_prev_correct>'.$is_correct.'</quiz_prev_correct>' . "\n";
					$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['COM_QUIZ_ANSWER_ACCEPTED']:$jq_language['COM_QUIZ_ANSWER_CORRECT']):$jq_language['COM_QUIZ_ANSWER_INCORRECT']));
					if( $qtype == 9) $msg_html = '';
					$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";
					$ret_str .= $this->JQ_GetQuestData($q_data[0], $quiz_id);
				}
			}
		}
		return $ret_str;
	}
	
	function JQ_NextPreview() {
		
		$appsLib = JqAppPlugins::getInstance();
		$plugins = $appsLib->loadApplications();
		
		$database = JFactory::getDBO();
		$ret_str = '';
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );

		$quest_ids = JFactory::getApplication()->input->get( 'quest_id', array(), '' );
		$answers = JFactory::getApplication()->input->get( 'answer', array(), '' );
		
		$preview_id = strval( JFactory::getApplication()->input->get( 'preview_id', '' ) );
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		
		if (!empty($quiz)) {
			$quiz = $quiz[0];
		} else { return $ret_str; }
		
		$query = "SELECT c_par_value FROM #__quiz_setup WHERE c_par_name = 'admin_preview'";
		$database->SetQuery( $query );
		$preview_code = $database->LoadResult();
		
		$q_quiz = 0;
		if (isset($quest_ids[0])) {
			$query = "SELECT c_quiz_id FROM #__quiz_t_question WHERE c_id = '".$quest_ids[0]."'";
			$database->SetQuery( $query );
			$q_quiz = $database->LoadResult();
		}

		$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$quiz->c_skin."'";
		$database->SetQuery( $query );
		$cur_tmpl = $database->LoadResult();
			
		if ($cur_tmpl && ($quiz_id == $q_quiz) && ($preview_id == $preview_code) && count($quest_ids)) {
			
			JoomlaquizHelper::JQ_load_template($cur_tmpl);
			
			$feedback_count = 0;	
			foreach($quest_ids as $ii=>$quest_id) {
				$answer = $answers[$ii];
				$query = "SELECT c_type from #__quiz_t_question WHERE c_id = '".$quest_id."'";
				$database->SetQuery( $query );
				$qtype = $database->LoadResult();
				$type = JoomlaquizHelper::getQuestionType($qtype);
				
				$is_correct = 0;
				$questtype1_answer_incorrect = '';
				
				$data = array();
				$data['quest_id'] = $quest_id;
				$data['quest_type'] = $data['type'] = $type;
				$data['answer'] = $answer;
				$data['questtype1_answer_incorrect'] = '';
				$data['is_correct'] = 0;
				
				$appsLib->triggerEvent( 'onNextPreviewQuestion' , $data );
				
				$questtype1_answer_incorrect = $data['questtype1_answer_incorrect'];
				$is_correct = $data['is_correct'];
						
				$query = "SELECT * FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND c_id = '".$quest_id."'";
				$database->SetQuery($query);
				$q_data = $database->LoadObjectList();
				
				$query = "SELECT count(*) FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
				$database->SetQuery($query);
				$q_data_count = $database->LoadResult();
				
				$q_data_count = 4;
				
				$jq_language = array();
				$jq_language['COM_QUIZ_ANSWER_INCORRECT'] = ($quiz->c_wrong_message) ? htmlspecialchars($quiz->c_wrong_message) : JText::_('COM_QUIZ_ANSWER_INCORRECT');
				$jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($quiz->c_right_message) ? htmlspecialchars($quiz->c_right_message) : JText::_('COM_QUIZ_ANSWER_CORRECT');
				
				$query = "SELECT * FROM #__quiz_t_question WHERE c_id = '".intval($quest_id)."'";
				$database->SetQuery( $query );
				$question = $database->LoadObjectList();

                $jq_language['COM_QUIZ_ANSWER_INCORRECT'] = ($question[0]->c_wrong_message || $question[0]->c_detailed_feedback) ? htmlspecialchars($question[0]->c_wrong_message . $question[0]->c_detailed_feedback) : JText::_('COM_QUIZ_ANSWER_INCORRECT');
                $jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($question[0]->c_right_message) ? htmlspecialchars($question[0]->c_right_message) : JText::_('COM_QUIZ_ANSWER_CORRECT');
				$jq_language['COM_QUIZ_ANSWER_ACCEPTED'] =(($question[0]->c_type==8) && ($question[0]->c_right_message)) ? htmlspecialchars($question[0]->c_right_message) : JText::_('COM_QUIZ_ANSWER_ACCEPTED');
				
				if ($cur_tmpl) {				
					if (!empty($q_data)) {
						
						$feedback_count++;
						$ret_str .= "\t" . '<feedback>' . "\n";
						$ret_str .= "\t" . '<quest_feedback>1</quest_feedback>' . "\n";
						$ret_str .= "\t" . '<quiz_prev_correct>'.$is_correct.'</quiz_prev_correct>' . "\n";
						$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['COM_QUIZ_ANSWER_ACCEPTED']:$jq_language['COM_QUIZ_ANSWER_CORRECT']):$jq_language['COM_QUIZ_ANSWER_INCORRECT']));
						
						if($qtype == 1) {
							//$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?($questtype1_answer_incorrect?$questtype1_answer_incorrect:$jq_language['COM_QUIZ_ANSWER_CORRECT']):(($questtype1_answer_incorrect)?($questtype1_answer_incorrect):($jq_language['COM_QUIZ_ANSWER_INCORRECT']))));
							$msg_html = ($is_correct)?$jq_language['COM_QUIZ_ANSWER_CORRECT']:$jq_language['COM_QUIZ_ANSWER_INCORRECT'];
							$msg_html .= ($questtype1_answer_incorrect)?'<br/>'.$questtype1_answer_incorrect:'';
							
							$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $msg_html );
						}
						
						if($qtype == 9) $msg_html = '';
						
						$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";
						$ret_str .= "\t" . '<quiz_allow_attempt>1</quiz_allow_attempt>' . "\n";
						$ret_str .= "\t" . '<feedback_quest_id>'.$quest_id.'</feedback_quest_id>' . "\n";
						$ret_str .= "\t" . '<feedback_quest_type>'.$qtype.'</feedback_quest_type>' . "\n";
						$ret_str .= "\t" . '</feedback>' . "\n";
					}
				}
			}
			
			$ret_str .= "\t" . '<feedback_count>'.$feedback_count.'</feedback_count>' . "\n";
			
			if (!empty($q_data)) {
				$ret_str .= "\t" . '<task>preview_finish</task>' . "\n";			
			}			
		}
		return $ret_str;
	}
	
	public function JQ_SeekQuestion() {
	
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$ret_str = '';
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		$z = 0;
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		
		if (!empty($quiz)) {
			$quiz = $quiz[0];
		} else { return $ret_str; }

		$now = JHtml::_('date',time(), 'Y-m-d H:i:s');
		if(!$this->userHasAccess($quiz, $my)){
			return $ret_str;
		}

		$stu_quiz_id = intval( JFactory::getApplication()->input->get( 'stu_quiz_id', 0 ) );

		$seek_quest_id = intval( JFactory::getApplication()->input->get( 'seek_quest_id', 0 ) );
		$user_unique_id = strval( JFactory::getApplication()->input->get( 'user_unique_id', '') );
		
		if (($quiz_id) && ($stu_quiz_id) && ($seek_quest_id)) {
			//'time is up' check
			if ($quiz->c_time_limit) {
				$user_time = 0;
                $query = "SELECT c_date_time, NOW() FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
                $database->SetQuery( $query );
                list($quiz_time2, $quiz_time1) = $database->loadRow();
                $quiz_time1a = strtotime($quiz_time1);
                $quiz_time2a = strtotime($quiz_time2);
				$user_time = $quiz_time1a - $quiz_time2a;
				if ($user_time > ($quiz->c_time_limit * 60)) {
					return $this->JQ_TimeIsUp($quiz, $stu_quiz_id);
				}
			}

			$query = "SELECT c_quiz_id, c_student_id, unique_id FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
			$database->SetQuery($query);
			$st_quiz_data = $database->LoadObjectList();

			$quiz_time = JHtml::_('date',time(), 'Y-m-d H:i:s');
			$start_quiz = 0;
			if (!empty($st_quiz_data)) {
				$start_quiz = $st_quiz_data[0]->c_quiz_id;
			} else { return $ret_str; }
			
			if ($user_unique_id != $st_quiz_data[0]->unique_id) { return ''; }
			if ($my->id != $st_quiz_data[0]->c_student_id) { return ''; }
			
			if ( ($quiz_id == $start_quiz) ) {
				$quesry = "SELECT count(*) FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND c_id = '".$seek_quest_id."' AND published = 1";
				$database->SetQuery( $query );
				if ($database->LoadResult()) {
					$quest_num = 1;
					// -- my chain ==//
					$q_data = array();
					$query = "SELECT q_chain FROM #__quiz_q_chain "
					. "\n WHERE s_unique_id = '".$user_unique_id."'";
					$database->SetQuery($query);
					$qch_ids = $database->LoadResult();
					if($qch_ids) {					
						$qchids = explode('*',$qch_ids);

						foreach ($qchids as $q_ord) {
						$query = "SELECT * FROM #__quiz_t_question WHERE c_id = ".intval($q_ord)." AND published = 1 ";
							$database->SetQuery( $query );
							$q_data_z[$z] = $database->LoadObjectList();
							$q_data[] = $q_data_z [$z][0];
							$z++;
						}				
					}
					// -- my chain ==//
					
					$i = 0;
					while ( ($i < count($q_data)) && ($q_data[$i]->c_id != $seek_quest_id) ) {
						$i ++;
					}
					$quest_num = $i + 1;

					$jq_language = array();
					$jq_language['COM_QUIZ_ANSWER_INCORRECT'] = ($quiz->c_wrong_message) ? htmlspecialchars($quiz->c_wrong_message) : JText::_('COM_QUIZ_ANSWER_INCORRECT');
					$jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($quiz->c_right_message) ? htmlspecialchars($quiz->c_right_message) : JText::_('COM_QUIZ_ANSWER_CORRECT');
		
					$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$quiz->c_skin."'";
					$database->SetQuery( $query );
					$cur_tmpl = $database->LoadResult();
					if ($cur_tmpl) {
						
						JoomlaquizHelper::JQ_load_template($cur_tmpl);
						if (isset($q_data[$i])) {

							$query = "SELECT c_date_time, NOW()  FROM #__quiz_r_student_quiz WHERE c_id = '$stu_quiz_id'";
							$database->SetQuery($query);
                            list($quiz_time2, $quiz_time1) = $database->loadRow();
							if ($quiz_time2) {
								$ret_str .= "\t" . '<quiz_past_time>'.intval(strtotime($quiz_time1)-strtotime($quiz_time2)).'</quiz_past_time>' . "\n";
							} else {
								$ret_str .= "\t" . '<quiz_past_time>0</quiz_past_time>' . "\n";
							}

							$quiz_count_quests = count($q_data);

							$ret_str .= "\t" . '<quiz_count_quests><![CDATA['.$quiz_count_quests.']]></quiz_count_quests>' . "\n";
							$ret_str .= "\t" . '<user_unique_id><![CDATA['.$user_unique_id.']]></user_unique_id>' . "\n";
							$ret_str .= "\t" . '<stu_quiz_id><![CDATA['.$stu_quiz_id.']]></stu_quiz_id>' . "\n";
							$ret_str .= "\t" . '<task>seek_quest</task>' . "\n";
							$ret_str .= $this->JQ_GetQuestData($q_data[$i], $quiz_id, $stu_quiz_id);
							$ret_str .= $this->JQ_GetPanelData($quiz_id, $q_data, $stu_quiz_id);
						}
					}
				}
			}
		}
		return $ret_str;
	}
	
	function JQ_ShowPage() {
		$database = JFactory::getDBO();
		
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		$database->setQuery("SELECT `c_show_qfeedback` FROM `#__quiz_t_quiz` WHERE `c_id` = '".$quiz_id."'");
		$c_show_qfeedback = $database->loadResult();
		$stu_quiz_id = intval( JFactory::getApplication()->input->get( 'stu_quiz_id', 0 ) );
		$ret = $this->JQ_GetStatistic($quiz_id, $stu_quiz_id, $c_show_qfeedback);
		
		return "\t" . '<quiz_statistic><![CDATA['.$ret.']]></quiz_statistic>' . "\n";
	}
	
	public function JQ_PrevQuestion() {
		
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$ret_str = '';
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		$timer = intval( JFactory::getApplication()->input->get( 'timer', 0 ) );
		
		$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$database->SetQuery ($query );
		$quiz = $database->LoadObjectList();
		
		if (!empty($quiz)) {
			$quiz = $quiz[0];
		} else { return $ret_str; }

		$now = JHtml::_('date',time(), 'Y-m-d H:i:s');
		if(!$this->userHasAccess($quiz, $my)){
			return $ret_str;
		}

		$stu_quiz_id = intval( JFactory::getApplication()->input->get( 'stu_quiz_id', 0 ) );
		$quest_ids = JFactory::getApplication()->input->get( 'quest_id', array(), '' );
		$answers = JFactory::getApplication()->input->get( 'answer', array(), '' );
		$user_unique_id = strval( JFactory::getApplication()->input->get( 'user_unique_id', '') );
		
		$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$quiz->c_skin."'";
		$database->SetQuery( $query );
		$cur_tmpl = $database->LoadResult();
				
		$blank_fbd	 = '';
		$feedback_count = 0;
		
		if ($cur_tmpl && is_array($quest_ids) && count($quest_ids) && $quiz_id && $stu_quiz_id) {
		
			for($q=0, $qn = count($quest_ids); $q < $qn; $q++) {
					
				$quest_id = $quest_ids[$q];
				$answer = $answers[$q];
				
				if ($answer == '~~~'|| $answer == ''|| $answer == 0) continue;
				
				// get question type
				$query = "SELECT c_type from #__quiz_t_question WHERE c_id = '".$quest_id."' AND published = 1";
				$database->SetQuery( $query );
				$qtype = $database->LoadResult();
					
				$query = "SELECT c_penalty from #__quiz_t_question WHERE c_id = '".$quest_id."' AND published = 1";
				$database->SetQuery( $query );
				$c_penalty = (int)$database->LoadResult();

				// insert results to the Database
					
				$is_avail = null;
				$is_correct = 0;
				$is_no_attempts = 0;
				$questtype1_answer_incorrect = '';
				$got_one_correct = false;
				
				$c_quest_cur_attempt = null;
				$c_all_attempts = null;

				$this->JQ_SaveAnswer($stu_quiz_id, $quest_id, $answer, $qtype, $c_penalty, $is_avail, $is_correct, $is_no_attempts, $questtype1_answer_incorrect, $got_one_correct, $c_quest_cur_attempt, $c_all_attempts, $timer);
				
				$query = "SELECT * FROM #__quiz_t_question WHERE c_id = '".intval($quest_id)."' AND published = 1";
				$database->SetQuery( $query );
				$question = $database->LoadObjectList();
				
				$jq_language = array();
				$jq_language['COM_QUIZ_ANSWER_INCORRECT'] = ($question[0]->c_wrong_message)?htmlspecialchars($question[0]->c_wrong_message):JText::_('COM_QUIZ_ANSWER_INCORRECT');
				$jq_language['COM_QUIZ_ANSWER_CORRECT'] = ($question[0]->c_right_message) ? htmlspecialchars($question[0]->c_right_message):JText::_('COM_QUIZ_ANSWER_CORRECT');
					
				if ($question[0]->c_partially_message) $jq_language['COM_QUIZ_PARTIALLY_CORRECT'] = htmlspecialchars($question[0]->c_partially_message);
				elseif ($question[0]->c_wrong_message) $jq_language['COM_QUIZ_PARTIALLY_CORRECT'] = htmlspecialchars($question[0]->c_wrong_message);
				else $jq_language['COM_QUIZ_PARTIALLY_CORRECT'] = $jq_language['COM_QUIZ_ANSWER_INCORRECT'];
					
				if ($got_one_correct) {
					if ($question[0]->c_partially_message) $jq_language['COM_QUIZ_ANSWER_INCORRECT'] = htmlspecialchars($question[0]->c_partially_message);
				}
					
				$jq_language['COM_QUIZ_ANSWER_ACCEPTED'] = JText::_('COM_QUIZ_ANSWER_ACCEPTED');
				if(($question[0]->c_type==8)&&($question[0]->c_right_message)) 
				$jq_language['COM_QUIZ_ANSWER_ACCEPTED'] = htmlspecialchars($question[0]->c_right_message);					
		
				if ($cur_tmpl) {
					
					JoomlaquizHelper::JQ_load_template($cur_tmpl);
					$feedback_count++;
					$ret_str .= "\t" . '<feedback>' . "\n";

				if (!$quiz->c_show_qfeedback && !$is_no_attempts && ($quiz->c_feedback && $question[0]->c_feedback )&& $qtype != 9 && !$question[0]->c_immediate) {
					$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['COM_QUIZ_ANSWER_ACCEPTED']:$jq_language['COM_QUIZ_ANSWER_CORRECT']): ($got_one_correct? $jq_language['COM_QUIZ_PARTIALLY_CORRECT']:$jq_language['COM_QUIZ_ANSWER_INCORRECT'])));
					if($qtype == 1)
					{
						//$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($questtype1_answer_incorrect)?$questtype1_answer_incorrect:$jq_language['COM_QUIZ_ANSWER_CORRECT']):(($questtype1_answer_incorrect)?($questtype1_answer_incorrect):($jq_language['COM_QUIZ_ANSWER_INCORRECT']))));	
						$msg_html = ($is_correct)?$jq_language['COM_QUIZ_ANSWER_CORRECT']:$jq_language['COM_QUIZ_ANSWER_INCORRECT'];
						$msg_html .= ($questtype1_answer_incorrect)?'<br/>'.$questtype1_answer_incorrect:'';
						
						$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $msg_html );
					}
						$ret_str .= "\t" . '<quest_feedback>1</quest_feedback>' . "\n";
						$ret_str .= "\t" . '<quest_feedback_repl_func>0</quest_feedback_repl_func>' . "\n";
						if ($blank_fbd && $blank_fbd_count) {
							$ret_str .= "\t" .  $blank_fbd;
							$ret_str .= "\t" . '<blank_fbd_count>'.$blank_fbd_count.'</blank_fbd_count>' . "\n";
						}

					} else {
						$ret_str .= "\t" . '<quest_feedback>0</quest_feedback>' . "\n";
						$ret_str .= "\t" . '<quest_feedback_repl_func><![CDATA[jq_QuizContinue();]]></quest_feedback_repl_func>' . "\n";
						$msg_html = ' ';
					}

					if ($is_no_attempts == 1 && $answer) {
						$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', JText::_('COM_MES_NO_ATTEMPTS'));
						$is_correct = 0;
					}
						
					$ret_str .= "\t" . '<quiz_prev_correct>'.$is_correct.'</quiz_prev_correct>' . "\n";
						
					$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";
					$ret_str .= "\t" . '<quiz_allow_attempt>'.$is_avail.'</quiz_allow_attempt>' . "\n";
					$ret_str .= "\t" . '<feedback_quest_id>'.$quest_id.'</feedback_quest_id>' . "\n";
					$ret_str .= "\t" . '<feedback_quest_type>'.$qtype.'</feedback_quest_type>' . "\n";
					$ret_str .= "\t" . '</feedback>' . "\n";
				}
			}
			
			$ret_str .= "\t" . '<feedback_count>'.$feedback_count.'</feedback_count>' . "\n";
			
			$query = "SELECT `q_chain` FROM `#__quiz_q_chain` AS a, `#__quiz_r_student_quiz` AS b WHERE `b`.`c_id` = '{$stu_quiz_id}' AND b.unique_id = a.s_unique_id ";
			$database->SetQuery($query);
			$q_chain = $database->loadResult();
			$all_quiz_quests = explode('*', $q_chain);
			$q_id = 0;
			foreach($all_quiz_quests as $quest) {
				if ( in_array( $quest, $quest_ids)) {
					break;
				}
				$q_id = $quest;			
			}
			
			if ($q_id) {
				$query = "SELECT * FROM `#__quiz_t_question` WHERE `c_id` = '{$q_id}' AND published = 1";
				$database->SetQuery($query);
				$q_data = $database->loadObjectList();
				$q_data = $q_data[0];
				$ret_str .= "\t" . '<task>prev</task>' . "\n";
				$ret_str .= $this->JQ_GetQuestData($q_data, $quiz_id, $stu_quiz_id);
			}
		}
		
		return $ret_str;
	}
	
	public function JQ_CheckBlank() {
		
		$database = JFactory::getDBO();
		
		$ret_str = '';
		$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
		$bid = intval( JFactory::getApplication()->input->get( 'bid', 0 ) );
		$text = JFactory::getApplication()->input->get( 'text', '' );
		
		$text =  urldecode(stripslashes($text));
		
		if ($quiz_id && $bid && $text) {
			$query = "SELECT * FROM `#__quiz_t_text` WHERE `c_blank_id` = ".$bid;
			$database->setQuery($query);
			$btexts = $database->loadObjectList();
			if (!empty($btexts)){
				$is_correct = false;
				foreach($btexts as $btext){	
					if ($btext->regexp) {	
						if (preg_match ($btext->c_text, $text)) {
							$is_correct = true; break;
						}
					} else {
						if (($btext->c_text) == $text){
							$is_correct = true; break;
						}
					}
				}	
				
				return "<task>blank_feedback</task> \t" . '<quest_blank_id><![CDATA['.$bid.']]></quest_blank_id>' . "\n". "\t" . '<is_correct><![CDATA['.intval($is_correct).']]></is_correct>' . "\n";			
			}
					
		}
		
		return '<task>blank_feedback</task>';
	}
		
	public function JQ_GetQuestData($q_data, $i_quiz_id, $stu_quiz_id = 0)
    {

        $database    = JFactory::getDBO();
        $ret_str     = '';
        $seek_quest_id
                     = intval(JFactory::getApplication()->input->get('seek_quest_id',
            0));
        $is_prev     = 0;
        $quest_count = 0;

        $database->SetQuery("SELECT a.template_name FROM #__quiz_templates as a, #__quiz_t_quiz as b WHERE b.c_id = '"
            . $i_quiz_id . "' and b.c_skin = a.id");
        $cur_template = $database->LoadResult();
        if ($cur_template) {
            JoomlaquizHelper::JQ_load_template($cur_template);
        }

        $query
            = "SELECT `c_random`, `c_pool`, `c_auto_breaks`, `c_pagination`, `c_enable_skip` FROM `#__quiz_t_quiz` WHERE `c_id` = '$i_quiz_id'";
        $database->SetQuery($query);

        $quiz = $database->loadObjectList();
        $quiz = $quiz[0];

        $pbreaks = array();
        if ($quiz->c_pagination == 2 && (!$quiz->c_random && !$quiz->c_pool)) {
            $query
                = "SELECT `c_question_id` FROM `#__quiz_t_pbreaks` WHERE `c_quiz_id` = '{$i_quiz_id}'";
            $database->SetQuery($query);
            $pbreaks = $database->loadColumn();
        }
        $pbreaks = (is_array($pbreaks) ? $pbreaks : array());

        $qchids = array();
        $query
                = "SELECT a.q_chain FROM #__quiz_q_chain AS a, #__quiz_r_student_quiz AS b"
            . "\n WHERE a.s_unique_id =  b.unique_id AND  b.c_id = '"
            . $stu_quiz_id . "'";
        $database->SetQuery($query);
        $qch_ids = $database->LoadResult();

        if ($qch_ids) {
            $qchids = explode('*', $qch_ids);
        }
        $qchids = (is_array($qchids) ? $qchids : array());
        if ($stu_quiz_id == 0) {
            $qchids = array($q_data->c_id);
        }

        if ($quiz->c_pagination == 0) {
            $pbreaks = $qchids;
        }

        $query
            = "SELECT c_id, ordering FROM `#__quiz_t_question` WHERE c_id IN ('"
            . implode("','", $qchids) . "') ORDER by ordering, c_id";
        $database->SetQuery($query);
        $tmp = $database->loadObjectList();

        $all_quiz_quests = array();
        foreach ($qchids as $qchid) {
            foreach ($tmp as $t) {
                if ($t->c_id == $qchid) {
                    $all_quiz_quests[] = $t;
                }
            }
        }

        $last_ordering  = -1;
        $last_pbreak_id = 0;
        foreach ($all_quiz_quests as $ii => $all_quiz_quest) {
            if ($all_quiz_quest->c_id != $q_data->c_id
                && in_array($all_quiz_quest->c_id, $pbreaks)
            ) {
                $last_pbreak_id = $all_quiz_quest->c_id;
                $last_ordering  = (isset($all_quiz_quests[$ii + 1]->ordering)
                    ? $all_quiz_quests[$ii + 1]->ordering : -1);
            }

            if ($all_quiz_quest->c_id == $q_data->c_id) {
                break;
            }
        }

        if ($last_ordering == -1 && $quiz->c_pagination == 0) {
            foreach ($all_quiz_quests as $all_quiz_quest) {
                if ($all_quiz_quest->c_id == $q_data->c_id) {
                    $last_ordering = $all_quiz_quest->ordering;
                }
            }
        }

        if ($last_pbreak_id) {
            $found = 0;
            foreach ($qchids as $qchid) {
                if ($qchid == $last_pbreak_id) {
                    $found = 1;
                    continue;
                }
                if (!$found) {
                    continue;
                }
                $q_ids[] = $qchid;
            }
        } else {
            $found = 0;
            foreach ($qchids as $k => $qchid) {
                if ($qchid == $q_data->c_id) {
                    $found = 1;
                }
                if (!$found) {
                    unset($qchids[$k]);
                }
            }
            $q_ids = $qchids;
        }

        $query
            = "SELECT * FROM `#__quiz_t_question` WHERE `ordering` >= '{$last_ordering}' AND c_id <> '{$last_pbreak_id}' AND c_id IN ('"
            . implode("','", $q_ids) . "') ORDER by ordering, c_id";
        $database->SetQuery($query);
        $tmp = $database->loadObjectList();

        $all_quests = array();
        foreach ($qchids as $qchid) {
            foreach ($tmp as $t) {
                if ($t->c_id == $qchid) {
                    $all_quests[] = $t;
                }
            }
        }

        $q_data = array();
        $query
                = "SELECT a.q_chain FROM #__quiz_q_chain AS a, #__quiz_r_student_quiz AS b"
            . "\n WHERE a.s_unique_id =  b.unique_id AND  b.c_id = '"
            . $stu_quiz_id . "'";
        $database->SetQuery($query);
        $qch_ids = $database->LoadResult();
        if ($qch_ids) {
            $qchids = explode('*', $qch_ids);
        }

        if (is_array($all_quests) && !empty($all_quests) && is_array($qchids) && !empty($qchids)
        ) {
            if ($qchids[0] <> $all_quests[0]->c_id) {
                $is_prev = 1;
            }
        }

        foreach ($all_quests as $q_data) {
            $quest_count++;
            $ret_add_script = '';
            $ret_str        .= "\t" . '<question_data>';
            $c_all_attempts = $q_data->c_attempts;
            $z              = 1;
            if ($stu_quiz_id && is_array($qchids) && !empty($qchids)) {

                foreach ($qchids as $qchid) {
                    if ($qchid == $q_data->c_id) {
                        $ret_str .= "\t" . '<quiz_quest_num>' . $z
                            . '</quiz_quest_num>' . "\n";
                        $z       = -1;
                        break;
                    }
                    $z++;
                }

            }
            if ($z != -1) {
                $ret_str .= "\t" . '<quiz_quest_num>X</quiz_quest_num>' . "\n";
            }

            if ($seek_quest_id) {
                if ($q_data->c_id != $seek_quest_id) {
                    $ret_str .= "\t" . '<quest_task>disabled</quest_task>'
                        . "\n";
                } else {
                    if ($stu_quiz_id) {
                        $query
                            = "SELECT c_id, c_attempts FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '"
                            . $stu_quiz_id . "' and c_question_id = '"
                            . $q_data->c_id . "'";
                        $database->SetQuery($query);
                        $c_tmp = $database->LoadObjectList();
                        if (!empty($c_tmp)) {
                            $c_quest_cur_attempt = (int)$c_tmp[0]->c_attempts;
                            if ($c_quest_cur_attempt >= $c_all_attempts
                                && $c_all_attempts != 0
                            ) {
                                $ret_str .= "\t"
                                    . '<quest_task>no_attempts</quest_task>'
                                    . "\n";
                                $msg_html
                                         = JoomlaQuiz_template_class::JQ_show_messagebox('',
                                    JText::_('COM_MES_NO_ATTEMPTS'));
                                $ret_str .= "\t"
                                    . '<quest_message_box><![CDATA[' . $msg_html
                                    . ']]></quest_message_box>' . "\n";
                            } elseif($c_quest_cur_attempt > $c_all_attempts
                                && $c_all_attempts == 0){
                                $ret_str .= "\t" . '<quest_task>disabled</quest_task>' . "\n";
                            } else {
                                $ret_str .= "\t" . '<quest_task>ok</quest_task>'
                                    . "\n";
                            }
                        } else {
                            $ret_str .= "\t" . '<quest_task>ok</quest_task>'
                                . "\n";
                        }
                    } else {
                        $ret_str .= "\t" . '<quest_task>ok</quest_task>' . "\n";
                    }
                }
            } elseif ($stu_quiz_id) {
                $query
                    = "SELECT c_id, c_attempts FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '"
                    . $stu_quiz_id . "' and c_question_id = '" . $q_data->c_id
                    . "'";
                $database->SetQuery($query);
                $c_tmp = $database->LoadObjectList();
                if (!empty($c_tmp)) {
                    $c_quest_cur_attempt = (int)$c_tmp[0]->c_attempts;
                    if ($c_quest_cur_attempt >= $c_all_attempts
                        && $c_all_attempts != 0
                    ) {
                        $ret_str .= "\t"
                            . '<quest_task>no_attempts</quest_task>' . "\n";
                        $msg_html
                                 = JoomlaQuiz_template_class::JQ_show_messagebox('',
                            JText::_('COM_MES_NO_ATTEMPTS'));
                        $ret_str .= "\t" . '<quest_message_box><![CDATA['
                            . $msg_html . ']]></quest_message_box>' . "\n";
                    } elseif($c_quest_cur_attempt > $c_all_attempts
                        && $c_all_attempts == 0){
                        $ret_str .= "\t" . '<quest_task>disabled</quest_task>' . "\n";
                    } else {
                        $ret_str .= "\t" . '<quest_task>ok</quest_task>' . "\n";
                    }
                } else {
                    $ret_str .= "\t" . '<quest_task>ok</quest_task>' . "\n";
                }
            } else {
                $ret_str .= "\t" . '<quest_task>ok</quest_task>' . "\n";
            }

            if ($cur_template) {

                $query
                    = "SELECT `c_flag_question` FROM `#__quiz_r_student_question` WHERE `c_stu_quiz_id` = '"
                    . $stu_quiz_id . "' and `c_question_id` = '" . $q_data->c_id
                    . "'";
                $database->SetQuery($query);
                $c_flag_question = ($database->LoadResult())
                    ? $database->LoadResult() : 0;

                $query
                    = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '"
                    . $stu_quiz_id . "' AND c_question_id = '" . $q_data->c_id
                    . "'";
                $database->SetQuery($query);
                $sid = $database->loadResult();

                JoomlaquizHelper::JQ_GetJoomFish($q_data->c_question,
                    'quiz_t_question', 'c_question', $q_data->c_id);
                ob_start();
                $q_data->c_question
                    = JoomlaquizHelper::JQ_ShowText_WithFeatures($q_data->c_question,
                    true);
                ob_end_clean();
                $ret_add = '';

                $type         = JoomlaquizHelper::getQuestionType($q_data->c_type);
                $class_suffix = JoomlaquizHelper::loadAddonsFunctions($type,
                    'JoomlaquizViewCaption',
                    $type . '/tmpl/' . $cur_template . '/caption', true);
                if ($class_suffix === false) {
                    $ret_add
                        = JoomlaQuiz_template_class::JQ_get_questcaption($q_data->c_question);
                } else {
                    if (method_exists('JoomlaquizViewCaption' . $class_suffix,
                        'getCaption')) {
                        $className = 'JoomlaquizViewCaption' . $class_suffix;
                        $ret_add   = $className::getCaption($q_data,
                            $stu_quiz_id);
                    }
                }

                $appsLib = JqAppPlugins::getInstance();
                $appsLib->loadApplications();
                $data               = array();
                $data['quest_type'] = $type;
                $data['q_data']     = $q_data;

                $appsLib->triggerEvent('onPointsForAnswer', $data);
                $q_data = $data['q_data'];

                $ret_str .= "\t" . '<quest_type>' . $q_data->c_type
                    . '</quest_type>' . "\n";
                $ret_str .= "\t" . '<quest_id>' . $q_data->c_id . '</quest_id>'
                    . "\n";
                $ret_str .= "\t" . '<flag_question>' . $c_flag_question
                    . '</flag_question>' . "\n";
                $ret_str .= "\t" . '<skip_question>'
                    . $this->JQ_GetNextQuestion($qch_ids, $i_quiz_id,
                        $stu_quiz_id, $q_data->c_id) . '</skip_question>'
                    . "\n";
                $ret_str .= "\t" . '<quest_score><![CDATA[' . $q_data->c_point
                    . ']]></quest_score>' . "\n";
                $ret_str .= "\t" . '<quest_separator><![CDATA['
                    . $q_data->c_separator . ']]></quest_separator>' . "\n";
                $ret_str .= ($q_data->c_time_limit) ? "\t"
                    . '<quest_limit_time>' . $q_data->c_time_limit
                    . '</quest_limit_time>' . "\n"
                    : "\t" . '<quest_limit_time>0</quest_limit_time>' . "\n";
                //if randomize quest
                $query
                    = "SELECT c_random from #__quiz_t_question WHERE c_id = '"
                    . $q_data->c_id . "' AND published = 1";
                $database->SetQuery($query);
                $qrandom = $database->LoadResult();

                $im_check = $q_data->c_immediate;

                $data                   = array();
                $data['quest_type']     = $type;
                $data['q_data']         = $q_data;
                $data['cur_template']   = $cur_template;
                $data['im_check']       = $im_check;
                $data['qrandom']        = $qrandom;
                $data['sid']            = $sid;
                $data['ret_str']        = '';
                $data['ret_add']        = $ret_add;
                $data['ret_add_script'] = $ret_add_script;

                $appsLib->triggerEvent('onCreateQuestion', $data);

                $ret_str        .= $data['ret_str'];
                $ret_add        = $data['ret_add'];
                $ret_add_script = $data['ret_add_script'];
            }

            $img_urls = array();
            $pat_im   = '/<img[^>]+src=([\'|\"])([^>]+)\1[^>]*>/iU';
            $pat_url  = '/^(http|https|ftp):\/\//i';
            $out_arr  = preg_split($pat_im, $ret_add);
            if (preg_match_all($pat_im, $ret_add, $quest_images,
                PREG_SET_ORDER)) {
                foreach ($quest_images as $img_c => $quest_image) {
                    $img_urls[$img_c] = @$quest_image[2];
                    if (preg_match($pat_url, $img_urls[$img_c], $url_match)) {
                        $img_urls[$img_c] = '';
                    }
                }
            }

            $out_html = "";
            if (!empty($out_arr)) {
                foreach ($out_arr as $html_c => $html_peace) {
                    if (count($out_arr) != $html_c && isset($img_urls[$html_c])) {
                        if (!$img_urls[$html_c]) {
                            $out_html .= $html_peace . $quest_images[$html_c][0];
                        } else {
                            $src_arr  = explode($quest_images[$html_c][2],
                                $quest_images[$html_c][0]);
                            $img_tag  = implode(JURI::root()
                                . $quest_images[$html_c][2], $src_arr);
                            $out_html .= $html_peace . $img_tag;
                        }
                    } else {
                        $out_html .= $html_peace;
                    }
                }
            }

            $ret_add = ($out_html) ? $out_html : $ret_add;
            $ret_str .= "\t" . '<quest_data><![CDATA[' . $ret_add
                . ']]></quest_data>' . "\n";
            $ret_str .= "\t" . '<quest_im_check><![CDATA[' . $data['im_check']
                . ']]></quest_im_check>' . "\n";

            if ($ret_add_script) {
                $ret_str .= "\t" . '<exec_quiz_script>1</exec_quiz_script>'
                    . "\n";
                $ret_str .= "\t" . '<quiz_script_data><![CDATA['
                    . $ret_add_script . ']]></quiz_script_data>' . "\n";
            } else {
                $ret_str .= "\t" . '<exec_quiz_script>0</exec_quiz_script>'
                    . "\n";
                $ret_str .= "\t"
                    . '<quiz_script_data><![CDATA[ ]]></quiz_script_data>'
                    . "\n";
            }

            $ret_str .= '</question_data>' . "\n";

            /* page breaking start */

            if ($quiz->c_pagination == 0) {
                break;
            }

            if ($quiz->c_pagination == 3 && $quiz->c_auto_breaks
                && ($quiz->c_auto_breaks == $quest_count)
            ) {
                break;
            }

            if (in_array($q_data->c_id, $pbreaks)) {
                break;
            }

            /* page breaking end */
        }

        $is_last = 0;

        if (!$seek_quest_id && $stu_quiz_id && is_array($all_quests)
            && !empty($all_quests)
            && is_array($qchids)
            && !empty($qchids)
        ) {
            $query
                = "SELECT c_question_id FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '"
                . $stu_quiz_id . "'";
            $database->SetQuery($query);
            $q_ids = $database->loadColumn();

            if (is_array($q_ids) && !empty($q_ids)) {
                $diff = array_diff($qchids, $q_ids);
                if (!empty($diff)) {
                    if (count($diff) == 1
                        && array_pop($diff) == $q_data->c_id
                    ) {
                        $is_last = 1;
                    }
                } else {
                    $is_last = 1;
                }
            }
        } elseif (!$seek_quest_id && is_array($all_quests) && !empty($all_quests)
            && is_array($qchids) && !empty($qchids)
        ) {
            if ($qchids[count($qchids) - 1] == $q_data->c_id) {
                $is_last = 1;
            }
        }

        $ret_str .= "\t" . '<is_last>' . $is_last . '</is_last>' . "\n";
        $ret_str .= "\t" . '<skip_type>' . $quiz->c_enable_skip . '</skip_type>'
            . "\n";
        $ret_str .= "\t" . '<quest_count>' . (int)$quest_count
            . '</quest_count>' . "\n";
        $ret_str .= "\t" . '<is_prev>' . $is_prev . '</is_prev>' . "\n";

        return $ret_str;
    }

    public function JQ_GetNextQuestion($qch_ids, $quiz_id, $stu_quiz_id, $current_quest)
	{
			$quest_id = 0;
			$database = JFactory::getDBO();
			$database->setQuery("SELECT * FROM `#__quiz_t_quiz` WHERE `c_id` = '".$quiz_id."'");
			$quiz_data = $database->loadObject();
			if($quiz_data->c_enable_skip && !$quiz_data->c_pagination && $qch_ids)
			{
				$qchids = explode('*', $qch_ids);
				if(count($qchids) == 1) return 0;
				
				$query = "SELECT c_question_id FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
				$database->SetQuery( $query );
				$q_ids = $database->loadColumn();
				
				if (is_array($qchids) && !empty($qchids)) {
					$diff = (is_array($q_ids) && !empty($q_ids)) ? array_diff ($qchids, $q_ids) : $qchids;
						if(!empty($diff)){
						$diff = array_values($diff);
						$key = array_search($current_quest, $diff);
						if($quiz_data->c_random && $quiz_data->c_enable_skip != 2){
							if(isset($diff[$key])) unset($diff[$key]);
							shuffle($diff);
							$quest_id = $diff[0];
						} else {
							$quest_id = (isset($diff[$key + 1])) ? $diff[$key + 1] : $diff[0];
						}
					}
				}
				return $quest_id;
			} else {
				return 0;
			}
	}
	
	public function JQ_GetPanelData($quiz_id, $q_data, $stu_quiz_id=0) {
		
		$database = JFactory::getDBO();
		$database->setQuery("SELECT qt.template_name FROM #__quiz_templates as qt LEFT JOIN #__quiz_t_quiz as q ON q.c_skin = qt.id WHERE q.c_id = '".$quiz_id."'");
		$tmpl = $database->loadResult();
		
		$panel_str = "\t" . '<quiz_panel_data><![CDATA[';
		$panel_data = $q_data;
		$panel_str .= JoomlaQuiz_template_class::JQ_panel_start();
		
		$k = $n = 1;
		$cquests = array();
		$all_quests = array();
		if ($stu_quiz_id){
			$query = "SELECT c_question_id FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' AND is_correct = 1";
			$database->SetQuery( $query );
			$cquests = (array)$database->loadColumn();
			
			$query = "SELECT c_question_id FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' ";
			$database->SetQuery( $query );
			$all_quests = (array)$database->loadColumn();
		}
		foreach ($panel_data as $panel_row) {
			JoomlaquizHelper::JQ_GetJoomFish($panel_row->c_question, 'quiz_t_question', 'c_question', $panel_row->c_id);
			
			$panel_row->c_question = strip_tags($panel_row->c_question);
			$panel_row->c_question = (strlen($panel_row->c_question)>200? JoomlaquizHelper::jq_substr($panel_row->c_question, 0, 160).'...': $panel_row->c_question);
			$panel_str .= JoomlaQuiz_template_class::JQ_panel_data($panel_row, $all_quests, $cquests, $stu_quiz_id, $k, $n);
			$k = 3 - $k;
			$n++;
		}
		$panel_str .= '</table>]]></quiz_panel_data>' . "\n";
		return $panel_str;
	}
	
	public function JQ_TimeIsUp($quiz, $stu_quiz_id=null) {
		
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$ret_str = '';
		if ($stu_quiz_id) {
			$query = "UPDATE #__quiz_r_student_quiz SET c_finished = '1' WHERE c_id = '".$stu_quiz_id."' AND c_student_id = '".$my->id."'";
			$database->SetQuery( $query );
			$database->query();
		}
				
		$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$quiz->c_skin."'";
		$database->SetQuery( $query );
		$cur_tmpl = $database->LoadResult();
		if ($cur_tmpl) {
			
			JoomlaquizHelper::JQ_load_template($cur_tmpl);
			
			$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', JText::_('COM_QUIZ_MES_TIMEOUT'));
			$quiz_id = intval( JFactory::getApplication()->input->get( 'quiz', 0 ) );
			$quest_ids = JFactory::getApplication()->input->get( 'quest_id', array(), '' );
			$answers = JFactory::getApplication()->input->get( 'answer', array(), '' );
			$user_unique_id = strval( JFactory::getApplication()->input->get( 'user_unique_id', '') );
			$ret_str .= "\t" . '<task>time_is_up</task>' . "\n";
			$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";
	
			for($q=0, $qn = count($quest_ids); $q < $qn; $q++) {
					$quest_id = $quest_ids[$q];
					$answer = $answers[$q];
					
					// get question type
					$query = "SELECT c_type from #__quiz_t_question WHERE c_id = '".$quest_id."' AND published = 1";
					$database->SetQuery( $query );
					$qtype = $database->LoadResult();
					
					$query = "SELECT c_penalty from #__quiz_t_question WHERE c_id = '".$quest_id."' AND published = 1";
					$database->SetQuery( $query );
					$c_penalty = (int)$database->LoadResult();

					// insert results to the Database
					
					$is_avail = null;
					$is_correct = 0;
					$is_no_attempts = 0;
					$questtype1_answer_incorrect = '';
					$got_one_correct = false;
					
					$c_quest_cur_attempt = null;
					$c_all_attempts = null;

					$this->JQ_SaveAnswer(
						$stu_quiz_id,
						$quest_id,
						$answer,
						$qtype,
						$c_penalty,
						$is_avail,
						$is_correct,
						$is_no_attempts,
						$questtype1_answer_incorrect,
						$got_one_correct,
						$c_quest_cur_attempt,
						$c_all_attempts
					);
			}
		}
				
		return $ret_str;
	}
	
	public function JQ_SaveAnswer($stu_quiz_id, $quest_id, $answer, $qtype, $c_penalty, &$is_avail, &$is_correct, &$is_no_attempts, &$questtype1_answer_incorrect, &$got_one_correct, &$c_quest_cur_attempt, &$c_all_attempts, $timer = 0) {	
		
		$return = false;
		$type = JoomlaquizHelper::getQuestionType($qtype);
		$appsLib = JqAppPlugins::getInstance();
		$appsLib->loadApplications();
		
		$data = array();
		$data['quest_type'] = $type;
		$data['stu_quiz_id'] = $stu_quiz_id;
		$data['quest_id'] = $quest_id;
		$data['answer'] = $answer;
		$data['qtype'] = $qtype;
		$data['c_penalty'] = $c_penalty;
		$data['is_avail'] = $is_avail;
		$data['is_correct'] = $is_correct;
		$data['is_no_attempts'] = $is_no_attempts;
		$data['questtype1_answer_incorrect'] = $questtype1_answer_incorrect;
		$data['got_one_correct'] = $got_one_correct;
		$data['c_quest_cur_attempt'] = $c_quest_cur_attempt;
		$data['c_all_attempts'] = $c_all_attempts;
		$data['timer'] = $timer;
		
		$appsLib->triggerEvent( 'onSaveQuestion' , $data);

		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$dispatcher->trigger('onJQuizAnswerSubmitted', array (&$data));
		
		$is_avail = $data['is_avail'];
		$is_correct = $data['is_correct'];
		$is_no_attempts = $data['is_no_attempts'];
		$questtype1_answer_incorrect = $data['questtype1_answer_incorrect'];
		$got_one_correct = $data['got_one_correct'];
		$c_quest_cur_attempt = $data['c_quest_cur_attempt'];
		$c_all_attempts = $data['c_all_attempts'];
		
		return $return;
	}
	
	public function JQ_GetStatistic($quiz_id, $stu_quiz_id, $c_show_qfeedback = 0) {
		$database = JFactory::getDBO();
		
		$database->SetQuery("SELECT a.template_name FROM #__quiz_templates as a, #__quiz_t_quiz as b WHERE b.c_id = '".$quiz_id."' and b.c_skin = a.id");
		$cur_template = $database->LoadResult();
		
		if ($cur_template) {
			JoomlaquizHelper::JQ_load_template($cur_template);
		} else {
			return '';
		}	
		
		$ret = JoomlaQuiz_template_class::JQ_final_feedback();
		
		$quest_per_page = intval( JFactory::getApplication()->input->get( 'quest_per_page', 25) );
		$limitstart = intval( JFactory::getApplication()->input->get( 'limitstart', 0 ) );
		
		$query = "SELECT * FROM `#__quiz_r_student_quiz` WHERE c_id = ".$stu_quiz_id;
		$database->setQuery($query);
		$stu_quiz = $database->loadObjectList();
		$stu_quiz = @$stu_quiz[0];
		
		$query = "SELECT q_chain FROM `#__quiz_q_chain` WHERE s_unique_id = '".$stu_quiz->unique_id."'";
		$database->setQuery($query);
		$q_chain = $database->loadResult();	
		$q_ids = explode('*', $q_chain);
		
		$total = count($q_ids);
		
		if (!isset($_REQUEST['quest_per_page']) && abs($quest_per_page-$total) < 6){
			$quest_per_page = $total;
		}
		if ($quest_per_page >= $total) {
			$limitstart = 0;
		}

		$q_ids = array_slice ($q_ids, $limitstart, $quest_per_page); 
		
		$query = "SELECT * FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
		$database->SetQuery( $query );
		$info = $database->LoadObjectList();
		
		$ret_questions = '';
		if (is_array($q_ids) && is_array($info))
		foreach($q_ids as $z => $q_id){
			$query = "SELECT * FROM `#__quiz_t_question` WHERE c_id = '".$q_id."' AND published = 1";
			$database->SetQuery( $query );
			$q_data = $database->LoadObjectList();
			
			if(isset($q_data[0])){
				$ret_questions .= $this->JQ_GetQuestData_Feedback($q_data[0], $quiz_id, $stu_quiz_id, ($z+$limitstart+1), $c_show_qfeedback). JoomlaQuiz_template_class::JQ_getFeedbackQuestionDelimeter();
			}
		}
		
		$ret = str_replace('<!-- QUESTIONS -->', $ret_questions, $ret);
		
		$qpp = array();
		$qpp[] = JHTML::_('select.option',5, '5');
		$qpp[] = JHTML::_('select.option',10, '10');
		$qpp[] = JHTML::_('select.option',25, '25');
		$qpp[] = JHTML::_('select.option',50, '50');
		$qpp[] = JHTML::_('select.option',$total, 'All');
		$javascript = ' onchange="javascript: pagination_go('.$limitstart.')" ';
		$quest_pp = JHTML::_('select.genericlist', $qpp, 'quest_per_page', 'class="inputbox" size="1" id="quest_per_page" '.$javascript, 'value', 'text', $quest_per_page );

		$ret_pagination = JText::_('COM_QUIZ_DISPLAY_NO').':&nbsp;'.$quest_pp;
		$pages = ceil($total/$quest_per_page);
		$page = 1;
		if ($pages > 1) {
			if ($limitstart == 0)
				$ret_pagination .= '&nbsp;&nbsp;'.JText::_('COM_QUIZ_FIRST').'&nbsp;&nbsp;';
			else
				$ret_pagination .= '&nbsp;&nbsp;<a href="javascript: pagination_go(0)">'.JText::_('COM_QUIZ_FIRST').'</a>&nbsp;&nbsp;';
			for($i=0; $i<$pages; $i++) {	
				if ($limitstart >= ($i*$quest_per_page) &&  $limitstart < ($i+1)*$quest_per_page){
					$ret_pagination .= ($i+1).'&nbsp;&nbsp;';
					$page = $i+1;
				}else
					$ret_pagination .= '<a href="javascript: pagination_go('.$i*$quest_per_page.')">'.($i+1).'</a>&nbsp;&nbsp;';			
			}
			if ($limitstart == $quest_per_page*($pages-1))
				$ret_pagination .= JText::_('COM_QUIZ_LAST');	
			else
				$ret_pagination .= '<a href="javascript: pagination_go('.$quest_per_page*($pages-1).')">'.JText::_('COM_QUIZ_LAST').'</a>';	
		}
		
		$ret = str_replace('<!-- PAGINATION -->', $ret_pagination, $ret);
		
		return $ret;
	}
	
	public function JQ_GetQuestData_Feedback($q_data, $i_quiz_id, $stu_quiz_id, $no = '', $c_show_qfeedback = 0) {
		
		$appsLib = JqAppPlugins::getInstance();
		$plugins = $appsLib->loadApplications();
		$database = JFactory::getDBO();

		$database->SetQuery("SELECT a.template_name, b.c_right_message, b.c_wrong_message FROM #__quiz_templates as a, #__quiz_t_quiz as b WHERE b.c_id = '".$i_quiz_id."' and b.c_skin = a.id");
        $quiz = $database->loadObject();
		$cur_template = $quiz->template_name;
		if ($cur_template) {
			JoomlaquizHelper::JQ_load_template($cur_template);
			JoomlaquizHelper::JQ_GetJoomFish($q_data->c_question, 'quiz_t_question', 'c_question', $q_data->c_id);
			ob_start();
			$q_data->c_question = JoomlaquizHelper::JQ_ShowText_WithFeatures($q_data->c_question, true);
			ob_end_clean();
			
			if($q_data->c_type == 6){
				$q_data->c_question=  JoomlaquizHelper::Blnk_replace_quest_fdb($q_data->c_id, $q_data->c_question, $stu_quiz_id);
			}
			
			$img_urls = array();
			$pat_im = '/<img[^>]+src=([\'|\"])([^>]+)\1[^>]*>/iU';
			$pat_url = '/^(http|https|ftp):\/\//i';
			$out_arr = preg_split($pat_im, $q_data->c_question);
			if(preg_match_all($pat_im, $q_data->c_question, $quest_images, PREG_SET_ORDER))
			{
				foreach($quest_images as $img_c => $quest_image){
					$img_urls[$img_c] = @$quest_image[2];
					if(preg_match($pat_url, $img_urls[$img_c], $url_match)){
						$img_urls[$img_c] = '';
					}
				}
			}
			
			$out_html = "";
			if(!empty($out_arr))
			{
				foreach($out_arr as $html_c => $html_peace){
					if(count($out_arr) != $html_c && isset($img_urls[$html_c])){
						if(!$img_urls[$html_c]){
						$out_html .= $html_peace.$quest_images[$html_c][0];
						} else {
						$src_arr = explode($quest_images[$html_c][2], $quest_images[$html_c][0]);
						$img_tag = implode(JURI::root().$quest_images[$html_c][2], $src_arr);
						$out_html .= $html_peace.$img_tag;
						}
					} else {
						$out_html .= $html_peace;
					}
				}
			}

			$q_data->c_question = $out_html;

			$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id = '".$q_data->c_id."' AND c_right = 1";
			$database->SetQuery( $query );
			$max_points = (floatval($database->LoadResult()) + $q_data->c_point);
			$q_data->c_point = $q_data->c_point.' - '.$max_points;

            $query = "SELECT `c_id`,`c_score`, `is_correct` FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$stu_quiz_id."' AND c_question_id = '".$q_data->c_id."'";
            $database->SetQuery( $query );
            $data_qrsq = $database->loadObject();
            $score = $data_qrsq->c_score;
            $is_correct = $data_qrsq->is_correct;
						
			$type = JoomlaquizHelper::getQuestionType($q_data->c_type);
			$data = array();
			$data['type'] = $data['quest_type'] = $type;
			$data['q_data'] = $q_data;
			$data['score'] = $score;
			$data['stu_quiz_id'] = $stu_quiz_id;
			$data['cur_template'] = $cur_template;
			
			$appsLib->triggerEvent( 'onFeedbackQuestion' , $data );
			$qoption = $data['qoption'];
									
			if($c_show_qfeedback){
				if($q_data->c_feedback)
				{
                    if($q_data->c_type == 1){       //Multiple Choice
                        $database->SetQuery("SELECT * FROM `#__quiz_t_choice` WHERE `c_question_id` = '".(int)$q_data->c_id."'");
                        $q_choices = $database->LoadObjectList();

                        $database->SetQuery("SELECT * FROM `#__quiz_r_student_choice` WHERE `c_sq_id` = '".$data_qrsq->c_id."' ");
                        $qrsc = $database->loadObject();

                        if($q_choices && is_array($q_choices)) {
                            foreach ($q_choices as $choice) {
                                if((int)$is_correct){
                                if ((int)$choice->c_right == (int)$is_correct) {
                                        $feedback_message = $q_data->c_right_message;
                                        $feedback_message.= $choice->c_incorrect_feed;
                                        break;
                                    }
                                }
                                else{
                                    $feedback_message = $q_data->c_wrong_message;
                                    $feedback_message .= $q_data->c_detailed_feedback;
                                    if($qrsc->c_choice_id == $choice->c_id ){
                                        $feedback_message .= $choice->c_incorrect_feed;
                                    break;
                                }
                            }
                        }
                        }
                        $qoption .= "\t" . '<div class="jq_question_feedback"><strong>'.JText::_('COM_QUIZ_FEEDBACK_QUESTION').':</strong><br/><br/>'.$feedback_message.'</div>' . "\n";
                    }
                    else {
                        if ($q_data->c_right_message) {
                            $c_right_message = $q_data->c_right_message;
                        } elseif ($quiz->c_right_message) {
                            $c_right_message = $quiz->c_right_message;
                        } else {
                            $c_right_message = JText::_('COM_QUIZ_CORRECT');
                        }

                        if ($q_data->c_wrong_message) {
                            $c_wrong_message = $q_data->c_wrong_message;
                        } elseif ($quiz->c_wrong_message) {
                            $c_wrong_message = $quiz->c_wrong_message;
                        } else {
                            $c_wrong_message = JText::_('COM_QUIZ_INCORRECT');
                        }

                        $begin_center = ($q_data->c_type == 7) ? '<center>' : '';
                        $end_center = ($q_data->c_type == 7) ? '</center>' : '';

                        if ($score && $score > 0) {
                            if ($score < $max_points) {
                                $qoption .= "\t" . '<div class="jq_question_feedback">' . $begin_center . '<strong>' . JText::_('COM_QUIZ_PARTIALLY_CORRECT') . ':</strong><br/><br/>' . $q_data->c_partially_message . $end_center . '</div>' . "\n";
                            } else {
                                $qoption .= "\t" . '<div class="jq_question_feedback">' . $begin_center . '<strong>' . JText::_('COM_QUIZ_FEEDBACK_QUESTION') . ':</strong><br/><br/>' . $c_right_message . $end_center . '</div>' . "\n";
                            }
                        } else {
                            if($is_correct){
                                $qoption .= "\t" . '<div class="jq_question_feedback">' . $begin_center . '<strong>' . JText::_('COM_QUIZ_FEEDBACK_QUESTION') . ':</strong><br/><br/>' . $c_right_message . $end_center . '</div>' . "\n";
                            }
                            else{
                            $qoption .= "\t" . '<div class="jq_question_feedback">' . $begin_center . '<strong>' . JText::_('COM_QUIZ_FEEDBACK_QUESTION') . ':</strong><br/><br/>' . $c_wrong_message . $end_center . '</div>' . "\n";
                        }

                        }
                    }
				}
			}
			
			$ret_str = JoomlaQuiz_template_class::JQ_final_feedback_question($no, $q_data->c_question, $qoption);
		}
		
		return $ret_str;
	}
	
	function JQ_GetQuestData_review($q_data, $i_quiz_id, $stu_quiz_id=0) {
		
		$appsLib = JqAppPlugins::getInstance();
		$plugins = $appsLib->loadApplications();
		
		$database = JFactory::getDBO();
		$stu_quiz_id = intval( JFactory::getApplication()->input->get( 'stu_quiz_id', 0 ) );
		
		$quest_count = 0;

		$ret_str = '';
		$ret_add_script = '';
		$database->SetQuery("SELECT a.template_name FROM #__quiz_templates as a, #__quiz_t_quiz as b WHERE b.c_id = '".$i_quiz_id."' and b.c_skin = a.id");
		$cur_template = $database->LoadResult();
		JoomlaquizHelper::JQ_load_template($cur_template);
		
		$query = "SELECT `c_random`, `c_pool`, `c_pagination` FROM `#__quiz_t_quiz` WHERE `c_id` = '$i_quiz_id'";
		$database->SetQuery($query);

		$quiz = $database->loadObjectList();
		$quiz = $quiz[0];
		
		$qchids = array();
		$query = "SELECT a.q_chain FROM #__quiz_q_chain AS a, #__quiz_r_student_quiz AS b"
				. "\n WHERE a.s_unique_id =  b.unique_id AND  b.c_id = '".$stu_quiz_id."'";
		$database->SetQuery($query);
		$qch_ids = $database->LoadResult();
		if($qch_ids) {					
			$qchids = explode('*',$qch_ids);
		}
		$qchids = (is_array($qchids)? $qchids: array());
		
		$pbreaks = array();
		if ($quiz->c_pagination == 2 &&  (!$quiz->c_random && !$quiz->c_pool) ) {
			$query = "SELECT `c_question_id` FROM `#__quiz_t_pbreaks` WHERE `c_quiz_id` = '{$i_quiz_id}'";
			$database->SetQuery($query);
			$pbreaks = $database->loadColumn();
		}
		$pbreaks = (is_array($pbreaks)? $pbreaks: array());
		
		if ($quiz->c_pagination == 0)
			$pbreaks = $qchids;

		$query = "SELECT c_id, ordering FROM `#__quiz_t_question` WHERE  c_id IN ('".implode("','", $qchids)."') AND published = 1 ORDER by ordering, c_id";
		$database->SetQuery($query);
		$tmp = $database->loadObjectList();
		$all_quiz_quests = array();
		foreach($qchids as $qchid) {
			foreach($tmp as $t) {		
				if ($t->c_id == $qchid) {
					$all_quiz_quests[] = $t; 
				}
			}
		}
		
		$last_ordering = -1;
		$last_pbreak_id = 0;	
		foreach($all_quiz_quests as $ii=>$all_quiz_quest) {
			
			if ($all_quiz_quest->c_id != $q_data->c_id && in_array($all_quiz_quest->c_id, $pbreaks)) {
				$last_pbreak_id = $all_quiz_quest->c_id;
				$last_ordering = (isset($all_quiz_quests[$ii+1]->ordering)?$all_quiz_quests[$ii+1]->ordering: -1);
			}	
			
			if ($all_quiz_quest->c_id == $q_data->c_id)
				break;
		}
			
		if ($last_ordering == -1 && $quiz->c_pagination == 0) {
			foreach($all_quiz_quests as $all_quiz_quest) {
				if ($all_quiz_quest->c_id == $q_data->c_id)
					$last_ordering = $all_quiz_quest->ordering;
			}
			
		}
		if ($last_pbreak_id) {
			$found = 0;
			foreach($qchids as $qchid) {
				if ($qchid == $last_pbreak_id) {
					$found = 1;
					continue;
				}
				if (!$found)
					continue;
				$q_ids[] = $qchid;
			}
		} else {
			$q_ids = $qchids;
		}
		
		$query = "SELECT * FROM `#__quiz_t_question` WHERE  `ordering` >= '{$last_ordering}' AND published = 1 AND c_id <> '{$last_pbreak_id}' AND c_id IN ('".implode("','", $q_ids)."') ORDER by ordering, c_id";
		$database->SetQuery($query);
		$tmp = $database->loadObjectList();
		$all_quests = array();
		foreach($qchids as $qchid) {
			foreach($tmp as $t) {		
				if ($t->c_id == $qchid) {
					$all_quests[] = $t; 
				}
			}
		}
		
		foreach($all_quests as $q_data) {
			$quest_count++;
			$ret_add_script = '';
			$ret_str .= "\t" . '<question_data>';
			
			if ($cur_template) {
				$z = 1;
				if ($stu_quiz_id && is_array($qchids) && !empty($qchids)) {

					foreach($qchids as $qchid) {
						if ($qchid == $q_data->c_id) {
							$ret_str .= "\t" . '<quiz_quest_num>'.$z.'</quiz_quest_num>' . "\n";					
							$z = -1;
							break;
						}
						$z++;
					}
					
				}
				if ($z != -1 ) {
					$ret_str .= "\t" . '<quiz_quest_num>X</quiz_quest_num>' . "\n";
				}
				JoomlaquizHelper::JQ_GetJoomFish($q_data->c_question, 'quiz_t_question', 'c_question', $q_data->c_id);
				ob_start();
				$q_data->c_question = JoomlaquizHelper::JQ_ShowText_WithFeatures($q_data->c_question, true);
				ob_end_clean();
				if($q_data->c_type == 6){
					$ret_str .= '<quest_data><![CDATA[<div>'.JoomlaquizHelper::Blnk_replace_quest_review($q_data->c_id, $q_data->c_question).'</div>]]></quest_data>';
				}else{
					$ret_str .= "\t" . '<quest_data><![CDATA[<div>'.$q_data->c_question.'</div>]]></quest_data>' . "\n";
				}
				$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id = '".$q_data->c_id."' AND c_right = 1";
				$database->SetQuery( $query );
				$q_data->c_point = $q_data->c_point.' - '.(floatval($database->LoadResult()) + $q_data->c_point);
				
				$ret_str .= "\t" . '<quest_type>'.$q_data->c_type.'</quest_type>' . "\n";
				$ret_str .= "\t" . '<quest_id>'.$q_data->c_id.'</quest_id>' . "\n";
				$ret_str .= "\t" . '<quest_score><![CDATA['.$q_data->c_point.']]></quest_score>' . "\n";
				$ret_str .= "\t" . '<quest_separator><![CDATA['.$q_data->c_separator.']]></quest_separator>' . "\n";
				$ret_str .= "\t" . '<quest_im_check>0</quest_im_check>' . "\n";

				$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$stu_quiz_id."' AND c_question_id = '".$q_data->c_id."'";
				$database->SetQuery( $query );
				$sid = $database->loadResult( );
				$type = JoomlaquizHelper::getQuestionType($q_data->c_type);
				
				$data = array();
				$data['quest_type'] = $data['type'] = $type;
				$data['q_data'] = $q_data;
				$data['cur_template'] = $cur_template;
				$data['stu_quiz_id'] = $stu_quiz_id;
				$data['sid'] = $sid;
				$data['ret_add'] = '';
				$data['ret_str'] = '';
				$data['ret_add_script'] = $ret_add_script;
				
				$appsLib->triggerEvent( 'onReviewQuestion' , $data );
				
				$ret_str .= $data['ret_str'];
				$ret_add = $data['ret_add'];
				$ret_add_script .= $data['ret_add_script'];
			}
			
			if ($ret_add_script) {
					$ret_str .= "\t" . '<exec_quiz_script>1</exec_quiz_script>' . "\n";
					$ret_str .= "\t" . '<quiz_script_data><![CDATA['.$ret_add_script.']]></quiz_script_data>' . "\n";
			} else {
					$ret_str .= "\t" . '<exec_quiz_script>0</exec_quiz_script>' . "\n";
					$ret_str .= "\t" . '<quiz_script_data><![CDATA[ ]]></quiz_script_data>' . "\n";
			}
			$ret_str .= "\t" . '<quest_task><![CDATA[review]]></quest_task>' . "\n";
			$ret_str .= '</question_data>' . "\n";
				
				
			if ($quiz->c_pagination == 0) { 
				break;		
			}
			
			if (in_array($q_data->c_id, $pbreaks)) {
				break;
			}
		}
		
		$is_last = 0;
		if (is_array($all_quests) && !empty($all_quests) && is_array($qchids) && !empty($qchids)) {
			if ($qchids[count($qchids)-1] == $q_data->c_id) {	
				$is_last = 1;
			}
		}
		$ret_str .= "\t" . '<is_last>'.$is_last.'</is_last>' . "\n";
		
		$ret_str .= "\t" . '<quest_count>'.(int)$quest_count.'</quest_count>' . "\n";
		$ret_str .= "\t" . '<is_prev>0</is_prev>' . "\n";
		
		return $ret_str;
	}

	private function checkFirstQuestion($q_data) {

		$jinput = JFactory::getApplication()->input;
		$qs = $jinput->get('qs', '0', 'integer');

		foreach ($q_data as $key => $question) {
			if ($question->c_id == $qs && $qs) {
				$temp_question = $question;
				unset ($q_data[$key]);
				array_unshift($q_data, $temp_question);
			}
		}

		return $q_data;
	}
}
?>