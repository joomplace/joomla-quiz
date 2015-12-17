<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

JLoader::register('JoomlaquizHelper', JPATH_SITE . '/components/com_joomlaquiz/helpers/joomlaquiz.php');
JLoader::register('plgJoomlaquizQuestion', JPATH_SITE . '/components/com_joomlaquiz/helpers/plgquestion.php');
JoomlaquizHelper::isJoomfish();

$controller = JControllerLegacy::getInstance('Joomlaquiz');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();