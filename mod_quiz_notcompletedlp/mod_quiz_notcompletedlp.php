<?php
/**
* JoomlaQuiz module for Joomla
* @version $Id: mod_quiz_notcompletedlp.php 2017-16-01 13:30:15
* @package JoomlaQuiz
* @subpackage mod_mod_quiz_notcompletedlp.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$document 	= JFactory::getDocument();

require_once __DIR__ . '/helper.php';

$result = modNotcompletedlpHelper::getResult();

require JModuleHelper::getLayoutPath('mod_quiz_notcompletedlp', $params->get('layout', 'default'));

?>