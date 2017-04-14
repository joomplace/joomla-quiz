<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 11.04.2017
 * Time: 14:49
 */

/** @var \Joomla\Registry\Registry $data */
$data = $displayData;
?>
<label>
    <input type="checkbox" name="subquestion-<?= $data->get('question') ?>" value="<?= $data->get('id') ?>" />
    <?= $data->get('text') ?>
</label>