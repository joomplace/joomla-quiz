<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

/** @var \Joomla\Registry\Registry $data */
$data = new Joomla\Registry\Registry($displayData);

$options = $data->get('options',array());
$right_options = count(array_filter($options,function ($opt){
    return $opt->right > 0;
}));
$type = $right_options>1?'checkbox':'radio';
if($data->get('shuffle')){
    shuffle($options);
}
$options = array_map(function($option) use ($type){
    $option = new \Joomla\Registry\Registry($option);
    $option->set('type',$type);
    return JLayoutHelper::render('question.option', $option);
},$options);
?>
<div>
    <div id="subquestion-<?= $data->get('id') ?>" data-id="<?= $data->get('id') ?>" class="subquestion_content">
        <div class="question_description">
            <?= $data->get('text','') ?>
        </div>
        <div class="question_options">
            <div class="form-control">
                <div class="controls">
                    <?= implode("\n",$options) ?>
                </div>
            </div>
        </div>
        <?php
            if($data->get('feedback')){
            ?>
                <div class="feedback-section"></div>
            <?php
            }
        ?>
    </div>
</div>