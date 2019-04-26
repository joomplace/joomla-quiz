<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
 defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

/**
 * HTML View class for the Joomlaquiz Deluxe Component
 */
class JoomlaquizViewQuestion extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
    public function display($tpl = null) 
    {
		$app = JFactory::getApplication();
        $submenu = 'questions';
        JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		
 		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
		if($this->item->c_id){
			$new_qtype_id = $this->item->c_type;
		}
	
		$data = array();
		$type = JoomlaquizHelper::getQuestionType($new_qtype_id);
		$data['quest_type'] = $type;
		$data['question_id'] = $this->item->c_id;
		
		$className = 'plgJoomlaquiz'.ucfirst($type);
		$appsLib = JqAppPlugins::getInstance();
		$appsLib->loadApplications();

		$get_options = (method_exists($className, 'onGetAdminOptions')) ? $appsLib->triggerEvent( 'onGetAdminOptions' , $data ) : array('');
		$this->options = $get_options[0];
		
		$get_form = (method_exists($className, 'onGetAdminForm')) ? $appsLib->triggerEvent( 'onGetAdminForm' , $data ) : array(array());
		$this->add_form = $get_form[0];
		
		$get_script = (method_exists($className, 'onGetAdminJavaScript')) ? $appsLib->triggerEvent( 'onGetAdminJavaScript' , $data ) : array('');
		$this->script = $get_script[0];
		
		$feedback_fields = (method_exists($className, 'onGetAdminFeedbackFields')) ? $appsLib->triggerEvent( 'onGetAdminFeedbackFields' , $data ) : array('');
		$this->feedback_fields = $feedback_fields[0];
		
		$add_tabs = (method_exists($className, 'onGetAdminTabs')) ? $appsLib->triggerEvent( 'onGetAdminTabs' , $data ) : array('');
		$this->add_tabs = $add_tabs[0];
		
		$is_feedback = (method_exists($className, 'onAdminIsFeedback')) ? $appsLib->triggerEvent( 'onAdminIsFeedback' , $data ) : array('');
		$this->is_feedback = $is_feedback[0];
		
		$is_points = (method_exists($className, 'onAdminIsPoints')) ? $appsLib->triggerEvent( 'onAdminIsPoints' , $data ) : array('');
		$this->is_points = $is_points[0];
		
		$is_penalty = (method_exists($className, 'onAdminIsPenalty')) ? $appsLib->triggerEvent( 'onAdminIsPenalty' , $data ) : array('');
		$this->is_penalty = $is_penalty[0];
		
		$is_reportname = (method_exists($className, 'onAdminIsReportName')) ? $appsLib->triggerEvent( 'onAdminIsReportName' , $data ) : array('');
		$this->is_reportname = $is_reportname[0];
				
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
				
		$this->addToolbar($this->item);
		parent::display($tpl);
    }
        
    protected function addToolbar($item)
	{
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JFactory::getApplication()->input->set('hidemainmenu', true);
        if ($canDo->get('core.edit')) {
            JToolBarHelper::apply('question.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
        }
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('question.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW',
                false);
            JToolBarHelper::custom('question.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY',
                false);
        }
		JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
        if((int)$item->c_quiz_id) {
            JToolBarHelper::custom('question.preview_quest', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_QUEST_PREVIEW', false);
        }
		JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
	}
}
?>
