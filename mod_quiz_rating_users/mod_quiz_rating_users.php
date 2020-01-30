<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_quiz_rating_users
 *
 * @copyright   Copyright (C) JoomPlace, www.joomplace.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('ModQuizRatingUsersHelper', __DIR__ . '/helper.php');

$list            = ModQuizRatingUsersHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

require JModuleHelper::getLayoutPath('mod_quiz_rating_users', $params->get('layout', 'default'));