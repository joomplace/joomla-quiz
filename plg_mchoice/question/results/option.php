<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 12.04.2017
 * Time: 16:39
 */

/** @var Joomla\Registry\Registry $displayData */
?>
<tr class="jq_feedback_question_content_container">
    <td class="jq_feedback_question_content_col_wide"><?= $displayData->get('text') ?></td>
    <td class="jq_feedback_question_content_col_narrow">
        <?php if($displayData->get('picked')){ ?>
        <img src="<?= JUri::root(); ?>/components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_pretty_green/images/result<?= $displayData->get('right')?'_true_green':'_false' ?>.png" border="0">
        <?php } ?>
    </td>
    <td class="jq_feedback_question_content_col_narrow">
        <?php if($displayData->get('right')){ ?>
        <img src="<?= JUri::root(); ?>/components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_pretty_green/images/result_true<?= $displayData->get('picked')?'_green':'' ?>.png" border="0">
        <?php } ?>
    </td>
    <td class="jq_feedback_question_content_col_narrow"><?= $displayData->get('stats')->percentage ?>%</td>
</tr>
