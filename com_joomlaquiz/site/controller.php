<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaquiz Component Controller
 */
class JoomlaquizController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = array())
	{
		
		$user = JFactory::getUser();
		$view = JFactory::getApplication()->input->getCmd('view', 'quiz');
		$task = JFactory::getApplication()->input->getCmd('task');
		$rel_id = JFactory::getApplication()->input->getInt('rel_id', 0);
		$package_id = JFactory::getApplication()->input->getInt('package_id', 0);
		
        parent::display();
	}	
}