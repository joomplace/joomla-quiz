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
 * Learning Paths HTML View class for the Joomlaquiz Deluxe Component
 */
class JoomlaquizViewLpaths extends JViewLegacy
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
        $submenu = 'lpaths';
        JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE . '/components/com_joomlaquiz/helpers/html');

        JoomlaquizHelper::addQuizzesSubmenu('lpaths');
        $this->addToolBar();

        $items = $this->get('Items');
        $pagination = $this->get('Pagination');
        $state = $this->get('State');
        $this->messageTrigger = $this->get('CurrDate');
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->items = $items;
        $this->pagination = $pagination;
        $this->state = $state;

        $quizzesFields = JHTML::_('select.options', $this->get("Quizzes"), 'value', 'text', $app->getUserStateFromRequest('quizzes.filter.quiz_id', 'filter_quiz_id'));

        JHtmlSidebar::addFilter(
            JText::_('COM_JOOMLAQUIZ_SELECT_QUIZ'),
            'filter_quiz_id',
            $quizzesFields
        );

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        $bar = JToolBar::getInstance('toolbar');
        JToolBarHelper::addNew('lpath.add');
        JToolBarHelper::editList('lpath.edit');
        JToolBarHelper::divider();
        JToolBarHelper::custom('lpaths.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::custom('lpaths.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
        JToolBarHelper::divider();
        JToolBarHelper::deleteList('', 'lpaths.delete');
        JToolBarHelper::divider();
        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small" onclick="javascript: tb_start(this);return false;" href="index.php?option=com_joomlaquiz&amp;tmpl=component&amp;view=configuration&amp;KeepThis=true&amp;TB_iframe=true&amp;height=350&amp;width=700" href="#"><i class="icon-options"></i>' . JText::_('COM_JOOMLAQUIZ_OPTIONS') . '</a></div>');
    }

    protected function getSortFields()
    {
        return array(
            'title' => JText::_('COM_JOOMLAQUIZ_TITLE2'),
            'published' => JText::_('COM_JOOMLAQUIZ_PUBLISHED')
        );
    }
}
