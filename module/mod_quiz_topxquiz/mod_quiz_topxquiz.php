<?php
/**
* JoomlaQuiz module for Joomla
* @version $Id: mod_quiz_topxquiz.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage mod_quiz_topxquiz.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$v_content_count = intval( $params->get( 'quiz_count', 10 ) ); 
$m_user_display  = intval( $params->get( 'user_display', 0 ) );
$moduleclass_sfx = $params->get( 'moduleclass_sfx', '' );

if ($v_content_count == 0) {
	$v_content_count = 5;
}

$result = modTopxquizHelper::getResult($params);
require JModuleHelper::getLayoutPath('mod_quiz_topxquiz', $params->get('layout', 'default'));

?>