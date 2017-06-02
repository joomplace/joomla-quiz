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
class JoomlaquizViewQuizcategory extends JViewLegacy
{
    protected $state;
    protected $item;
    protected $form;

    public function display($tpl = null)
    {
        $submenu = 'quiz';
        JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE . '/components/com_joomlaquiz/helpers/html');

        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $user = JFactory::getUser();
        $isNew = ($this->item->c_id == 0);
        JToolBarHelper::apply('quizcategory.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('quizcategory.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('quizcategory.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::custom('quizcategory.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        JToolBarHelper::cancel('quizcategory.cancel', 'JTOOLBAR_CANCEL');
        JToolBarHelper::divider();
        JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
    }
}

?>
