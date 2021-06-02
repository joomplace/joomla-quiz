<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Questions Controller
 */
class JoomlaquizControllerQuestions extends JControllerAdmin
{
        /**
         * Proxy for getModel.
         * @since       1.6
         */
        public function getModel($name = 'Questions', $prefix = 'JoomlaquizModel', $config = array('ignore_request' => true))
        {
            $model = parent::getModel($name, $prefix, $config);
            return $model;
        }
				
		public function saveOrderAjax()
		{
			$pks = $this->input->post->get('cid', array(), 'array');
			$order = $this->input->post->get('order', array(), 'array');

			// Sanitize the input
			JArrayHelper::toInteger($pks);
			JArrayHelper::toInteger($order);

			// Get the model
			$model = $this->getModel();
			// Save the ordering
			$return = $model->saveorder($pks, $order);
			if ($return)
			{
				echo "1";
			}
			// Close the application
			JFactory::getApplication()->close();
		}

		public function new_question_type(){

			require_once(JPATH_BASE.'/components/com_joomlaquiz/views/newquestion/view.html.php');
			$view = $this->getView("newquestion");
			$view->display();
		}

		public function quizzes()
		{
			$this->setRedirect('index.php?option=com_joomlaquiz&view=quizzes');
		}
		
		public function cancel(){
			$this->setRedirect('index.php?option=com_joomlaquiz&view=questions');
		}
		
		public function uploadquestions(){
			$model = $this->getModel();
			$ii = $model->uploadQuestions();			
			$this->setRedirect( "index.php?option=com_joomlaquiz&view=questions", $ii.JText::_('COM_JOOMLAQUIZ_QUESTION_UPLOAD') );
		}
		
		public function move_question_sel(){
			$cid = $this->input->get('cid', array(), 'array');
			if (!is_array( $cid ) || empty( $cid )) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_ITEM_TO_MOVE')."'); window.history.go(-1);</script>\n";
				exit;
			}
            $session = JFactory::getSession();
            $session->set('com_joomlaquiz.move.questions.cids', $cid);
			$this->setRedirect('index.php?option=com_joomlaquiz&view=questions&layout=move_questions');
		}
		
		public function move_question_cat(){
			$cid = $this->input->get('cid', array(), 'array');
			if (!is_array( $cid ) || empty( $cid )) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_ITEM_TO_MOVE')."'); window.history.go(-1);</script>\n";
				exit;
			}
            $session = JFactory::getSession();
            $session->set('com_joomlaquiz.move.questions.cids', $cid);
			$this->setRedirect('index.php?option=com_joomlaquiz&view=questions&layout=move_questions_cat');
		}
		
		public function move_question_cat_ok(){
			$database = JFactory::getDBO();
            $session = JFactory::getSession();
            $cid = $session->get('com_joomlaquiz.move.questions.cids');
			$catMove = strval( JFactory::getApplication()->input->get('catmove') );
			$cids = implode( ',', $cid );
			$total = count( $cid );
			
			$query = "SELECT distinct c_ques_cat FROM #__quiz_t_question WHERE c_id IN ( $cids )";
			$database->SetQuery( $query );
			$ch_cat = $database->LoadObjectList();

			$query = "UPDATE #__quiz_t_question"
			. "\n SET c_ques_cat = '$catMove'"
			. "WHERE c_id IN ( $cids )"
			;
			$database->setQuery( $query );
			if ( !$database->execute() ) {
				echo "<script> alert('". $database->getErrorMsg() ."'); window.history.go(-1); </script>\n";
				exit();
			}

			if (!empty($ch_cat)) {
				foreach ($ch_cat as $c_cat) {
					JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($c_cat->c_ques_cat);
				}
			}
			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($catMove);
			
			$database->setQuery("SELECT `title` FROM #__categories WHERE id = '".$catMove."'");
			$c_title = $database->loadResult();
			
			$msg = $total .JText::_('COM_JOOMLAQUIZ_QUESTION_MOVED_TO').$c_title;
			$this->setRedirect( 'index.php?option=com_joomlaquiz&view=questions', $msg );
		}
		
		public function move_question(){
			$database = JFactory::getDBO();
            $session = JFactory::getSession();
            $cid = $session->get('com_joomlaquiz.move.questions.cids');
			
			$quizMove = strval( JFactory::getApplication()->input->get('quizmove') );
			$cids = implode( ',', $cid );
			$total = count( $cid );
			
			$query = "SELECT distinct c_quiz_id FROM #__quiz_t_question WHERE c_id IN ( $cids )";
			$database->SetQuery( $query );
			$ch_quizzes = $database->LoadObjectList();

			$query = "UPDATE #__quiz_t_question"
			. "\n SET c_quiz_id = '$quizMove'"
			. "WHERE c_id IN ( $cids )"
			;
			$database->setQuery( $query );
			if ( !$database->execute() ) {
				echo "<script> alert('". $database->getErrorMsg() ."'); window.history.go(-1); </script>\n";
				exit();
			}
			//re-calculate quizzes TotalScore
			if (!empty($ch_quizzes)) {
				foreach ($ch_quizzes as $c_q) {
					JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($c_q->c_quiz_id);
				}
			}
			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($quizMove);
			
			$database->setQuery("SELECT `c_title` FROM #__quiz_t_quiz WHERE c_id = '".$quizMove."'");
			$c_title = $database->loadResult();
			
			$msg = $total .JText::_('COM_JOOMLAQUIZ_QUESTION_MOVED_TO').$c_title;
			$this->setRedirect( 'index.php?option=com_joomlaquiz&view=questions', $msg );
		}
		
		public function copy_question_sel(){
			$cid = $this->input->get('cid', array(), 'array');
			if (!is_array( $cid ) || empty( $cid )) {
				echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_ITEM_TO_MOVE')."'); window.history.go(-1);</script>\n";
				exit;
			}

            $session = JFactory::getSession();
            $session->set('com_joomlaquiz.copy.questions.cids', $cid);
			$this->setRedirect('index.php?option=com_joomlaquiz&view=questions&layout=copy_questions');
		}
		
		public function copy_question()
		{
			$model = $this->getModel();
			$msg = $model->copyQuestions();
			$this->setRedirect( 'index.php?option=com_joomlaquiz&view=questions', $msg);
		}
		
		public function add_pbreak(){
			$database = JFactory::getDBO();
			$app = JFactory::getApplication();
			
			$quiz_id = intval( $app->getUserStateFromRequest('quizzes.filter.quiz_id', 'filter_quiz_id') );
			$quest_id = intval( JFactory::getApplication()->input->get( 'quest_id', 0 ) );
			
			if ($quiz_id && $quest_id){
				$query = "DELETE FROM `#__quiz_t_pbreaks` WHERE `c_question_id` = '{$quest_id}'";
				$database->setQuery( $query );
				$database->execute();
				
				$query = "INSERT INTO `#__quiz_t_pbreaks` SET `c_question_id` = '{$quest_id}', `c_quiz_id` = '{$quiz_id}'";
				$database->setQuery( $query );
				$database->execute();	
			}
			$this->setRedirect("index.php?option=com_joomlaquiz&view=questions");
		}
		
		public function delete_pbreak(){
			$database = JFactory::getDBO();
			$app = JFactory::getApplication();
	
			$pb_id = intval( JFactory::getApplication()->input->get( 'pid', 0 ) );
			
			if ($pb_id){
				$query = "DELETE FROM `#__quiz_t_pbreaks` WHERE `c_question_id` = '{$pb_id}'";
				$database->setQuery( $query );
				$database->execute();
			}
			$this->setRedirect("index.php?option=com_joomlaquiz&view=questions");
		}
		
		public function delete_invalid()
		{
			$db = JFactory::getDBO();
			$q_id = JFactory::getApplication()->input->get('q_id');
			
			$db->setQuery("SELECT `c_id` FROM #__quiz_r_student_question WHERE `c_question_id` = '".$q_id."'");
			$cid = $db->loadColumn();
			$cids = !empty($cid) ? implode(',', $cid) : '';
			
			if($cids != ''){
				JoomlaquizHelper::JQ_Delete_Items($cids, 'remove/reports/', 'removeReports');
				
				$query = "DELETE FROM #__quiz_r_student_question"
				. "\n WHERE c_id IN ( $cids )";
				$db->setQuery( $query );
				if (!$db->execute()) {
					echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
			}
			$query = "DELETE FROM `#__quiz_t_question` WHERE `c_id` = '".$q_id."'";
			$db->setQuery( $query );
			$db->execute();
			
			ob_start();
			ob_end_clean();
			ob_end_clean();
			echo 'success';
			die;
		}

		public function hotspot_converter(){
			
			$database = JFactory::getDBO();
			$mainframe = JFactory::getApplication();
				
			if(!empty($cid)){
				$database->setQuery("SELECT * FROM #__quiz_t_hotspot WHERE c_question_id IN (".implode(',', $cid).")");
				$hs_datas = $database->loadObjectList();
								
				if(!empty($hs_datas)){
					foreach($hs_datas as $ii => $hs_data){
						
						$json_paths = '[';
						$json_paths .= '"M '.$hs_data->c_start_x.' '.$hs_data->c_start_y;
						$json_paths .= ' L '.($hs_data->c_start_x + $hs_data->c_width).' '.$hs_data->c_start_y;
						$json_paths .= ' L '.($hs_data->c_start_x + $hs_data->c_width).' '.($hs_data->c_start_y + $hs_data->c_height);
						$json_paths .= ' L '.$hs_data->c_start_x.' '.($hs_data->c_start_y + $hs_data->c_height);
						$json_paths .= ' L '.$hs_data->c_start_x.' '.$hs_data->c_start_y;
						$json_paths .= 'Z"]';
						
						$database->setQuery("SELECT COUNT(c_id) FROM #__quiz_t_ext_hotspot WHERE c_question_id = '".$hs_data->c_question_id."'");
						$exists = $database->loadResult();
						
						if(!$exists){
							$database->setQuery("INSERT INTO #__quiz_t_ext_hotspot (`c_id`, `c_question_id`, `c_paths`) VALUES ('', '".$hs_data->c_question_id."', '".$json_paths."')");
							$database->query();
						} else {
							$database->setQuery("UPDATE #__quiz_t_ext_hotspot SET `c_paths` = '".$json_paths."' WHERE `c_question_id` = '".$hs_data->c_question_id."'");
							$database->query();
						}
					}
				}
			}
			
			$mainframe->redirect('index.php?option=com_joomlaquiz&view=questions');
		}
		
		public function deleteQuestions()
        {
            $app = JFactory::getApplication();
            $ids = $app->input->get('cid', array(),'array');
            $ids = ArrayHelper::toInteger($ids);

            $model = $this->getModel();
            $model->delete($ids);

            $app->redirect('index.php?option=com_joomlaquiz&view=questions');
		}

		public function checkComplitedQuestions()
        {
            $this->checkToken();

            $app = JFactory::getApplication();
			$ids = $app->input->get('cid', array(),'array');
            $ids = ArrayHelper::toInteger($ids);

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            $ids_arr = array();
            foreach($ids as $id) {
                $ids_arr[] = $db->q($id);
            }
            $ids_str = implode(',', $ids_arr);

			if(!empty($ids)) {
				$query->select($db->qn('c_question_id'))
					->from($db->qn('#__quiz_r_student_question'))
					->where($db->qn('c_question_id').' IN ('.$ids_str.')')
					->group($db->qn('c_question_id'));
				$db->setQuery($query);
				$q_ids = $db->loadColumn();
			} else {
                $app->enqueueMessage(JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'), 'warning');
                $app->redirect('index.php?option=com_joomlaquiz&view=questions');
            }

			if(!empty($q_ids)) {
				$cids = '';
                foreach($ids as $id) {
                    $cids .= '<input type="hidden" name="cid[]" value="'.$id.'" />';
                }

                $app->enqueueMessage(JText::sprintf('COM_JOOMLAQUIZ_QUESTIONS_DELETE_COMPLITED_MSG', implode(',', $q_ids)) .
					"<form action=\"#\" method=\"post\" name=\"message-Form\" id=\"message-Form\">
							<div>
							<br>
								<button class=\"btn\" type=\"submit\" title=\"Delete\"><i>".JText::_('COM_JOOMLAQUIZ_QUESTIONS_DELETE_COMPLITED_BTN_DELETE')."</i></button>
								<a class=\"btn\" type=\"button\" title=\"Cancel\" href=\"".JRoute::_('index.php?option=com_joomlaquiz&view=questions')."\"><i>".JText::_('COM_JOOMLAQUIZ_QUESTIONS_DELETE_COMPLITED_BTN_CANCEL')."</i></a>
								".$cids."
								<input type=\"hidden\" name=\"option\" value=\"com_joomlaquiz\" />
								<input type=\"hidden\" name=\"task\" value=\"questions.deleteQuestions\" />
							</div>
						</form>", 'message');

                JFactory::getSession()->set('joomlaquiz.questions.marked_for_deletion', implode(',', $ids));
                $app->redirect('index.php?option=com_joomlaquiz&view=questions');

			} else {
			    $this->deleteQuestions();
            }

            return true;
		}
}