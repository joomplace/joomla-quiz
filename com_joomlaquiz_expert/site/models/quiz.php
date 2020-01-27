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
 * Quiz Model.
 *
 */
class JoomlaquizModelQuiz extends JModelList
{	
	public function getQuizParams($quiz_id = 0){
		
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$app = JFactory::getApplication('site');
		$jinput = $app->input;
		$query = $db->getQuery(true);

		/* get menu item params */
		// check because fails on article save because of content plugin
		if(!$app->isAdmin())
			$params = $app->getParams();
		else
			$params = new JRegistry();
		
		$error_info = '';
		if (!isset($quiz_id) || !$quiz_id) {
			$quiz_id = $jinput->get( 'quiz_id', $params->get('quiz_id', 0, 'INT'), 'INT');
		}
		
		$article_id 	= $jinput->get( 'article_id', 0, 'INT');
		
		/* not used anywhere in code */
		// $lpath_id 		= $jinput->get( 'lpath_id', $params->get('lpath_id', 0, 'INT'), 'INT');
		/* learning path ??? */
		$lid 			= $jinput->get( 'lid', 0, 'INT');
		
		/* packages needs? */
		$rel_id 		= $jinput->get( 'rel_id', 0, 'INT');
		$package_id 	= $jinput->get( 'package_id', 0, 'INT');
		$vm 			= $package_id < 1000000000;

		if ($package_id && $rel_id && !$vm) {
			$payment_query = "SELECT user_id, id, pid"
				. "\n FROM `#__quiz_payments`"
				. "\n WHERE id = '" . ($package_id-1000000000) . "' AND status IN ('Confirmed')"
			;

			$db->setQuery($payment_query);
			$payment = $db->loadObject();

			if (!$payment || $user->id != $payment->user_id) {
				$payment_id_query = "SELECT p.id"
				. "\n FROM `#__quiz_payments` AS p"
				. "\n INNER JOIN `#__quiz_products` AS qp ON qp.pid = p.pid"
				. "\n WHERE p.user_id = {$user->id} AND qp.id = '{$rel_id}' AND p.status IN ('Confirmed') "
				;

				$db->setQuery($payment_id_query);
				$payment_id = $db->loadResult();

                $offset = $vm ? 0 : 1000000000;
				if ($package_id && (int)$payment_id) {
					$package_id = (int)$payment_id + $offset;
				}
			}
		}

		$quiz_params = new stdClass;
		
		/* видимо предпроверка на то куплен ли пакет */
		if( ( $rel_id && !$user->id ) || ( !$package_id && $rel_id ) || ( $package_id && !$rel_id )	){
			$quiz_params->error = 1;
			$quiz_params->message = '<p align="left">'.JText::_('COM_QUIZ_NOT_AVAILABLE').'</p>';
			return $quiz_params;
		}
		
		/* понять что это значит */
		if ($rel_id && !$quiz_id && !$article_id) {
			$quiz_params = JoomlaquizHelper::JQ_checkPackage($package_id, $rel_id, $vm);
			return $quiz_params;
		}
		
		/* возможно соединить с предыдущей функцией? */
		if ($rel_id && $article_id) 
			$lid = JoomlaquizHelper::JQ_checkPackage($package_id, $rel_id, $vm);
			
		/* вход в learning path */
		if($article_id && $lid){
		
			
			$query->clear();
			$query->select($db->qn('order'))
				->from($db->qn('#__quiz_lpath_quiz'))
				->where($db->qn('lid').' = '.$db->q($lid))
				->where($db->qn('type').' = '.$db->q('a'))
				->where($db->qn('qid').' = '.$db->q($article_id));
		
			$sub_query = (string)$query;

			$query->clear();
			$query->select($db->qn( array('a.type','a.qid')))
				->from($db->qn('#__quiz_lpath_quiz', 'a'))
				->where($db->qn('a.lid').' = '.$db->q($lid).' AND '.$db->qn('a.order').' > ('.$sub_query.')')
				->order('a.order ASC');

			$db->setQuery($query,0,1);
			/* loading next step */
			$next = $db->loadObject();
			
			$learning_path_stage = (object)array(
					'oid' => $package_id,
					'uid' => $user->id,
					'rel_id' => $rel_id,
					'lpid' => $lid,
					'type' => 'a',
					'qid' => $article_id,
					'stage' => '1',
				);
			
			/* updating stage: why 1? */
			if(!$db->insertObject('#__quiz_lpath_stage', $learning_path_stage)){
				$db->updateObject('#__quiz_lpath_stage', $learning_path_stage, array('oid','uid','rel_id','lpid','type','qid'));
			}
			
			/* getting article data*/
			$article_data = $this->JQ_GetArticle($article_id, $package_id, $rel_id, $lid, $next);
			
			ob_clean();
			ob_start();
			include_once(JPATH_SITE.'/components/com_joomlaquiz/views/quiz/tmpl/article.php');
			$content = ob_get_contents();
			ob_clean();
			
			$quiz_params->error = 1;
			$quiz_params->message = $content;
			
			return $quiz_params;
		}
		
		$query->clear();
		$query->select($db->qn('a').'.*')
			->select($db->qn('b.template_name'))
			->from($db->qn('#__quiz_t_quiz','a'))
			->from($db->qn('#__quiz_templates','b'))
			->where($db->qn('a.c_id').' = '.$db->q($quiz_id))
			->where($db->qn('a.c_skin').' = '.$db->qn('b.id'));
		$db->setQuery($query);
		$quiz_params = $db->loadObject();
		
		if($quiz_params){
			$quiz_params->error = 0;
			$quiz_params->message = '';
		}
				
		/* if not published - clear data and set message */
		if ($quiz_params->published != 1) {
			$quiz_params = new stdClass;
			$quiz_params->error = 1;
			$quiz_params->message = '<p align="left">'.JText::_('COM_QUIZ_NOT_AVAILABLE').'</p>';
			return $quiz_params;
		}

		/* if one time and already passed - clear data and set message */
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('1')
			->from('#__quiz_r_student_quiz')
			->where($db->qn('c_passed').' = '.$db->q('1'))
			->where($db->qn('c_quiz_id').' = '.$db->q($quiz_params->c_id))
			->where($db->qn('c_student_id').' = '.$db->q($user->id));
		if ($quiz_params->one_time == 1 && $db->setQuery($query)->loadResult()) {
			$quiz_params = new stdClass;
			$quiz_params->error = 1;
			$quiz_params->message = '<p align="left">'.JText::_('COM_QUIZ_ALREADY_PASSED').'</p>';
			return $quiz_params;
		}

		/* if not bought - clear data and set message */
		if ($quiz_params && $quiz_params->paid_check == 1 && !$rel_id) {
			$quiz_params->error = 1;
			$quiz_params->message = '<p align="left">'.JText::_('COM_NOT_SUBSCRIBED').'</p>';
			return $quiz_params;
		}
		
		/* assign package purchase data(but there can be no data) */
		$quiz_params->rel_id = $rel_id;
		$quiz_params->package_id = $package_id;
		$quiz_params->lid = $lid;
				
		if($rel_id) {
			/* if bought package */
			$query->clear();
			$query->select('*')
				->from($db->qn('#__quiz_products'))
				->where($db->qn('id').' = '.$db->q($rel_id));
			$db->setQuery($query);
			$prod_data = $db->loadObjectList();
			
			/* if no such product - clear data and set message */
			if(empty($prod_data)) {
				$quiz_params->error = 1;
				$quiz_params->message = '<p align="left">'.JText::_('COM_QUIZ_LPATH_NOT_AVAILABLE').'</p>';
				return $quiz_params;
			}
			$quiz_params->product_data = $prod_data[0];
			
			$product_stat = array();
			$query->clear();
			$query->select('*')
				->from($db->qn('#__quiz_products_stat'))
				->where($db->qn('uid').' = '.$db->q($user->id))
				->where($db->qn('oid').' = '.$db->q($package_id))
				->where($db->qn('qp_id').' = '.$db->q($rel_id));
			$db->setQuery($query);
			$products_stat = $db->loadObjectList('qp_id');
			
			/* checks for X-days access and period access  */
			if($quiz_params->product_data->xdays) {
				/* X-days access */
				if(!empty($products_stat) && array_key_exists($rel_id, $products_stat)) {
					$confirm_date = strtotime($products_stat[$rel_id]->xdays_start);
				} else {
					if ($vm) {
						$query->clear();
						$query->select('UNIX_TIMESTAMP('.$db->qn('order_history.created_on').')')
							->from($db->qn('#__virtuemart_order_histories','order_history'))
							->join('inner',$db->qn('#__virtuemart_order_items', 'order_item').' ON '.$db->qn('order_item.virtuemart_order_id').' = '.$db->qn('order_history.virtuemart_order_id'))
							->where($db->qn('order_history.order_status_code').' = '.$db->q('C'))
							->where($db->qn('order_item.virtuemart_order_id').' = '.$db->q($package_id))
							->where($db->qn('order_item.virtuemart_product_id').' = '.$db->q($quiz_params->product_data->pid))
							->order($db->qn('order_history.created_on').' DESC ');
					} else {
						$query->clear();
						$query->select('UNIX_TIMESTAMP('.$db->qn('p.confirmed_time').')')
							->from($db->qn('#__quiz_payments','p'))
							->where($db->qn('p.status').' = '.$db->q('Confirmed'))
							->where($db->qn('p.id').' = '.$db->q($package_id-1000000000))
							->where($db->qn('p.pid').' = '.$db->q($quiz_params->product_data->pid))
							->order($db->qn('p.confirmed_time').' DESC ');
					}
					$db->setQuery($query,0,1);
					$confirm_date = $db->loadResult();
				}
				
				/* check if expired */
				$ts_day_end = $confirm_date + $quiz_params->product_data->xdays*24*60*60;
				if($confirm_date && strtotime(JHtml::_('date')) > $ts_day_end) {
					$quiz_params->error = 1;
					$quiz_params->message = '<p align="left">'.JText::_('COM_ACCESS_EXPIRED').'</p>';
					return $quiz_params;
				}

			} else if (
						($quiz_params->product_data->period_start && $quiz_params->product_data->period_start != '0000-00-00')
						|| 
						($quiz_params->product_data->period_end && $quiz_params->product_data->period_end != '0000-00-00')
					){
				/* period access */
				
				if(!empty($products_stat) && array_key_exists($rel_id, $products_stat)) {
					$quiz_params->product_data->period_start = $products_stat[$rel_id]->period_start;
					$quiz_params->product_data->period_end = $products_stat[$rel_id]->period_end;
				}	
				
				$ts_start = null;
				if($quiz_params->product_data->period_start && $quiz_params->product_data->period_start != '0000-00-00') {
					$ts_start = strtotime($quiz_params->product_data->period_start . ' 00:00:00');
				}

				$ts_end = null;
				if($quiz_params->product_data->period_end && $quiz_params->product_data->period_end != '0000-00-00') {
					$ts_end = strtotime($quiz_params->product_data->period_end . ' 23:59:59');
				}

				$ts = strtotime(JHtml::_('date'));
				if(($ts_start && $ts_start > $ts) || ($ts_end && $ts_end < $ts)) {
					$quiz_params->error = 1;
					$quiz_params->message = '<p align="left">'.JText::_('COM_ACCESS_EXPIRED').'</p>';
					return $quiz_params;
				}
			}

			/* Check attempts */
			$wait_time = '';
			$is_attempts = JoomlaquizHelper::isQuizAttepmts($quiz_id, 0, $rel_id, $package_id, $wait_time);

			/* if no attempts */
			if (!$is_attempts) {
				$quiz_params->error = 1;
				$quiz_params->message = '<p align="left">'.JText::_('COM_ACCESS_NO_ATTEMPTS').'</p>';
				return $quiz_params;
			}
			
			$query->clear();
			$query->select('*')
				->from($db->qn('#__quiz_r_student_quiz','stud_quiz'))
				->where($db->qn('stud_quiz.c_order_id').' = '.$db->q($package_id))
				->where($db->qn('stud_quiz.c_rel_id').' = '.$db->q($rel_id))
				->where($db->qn('stud_quiz.c_quiz_id').' = '.$db->q($quiz_id))
				->where($db->qn('stud_quiz.c_student_id').' = '.$db->q($user->id))
				->order($db->qn('stud_quiz.c_passed').' DESC ')
				->order($db->qn('stud_quiz.c_date_time').' DESC ');
			$db->setQuery($query,0,1);
			$quiz_params->rel_data = $db->loadObject();
			
			$doing_quiz = 1;

		} elseif ($lid) {
			$query->clear();
			$query->select('*')
				->from($db->qn('#__quiz_r_student_quiz','stud_quiz'))
				->where($db->qn('stud_quiz.c_lid').' = '.$db->q($lid))
				->where($db->qn('stud_quiz.c_quiz_id').' = '.$db->q($quiz_id))
				->where($db->qn('stud_quiz.c_student_id').' = '.$db->q($user->id))
				->order($db->qn('stud_quiz.c_passed').' DESC ')
				->order($db->qn('stud_quiz.c_date_time').' DESC ');
			$db->setQuery($query,0,1);
			$quiz_params->lid_data = $db->loadObject();
			
			$tmp = '';
			$is_attempts = JoomlaquizHelper::isQuizAttepmts($quiz_id, $lid, 0, 0, $tmp);
			
			$wait_time = '';
			$is_attempts = JoomlaquizHelper::isQuizAttepmts($quiz_id, 0, 0, 0, $wait_time);
			if (!$is_attempts) {
				if ($wait_time){
					/* might be replaced with spritf */
					$message = str_replace("{text}", ($wait_time>60? floor($wait_time/60).' '.JText::_('COM_QUIZ_MINUTES'): $wait_time. ' seconds'), JText::_('COM_QUIZ_COMEBACK_LATER'));
				}else {
					$message = JText::_('COM_QUIZ_ALREADY_TAKEN');
				}
				
				$quiz_params->error = 1;
				$quiz_params->message = $message;
				return $quiz_params;
			}	

			/*
			if(JComponentHelper::getParams('com_joomlaquiz')->get('restrict_on_passed',0)){
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select($db->qn('c_id'))
					->from($db->qn('#__quiz_r_student_quiz'))
					->where($db->qn('c_quiz_id').' = '.$db->q($quiz_params->c_id))
					->where($db->qn('c_student_id').' = '.$db->q($user->id))
					->where($db->qn('c_passed').' = '.$db->q(1));
				$passed = $db->setQuery($query,0,1)->loadResult();
				
				if ($passed) {
					$message = JText::_('COM_QUIZ_ALREADY_PASSED');
					$quiz_params->error = 1;
					$quiz_params->message = $message;
					return $quiz_params;
				}
			}
			*/

			$doing_quiz = 1;
		} else {
			$doing_quiz = 1;
			
			$wait_time = '';
			$is_attempts = JoomlaquizHelper::isQuizAttepmts($quiz_id, 0, 0, 0, $wait_time);
			if (!$is_attempts) {
				if ($wait_time){
					/* might be replaced with spritf */
					$message = str_replace("{text}", ($wait_time>60? floor($wait_time/60).' '.JText::_('COM_QUIZ_MINUTES'): $wait_time. JText::_('COM_QUIZ_SECONDS')), JText::_('COM_QUIZ_COMEBACK_LATER'));
				}else {
					$message = JText::_('COM_QUIZ_ALREADY_TAKEN');
				}
				$quiz_params->error = 1;
				$quiz_params->message = $message;
				return $quiz_params;
			}

			/*
			if(JComponentHelper::getParams('com_joomlaquiz')->get('restrict_on_passed',0)){
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select($db->qn('c_id'))
					->from($db->qn('#__quiz_r_student_quiz'))
					->where($db->qn('c_quiz_id').' = '.$db->q($quiz_params->c_id))
					->where($db->qn('c_student_id').' = '.$db->q($user->id))
					->where($db->qn('c_passed').' = '.$db->q(1));
				$passed = $db->setQuery($query,0,1)->loadResult();
				
				if ($passed) {
					$message = JText::_('COM_QUIZ_ALREADY_PASSED');
					$quiz_params->error = 1;
					$quiz_params->message = $message;
					return $quiz_params;
				}
			}
			*/

            /* check if has access */
            $category = JTable::getInstance('Category');
            $my_acl   = $user->getAuthorisedViewLevels();
            $category->load($quiz_params->c_category_id);
            if ((!JFactory::getUser()->authorise('core.view',
                        'com_joomlaquiz.quiz.' . $quiz_params->c_id)
                    || !in_array($category->access, $my_acl))
                /* c_guest must be excluded (legacy purpose @deprecated 3.8) */
                && (!JFactory::getUser()->id)
            ) {
                $quiz_params->error   = 1;
                $quiz_params->message = '<p align="left">'
                    . JText::_('COM_QUIZ_REG_ONLY') . '</p>';

                return $quiz_params;
            }
		}
		
		if ($doing_quiz ==  1) {
			/* checking quiz pool settings */
			$doing_pool = 1;
			$query->clear();
			$query->select($db->qn('c_pool'))
				->from('#__quiz_t_quiz')
				->where($db->qn('c_id').' = '.$db->q($quiz_id));
			$db->setQuery($query);
			$c_pool = $db->loadResult();
			if($c_pool) {
				$query->clear();
				$query->select($db->qn('q_count'))
					->from('#__quiz_pool')
					->where($db->qn('q_id').' = '.$db->q($quiz_id));
				$db->setQuery($query);
				if(!$db->loadResult()){
					/* no pool questions assigned to quiz */
					$error_info = JText::_('COM_JOOMLAQUIZ_NO_COUNT_QUESTIONS');
					$doing_pool = 0;
				} else {
					$query->clear();
					$query->select('COUNT(*)')
						->from('#__quiz_t_question')
						->where($db->qn('c_quiz_id').' = '.$db->q('0'))
						->where($db->qn('published').' = '.$db->q('1'));
					$db->setQuery($query);
					if(!$db->loadResult()) {
						if($c_pool == 1) 
							$error_info = JText::_('COM_JOOMLAQUIZ_NOQUESTIONS_IN_POOL');
						$doing_pool = 0;
					}
				}
			} else{
				$doing_pool = 0;
			}
		
			/* checking questions count */
			$query->clear();
			$query->select('COUNT(*)')
				->from('#__quiz_t_question')
				->where($db->qn('c_quiz_id').' = '.$db->q($quiz_id))
				->where($db->qn('published').' = '.$db->q('1'));
			$db->setQuery($query);
			$c_quests = $db->loadResult();
			
			if(!$c_quests && !$c_pool) $error_info = JText::_('COM_JOOMLAQUIZ_NOQUESTIONS_IN_QUIZ');
			if (!$c_quests && !$doing_pool) {
				$doing_quiz = -1;
			}
		}	
		
		if ($doing_quiz == 1) {
		
			//Replace user name and email fields
			if(!$user->id){
				$username_field = '<label for="jq_user_name">'.JText::_('COM_JOOMLAQUIZ_INPUT_USER_NAME').'</label><input style="max-width:100%;" type="text" size="35" name="jq_user_name" id="jq_user_name" class="inputbox jq_inputbox" value=""/>';
				$usersurname_field = '<label for="jq_user_surname">'.JText::_('COM_JOOMLAQUIZ_INPUT_USER_SURNAME').'</label><input style="max-width:100%;" type="text" size="35" name="jq_user_surname" id="jq_user_surname" class="inputbox jq_inputbox" value=""/>';
				$email_field = '<label for="jq_user_email">'.JText::_('COM_JOOMLAQUIZ_INPUT_USER_EMAIL').'</label><input style="max-width:100%;" type="text" size="35" name="jq_user_email" id="jq_user_email" class="jq_inputbox" value=""/>';

			} else {
				$username_field = $usersurname_field = $email_field = '';
			}
			$quiz_params->c_description = preg_replace('/#name#/', $username_field, $quiz_params->c_description, 1);
			$quiz_params->c_description = preg_replace('/#surname#/', $usersurname_field, $quiz_params->c_description, 1);
			$quiz_params->c_description = preg_replace('/#email#/', $email_field, $quiz_params->c_description, 1);
			
			JPluginHelper::importPlugin('content');
			$dispatcher = JEventDispatcher::getInstance();
            $result_event = $dispatcher->trigger('onQuizCustomFieldsRender', array($quiz_params->c_description));
            $processed_desc = '';
            if($result_event && !empty($result_event)){
                $processed_desc = $result_event[0];
            }
            if ($processed_desc) {
                $quiz_params->c_description = $processed_desc;
            }
		
			/* setting up session vars - need to check it it is used anywhere */
            $session = JFactory::getSession();
            $session->set('quiz_lid', $lid);
            $session->set('quiz_rel_id', $rel_id);
            $session->set('quiz_package_id', $package_id);
			
			$query->clear();
			$query->select('COUNT(*)')
				->from('#__quiz_t_question')
				->where($db->qn('c_quiz_id').' = '.$db->q($quiz_id))
				->where($db->qn('c_type').' = '.$db->q('4'))
				->where($db->qn('published').' = '.$db->q('1'));
			$db->setQuery($query);
			$quiz_params->if_dragdrop_exist = $db->loadResult();
			
			$quiz_params->c_description = JoomlaquizHelper::JQ_ShowText_WithFeatures($quiz_params->c_description);

			$quiz_params->is_attempts = $is_attempts;
			$quiz_params->force = $jinput->get('force', 0, 'INT');
			
			/* if no attempts don`t allow force */
			if (!$is_attempts) {
                $quiz_params->force = 0;
            } else {
			    if(isset($quiz_params->product_data) && $quiz_params->product_data->type == 'l'){
                    $quiz_params->force = 1;
                }
            }
			
			return $quiz_params;
			
		}elseif($doing_quiz == -1){
			if(!$error_info){
				$quiz_params->error = 1;
				$quiz_params->message = '<p align="left">'.JText::_('COM_QUIZ_NOT_AVAILABLE').'</p><br />';
				return $quiz_params;
			} else {
				$quiz_params->error = 1;
				$quiz_params->message = '<p align="left">'.JText::_('COM_JOOMLAQUIZ_QUIZ_IS_MISCONFIGURED').'</p><small>'.$error_info.'</small><br/>';
				return $quiz_params;
			}
		}
	}
		
	public function JQ_GetArticle($article_id, $package_id, $rel_id, $lid, $next) {
		
		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
				
		$query = "SELECT title FROM `#__quiz_lpath` WHERE `id` = '{$lid}' AND published = 1";
		$database->SetQuery( $query );
		$lpath_name = $database->loadResult();
			
		$lang = JFactory::getLanguage();
		$lang->load('com_content', JPATH_SITE);
		
		require_once JPATH_BASE . '/components/com_content/models/article.php';
		require_once JPATH_BASE . '/components/com_content/helpers/query.php';
		require_once JPATH_BASE . '/components/com_content/helpers/route.php';
		require_once JPATH_BASE . '/components/com_content/helpers/icon.php';
		JFactory::getApplication()->input->set('id', $article_id);
		
		$model = new ContentModelArticle();

		$user		= JFactory::getUser();
		$document	= JFactory::getDocument();
		$dispatcher	= JDispatcher::getInstance();
		$pathway	= $mainframe->getPathway();
		$params		= $mainframe->getParams('com_content');
		
		// Initialize variable
		$article  = & $model->getItem($article_id);
		
		$aparams = new JRegistry;
		$ap	=& $aparams->loadArray(json_decode($article->attribs));
		$params->merge($ap);

		$article->rel_id = $rel_id;
		$article->package_id = $package_id;
		$article->lid = $lid;
		if(!empty($next)) {
			$article->next = '&' . ($next->type == 'q' ? 'quiz_id' : 'article_id' ) . '=' . $next->qid;
		} else {
			$article->next = null;
		}
		
		if (($article->id == 0)) {
			$id = JFactory::getApplication()->input->get( 'id', '', 'default', 'int' );
            JFactory::getApplication()->enqueueMessage(JText::sprintf( 'Article # not found', $id ), 'error');
            return false;
		}
	
		$access = null;
		$params->def('page_heading', $lpath_name);
		$article->slug			= $article->alias ? ($article->id.':'.$article->alias) : $article->id;
		$article->catslug		= $article->category_alias ? ($article->catid.':'.$article->category_alias) : $article->catid;
		$article->parent_slug	= $article->category_alias ? ($article->parent_id.':'.$article->parent_alias) : $article->parent_id;
		
		$limitstart	= JFactory::getApplication()->input->get('limitstart', 0, '', 'int');
		
		$params->set('show_item_navigation', false);
		
		if ($article->fulltext) {
			$article->text = $article->fulltext;
		}
		else  {
			$article->text = $article->introtext;
		}
		
		if ($article->params->get('show_intro','1') == '1') {
			$article->text = $article->introtext.' '.$article->fulltext;
		}

		$offset = 0;
		
		//
		// Process the content plugins.
		//
		JPluginHelper::importPlugin('content');
		$results = $dispatcher->trigger('onContentPrepare', array ('com_content.article', &$article, &$params, $offset));
		$results = $dispatcher->trigger('onContentAfterTitle', array('com_content.article', &$article, &$params, $offset));
		$article->event = new stdClass;
		$article->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_content.article', &$article, &$params, $offset));
		$article->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.article', &$article, &$params, $offset));
		$article->event->afterDisplayContent = trim(implode("\n", $results));
		
		// Increment the hit counter of the article.
		if (!$params->get('intro_only') && $offset == 0) {
			$model->hit();
		}
					
		$print = JFactory::getApplication()->input->get('print');
		if ($print) {
			$document->setMetaData('robots', 'noindex, nofollow');
		}
		
		$data = new stdClass();
		$data->article = $article;
		$data->params = $params;
		$data->user = $user;
		$data->print = $print;
		$data->access = $access;
	
		return $data;	
	}
}