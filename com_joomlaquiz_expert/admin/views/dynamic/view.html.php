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
class JoomlaquizViewDynamic extends JViewLegacy
{

    public function display($tpl = null) 
    {
		$submenu = 'dynamic';
		JoomlaquizHelper::showTitle($submenu);	 
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		$this->lists	= $this->get('Lists');
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
		
		JoomlaquizHelper::addReportsSubmenu('dynamic');
		$this->sidebar = JHtmlSidebar::render();
		$this->addToolbar();
		parent::display($tpl);
    }
        
    protected function addToolbar()
	{
		JToolBarHelper::custom('dynamic.get_dynamic_csv', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_CSV_REPORT', false);
		//JToolBarHelper::custom('dynamic.get_dynamic', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_VIEW_REPORT', false);
		JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
	}
}
?>
