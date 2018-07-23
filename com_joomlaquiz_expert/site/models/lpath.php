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
		
		$lpath_id = intval($mainframe->input->get( 'lpath_id', $params->get('lpath_id', 0) ));
		$rel_id = intval($mainframe->input->get( 'rel_id', 0));
		$package_id = intval($mainframe->input->get( 'package_id', 0));
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
            if($lpath->package_id && $lpath->rel_id){
                $query = "SELECT `type`, `qid`"
                    . "\n FROM `#__quiz_lpath_stage`"
                    . "\n WHERE uid = '{$my->id}' AND oid = '{$package_id}' AND rel_id = '{$rel_id}' AND lpid = '{$lpath->id}' AND stage = 1"
                ;
            } else {
                $query = $database->getQuery(true);
                $query->select( $database->qn(array('type', 'qid')) )
                    ->from($database->qn('#__quiz_lpath_stage'))
                    ->where( $database->qn('uid') . '=' . $database->q((int)$my->id ))
                    ->where( $database->qn('lpid') . '=' . $database->q((int)$lpath->id ));
            }
            $database->SetQuery( $query );
			$lpath_stages = $database->loadObjectList();

			if(is_array($lpath_stages) && count($lpath_stages))
			foreach($lpath_stages as $ls) {
				$passed_steps[$ls->type][$ls->qid] = 1;	
			}

            if($lpath->package_id && $lpath->rel_id){
                $query = "SELECT * FROM #__quiz_r_student_quiz WHERE c_student_id = '{$my->id}' AND c_order_id ='{$package_id}' AND c_rel_id = '{$rel_id}' AND c_passed = 1 ";
            } else {
                $query = "SELECT * FROM #__quiz_r_student_quiz WHERE c_student_id = '{$my->id}' AND c_passed = 1 ";
            }

			$database->SetQuery( $query );		
			$passed_quizzes = $database->loadObjectList();
			if(is_array($passed_quizzes) && count($passed_quizzes))
			foreach($passed_quizzes as $ls) {
				if (!array_key_exists($ls->c_quiz_id, $passed_steps['q'])){
                    if($lpath->package_id && $lpath->rel_id){
                        $query = "UPDATE `#__quiz_lpath_stage` SET `stage` = 1 "
                            . "\n WHERE uid = {$my->id} AND rel_id = {$rel_id} AND oid = $package_id AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$ls->c_quiz_id}";
                    } else {
                        $query = "UPDATE `#__quiz_lpath_stage` SET `stage` = 1 "
                            . "\n WHERE uid = {$my->id} AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$ls->c_quiz_id}";
                    }
					$database->SetQuery( $query );
					$database->execute();
					$passed_steps['q'][$ls->c_quiz_id] = 1;
				}
			}

			$link = true;
			if(is_array($lpath_all ) && count($lpath_all ))
			foreach($lpath_all as $i=>$row) {
				$lpath_all[$i]->show_link = $link;
				if($link == true && !array_key_exists($lpath_all[$i]->all_id, $passed_steps[$row->type])) {
					$link = false;
				}
			}
			return array($lpath, $lpath_all);
		}
		else if($lpath_id && is_object($lpath_id)){
            $lpath->error = isset($lpath_id->error) ? $lpath_id->error : 1;
            $lpath->message = isset($lpath_id->message) ? $lpath_id->message : '<p align="left">'.JText::_('COM_QUIZ_LPATH_NOT_AVAILABLE').'</p>';
            return array($lpath, null);
        }
		else {
			$lpath->error = 1;
			$lpath->message = '<p align="left">'.JText::_('COM_QUIZ_LPATH_NOT_AVAILABLE').'</p>';
			return array($lpath, null);
		}		
	}
}