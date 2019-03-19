<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controlleradmin');
 
/**
 * Results Controller
 */
class JoomlaquizControllerResults extends JControllerAdmin
{
        /**
         * Proxy for getModel.
         * @since       1.6
         */
        public function getModel($name = 'Results', $prefix = 'JoomlaquizModel', $config = array('ignore_request' => true))
        {
                $model = parent::getModel($name, $prefix, $config);
                return $model;
        }
		
		public function csv_summary(){
			$cid = JFactory::getApplication()->input->get('cid', array(), '');
						
			$model = $this->getModel();
			$model->JQ_csv_summaryReport($cid);
			
			return;
		}
		
		public function csv_report(){
			$cid = JFactory::getApplication()->input->get('cid', array(), '');
			$model = $this->getModel();
			$model->JQ_csv_report($cid);
			
			return;
		}
		
		public function stu_report(){
			$cid = JFactory::getApplication()->input->get('cid', array(), '');
			$cid = $cid[0];
			
			$this->setRedirect('index.php?option=com_joomlaquiz&view=results&layout=stu_report&cid='.$cid);
			return;
		}
		
		public function view_report(){
			$this->setRedirect('index.php?option=com_joomlaquiz&view=results');
			return;
		}

		public function del_flags(){
			$app = JFactory::getApplication();
			$database = JFactory::getDBO();

			$cid = JFactory::getApplication()->input->get('cid', array(), '');
			if(!empty($cid)){
				$query = "UPDATE `#__quiz_r_student_question` SET `c_flag_question` = '' WHERE `c_stu_quiz_id` IN (".implode(',', $cid).")";
				$database->setQuery($query);
				$database->query();
			}

			$app->redirect("index.php?option=com_joomlaquiz&view=results");
			return true;
		}

		public function csv_flag_questions(){

			$mainframe = JFactory::getApplication();
			$database = JFactory::getDBO();

			$quiz_id 	= intval( JFactory::getApplication()->input->get('filter_quiz_id') );
			$cid = JFactory::getApplication()->input->get('cid', array(), '');

			if(!$quiz_id){
				echo "<script> alert('Select quiz, please!'); window.history.go(-1); </script>\n";
				exit();
			}
			
			$str = '"Question", "Number of times it has been flagged", "Who did the flagging"'."\n";
			
			$database->setQuery("SELECT * FROM `#__quiz_t_question` WHERE `c_quiz_id` = '".$quiz_id."' AND `published` = 1");
			$questions = $database->loadObjectList();
			
			if(empty($questions) && !empty($cid)){
				$questions = array();
				$query = "SELECT a.q_chain FROM `#__quiz_q_chain` AS a, `#__quiz_r_student_quiz` AS b"
						. "\n WHERE a.s_unique_id =  b.unique_id AND  b.c_id IN (".implode(',', $cid).")";
				$database->SetQuery($query);
				$qch_ids = $database->LoadColumn();
				
				if(!empty($qch_ids)){
					foreach($qch_ids as $qch_id){
						$qchids = explode('*', $qch_id);
						$query = $database->getQuery(true);
						$query->select('*')
							->from('`#__quiz_t_question`')
							->where("`c_id` IN (".implode(',', $qchids).")")
							->where("`published` = 1");
						//if(JComponentHelper::getParams('com_joomlaquiz')->get('hide_boilerplates')){
						//	$query->where('`c_type` != 9');
						//}
						$database->setQuery($query);
						$question = $database->loadObjectList();

						$questions = array_merge($questions, $question);
					}
				}

			}

			$numbers = $stu_quiz_id = array();
			if(!empty($questions)){
				foreach($questions as $question){
					
					$database->setQuery("SELECT COUNT(c_flag_question) FROM `#__quiz_r_student_question` WHERE `c_flag_question` = 1 AND c_question_id = '".$question->c_id."'");
					$numbers[] = $database->loadResult();
					
					$database->setQuery("SELECT GROUP_CONCAT(`c_stu_quiz_id`) FROM `#__quiz_r_student_question` WHERE `c_flag_question` = 1 AND `c_question_id` = '".$question->c_id."' GROUP BY `c_question_id`");
					$stu_quiz_id[] = $database->loadResult();
				}
			}

			$users = array();
			
			if(!empty($stu_quiz_id)){
				foreach($stu_quiz_id as $cid){
					if($cid){
						$database->setQuery("SELECT u.`name` FROM `#__users` as u LEFT JOIN `#__quiz_r_student_quiz` as sq ON sq.`c_student_id` = u.id WHERE sq.`c_student_id` <> 0 AND sq.`c_quiz_id` = '".$quiz_id."' AND sq.`c_id` IN (".$cid.")");
												
						$users[] = $database->loadColumn();
					} else {
						$users[] = 'Anonymous';
					}
				}
			}
			
			foreach($questions as $ii => $question){
				$str .= '"'.trim(strip_tags($question->c_question)).'", "'.$numbers[$ii].'", "'.((is_array($users[$ii]) && !empty($users[$ii])) ? implode(',', $users[$ii]) : "Anonymous").'"'."\n";
			}
			
			$UserBrowser = '';
			if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) $UserBrowser = "IE";
			header("Content-Type:application/vnd.ms-excel");
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			if ($UserBrowser == 'IE') {
				header("Content-Disposition: inline; filename=flag_questions.csv ");
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header("Content-Disposition: inline; filename=flag_questions.csv ");
				header('Pragma: no-cache');
			}
			echo $str;
			die();
		}
}
