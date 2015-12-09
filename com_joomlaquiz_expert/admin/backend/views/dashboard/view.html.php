<?php
/**
* Joomlaquiz component for Joomla 3.0
* @package Joomlaquiz
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class JoomlaquizViewDashboard extends JViewLegacy
{

    public $messageTrigger = false;

	function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
        $this->dashboardItems = $this->get('Items');
		$this->addToolBar();
		$this->setDocument();
        $this->messageTrigger = $this->get('CurrDate');
		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		JToolBarHelper::title(JText::_('COM_JOOMLAQUIZ').': '.JText::_('COM_JOOMLAQUIZ_MANAGER_DASHBOARD'), 'dashboard');
	}

	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JOOMLAQUIZ').': '.JText::_('COM_JOOMLAQUIZ_MANAGER_DASHBOARD'));
		$document->addScript('components/com_joomlaquiz/assets/js/js.js');
	}




}