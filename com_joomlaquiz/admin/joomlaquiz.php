<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_joomlaquiz'))
{
    throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

JLoader::register('JoomlaquizHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'joomlaquiz.php');
JLoader::register('plgJoomlaquizQuestion', JPATH_SITE . '/components/com_joomlaquiz/helpers/plgquestion.php');
 
$controller = JControllerLegacy::getInstance('Joomlaquiz');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();