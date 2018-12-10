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
 * Statistic HTML View class for the Joomlaquiz Deluxe Component
 */
 
class JoomlaquizViewStatistic extends JViewLegacy
{

    public $messageTrigger = false;

    function display($tpl = null) 
	{
		$submenu = 'statistic';

        $document = JFactory::getDocument();
        $document->addScript('components/com_joomlaquiz/assets/js/js.js');

		JoomlaquizHelper::showTitle($submenu);
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		JoomlaquizHelper::addReportsSubmenu('statistic');
		$this->sidebar = JHtmlSidebar::render();
        $this->messageTrigger = $this->get('CurrDate');
		$statistic		= $this->get('StatisticData');
		
		if (!empty($errors = $this->get('Errors')))
		{
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
		
		$this->statistic	= $statistic;
						
        parent::display($tpl);
    }
}
