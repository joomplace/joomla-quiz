<?php
/**
* JoomlaQuiz module for Joomla
* @version $Id: mod_quiz_availablelp.php 2017-16-01 13:30:15
* @package JoomlaQuiz
* @subpackage mod_quiz_availablelp.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$document 	= JFactory::getDocument();

require_once __DIR__ . '/helper.php';

$result = modAvailablelpHelper::getResult();

require JModuleHelper::getLayoutPath('mod_quiz_availablelp', $params->get('layout', 'default'));

?>