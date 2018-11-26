<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controllerform');
 
/**
 * Quiz Controller
 */
class JoomlaquizControllerQuiz extends JControllerForm
{
  	public function getModel($name = 'quiz', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function view_preview()
	{

		$database = JFactory::getDBO();
				
		$quest_id = intval( JFactory::getApplication()->input->get('c_id', 0));
		
		$preview_id = strval( JFactory::getApplication()->input->get('preview_id', ''));
		
		$query = "SELECT `c_par_value` FROM `#__quiz_setup` WHERE `c_par_name` = 'admin_preview'";
		$database->SetQuery( $query );
		$preview_code = $database->LoadResult();
		
		if ($quest_id ) {
			$query = "SELECT `c_quiz_id` FROM `#__quiz_t_question` WHERE `c_id` = '".$quest_id."' AND `published` = 1";
			$database->SetQuery( $query );
			$quiz_id = $database->LoadResult();

			$query = "SELECT a.*, b.`template_name` FROM `#__quiz_t_quiz` as a, `#__quiz_templates` as b WHERE a.`c_id` = '".$quiz_id."' and a.`c_skin` = b.`id`";
			$database->SetQuery($query);
			$quiz_params = $database->LoadObjectList();
	
			if (!empty($quiz_params)) {
				
				$query = "SELECT count(*) FROM `#__quiz_t_question` WHERE `c_id` = '".$quest_id."' AND `c_type` = 4 AND `published` = 1" ;
				$database->SetQuery( $query );
				$quiz_params[0]->if_dragdrop_exist = $database->LoadResult();
				
				$quiz_params[0]->rel_id = 0;
				$quiz_params[0]->package_id = 0;
				$quiz_params[0]->lid = 0;
	
				$this->quiz_params = $quiz_params[0];
				$this->is_preview = true;
				$this->preview_quest = $quest_id;
				$this->preview_id = $preview_id;

				include_once(JPATH_SITE.'/components/com_joomlaquiz/views/quiz/tmpl/default.php');
			} else {
				echo '<p align="left">'.JText::_('COM_QUIZ_NOT_AVAILABLE').'<br>(Error code: 0001 - Template for quiz not found.)</p>';
				echo JoomlaquizHelper::poweredByHTML();
			}
		} else {
			echo '<p align="left">'.JText::_('COM_QUIZ_NOT_AVAILABLE').'<br>(Error code: 0003 - You have no permissions to preview this question.)</p>';
			echo JoomlaquizHelper::poweredByHTML();
		}

	}
}
