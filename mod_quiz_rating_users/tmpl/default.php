<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_quiz_rating_users
 *
 * @copyright   Copyright (C) JoomPlace, www.joomplace.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$k = 1;
?>
<div class="mod_quiz_rating_users-module<?php echo $moduleclass_sfx; ?>">
    <?php if(empty($list)): ?>
        <?php echo JText::_('MOD_QUIZ_RATING_USERS_FE_NO_ITEMS'); ?>
    <?php else: ?>
        <?php foreach ($list as $item): ?>
            <?php
            echo $k . '. ' . $item->name . '<br />';
            $k++;
            ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>