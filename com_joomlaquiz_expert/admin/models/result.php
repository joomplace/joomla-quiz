<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Result model.
 *
 */
class JoomlaquizModelResult extends JModelList
{
	protected $text_prefix = 'COM_JOOMLAQUIZ';
		
	public function del_stu_report($cid){
		$database = JFactory::getDBO();
		if (!empty( $cid )) {
				$cids = implode( ',', $cid );
				JoomlaquizHelper::JQ_Delete_Items($cids, 'remove/reports/', 'removeReports');
								
				$query = "SELECT c_stu_quiz_id FROM #__quiz_r_student_question "
				. "\n WHERE c_id IN ( $cids )";
				$database->setQuery( $query );
				$id = $database->loadResult();
				
				$query = "DELETE FROM #__quiz_r_student_question"
				. "\n WHERE c_id IN ( $cids )";
				$database->setQuery( $query );
				if (!$database->execute()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
		}
		
	}
	
	public function getQuestionReport(){
	
		$database = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get('cid');
		
		$query = "SELECT q.c_type, q.c_id,q.c_image, sq.c_stu_quiz_id, q.c_question, c_title_true, c_title_false, sq.c_score, sq.`remark`, sq.reviewed FROM #__quiz_t_question as q, #__quiz_r_student_question as sq"
		. "\n WHERE q.c_id = sq.c_question_id and sq.c_id = '".$id."' AND q.published = 1"
		;
		$database->SetQuery( $query );
		$q_data = $database->LoadObjectList();
		
		$lists = array();
		if (!empty($q_data)) {
			$q_type = $q_data[0]->c_type;
			$q_id = $q_data[0]->c_id;
			$qid = $q_data[0]->c_stu_quiz_id;
			$query = "SELECT u.username, u.name, u.email FROM #__users as u, #__quiz_r_student_quiz as q WHERE q.c_id = '".$qid."'"
			. "\n and q.c_student_id = u.id";
			$database->SetQuery( $query );
			$user_info = $database->LoadObjectList();

         if(empty($user_info[0]->name)||empty($user_info[0]->username)){
                $query = "SELECT user_email,user_name FROM #__quiz_r_student_quiz WHERE c_id='$qid'";
                $database->SetQuery( $query );
                $unreg_user_info = $database->LoadObjectList();
                $user_info[0] = new stdClass();
                $user_info[0]->name = $unreg_user_info[0]->user_name;
                $user_info[0]->email = $unreg_user_info[0]->user_email;
            }
			
			if (!empty($user_info)) {
			    $lists['user'] = $user_info[0];
			}
			else { 
				$lists['user'] = new stdClass;
				$lists['user']->username = JText::_('COM_JOOMLAQUIZ_ANONYMOUS2'); $lists['user']->name = " - "; $lists['user']->email = " - ";
			}
			
			$lists['c_type'] = $q_type;
			$lists['q_id'] = $q_id;
			$lists['qid'] = $qid;
			$lists['question'] = $q_data[0]->c_question;
			$lists['title_true'] = $q_data[0]->c_title_true;
			$lists['title_false'] = $q_data[0]->c_title_false;
			$lists['image'] = $q_data[0]->c_image;
			$lists['remark'] = $q_data[0]->remark;
			$lists['reviewed'] = $q_data[0]->reviewed;
		}
		
		return $lists;
	}
}