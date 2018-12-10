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
 * Products HTML View class for the Joomlaquiz Deluxe Component
 */
 
class JoomlaquizViewProducts extends JViewLegacy
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
        $submenu = 'products';
		JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
        $this->messageTrigger = $this->get('CurrDate');
		JoomlaquizHelper::addPaymentsSubmenu('products');
        $this->addToolBar();
        	        	 
        $items 		= $this->get('Items');
        $pagination = $this->get('Pagination');
        $state		= $this->get('State');
        
		$this->categoriesFields = $this->get('Categories');
		
		$quizzesFields = JHTML::_('select.options', $this->get('Quizzes'), 'value', 'text', $app->getUserStateFromRequest('products.filter.quiz_id', 'filter_quiz_id') );
		
		JHtmlSidebar::addFilter(
			JText::_('COM_JOOMLAQUIZ_SELECT_QUIZ'),
			'filter_quiz_id',
			$quizzesFields
		);
		
		$lpathsFields = JHTML::_('select.options', $this->get('Lpaths'), 'value', 'text', $app->getUserStateFromRequest('products.filter.lpath_id', 'filter_lpath_id') );
		
		JHtmlSidebar::addFilter(
			JText::_('COM_JOOMLAQUIZ_SELECT_LEARNING_PATH'),
			'filter_lpath_id',
			$lpathsFields
		);
		
        if (!empty($errors = $this->get('Errors')))
        {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }
              
        $this->items = $items;
        $this->pagination = $pagination;
		$this->state = $state;
		
		$this->sidebar = JHtmlSidebar::render();	
        parent::display($tpl);
    }
 
    /**
    * Setting the toolbar
    */
	protected function addToolBar() 
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('product.add');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('product.edit');
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'products.delete');
        }
    }
	
	protected function getSortFields()
	{
		return array(
			'name' => JText::_('COM_JOOMLAQUIZ_PRODUCT_NAME'),
			'product_sku' => JText::_('COM_JOOMLAQUIZ_PRODUCT_SKU'),
			'category_name' => JText::_('COM_JOOMLAQUIZ_CATEGORY_NAME')
		);
	}
}
