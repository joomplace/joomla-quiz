<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 20.07.2017
 * Time: 11:02
 */
/** @var \Joomla\Registry\Registry $displayData */
?>
<div class="control-group">
    <label class="control-label" for="choice<?= $displayData->get('id',0) ?>"><?= $displayData->get('title'); ?></label>
    <div class="controls">
        <select id="choice<?= $displayData->get('id',0) ?>" data-category="<?= $displayData->get('catid',0); ?>">
            <option value="" data-points="0">-- make a selection --</option>
            <?php
            array_map(function ($option){
                ?>
                <option value="<?= $option->id ?>" data-points="<?= $option->points ?>"><?= $option->text ?></option>
                <?php
            },$displayData->get('options',array()));
            ?>
        </select>
    </div>
</div>