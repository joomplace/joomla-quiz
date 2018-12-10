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
 * Reactivates HTML View class for the Joomlaquiz Deluxe Component
 */
 
class JoomlaquizViewReactivates extends JViewLegacy
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
        $submenu = 'reactivates';
		JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
        $this->messageTrigger = $this->get('CurrDate');
		JoomlaquizHelper::addPaymentsSubmenu('reactivates');
        $this->addToolBar();
        	        	 
        $items 		= $this->get('Items');
        $pagination = $this->get('Pagination');
        $state		= $this->get('State');
        
		$usersFields = JHTML::_('select.options', $this->get('Users'), 'value', 'text', $app->getUserStateFromRequest('payments.filter.user_id', 'filter_user_id') );
		
		JHtmlSidebar::addFilter(
			JText::_('COM_JOOMLAQUIZ_SELECT_USER'),
			'filter_user_id',
			$usersFields
		);		
		
        if (!empty($errors = $this->get('Errors')))
        {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }
              
        $this->items = $items;
        $this->pagination = $pagination;
		$this->state = $state;
		$this->model = $this->getModel();
		
		$this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }
 
    /**
    * Setting the toolbar
    */
	protected function addToolBar() 
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('reactivate.edit');
        }
    }
	
	protected function getSortFields()
	{
		return array(
			'order_id' => JText::_('COM_JOOMLAQUIZ_ORDER'),
			'order_status_name' => JText::_('COM_JOOMLAQUIZ_ORDER_STATUS'),
			'name' => JText::_('COM_JOOMLAQUIZ_USER_NAME')
		);
	}
}
