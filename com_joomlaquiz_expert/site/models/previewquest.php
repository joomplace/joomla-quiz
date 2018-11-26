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
 * Preview Question Model.
 *
 */
class JoomlaquizModelPreviewquest extends JModelList
{	
	public function JQ_previewQuestion(){
		
		$database = JFactory::getDBO();
		$return = array();
		
		$quest_id = intval( JFactory::getApplication()->input->get('c_id', 0));
		$preview_id = strval( JFactory::getApplication()->input->get('preview_id', ''));
		$query = "SELECT c_par_value FROM #__quiz_setup WHERE c_par_name = 'admin_preview'";
		$database->SetQuery( $query );
		$preview_code = $database->LoadResult();
		
		if ($quest_id && ($preview_id == $preview_code)) {
			$query = "SELECT c_quiz_id FROM #__quiz_t_question WHERE c_id = '".$quest_id."' AND published = 1";
			$database->SetQuery( $query );
			$quiz_id = $database->LoadResult();
			
			if ($quiz_id) {
				$query = "SELECT a.*, b.template_name FROM #__quiz_t_quiz as a, #__quiz_templates as b WHERE a.c_id = '".$quiz_id."' and a.c_skin = b.id";
				$database->SetQuery($query);
				$quiz_params = $database->LoadObjectList();
		
				if (!empty($quiz_params)) {
					$query = "SELECT count(*) FROM #__quiz_t_question WHERE c_id = '".$quest_id."' AND c_type = 4 AND published = 1" ;
					$database->SetQuery( $query );
					$quiz_params[0]->if_dragdrop_exist = $database->LoadResult();
					
					$quiz_params[0]->rel_id = 0;
					$quiz_params[0]->package_id = 0;
					$quiz_params[0]->lid = 0;
					
					$return['is_available'] = true;
					$return['quiz_params'] = $quiz_params[0];
					$return['is_preview'] = true;
					$return['quest_id'] = $quest_id;
					$return['preview_id'] = $preview_id;
					
					return $return;
				} else {
					
					$return['is_available'] = false;
					$return['error_code'] = '0001';
					
					return $return;
				}
			} else {
				$return['is_available'] = false;
				$return['error_code'] = '0003';
				
				return $return;
			}
		} else {
			$return = array();
			$return['is_available'] = false;
			$return['error_code'] = '0003';
				
			return $return;
		}
	}
}
