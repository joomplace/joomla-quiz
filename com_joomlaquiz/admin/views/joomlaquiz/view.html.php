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
 * HTML View class for the Joomlaquiz Deluxe Component
 */
class JoomlaquizViewJoomlaquiz extends JViewLegacy
{
        function display($tpl = null) 
        {
        	$submenu = 'about';
        	JoomlaquizHelper::showTitle($submenu);
        	$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
			
        	$document = JFactory::getDocument();
        	$document->addScript(JURI::root().'administrator/components/com_joomlaquiz/assets/js/js.js');
        	$this->version = JoomlaquizHelper::getVersion();
			$this->addToolbar();
            parent::display($tpl);
        }
        
        protected function addToolbar(){
            $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
            if ($canDo->get('core.create')) {
                JToolbarHelper::preferences('com_joomlaquiz');
            }
        }
}