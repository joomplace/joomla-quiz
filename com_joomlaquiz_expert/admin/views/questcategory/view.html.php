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
class JoomlaquizViewQuestcategory extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
    public function display($tpl = null) 
    {
		$submenu = 'questcategory';
		JoomlaquizHelper::showTitle($submenu);	 
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->qc_tag_dd = JHTML::_('select.genericlist', $this->get('Tags'), 'qc_tag_dd', 'class="" size="1"', 'value', 'text', $this->item->qc_tag );
		
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
        JFactory::getApplication()->input->set('hidemainmenu', true);
        if ($canDo->get('core.edit')) {
            JToolBarHelper::apply('questcategory.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('questcategory.save', 'JTOOLBAR_SAVE');
        }
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('questcategory.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW',
                false);
            JToolBarHelper::custom('questcategory.save2copy', 'save-copy.png', 'save-copy_f2.png',
                'JTOOLBAR_SAVE_AS_COPY', false);
        }
		JToolBarHelper::cancel('questcategory.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
	}
}
?>
