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
 * Quiz Categories HTML View class for the Joomlaquiz Deluxe Component
 */
 
class JoomlaquizViewCertificates extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
    public $messageTrigger = false;
    function display($tpl = null) 
	{
        $document = JFactory::getDocument();
        $document->addScript('components/com_joomlaquiz/assets/js/js.js');

        $submenu = 'certificates';
		JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
        $this->messageTrigger = $this->get('CurrDate');
		JoomlaquizHelper::addSettingsSubmenu('certificates');
		$this->sidebar = JHtmlSidebar::render();
        $this->addToolBar();        	 
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
            JToolBarHelper::addNew('certificate.add');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('certificate.edit');
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'certificates.delete');
        }
    }
	
	protected function getSortFields()
	{
		return array(
			'cert_name' => JText::_('COM_JOOMLAQUIZ_NAME'),
			'cert_file' => JText::_('COM_JOOMLAQUIZ_FILENAME')
		);
	}
}
