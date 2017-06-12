<?php
/**
 * JoomlaQuiz module for Joomla
 * @version $Id: default.php 2011-03-03 17:30:15
 * @package JoomlaQuiz
 * @subpackage default.php
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// no direct access

defined('_JEXEC') or die;

?>
<style>
    span[class*="quiz-"]:before {
        content: "";
        display: inline-block;
        width: 10px;
        height: 10px;
        margin-right: 5px;
    }
    tr.quiz-passed > td,
    span.quiz-passed:before{
        background: rgba(64,128,0,0.2)!important;
    }
    tr.quiz-failed > td,
    span.quiz-failed:before{
        background: rgba(128,0,0,0.2)!important;
    }
    tr.quiz-pending > td,
    span.quiz-pending:before{
        background: rgba(256,256,0,0.2)!important;
    }
    tr.quiz-na > td,
    span.quiz-na:before{
        background: rgba(192,192,192,0.2)!important;
    }
</style>
<table class="table">
    <thead>
    <tr>
        <th>
            <?= JText::_('QUIZ_MOD_SUMMARY_QUIZ_NAME') ?>
        </th>
        <th>
            <?= JText::_('QUIZ_MOD_SUMMARY_QUIZ_SCORE') ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($results){
        array_map(function($quiz){
            ?>
            <tr class="quiz-<?= $quiz->status==2?'passed':($quiz->status==1?'failed':($quiz->status==='0'?'pending':'na')) ?>">
                <td><?= $quiz->c_title ?></td>
                <td><?= $quiz->score ?></td>
            </tr>
            <?php
        },$results);
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2">
            <b><?= JText::_('QUIZ_MOD_SUMMARY_LEGEND') ?></b>
            <table>
                <tr>
                    <td>
                        <span class="quiz-passed"><?= JText::_('QUIZ_MOD_SUMMARY_PASSED') ?></span>
                        <br/>
                        <span class="quiz-pending"><?= JText::_('QUIZ_MOD_SUMMARY_PENDING') ?></span>
                    </td>
                    <td>
                        <span class="quiz-failed"><?= JText::_('QUIZ_MOD_SUMMARY_FAILED') ?></span>
                        <br/>
                        <span class="quiz-na"><?= JText::_('QUIZ_MOD_SUMMARY_NA') ?></span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tfoot>
</table>