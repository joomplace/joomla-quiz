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
 * Ajax action Controller
 */
class JoomlaquizControllerAjaxaction extends JControllerForm
{
  	public function getModel($name = 'ajaxaction', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function procces(){
		$model = $this->getModel();
		$model->JQ_analizeAjaxRequest();
		
		return;
	}

	public function flag_question(){
	
		$database = JFactory::getDBO();
		$quiz_id = JRequest::getVar('quiz_id');
		$quest_id = JRequest::getVar('quest_id');
		$stu_quiz_id = JRequest::getVar('stu_quiz_id');
		$flag_quest = JRequest::getVar('flag_quest');

		$database->setQuery("UPDATE `#__quiz_r_student_question` SET `c_flag_question` = '".$flag_quest."' WHERE `c_stu_quiz_id` = '".$stu_quiz_id."' AND `c_question_id` = '".$quest_id."'");
		$database->query();	
		
		die;
	}
}
