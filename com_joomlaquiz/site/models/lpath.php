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
 * Learning Path Model.
 *
 */
class JoomlaquizModelLpath extends JModelList
{	
	public function getLearningPaths(){
		
		$database = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		$my = JFactory::getUser();
		
		if($mainframe->isAdmin()){
			$params = JComponentHelper::getParams('com_joomlaquiz');
		} else {
			$params = $mainframe->getParams();
		}
		
		$lpath_id = intval(JFactory::getApplication()->input->get( 'lpath_id', $params->get('lpath_id', 0) ));
		$rel_id = intval(JFactory::getApplication()->input->get( 'rel_id', 0));
		$package_id = intval(JFactory::getApplication()->input->get( 'package_id', 0));
		$vm = $package_id < 1000000000;
		
		$lpath = new stdClass;
		if (!$my->id) { 
			$lpath->error = 1;
			$lpath->message = '<p align="left">'.JText::_('COM_LPATH_FOR_REGISTERED').'</p>';
			return array($lpath, null);
		}
						
		$_SESSION['quiz_check_rel_item'] = 0;	
		
		if (!$lpath_id) {
			$lpath_id = JoomlaquizHelper::JQ_checkPackage($package_id, $rel_id, $vm);
		} else {
			$query = "SELECT `paid_check` "
			. "\n FROM #__quiz_lpath"
			. "\n WHERE id = {$lpath_id} AND `published` = 1 " 
			;
			$database->SetQuery( $query );
			if ($database->loadResult()) {
				$lpath_id = 0;
			}
		}
		
		if($lpath_id && !is_object($lpath_id)) {
			$_SESSION['quiz_check_rel_item'] = 1;

			$query = "SELECT * FROM `#__quiz_lpath` WHERE `id` = {$lpath_id} AND published = 1";

			$database->SetQuery( $query );
			$lpath = $database->loadObjectList();
			
			if(count($lpath)) {
				$lpath = $lpath[0];
			} else {
				$lpath = new stdClass;
				$lpath->error = 1;
				$lpath->message = '<p align="left">'.JText::_('COM_LPATH_NOT_AVAILABLE').'</p>';
				return array($lpath, null);
			}

			$lpath->rel_id = $rel_id;
			$lpath->package_id = $package_id;
			JoomlaquizHelper::JQ_GetJoomFish($lpath->title, 'quiz_lpath', 'title', $lpath->id);
			JoomlaquizHelper::JQ_GetJoomFish($lpath->short_descr, 'quiz_lpath', 'short_descr', $lpath->id);
			JoomlaquizHelper::JQ_GetJoomFish($lpath->descr, 'quiz_lpath', 'descr', $lpath->id);

			$query = "SELECT l_q.*, q.*, c.*, '{$package_id}' AS `package_id`, "
				. ' IF(l_q.type = \'q\', q.c_id, c.id) AS all_id, IF(l_q.type = \'q\', q.c_title, c.title) AS title, '
				. ' IF(l_q.type = \'q\', q.c_short_description, c.introtext) AS short_description,'
				. ' IF(l_q.type = \'q\', q.c_description, c.fulltext) AS description '
				. ' FROM #__quiz_lpath_quiz AS l_q'
				. ' LEFT JOIN #__quiz_t_quiz AS q ON (q.c_id = l_q.qid AND l_q.type = \'q\')'
				. ' LEFT JOIN #__content AS c ON (c.id = l_q.qid AND l_q.type = \'a\')'
				. ' WHERE l_q.lid = ' . $lpath->id
				. ' ORDER BY l_q.order'
				;
			$database->setQuery($query);
			$lpath_all = $database->loadObjectList();
			if(empty($lpath_all) || !is_array($lpath_all)) {
				$lpath->error = 1;
				$lpath->message = '<p align="left">'.JText::_('COM_LPATH_NOT_AVAILABLE').'</p>';
				return array($lpath, null);
			}
		
			$passed_steps = array('q'=>array(), 'a'=>array());
			$query = "SELECT `type`, `qid`"
				. "\n FROM `#__quiz_lpath_stage`"
				. "\n WHERE uid = '{$my->id}' AND oid = '{$package_id}' AND rel_id = '{$rel_id}' AND lpid = '{$lpath->id}' AND stage = 1"
				;
			$database->SetQuery( $query );
			$lpath_stages = $database->loadObjectList();


//			if ($lpath_stages) {
//				$lpath_all_revers = array_reverse($lpath_all);
//
//				$i = -1;
//				foreach ($lpath_all_revers as $step) {
//					foreach ($lpath_stages as $stage) {
//						if ($step->qid == $stage->qid) {
//							if ($lpath_all_revers[$i]->type == 'a') {
//
//								for ($j = $i; $j >= 0; $j--) {
//									if ($lpath_all_revers[$j]->type == 'a') {
//										$lpath_stages = $this->ckeckLpArticles($my->id, $lpath->id, $lpath_all_revers[$j]->qid, $package_id, $rel_id);
//									}
//									else {
//										break;
//									}
//								}
//
//							}
//							break 2;
//						}
//					}
//					$i++;
//				}
//			}
//			else {
//				for ($i = 0; $i < count($lpath_all); $i ++) {
//					if ($lpath_all[$i]->type == 'a' && $lpath_all[$i+1]->type == 'q') {
//						$lpath_stages = $this->ckeckLpArticles($my->id, $lpath->id, $lpath_all[0]->qid, $package_id, $rel_id);
//					}
//					if ($lpath_all[$i]->type == 'q') {
//						break;
//					}
//				}
//			}


			if(is_array($lpath_stages) && count($lpath_stages))
			foreach($lpath_stages as $ls) {
				$passed_steps[$ls->type][$ls->qid] = 1;	
			}
		
			$query = "SELECT * FROM #__quiz_r_student_quiz WHERE c_student_id = '{$my->id}' AND c_order_id = '{$package_id}' AND c_rel_id = '{$rel_id}' AND c_passed = 1 ";
			$database->SetQuery( $query );		
			$passed_quizzes = $database->loadObjectList();
			if(is_array($passed_quizzes) && count($passed_quizzes))
			foreach($passed_quizzes as $ls) {
				if (!array_key_exists($ls->c_quiz_id, $passed_steps['q'])){
					$query = "UPDATE `#__quiz_lpath_stage` SET `stage` = 1 "
								. "\n WHERE uid = {$my->id} AND rel_id = {$rel_id} AND oid = $package_id AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$ls->c_quiz_id}";
					$database->SetQuery( $query );
					$database->execute();
					$passed_steps['q'][$ls->c_quiz_id] = 1;
				}
			}

			$link = true;
			if(is_array($lpath_all ) && count($lpath_all ))
			foreach($lpath_all as $i=>$row) {
				if($row->type == 'q') {
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->c_title, 'quiz_t_quiz', 'c_title', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->description, 'quiz_t_quiz', 'c_description', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->short_description, 'quiz_t_quiz', 'c_short_description', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->c_right_message, 'quiz_t_quiz', 'c_right_message', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->c_wrong_message, 'quiz_t_quiz', 'c_wrong_message', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->c_pass_message, 'quiz_t_quiz', 'c_pass_message', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->c_unpass_message, 'quiz_t_quiz', 'c_unpass_message', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->c_metadescr, 'quiz_t_quiz', 'c_metadescr', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->c_keywords, 'quiz_t_quiz', 'c_keywords', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->c_metatitle, 'quiz_t_quiz', 'c_metatitle', $lpath_all[$i]->all_id);

				} else {
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->title, 'content', 'title', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->short_description, 'content', 'introtext', $lpath_all[$i]->all_id);
					JoomlaquizHelper::JQ_GetJoomFish($lpath_all[$i]->description, 'content', 'fulltext', $lpath_all[$i]->all_id);
				}

				$lpath_all[$i]->show_link = $link;
				if($link == true && $row->type != 'a'  && !array_key_exists($lpath_all[$i]->all_id, $passed_steps[$row->type])) {
					$link = false;
				}
			}
			
			return array($lpath, $lpath_all);
		} else {
			$lpath->error = 1;
			$lpath->message = '<p align="left">'.JText::_('COM_QUIZ_LPATH_NOT_AVAILABLE').'</p>';
			return array($lpath, null);
		}		
	}

	protected function ckeckLpArticles($myId, $lpathId, $lpathQid, $package_id, $rel_id ) {
		$database = JFactory::getDBO();
		$query = "INSERT INTO `#__quiz_lpath_stage`"
			. "\n SET uid = {$myId}, lpid = {$lpathId}, `type` = 'a', qid = {$lpathQid}, stage = 1, oid = 0, rel_id = 0 ";
		$database->SetQuery( $query );
		$database->execute();

		$query = "SELECT `type`, `qid`"
			. "\n FROM `#__quiz_lpath_stage`"
			. "\n WHERE uid = '{$myId}' AND oid = '{$package_id}' AND rel_id = '{$rel_id}' AND lpid = '{$lpathId}' AND stage = 1";
		$database->SetQuery( $query );
		return $database->loadObjectList();
	}
}
