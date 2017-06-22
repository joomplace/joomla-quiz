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

class JoomlaquizViewDashboard extends JViewLegacy
{

    public $messageTrigger = false;

	function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
        $this->dashboardItems = $this->get('Items');
        
		/* db check and fix */
        $this->errors = array();
		if(JComponentHelper::getParams('com_joomlaquiz')->get('db_fix',0)){
			$this->db_state = $this->get('DatabaseState');
            $this->results = array();
			foreach ($this->db_state as $cs_state){
                $this->errors = array_merge($this->errors,$cs_state->check());
                $this->results = array_merge($this->results,$cs_state->getStatus());
            }
			if($this->errors){
				JToolbarHelper::custom('dashboard.fix', 'refresh', 'refresh', 'COM_JOOMLAQUIZ_MANAGER_DASHBOARD_FIX_DB', false);
			}	
			JToolbarHelper::custom('dashboard.fixencode', 'refresh', 'refresh', 'COM_JOOMLAQUIZ_MANAGER_DASHBOARD_FIX_DB_ENCODING', false);

			$lang = JFactory::getLanguage();
			$extension = 'com_installer';
			$base_dir = JPATH_ADMINISTRATOR;
			$language_tag = $lang->getTag();
			$reload = true;
			$lang->load($extension, $base_dir, $language_tag, $reload);
		}
		/* db check end */
		
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