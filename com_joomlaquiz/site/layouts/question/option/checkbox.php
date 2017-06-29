<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 11.04.2017
 * Time: 14:49
 */

/** @var \Joomla\Registry\Registry $data */
$data = $displayData;
$stats = $data->get('stats');
?>
<label>
    <?php if($stats!==null){ ?>
        <span class="stat"><?= $stats?$stats->percentage.'%':'0%' ?></span>
    <?php } ?>
    <input
        <?= $data->get('disabled')?'disabled':'' ?>
        type="checkbox"
        name="subquestion-<?= $data->get('question') ?>"
        value="<?= $data->get('id') ?>"
        <?= $data->get('show_correct',false)?($data->get('right',0)?'checked':''):($data->get('picked')?'checked':'') ?>
    />
    <?= $data->get('text') ?>
</label>