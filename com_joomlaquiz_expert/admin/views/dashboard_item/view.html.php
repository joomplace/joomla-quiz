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

class JoomlaquizViewDashboard_item extends JViewLegacy
{
    protected $form;
    protected $item;
    protected $state;
    public function display($tpl = null)
    {

        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');
        if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        $isNew = $this->item->id == 0;
        JToolBarHelper::title(JText::_('COM_JOOMLAQUIZ').': '.JText::_('COM_JOOMLAQUIZ_DASHBOARD_ITEM_EDITING'));
        $this->addToolBar();

        parent::display($tpl);
    }

    protected function addToolBar()
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        if ($canDo->get('core.edit')) {
            JToolBarHelper::apply('dashboard_item.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('dashboard_item.save', 'JTOOLBAR_SAVE');
        }
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('dashboard_item.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }
        JToolBarHelper::cancel('dashboard_item.cancel', 'JTOOLBAR_CANCEL');
    }
  
}
