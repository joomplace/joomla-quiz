<?php
/**
* Joomlaquiz component for Joomla 3.0
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JoomlaquizViewDashboard_Items extends JViewLegacy
{
    protected $items = null;
	function display($tpl = null)
	{
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');

		$this->items = $this->get('Items');
        $this->state		= $this->get('State');
        $this->pagination	= $this->get('Pagination');

		if (!empty($errors = $this->get('Errors')))
		{
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

		$this->addToolBar();
		$this->setDocument();

		parent::display($tpl);
	}

	protected function addToolBar()
	{
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
		JToolBarHelper::title(JText::_('COM_JOOMLAQUIZ').': '.JText::_('COM_JOOMLAQUIZ_MANAGER_DASHBOARD_ITEMS'), 'dashboard items');

        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('dashboard_items.add');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('dashboard_items.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'dashboard_items.delete', 'JTOOLBAR_DELETE');
        }
	}

	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JOOMLAQUIZ').': '.JText::_('COM_JOOMLAQUIZ_MANAGER_DASHBOARD_ITEMS'));
	}
}
