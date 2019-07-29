<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
class JoomlaquizViewUserquiz extends JViewLegacy
{
    protected $pagination;
    protected $state;

    function display($tpl = null) 
	{
        $this->listQuizzes = $this->get('Quizzes');
        $this->current_quizName = $this->get('Quizname');
        $this->assignedUsers = $this->get('Assigned');
        $this->listUsers = $this->get('Users');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');

        $submenu = 'userquiz';
        JoomlaquizHelper::addQuizzesSubmenu($submenu);
		JoomlaquizHelper::showTitle($submenu);
		$this->addToolBar();
			
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
		    return false;
		}

        $this->sidebar = JHtmlSidebar::render();
		
        parent::display($tpl);
    }

    protected function addToolBar() 
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');

        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('userquiz.assign', 'thumbs-up', 'thumbs-up', 'COM_JOOMLAQUIZ_USERQUIZ_BUTTON_ASSIGN', false);
        }
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('userquiz.unassign', 'thumbs-down', 'thumbs-down', 'COM_JOOMLAQUIZ_USERQUIZ_BUTTON_UNASSIGN', false);
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::custom('userquiz.notify', 'users', 'users', 'COM_JOOMLAQUIZ_USERQUIZ_BUTTON_NOTIFY', false);
        }
    }
	
}