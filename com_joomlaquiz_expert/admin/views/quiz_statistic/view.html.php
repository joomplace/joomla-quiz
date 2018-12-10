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

class JoomlaquizViewQuiz_Statistic extends JViewLegacy
{
	protected $items = null;

	function display($tpl = null)
	{
		// Get application
		$app = JFactory::getApplication();
		$context = "joomlaquiz.list.admin.joomlaquiz";

		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');

		$this->items = $this->get('Items');
		$this->state		= $this->get('State');
		$this->pagination	= $this->get('Pagination');

		$this->filter_order 	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'name', 'cmd');
		$this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');

		$this->filterForm    	= $this->get('FilterForm');
		
		$this->activeFilters 	= $this->get('ActiveFilters');

		if (!empty($errors = $this->get('Errors')))
		{
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

		$this->addToolBar();

		parent::display($tpl);

		$this->setDocument();
	}

	protected function addToolBar()
	{
		//$input = JFactory::getApplication()->input;

		$title = JText::_('COM_JOOMLAQUIZ').': '.JText::_('COM_JOOMLAQUIZ_REALTIME_TRACK');

		JToolBarHelper::title($title, 'joomlaquiz');
	}

	/**
	 * Set page parametres.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
	}
}
