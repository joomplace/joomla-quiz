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
	public function getLearningPaths()
    {
        $lpath = new stdClass;
        $my = JFactory::getUser();

        if (!$my->id) {
            $lpath->error = 1;
            $lpath->message = '<p align="left">'.JText::_('COM_LPATH_FOR_REGISTERED').'</p>';
            return array($lpath, null);
        }

		$database = JFactory::getDBO();
		$mainframe = JFactory::getApplication();

		if($mainframe->isAdmin()){
			$params = JComponentHelper::getParams('com_joomlaquiz');
		} else {
			$params = $mainframe->getParams();
		}
		
		$lpath_id = intval($mainframe->input->get( 'lpath_id', $params->get('lpath_id', 0) ));
		$rel_id = intval($mainframe->input->get( 'rel_id', 0));
		$package_id = intval($mainframe->input->get( 'package_id', 0));
		$vm = !empty($package_id) && $package_id < 1000000000;

		if (!$lpath_id) {
			$lpath_id = JoomlaquizHelper::JQ_checkPackage($package_id, $rel_id, $vm);
		} else {
			$query = "SELECT `paid_check` "
			. "\n FROM #__quiz_lpath"
			. "\n WHERE id = {$lpath_id} AND `published` = 1 " 
			;
			$database->SetQuery($query);
			if ($database->loadResult()) {
				$lpath_id = 0;
			}
		}
		
		if($lpath_id && !is_object($lpath_id)) {

			$query = "SELECT * FROM `#__quiz_lpath` WHERE `id` = {$lpath_id} AND published = 1";
			$database->SetQuery( $query );
			$lpath = $database->loadObjectList();
			
			if(!empty($lpath)) {
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

			$quiz_id_in_lp = array();
			foreach ($lpath_all as $lpath_one) {
                $quiz_id_in_lp[] = $database->q($lpath_one->qid);
            }
            $quiz_id_in_lp_string = implode(',', $quiz_id_in_lp);

			$passed_steps = array('q'=>array(), 'a'=>array());
            $count_passed_steps = array();

            $query = $database->getQuery(true);
            $query->select( $database->qn(array('type', 'qid')) )
                ->from($database->qn('#__quiz_lpath_stage'))
                ->where( $database->qn('uid') . '=' . $database->q((int)$my->id))
                ->where( $database->qn('lpid') . '=' . $database->q((int)$lpath->id))
                ->where( $database->qn('stage') . '=' . $database->q('1'));
            if($lpath->package_id && $lpath->rel_id){
                $query->where( $database->qn('oid') . '=' . $database->q((int)$package_id));
                $query->where( $database->qn('rel_id') . '=' . $database->q((int)$rel_id));
                $query->where( $database->qn('stage') . '=' . $database->q('1'));
            }
            $database->SetQuery( $query );
			$lpath_stages = $database->loadObjectList();

			if(is_array($lpath_stages) && !empty($lpath_stages)) {
                foreach ($lpath_stages as $ls) {
                    $passed_steps[$ls->type][$ls->qid] = 1;
                }
            }

            if($lpath->package_id && $lpath->rel_id){
                $query = "SELECT * FROM `#__quiz_r_student_quiz` WHERE `c_student_id` = '{$my->id}' AND `c_order_id` ='{$package_id}' AND `c_rel_id` = '{$rel_id}' AND `c_passed` = '1' AND `c_quiz_id` IN ({$quiz_id_in_lp_string}) ";
            } else {
                $query = "SELECT * FROM `#__quiz_r_student_quiz` WHERE `c_student_id` = '{$my->id}' AND `c_passed` = '1' AND `c_quiz_id` IN ({$quiz_id_in_lp_string}) ";
            }
			$database->SetQuery( $query );		
			$passed_quizzes = $database->loadObjectList();

			if(is_array($passed_quizzes) && !empty($passed_quizzes)) {
                foreach ($passed_quizzes as $ls) {
                    if (empty($count_passed_steps[$ls->c_quiz_id])) {
                        $count_passed_steps[$ls->c_quiz_id] = 0;
                    }
                    $count_passed_steps[$ls->c_quiz_id] += 1;

                    if (!array_key_exists($ls->c_quiz_id, $passed_steps['q'])) {
                        if ($lpath->package_id && $lpath->rel_id) {
                            $query = "UPDATE `#__quiz_lpath_stage` SET `stage` = 1 "
                                . "\n WHERE uid = {$my->id} AND rel_id = {$rel_id} AND oid = $package_id AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$ls->c_quiz_id}";
                        } else {
                            $query = "UPDATE `#__quiz_lpath_stage` SET `stage` = 1 "
                                . "\n WHERE uid = {$my->id} AND lpid = {$lpath->id} AND `type` = 'q' AND qid = {$ls->c_quiz_id}";
                        }
                        $database->SetQuery($query);
                        $database->execute();
                        $passed_steps['q'][$ls->c_quiz_id] = 1;
                    }
                }
            }

            //Check product's attempts
            if($lpath->package_id && $lpath->rel_id){
                $product_quantity = 1;
                if($vm){
                    $query = "SELECT vm_oi.product_quantity"
                        . "\n FROM #__virtuemart_orders AS vm_o"
                        . "\n INNER JOIN #__virtuemart_order_items AS vm_oi ON vm_oi.virtuemart_order_id = vm_o.virtuemart_order_id"
                        . "\n INNER JOIN #__quiz_products AS qp ON qp.pid = vm_oi.virtuemart_product_id"
                        . "\n WHERE vm_o.virtuemart_user_id=".$my->id." AND vm_o.virtuemart_order_id=".$lpath->package_id." AND qp.id=".$lpath->rel_id." AND vm_o.order_status IN ('C')";
                    $database->SetQuery($query);
                    $product_quantity = (int)$database->loadResult();
                    $product_quantity = $product_quantity ? $product_quantity : 1;
                }
                $productUsedAttempts = JoomlaquizHelper::getProductUsedAttempts($lpath->rel_id, $product_quantity);
            }

            $link = true;
			if(is_array($lpath_all) && !empty($lpath_all)) {
                foreach ($lpath_all as $i => $row) {
                    $lpath_all[$i]->show_link = $link;
                    if ($link == true && !array_key_exists($lpath_all[$i]->all_id, $passed_steps[$row->type])) {
                        $link = false;
                    }

                    //Check product's attempts
                    if($lpath->package_id && $lpath->rel_id){
                        if(isset($productUsedAttempts['quizzes']['left'][$row->qid]) && !$productUsedAttempts['quizzes']['left'][$row->qid]) {
                            $lpath_all[$i]->show_link = false;
                        }
                    }
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