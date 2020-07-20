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
 * Results Model.
 *
 */
class JoomlaquizModelResults extends JModelList
{	
	public function getResults()
    {
        $user = JFactory::getUser();
        // Only for logged users
        if(!$user->id) {
            return;
        }

        $app = JFactory::getApplication();
        $limitstart	= $app->input->getInt('limitstart', 0);
        $limit = $app->getUserStateFromRequest('com_joomlaquiz.limit', 'limit', 20, 'INT');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // loading quizzes ids
        $query->select($db->qn('c_id'))
            ->from($db->qn('#__quiz_t_quiz'));
        $db->setQuery($query);
        $quiz_ids = $db->loadColumn();

        // checking access to results
        if($quiz_ids) {
            foreach ($quiz_ids as $key => $quiz) {
                if (!$user->authorise('core.result', 'com_joomlaquiz.quiz.' . $quiz)) {
                    unset($quiz_ids[$key]);
                }
            }
            $quiz_ids = array_values($quiz_ids);
        }
        if(!$quiz_ids){
            return;
        }

        foreach ($quiz_ids as $quiz_id){
            $quiz_id = $db->q($quiz_id);
        }

        $query->clear();
        $query->select($db->qn('sq.c_quiz_id'))
            ->select($db->qn('sq.c_id'))
            ->select($db->qn('q.c_grading'))
            ->select($db->qn('q.c_pool'))
            ->from($db->qn('#__quiz_r_student_quiz','sq'))
            ->join('left',$db->qn('#__quiz_r_student_question','squ').' ON '.$db->qn('sq.c_id').' = '.$db->qn('squ.c_stu_quiz_id'))
            ->join('left',$db->qn('#__quiz_t_quiz','q').' ON '.$db->qn('sq.c_quiz_id').' = '.$db->qn('q.c_id'))
            ->where($db->qn('q.c_id').' IN ('.implode(",", $quiz_ids).')')
            ->group($db->qn('squ.c_stu_quiz_id'))
            ->order($db->qn('sq.c_date_time').' DESC')
            ->setLimit($limit, $limitstart);

        if(!$user->authorise('core.managefe','com_joomlaquiz')){
            $query->where($db->qn('sq.c_student_id').' = '.$db->q($user->id));
        }

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $total_rows = 0;
        if($rows && !empty($rows)){
            $query->clear();
            $query->select('1')
                ->from($db->qn('#__quiz_r_student_quiz','sq'))
                ->join('left',$db->qn('#__quiz_r_student_question','squ').' ON '.$db->qn('sq.c_id').' = '.$db->qn('squ.c_stu_quiz_id'))
                ->join('left',$db->qn('#__quiz_t_quiz','q').' ON '.$db->qn('sq.c_quiz_id').' = '.$db->qn('q.c_id'))
                ->where($db->qn('q.c_id').' IN ('.implode(",", $quiz_ids).')')
                ->group($db->qn('squ.c_stu_quiz_id'));
            if(!$user->authorise('core.managefe','com_joomlaquiz')){
                $query->where($db->qn('sq.c_student_id').' = '.$db->q($user->id));
            }
            $db->setQuery($query);
            $db->execute();
            $total_rows = $db->getNumRows();
        }

        $quizzes = array();
        foreach($rows as $row){

            $query->clear();
            $query->select($db->qn('sq.unique_id'))
                ->select($db->qn('sq.c_id','id'))
                ->select('SUM('.$db->qn('squ.c_score').') AS user_score')
                ->select($db->qn('sq.c_passed'))
                ->select($db->qn('sq.c_total_time'))
                ->select($db->qn('sq.c_date_time'))
                ->select($db->qn('sq.c_passing_score','sq_c_passing_score'))
                ->select($db->qn('sq.c_quiz_id'))
                ->select($db->qn('sq.c_total_score'))
                ->select($db->qn('sq.user_name'))
                ->select($db->qn('sq.user_surname'))
                ->select($db->qn('q.c_id'))
                ->select($db->qn('q.c_title'))
                ->select($db->qn('q.c_author'))
                ->select($db->qn('q.c_passing_score'))
                ->select($db->qn('sq.c_student_id'))
                ->select($db->qn('u.username'))
                ->select($db->qn('u.name'))
                ->select($db->qn('u.email'))
                ->select($db->qn(array('sq.c_max_score'),array('c_full_score')))
                ->select($db->qn('q.c_pool'))
                ->select($db->qn('ch.q_chain'))
                ->select($db->qn('q.c_grading'))
                ->select($db->qn('q.c_certificate'))
                ->from($db->qn('#__quiz_r_student_quiz','sq'))
                ->join('left',$db->qn('#__quiz_r_student_question','squ').' ON '.$db->qn('sq.c_id').' = '.$db->qn('squ.c_stu_quiz_id'))
                ->join('left', $db->qn('#__users','u').' ON '.$db->qn('sq.c_student_id').' = '.$db->qn('u.id'))
                ->join('left', $db->qn('#__quiz_q_chain','ch').' ON '.$db->qn('ch.s_unique_id').' = '.$db->qn('sq.unique_id'))
                ->join('left', $db->qn('#__quiz_t_quiz','q').' ON '.$db->qn('sq.c_quiz_id').' = '.$db->qn('q.c_id'))
                ->where($db->qn('sq.c_id').' = '.$db->q((int)$row->c_id));

            if($row->c_grading){
                switch($row->c_grading){
                    case 1:
                        // grade by first attempt
                        $query->select($db->qn('sq.c_id','id'))
                            ->select($db->qn('sq.c_total_score'))
                            ->order($db->qn('sq.c_id').' ASC');
                        break;
                    case 2:
                        // grade by last attempt
                        $query->select($db->qn('sq.c_id','id'))
                            ->select($db->qn('sq.c_total_score'))
                            ->order($db->qn('sq.c_id').' DESC');
                        break;
                    case 3:
                        // grade by highest score
                        $query->select($db->qn('sq.c_id','id'))
                            ->select($db->qn('sq.c_total_score'))
                            ->order($db->qn('sq.c_total_score').' DESC');
                        break;
                    case 4:
                        // grade by average score
                        $query->select('MAX('.$db->qn('sq.c_id').') AS '.$db->qn('id' /* was `c_id` */))
                            ->select('AVG('.$db->qn('sq.c_total_score').') AS '.$db->qn('c_total_score'))
                            ->order($db->qn('sq.c_date_time').' DESC')
                            ->group($db->qn('sq.c_quiz_id'));
                        break;
                }
            }

            $db->setQuery( $query );
            $data = $db->loadObject();

            // check manual grading
            $query->clear();
            $query->select($db->q('1'))
                ->from($db->qn('#__quiz_t_question','q'))
                ->from($db->qn('#__quiz_r_student_question','sq'))
                ->where($db->qn('q.published').' = '.$db->q('1'))
                ->where($db->qn('q.c_manual').' = '.$db->q('1'))
                ->where($db->qn('sq.reviewed').' = '.$db->q('0'))
                ->where($db->qn('q.c_id').' = '.$db->qn('sq.c_question_id'))
                ->where($db->qn('sq.c_stu_quiz_id').' = '.(int)$db->q($row->c_id));
            $db->setQuery( $query );
            $not_graded = $db->loadResult();

            if ($not_graded){
                $data->c_passed = -1;
                $data->c_total_score = JText::_('COM_JQ_SCORE_PENDING');
            }

            $quizzes[] = $data;
        }

        for($i=0, $n=count($quizzes); $i<$n; $i++) {
            if ($quizzes[$i]->c_pool) {
                $qids = str_replace('*', ",", $quizzes[$i]->q_chain);
                $total_score = JoomlaquizHelper::getTotalScore($qids, $quizzes[$i]->c_id);
                $quizzes[$i]->c_full_score = $total_score;
            }
        }

        jimport('joomla.html.pagination');
        $pagination = new JPagination($total_rows, $limitstart, $limit);

        return array($quizzes, $pagination);
	}
	
	public function getQuizParams(){
		
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$app = JFactory::getApplication();
		$jinput = $app->input;
	
		$stu_id = $jinput->get('id', 0, 'INT');
		
		$share_id = $jinput->get('share_id', 0, 'INT');

		/* check share token */
		$is_share = false;
		if($share_id != ''){
			$query->clear();
			$query->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__quiz_r_student_share'))
				->where($db->qn('id').' = '.$db->q($share_id));
			$db->setQuery($query);
			$is_share = $db->loadResult();
		}

        if($user->authorise('core.managefe','com_joomlaquiz')){
			$is_share = 1;
		}
		
		if ((!$user->id || !$stu_id) && !$is_share){
			$app->redirect(JRoute::_('index.php?option=com_joomlaquiz&view=results'));
			die;
		}
		
		
		/* getting result */
		$query->clear();
		$query->select('*')
			->from($db->qn('#__quiz_r_student_quiz'))
			->where($db->qn('c_id').' = '.$db->q($stu_id));
			
		if(!$is_share){
			/* if not is_share add user_id check */
			$query->where($db->qn('c_student_id').' = '.$db->q($user->id));
		}
		
		$db->setQuery($query);
		$result_data = $db->loadObject();

		/* getting quiz by result */
		$quiz_id = $result_data->c_quiz_id;
		
		$query->clear();
		$query->select($db->qn('a').'.*')
			->select($db->qn('b.template_name'))
			->from($db->qn('#__quiz_t_quiz','a'))
			->from($db->qn('#__quiz_templates','b'))
			->where($db->qn('a.c_id').' = '.$db->q($quiz_id))
			->where($db->qn('a.c_skin').' = '.$db->qn('b.id'));
		$db->setQuery($query);
		$quiz_params = $db->loadObject();
		
		/* if quiz - clear up errors */
		if(!empty($quiz_params)){
			$quiz_params->error = 0;
			$quiz_params->message = '';
		}
		
		/* check if published - need to ignore if share, or ignore at all at this view, it`s result view, not quiz */
		if (!isset($quiz_params) || $quiz_params->published != 1) {
			$quiz_params = new stdClass;
			$quiz_params->error = 1;
			$quiz_params->message = '<p align="left">'.JText::_('COM_RESULTS_FOR_REGISTERED').'</p>';
			return $quiz_params;
		}

		if(1 /* is this part needed at all??? in results we don`t need to check if it`s misconfigured! */ ){
			$doing_quiz = 1;
			$doing_pool = 1;
			
			$query = "SELECT c_pool FROM #__quiz_t_quiz WHERE c_id = '".$quiz_id."'";
			$db->SetQuery($query);
			if(!$db->loadResult()) {
				$doing_pool = 0;
			} else{
				$query = "SELECT q_count FROM #__quiz_pool WHERE q_id = '".$quiz_id."'";
				$db->SetQuery($query);
				if(!$db->loadResult())  {
					$doing_pool = 0;
				} else {
					$query = "SELECT COUNT(*) FROM #__quiz_t_question WHERE c_quiz_id = '0' AND published = 1";
					$db->SetQuery($query);
					if(!$db->loadResult()) {
						$doing_pool = 0;
					}
				}
			}

			$query = "SELECT COUNT(*) FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND published = 1";
			$db->SetQuery($query);
			if (!$db->LoadResult() && !$doing_pool) {
				$doing_quiz = -1;
			}
		}
		
		if ($doing_quiz == 1) {
            $session = JFactory::getSession();
            $session->set('quiz_lid', 0);
            $session->set('quiz_rel_id', 0);
            $session->set('quiz_package_id', 0);
			
			$query = "SELECT count(*) FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND c_type = 4 AND published = 1";
			$db->SetQuery( $query );
			$quiz_params->if_dragdrop_exist = $db->loadResult();
			$quiz_params->c_description = JoomlaquizHelper::JQ_ShowText_WithFeatures($quiz_params->c_description);
			$quiz_params->is_attempts = 0;
			$quiz_params->rel_id = 0;
			$quiz_params->package_id = 0;
			$quiz_params->lid = 0;	
			$quiz_params->force = 0;
			$quiz_params->result_data = $result_data;
			
			$quiz_params->share_id = ($share_id != '') ? $share_id : 0;
            JFactory::getSession()->set('share_id', $quiz_params->share_id);

			return $quiz_params;
			
		} elseif ($doing_quiz == -1) {
			$quiz_params->error = 1;
			$quiz_params->message = '<p align="left">'.JText::_('COM_QUIZ_NOT_AVAILABLE').'</p><br />';
			return $quiz_params;
		}
	}
}