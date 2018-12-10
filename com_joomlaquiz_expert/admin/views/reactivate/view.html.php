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
class JoomlaquizViewReactivate extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
    public function display($tpl = null) 
    {
		$submenu = 'reactivate';
		JoomlaquizHelper::showTitle($submenu);	 
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->lists	= $this->get('Lists');
		
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
	
		$this->addToolbar();
		parent::display($tpl);
    }
        
    protected function addToolbar()
	{
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('reactivate.apply_reactivate', 'save-new.png', 'save-new_f2.png',
                'COM_JOOMLAQUIZ_REACTIVATE', false);
        }
		JToolBarHelper::cancel('reactivate.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
	}
}
?>
