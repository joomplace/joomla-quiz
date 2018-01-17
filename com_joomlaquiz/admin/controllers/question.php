<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controllerform');
 
/**
 * Question Controller
 */
class JoomlaquizControllerQuestion extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
    protected function allowEdit($data = array(), $key = 'c_id')
    {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_joomlaquiz');             
    }
	
	public function getContentEditor($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->qn('c_choice'))
				->from($db->qn('#__quiz_t_choice'))
				->where($db->qn('c_id').' = '.$db->q($id));
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	static public function JQ_editorArea($name, $content, $hiddenField, $width, $height, $col, $row)
	{
		$editor = JFactory::getEditor();
		$id = JFactory::getApplication()->input->get('id',0,'INT');
		if(!$content){
			$content = self::getContentEditor($id);
		}
		echo $editor->display($hiddenField, $content, $width, $height, $col, $row, array('pagebreak', 'readmore'));
	}
	
	public function edit_field(){
		require_once(JPATH_BASE.'/components/com_joomlaquiz/views/editor/view.html.php');
		$view = $this->getView("editor");
		$view->display();
	}

	public function save(){
        $app  = JFactory::getApplication();
        $data = $app->input->get('jform',array(),'array');
        $task = $app->input->get('task');

        $c_type = !empty($data['c_type']) ? $data['c_type'] : 0;
        if (!$data['c_id'] && !$this->c_type) {
            $new_qtype_id = $app->getUserStateFromRequest("question.new_qtype_id", 'new_qtype_id', 0);
            $c_type = $new_qtype_id;
        }
        $question_type = JoomlaquizHelper::getQuestionType($c_type);
	
		switch($task){
			case 'save2copy':
				parent::save();
				break;
			case 'save2new':
				parent::save();
				$this->setRedirect( 'index.php?option=com_joomlaquiz&view=question&layout=edit');
				break;
			case 'apply':
                if(($question_type == 'memory' || $question_type == 'imgmatch') && $_FILES['Filedata']['size'] !== 0) {
                    require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");
                    $appsLib = JqAppPlugins::getInstance();
                    $appsLib->loadApplications();
                    $arrayParams = array();
                    $arrayParams['quest_type'] = $question_type;
                    $appsLib->triggerEvent('onAdminSaveOptions', $arrayParams);
                }
				parent::save();
				break;
			case 'save':
			default:
			parent::save();
			if(!$data['c_quiz_id']){
				$this->setRedirect( 'index.php?option=com_joomlaquiz&view=questions&quiz_id=0');
			}else{
				$this->setRedirect( 'index.php?option=com_joomlaquiz&view=questions&quiz_id='.$data['c_quiz_id'] );
			}
		}

	}

	public function cancel(){
		parent::cancel();
		$data = JFactory::getApplication()->input->get('jform',array(),'array');
		$this->setRedirect( 'index.php?option=com_joomlaquiz&view=questions&quiz_id='.$data['c_quiz_id'] );
		
	}

	public function preview_quest()
	{
		$database = JFactory::getDBO();

		$c_id = JFactory::getApplication()->input->get('c_id');

		$query = "DELETE FROM `#__quiz_setup` WHERE `c_par_name` = 'admin_preview'";
		$database->SetQuery( $query );
		$database->query();

		$preview_unique_id = md5(uniqid(rand(), true));
		$query = "INSERT INTO `#__quiz_setup` (`c_par_name`, `c_par_value`) VALUES ('admin_preview', '".$preview_unique_id."')";
		$database->SetQuery( $query );
		$database->query();

		$this->setRedirect( JURI::root() . "index.php?option=com_joomlaquiz&task=quiz.view_preview&preview_id=".$preview_unique_id."&c_id=". $c_id );
	}
}
