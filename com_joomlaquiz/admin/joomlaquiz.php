<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JLoader::registerNamespace('Joomplace\Component\Joomlaquiz\Administrator',JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_joomlaquiz',false,false,'psr4');
JLoader::registerNamespace('Joomplace\Quiz\Site',JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_joomlaquiz',false,false,'psr4');
$input = JFactory::getApplication()->input;
$component = new \Joomplace\Component\Joomlaquiz\Administrator\Dispatcher();
$component->dispatch($input->get('task'));

