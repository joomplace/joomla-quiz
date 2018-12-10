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
 * Templates HTML View class for the Joomlaquiz Deluxe Component
 */
 
class JoomlaquizViewTemplates extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
    function display($tpl = null) 
	{
        $document = JFactory::getDocument();
        $document->addScript('components/com_joomlaquiz/assets/js/js.js');

		$app = JFactory::getApplication();
		$layout = $app->input->get('layout');
        $this->messageTrigger = $this->get('CurrDate');
		if($layout == 'edit_css'){
			
			$cid = $app->input->get('cid');

			$submenu = 'edit_css';
			JoomlaquizHelper::showTitle($submenu);
			$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
			$this->addCssToolBar();
			$model = JModelLegacy::getInstance("Templates", "JoomlaquizModel");
			$this->content = $model->getCssContent();
			$this->template = $model->getTemplateName();
			$this->cid = $cid;
		} else {
			$submenu = 'templates';
			JoomlaquizHelper::showTitle($submenu);
			$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
        
			JoomlaquizHelper::addSettingsSubmenu('templates');
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
 		}
		
        parent::display($tpl);
    }
 
    /**
    * Setting the toolbar
    */
	protected function addToolBar() 
    {
		/*
        JToolBarHelper::custom('templates.add_template', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_INSTALL', false);
		JToolBarHelper::custom('templates.del_template', 'delete.png', 'delete_f2.png', 'COM_JOOMLAQUIZ_UNINSTALL', true);
        JToolBarHelper::divider();
		*/

        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        if ($canDo->get('core.edit')) {
            JToolBarHelper::custom('templates.edit_css', 'edit.png', 'edit_f2.png', 'COM_JOOMLAQUIZ_EDIT_CSS', true);
        }
    }
	
	protected function addCssToolBar() 
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        if ($canDo->get('core.edit')) {
            JToolBarHelper::custom('templates.save_css', 'save.png', 'save_f2.png', 'COM_JOOMLAQUIZ_SAVE', false);
        }
		JToolBarHelper::cancel('templates.cancel', 'JTOOLBAR_CANCEL');
	}
	
	protected function getSortFields()
	{
		return array(
			'template_name' => JText::_('COM_JOOMLAQUIZ_NAME')
		);
	}
}
