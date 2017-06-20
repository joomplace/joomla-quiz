<?php
/**
 * JoomlaQuiz module for Joomla
 * @version $Id: default.php 2017-16-01 13:30:15
 * @package JoomlaQuiz
 * @subpackage default.php
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;
?>

<div>
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th><?php echo JText::_('MOD_QUIZ_COMPLETED_LP_TITLE'); ?></th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <?php if (!$result) { ?>
            <tbody>
            <tr>
                <td>No results</td>
            </tr>
            </tbody>
        <?php } else { ?>
            <tbody>
            <?php foreach ($result as $lp) {
                ?>
                <tr>
                    <td><?php echo $lp->title; ?></td>
                    <td><?php echo $lp->category; ?></td>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=lpath&lpath_id=' . $lp->id); ?>"><?php echo JText::_('MOD_QUIZ_COMPLETED_LP_LINK'); ?></a>
                    </td>
                </tr>
                <?php
            } ?>
            </tbody>
        <?php }
        ?>
    </table>
</div>

