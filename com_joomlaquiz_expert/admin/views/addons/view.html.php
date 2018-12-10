<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
 defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
 
/**
 * HTML View class for the Joomlaquiz Deluxe Component
 */
class JoomlaquizViewAddons extends JViewLegacy
{
    public $messageTrigger = false;

    public function display($tpl = null) 
    {
		$submenu = 'addons';

        $document = JFactory::getDocument();
        $document->addScript('components/com_joomlaquiz/assets/js/js.js');

		JoomlaquizHelper::showTitle($submenu);	 
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
        $this->messageTrigger = $this->get('CurrDate');
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
		
		JoomlaquizHelper::addSettingsSubmenu('addons');
		$this->sidebar = JHtmlSidebar::render();
		$this->addToolbar();
		parent::display($tpl);
    }
        
    protected function addToolbar()
	{
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('addons.install', 'save-new.png', 'save-new_f2.png', 'COM_JOOMLAQUIZ_INSTALL',
                false);
        }
	}
}
?>
