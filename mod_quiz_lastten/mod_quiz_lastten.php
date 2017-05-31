<?php
/**
* JoomlaQuiz module for Joomla
* @version $Id: mod_quiz_lastten.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage mod_quiz_lastten.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document 	= JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/assets/css/joomlaquiz.css');

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$user_profile		= intval( $params->get( 'user_profile', 1 ) );
$m_user_display  	= intval( $params->get( 'user_display', 0 ) );
$v_content_count 	= intval( $params->get( 'quiz_count', 10 ) );
$moduleclass_sfx 	= $params->get( 'moduleclass_sfx', '' );

$result = modLasttenHelper::getResult($params);

require JModuleHelper::getLayoutPath('mod_quiz_lastten', $params->get('layout', 'default'));

?>