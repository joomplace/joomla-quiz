<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

/**
* Quizzes HTML View class for the Joomlaquiz Deluxe Component
*/
 
class JoomlaquizViewQuizzes extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

    public $messageTrigger = false;

    function display($tpl = null) 
	{
        $document = JFactory::getDocument();
        $document->addScript('components/com_joomlaquiz/assets/js/js.js');
		$app = JFactory::getApplication();
		$layout = $app->input->get('layout');
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
        $this->messageTrigger = $this->get('CurrDate');
		if($layout == 'import_quizzes'){
			JoomlaquizHelper::addQuizzesSubmenu('import_quizzes');
		} else {
			JoomlaquizHelper::addQuizzesSubmenu('quizzes');
		}
				
		if($layout == 'copy_quizzes'){
			$submenu = 'copy_quizzes';
			JoomlaquizHelper::showTitle($submenu);
			
			$categoryFields = JHTML::_('select.genericlist', $this->get("Categories"), 'categorycopy', 'class="input-medium" size="1"', 'value', 'text', 0); 
			$this->categoryFields = $categoryFields;
			$this->copy_quizzes = $this->get('CopyQuizzes');
			
			$this->addCopyToolBar();
		} elseif($layout == 'move_quizzes'){
			$submenu = 'move_quizzes';
			JoomlaquizHelper::showTitle($submenu);
						
			$categoryFields = JHTML::_('select.genericlist', $this->get("Categories"), 'categorymove', 'class="input-medium" size="1"', 'value', 'text', 0); 
			$this->categoryFields = $categoryFields;
			$this->move_quizzes = $this->get('MovingQuizzes');
			
			$this->addMoveToolBar();		
		} elseif($layout == 'import_quizzes'){
			$submenu = 'import_quizzes';
			JoomlaquizHelper::showTitle($submenu);
			
			
			$this->addImportToolBar();
		} else {
			$submenu = 'quizzes';
			JoomlaquizHelper::showTitle($submenu);
			$this->addToolBar();
			
			$items 		= $this->get('Items');
			$pagination = $this->get('Pagination');
			$state		= $this->get('State');
			
			if (!empty($errors = $this->get('Errors')))
			{
                JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
				return false;
			}
			
			$this->items = $items;
			$this->pagination = $pagination;
			$this->state = $state;
			
			$categoryFields = JHTML::_('select.options', $this->get("Categories") ,'value', 'text', $app->getUserStateFromRequest('quizzes.filter.category_id', 'filter_category_id'));
			
			JHtmlSidebar::addFilter(
				JText::_('COM_JOOMLAQUIZ_SELECT_CATEGORY'),
				'filter_category_id',
				$categoryFields
			);
			
		}
		
		$this->sidebar = JHtmlSidebar::render();
		
        parent::display($tpl);
    }
	
	protected function addCopyToolBar(){
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JToolBarHelper::cancel('quiz.cancel', 'JTOOLBAR_CANCEL');
        if ($canDo->get('core.create') && $canDo->get('core.edit')) {
            JToolBarHelper::custom('quizzes.copy_quizzes', 'copy.png', 'copy_f2.png', 'COM_JOOMLAQUIZ_COPY', false);
        }
	}
	
	protected function addMoveToolBar() {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JToolBarHelper::cancel('quiz.cancel', 'JTOOLBAR_CANCEL');
        if ($canDo->get('core.create') && $canDo->get('core.edit')) {
            JToolBarHelper::custom('quizzes.move_quizzes', 'move.png', 'move_f2.png', 'COM_JOOMLAQUIZ_MOVE', false);
        }
	}
		
	protected function addImportToolBar()
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JToolBarHelper::cancel('quiz.cancel', 'JTOOLBAR_CANCEL');
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('quizzes.import_quizzes', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_IMPORT',
                false);
        }
    }
	
    /**
    * Setting the toolbar
    */
    protected function addToolBar() 
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');

        if ($canDo->get('core.create'))
        {
            JToolBarHelper::addNew('quiz.add');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('quiz.edit');
            JToolBarHelper::custom('quizzes.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('quizzes.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'quizzes.delete');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::custom('quizzes.export_quizzes', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_EXPORT', true);
            JToolBarHelper::custom('quizzes.export_quizzes_all', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_EXPORT_ALL', false);
        }
        if ($canDo->get('core.create') && $canDo->get('core.edit')) {
            JToolBarHelper::custom('quizzes.move_quiz_sel', 'move.png', 'move_f2.png', 'COM_JOOMLAQUIZ_MOVE', true);
            JToolBarHelper::custom('quizzes.copy_quiz_sel', 'copy.png', 'copy_f2.png', 'COM_JOOMLAQUIZ_COPY', true);
        }

        JToolBarHelper::custom('quizzes.quizcategories', 'previous.png', 'previous_f2.png', 'COM_JOOMLAQUIZ_CATEGORIES', false);

    }
	
	protected function getSortFields()
	{
		return array(
			'c_title' => JText::_('COM_JOOMLAQUIZ_NAME'),
			'c_author' => JText::_('COM_JOOMLAQUIZ_AUTHOR'),
			'c_category' => JText::_('COM_JOOMLAQUIZ_CATEGORY'),
			'published' => JText::_('JPUBLISHED'),
			'c_id' => JText::_('JGRID_HEADING_ID'),
			'c_full_score' => JText::_('COM_JOOMLAQUIZ_TOTAL_SCORE'),
			'c_passing_score' => JText::_('COM_JOOMLAQUIZ_PASSING_SCORE'),
			'c_time_limit' => JText::_('COM_JOOMLAQUIZ_TIME_LIMIT'),
			'c_created_time' => JText::_('COM_JOOMLAQUIZ_CREATED_ON')
		);
	}
	
}
