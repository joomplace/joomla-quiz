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
* Questions HTML View class for the Joomlaquiz Deluxe Component
*/
 
class JoomlaquizViewQuestions extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
    public $messageTrigger = false;
	
    function display($tpl = null) 
	{		
            $document = JFactory::getDocument();
            $document->addScript('components/com_joomlaquiz/assets/js/js.js');
			$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
			$app = JFactory::getApplication();
			$layout = $app->input->get('layout');
            $this->messageTrigger = $this->get('CurrDate');
			$quiz_id = JFactory::getApplication()->input->get('quiz_id');
			
			if(isset($quiz_id) && !$quiz_id){
				JoomlaquizHelper::addQuestionsSubmenu('questions_pool');
			} elseif($layout == 'uploadquestions') {
				JoomlaquizHelper::addQuestionsSubmenu('uploadquestions');
			} else {
				JoomlaquizHelper::addQuestionsSubmenu('questions');
			}
			
			if($layout == 'copy_questions'){
				$submenu = 'copy_questions';
				JoomlaquizHelper::showTitle($submenu);
				$quizzes = JoomlaquizHelper::getQuizzesForSelect();
								
				$quizzesFields = JHTML::_('select.genericlist', $quizzes, 'quizcopy', 'class="input-medium" size="1"', 'value', 'text', 0); 
				$this->quizzesFields = $quizzesFields;
				$this->copy_questions = $this->get('CopyQuestions');
				
				$this->addCopyToolBar();
			}elseif($layout == 'move_questions'){
				$submenu = 'move_questions';
				JoomlaquizHelper::showTitle($submenu);
				$quizzes = JoomlaquizHelper::getQuizzesForSelect();

				$quizzesFields = JHTML::_('select.genericlist', $quizzes, 'quizmove', 'class="input-medium" size="1"', 'value', 'text', 0);
				$this->quizzesFields = $quizzesFields;
				$this->move_questions = $this->get('MoveQuestions');
				
				$this->addMoveToolBar();
			}elseif($layout == 'move_questions_cat'){
				$submenu = 'move_questions_cat';
				JoomlaquizHelper::showTitle($submenu);
				
				$questCatFields = JHTML::_('select.genericlist', $this->get("QuestionCategories"), 'catmove', 'class="input-medium" size="1"', 'value', 'text', 0); 
				$this->questCatFields = $questCatFields;
				$this->move_questions_cat = $this->get('MoveQuestionsCat');
				
				$this->addMoveCatToolBar();
			}elseif($layout == 'uploadquestions'){
				$submenu = 'uploadquestions';
				JoomlaquizHelper::showTitle($submenu);
				$quizzes = JoomlaquizHelper::getQuizzesForSelect();
				
				$quizzesFields = JHTML::_('select.genericlist', $quizzes, 'filter_quiz_id', 'class="input-medium" size="1" ', 'value', 'text', 0);
			
				$this->quizzesFields = $quizzesFields;
				
				$this->addUploadquestToolBar();
			} else {
				$submenu = 'questions';
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
				
				$enabled = array();
				$enabled[] = JHTML::_('select.option', 0, JText::_('COM_JOOMLAQUIZ_INVALIDE_QUESTION'));
				$enabled[] = JHTML::_('select.option', 1, JText::_('COM_JOOMLAQUIZ_ACTIVE_QUESTION'));
				$enabledFields = JHTML::_('select.options', $enabled, 'value', 'text', $app->getUserStateFromRequest('quizzes.filter.enabled', 'filter_enabled'));
				
				JHtmlSidebar::addFilter(
					JText::_('COM_JOOMLAQUIZ_SELECT_STATUS'),
					'filter_enabled',
					$enabledFields
				);
				
				if(isset($_REQUEST['quiz_id']) && $app->getUserState('quizzes.filter.quiz_id') != $_REQUEST['quiz_id'])
				{
					$app->setUserState('quizzes.filter.quiz_id', $_REQUEST['quiz_id'] );
				}

				$quizzes = JoomlaquizHelper::getQuizzesForSelect();

				$quizzesFields = JHTML::_('select.options', $quizzes, 'value', 'text', $state->get('filter.quiz_id'));
				
				JHtmlSidebar::addFilter(
					JText::_('COM_JOOMLAQUIZ_SELECT_QUIZ'),
					'quiz_id',
					$quizzesFields
				);
				
				$qtypesFields = JHTML::_('select.options', $this->get("QuestionType"), 'value', 'text', $app->getUserStateFromRequest('quizzes.filter.qtype_id', 'filter_qtype_id')); 
				
				JHtmlSidebar::addFilter(
					JText::_('COM_JOOMLAQUIZ_SELECT_QUESTION_TYPE'),
					'filter_qtype_id',
					$qtypesFields
				);
				
				$qcategoriesFields = JHTML::_('select.options', $this->get("QuestionCategories"), 'value', 'text', $app->getUserStateFromRequest('quizzes.filter.ques_cat', 'filter_ques_cat'));
				
				JHtmlSidebar::addFilter(
					JText::_('COM_JOOMLAQUIZ_NO_CATEGORY'),
					'filter_ques_cat',
					$qcategoriesFields
				);
				
				$this->pbreaks = $this->get("PageBreaks");
			}
		
		$this->sidebar = JHtmlSidebar::render();
		
        parent::display($tpl);
    }
	
	protected function addCopyToolBar(){
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
        if ($canDo->get('core.create') && $canDo->get('core.edit')) {
            JToolBarHelper::custom('questions.copy_question', 'copy.png', 'copy_f2.png', 'COM_JOOMLAQUIZ_COPY', false);
        }
	}

    protected function addMoveToolBar()
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
        if ($canDo->get('core.create') && $canDo->get('core.edit')) {
            JToolBarHelper::custom('questions.move_question', 'move.png', 'move_f2.png', 'COM_JOOMLAQUIZ_MOVE', false);
        }
    }
	
	protected function addMoveCatToolBar()
	{
		JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::custom('questions.move_question_cat_ok', 'move.png', 'move_f2.png', 'COM_JOOMLAQUIZ_MOVE_CAT', false);
	}
	
	protected function addUploadquestToolBar()
	{
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JToolBarHelper::cancel('questions.cancel', 'JTOOLBAR_CANCEL');
        if ($canDo->get('core.create') && $canDo->get('core.edit')) {
            JToolBarHelper::custom('questions.uploadquestions', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_UPLOAD', false);
        }
	}
	
    /**
    * Setting the toolbar
    */
    protected function addToolBar() 
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        $bar = JToolBar::getInstance('toolbar');
        if ($canDo->get('core.create')) {
            $bar->appendButton('Custom', '<div id="toolbar-new" class="btn-group"><a class="btn btn-small btn-success" onclick="javascript: tb_start(this);return false;" href="index.php?option=com_joomlaquiz&amp;tmpl=component&amp;task=questions.new_question_type&amp;KeepThis=true&amp;TB_iframe=true&amp;height=350&amp;width=700" href="#"><i class="icon-new icon-white"></i>' . JText::_('COM_JOOMLAQUIZ_NEW') . '</a></div>');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('question.edit');
            JToolBarHelper::custom('questions.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('questions.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'questions.checkComplitedQuestions');
        }
        if ($canDo->get('core.create') && $canDo->get('core.edit')) {
            JToolBarHelper::custom('questions.move_question_sel', 'move.png', 'move_f2.png', 'COM_JOOMLAQUIZ_MOVE', true);
            JToolBarHelper::custom('questions.copy_question_sel', 'copy.png', 'copy_f2.png', 'COM_JOOMLAQUIZ_COPY', true);
        }
		JToolBarHelper::custom('questions.quizzes', 'previous.png', 'previous_f2.png', 'COM_JOOMLAQUIZ_QUIZZES', false);
    }
	
	protected function getSortFields()
	{
		return array(
			'c_id' => JText::_('JGRID_HEADING_ID'),
			'c_question' => JText::_('COM_JOOMLAQUIZ_TEXT'),
			'published' => JText::_('JPUBLISHED'),
			'ordering' => JText::_('JORDERING'),
			'c_type' => JText::_('COM_JOOMLAQUIZ_TYPE'),
			'c_title' => JText::_('COM_JOOMLAQUIZ_QUIZ')
		);
	}
}
