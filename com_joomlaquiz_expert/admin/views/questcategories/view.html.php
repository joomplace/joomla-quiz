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
 * Question Categories HTML View class for the Joomlaquiz Deluxe Component
 */
 
class JoomlaquizViewQuestcategories extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
    public $messageTrigger = false;
    function display($tpl = null) 
	{
        $document = JFactory::getDocument();
        $document->addScript('components/com_joomlaquiz/assets/js/js.js');

        $submenu = 'questcategories';
		JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		
		JoomlaquizHelper::addQuestionsSubmenu('questcategories');
		$this->sidebar = JHtmlSidebar::render();
        $this->addToolBar();
        $this->messageTrigger = $this->get('CurrDate');
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
 			  
        parent::display($tpl);
    }
 
    /**
    * Setting the toolbar
    */
	protected function addToolBar() 
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('questcategory.add');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('questcategory.edit');
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'questcategories.delete');
        }
    }
	
	protected function getSortFields()
	{
		return array(
			'qc_category' => JText::_('COM_JOOMLAQUIZ_NAME'),
			'description' => JText::_('COM_JOOMLAQUIZ_DESCRIPTION'),
			'qc_tag' => JText::_('COM_JOOMLAQUIZ_MAIN_CATEGORY')
		);
	}
}
