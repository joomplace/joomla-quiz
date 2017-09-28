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

        $input = JFactory::getApplication()->input;

        $database = JFactory::getDBO();
        $quiz_id = $input->get('quiz_id',0,'INT');
        $quest_id = $input->get('quest_id',0,'INT');
        $stu_quiz_id = $input->get('stu_quiz_id',0,'INT');
        $flag_quest = $input->get('flag_quest',0,'INT');

        $database->setQuery("UPDATE `#__quiz_r_student_question` SET `c_flag_question` = ".$database->q($flag_quest)." WHERE `c_stu_quiz_id` = ".$database->q($stu_quiz_id)." AND `c_question_id` = ".$database->q($quest_id)."");
        $database->execute();
		
		die;
	}
}
