<?php
/**
 * Created by PhpStorm.
 * User: shvets_a
 * Date: 03.11.2017
 * Time: 18:37
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JoomlaquizViewCleaning extends JViewLegacy
{
    protected $form;

    public function display($tpl = null) {

        $submenu = 'cleaning';
        JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');

        $this->form		= $this->get('Form');

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $user		= JFactory::getUser();
        $isNew		= ($this->item->c_id == 0);

        JToolBarHelper::custom('cleaning.cleaning', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_CLEANING_CLEANING', false);

    }
}