<?php
/**
 * Joomlaquiz component for Joomla 3.0
 * @package Joomlaquiz
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class JoomlaquizControllerDashboard extends JControllerLegacy
{
	
	public function fix(){
		$model = $this->getModel('Dashboard');
		$model->fix();
		
		// Refresh versionable assets cache
		JFactory::getApplication()->flushAssets();

		$this->setRedirect(JRoute::_('index.php?option=com_joomlaquiz', false));
	}

	public function fixEncode(){
		$model = $this->getModel('Dashboard');
		$model->fixEncode();
	}

}
