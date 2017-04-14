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
    protected $quizes;
    protected $ordering_list;
	
    public function display($tpl = null) 
    {
		$app = JFactory::getApplication();
        $submenu = 'questions';
        JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		
 		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		

		$app = JFactory::getApplication();
		$filter_quiz_id = $app->getUserStateFromRequest('questions.filter.quiz_id', 'filter_quiz_id', '');
		
		$db    = JFactory::getDbo();
		if(!$filter_quiz_id){
			$c_id = $app->input->get('c_id', 0);
			if($c_id){
				$db->setQuery("SELECT `c_quiz_id` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
				$c_quiz_id = $db->loadResult();
			} else {
				$c_quiz_id = 0;
			}
			
			$filter_quiz_id = $c_quiz_id;
		}
	
        $query="SELECT `ordering` as value, CONCAT(`ordering`, '. ', `c_question`) as text FROM #__quiz_t_question WHERE `c_quiz_id` = '".$filter_quiz_id."' ORDER BY `ordering`";
        $db->setQuery($query);
        $ordering_list = $db->loadObjectlist();

        $this->item->ordering_list = JHTML::_("select.genericlist", $ordering_list, 'ordering', 'class="text_area" size="1"', 'value', 'text', $this->item->ordering);

		$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
		if($this->item->c_id){
			$new_qtype_id = $this->item->c_type;
		}
	
		$data = array();
		$type = JoomlaquizHelper::getQuestionType($new_qtype_id);
		$this->type = $type;
		$data['quest_type'] = $type;
		$data['question_id'] = $this->item->c_id;
		
		$className = 'plgJoomlaquiz'.ucfirst($type);
		$appsLib = JqAppPlugins::getInstance();
		$appsLib->loadApplications();
		
        $this->quizzes		= $this->get('QuizzesList');
        $this->ordering_list = $this->get('Ordering');

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
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
				
		$this->addToolbar();
		parent::display($tpl);
    }
        
    protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user		= JFactory::getUser();
		$isNew		= ($this->item->c_id == 0);
		JToolBarHelper::apply('question.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('question.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::custom('question.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::custom('question.preview_quest', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_QUEST_PREVIEW', false);
		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
	}
}
?>
