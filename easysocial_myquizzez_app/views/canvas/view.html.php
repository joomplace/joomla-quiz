<?php

defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class MyQuizzezViewCanvas extends SocialAppsView
{
	private $db;
    //private $params;

	public function display($tpl = null, $docType = null)
	{
			// Attach CSS
			$document	= JFactory::getDocument();
			$css		= JURI::base() . 'media/com_easysocial/apps/user/myquizzes/assets/styles/style.css';
			$document->addStyleSheet($css);
            $this->params = JFactory::getApplication()->input;
			if(JRequest::getVar('task', '', 'REQUEST') == 'app'){
				$app = 1;	
			}else{
				$app = 0;
			}
			
			$userid	= JFactory::getUser()->id;
			
			//$def_limit = $this->params->get('count', 10);
            $this->category = trim($this->params->get('category'), ',');
            $def_limit = $this->params->get('count', 10);

            $limit = JRequest::getVar('limit', $def_limit, 'REQUEST');
			$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');
			
			$model 	= $this->getModel( 'MyQuizzez' );
			$rows = $model->getQuiz($userid, $limitstart, $limit, $this->category);
			
			$quizzes = array();

			$this->db = Foundry::db();
			
			foreach($rows as $i=>$row) {
				$sql = "SELECT q_chain FROM #__quiz_q_chain WHERE s_unique_id = '".$row->unique_id."'";
				$this->db->setQuery($sql);
				$chain_question_ids = $this->db->loadResult();
				$chain_question_ids = str_replace('*', ',', $chain_question_ids);
		
				$sql = "SELECT SUM(c_point) FROM #__quiz_t_question WHERE c_id IN (".$chain_question_ids.") AND published = 1";
				$this->db->setQuery( $sql );
				$rows[$i]->max_score = $this->db->loadResult();
				
				$sql = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id IN (".$chain_question_ids.") AND c_right = 1";
				$this->db->setQuery( $sql );
				$rows[$i]->max_score += $this->db->loadResult();
				
				$sql = "SELECT COUNT(*) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 AND `c_student_id` <> '".@$row->c_student_id."' AND `c_total_score` > '".$row->c_total_score."' GROUP BY `c_student_id`";
				$this->db->setQuery($sql);
				$rows[$i]->rank  = 1 + count($this->db->loadResultArray());
				
				if (isset($quizzes[$row->c_quiz_id])) {
					$rows[$i]->total = $quizzes[$row->c_quiz_id]->total;
					$rows[$i]->total_tries = $quizzes[$row->c_quiz_id]->total_tries;
					$rows[$i]->total_passed = $quizzes[$row->c_quiz_id]->total_passed;
					$rows[$i]->total_score_avg = $quizzes[$row->c_quiz_id]->total_score_avg;
				} else {
					$quizzes[$row->c_quiz_id] = new stdClass();									
					
					$sql = "SELECT COUNT(*) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 GROUP BY `c_student_id` ";
					$this->db->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total  = count($this->db->loadResultArray());
					$rows[$i]->total = $quizzes[$row->c_quiz_id]->total;
					
					$sql = "SELECT COUNT(c_id) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' ";
					$this->db->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total_tries  = (int)$this->db->loadResult();
					$rows[$i]->total_tries = $quizzes[$row->c_quiz_id]->total_tries;
					
					$sql = "SELECT COUNT(*) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 AND c_passed = 1 GROUP BY c_student_id";
					$this->db->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total_passed  = count($this->db->loadResultArray());
					$rows[$i]->total_passed = $quizzes[$row->c_quiz_id]->total_passed;
					
					$sql = "SELECT AVG(c_total_score) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 ";
					$this->db->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total_score_avg  = $this->db->loadResult();
					$rows[$i]->total_score_avg = $quizzes[$row->c_quiz_id]->total_score_avg;
				}
				
			}		
			
			$total = $model->countQuiz($userid, $this->category);
			$introtext = $this->params->get("introtext", 0);
			
			$mainframe = JFactory::getApplication();
			$caching = $this->params->get('cache', 1);
			if($caching)
			{
				$caching = $mainframe->getCfg('caching');
			}
			
			$this->set('userid',$userid);
			$this->set('limit',$limit);
			$this->set('limitstart',$limitstart);
			$this->set('rows',$rows);
			$this->set('app',$app);
			$this->set('total',$total);
			$this->set('introtext',$introtext);
			$this->set('params',$this->params);

		echo parent::display( 'canvas/default' );
	}
}