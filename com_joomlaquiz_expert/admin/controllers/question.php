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
jimport('joomla.application.component.helper');
 
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
	
	static public function JQ_editorArea($name=null, $content, $hiddenField, $width, $height, $col, $row)
	{
		if(!$name){
            $name = JFactory::getConfig()->get('editor', 'none');
        }
        $editor = JEditor::getInstance($name);
		$id = JFactory::getApplication()->input->get('id',0,'INT');
		if(!$content){
			$content = @self::getContentEditor($id);
		}
		if($name == 'none'){
            echo $editor->display($hiddenField, $content, $width, $height, $col, $row, false);
        } else {
            echo $editor->display($hiddenField, $content, $width, $height, $col, $row, true);
        }
	}
	
	public function edit_field(){
		require_once(JPATH_BASE.'/components/com_joomlaquiz/views/editor/view.html.php');
		$view = $this->getView("editor");
		$view->display();
	}

		public function save($key = NULL, $urlVar = NULL){
		$data = JFactory::getApplication()->input->get('jform',array(),'array');
		$task = JFactory::getApplication()->input->get('task');

        $is_set_default = JComponentHelper::getParams('com_joomlaquiz')->get('is_set_default');

        if ($data["c_id"] == 0 && $is_set_default) {
            $session = JFactory::getSession();
            $session->set('jform_c_point_d', $data["c_point"]);
            $session->set('jform_c_attempts_d', $data["c_attempts"]);
            $session->set('jform_c_feedback_d', $data["c_feedback"]);
            $session->set('jform_c_right_message_d', $data["c_right_message"]);
            $session->set('jform_c_wrong_message_d', $data["c_wrong_message"]);
            $session->set('jform_c_detailed_feedback_d', $data["c_detailed_feedback"]);
        }

		switch($task){
			case 'save2copy':
				parent::save();
				break;
			case 'save2new':
				parent::save();
				$this->setRedirect( 'index.php?option=com_joomlaquiz&view=question&layout=edit');
				break;
			case 'apply':
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

	public function cancel($key = NULL){
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
