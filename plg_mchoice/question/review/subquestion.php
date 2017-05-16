<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

jimport('question.helper',JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
/** @var \Joomla\Registry\Registry $data */
$displayData = \Joomplace\Quiz\Question\Mchoice\Helper::addResultStatistic($displayData, $displayData->result_id);
$data = new Joomla\Registry\Registry($displayData);

$options = $data->get('options',array());
$right_options = count(array_filter($options,function ($opt){
    return $opt->right > 0;
}));
$type = $right_options>1?'checkbox':'radio';
if($data->get('shuffle')){
    shuffle($options);
}

$review = $data->get('review',false);

$optionsHtml = array_map(function($option) use ($type, $review){
    $option = new \Joomla\Registry\Registry($option);
    $option->set('type',$type);
    return JLayoutHelper::render('question.review.option', $option);
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
                    <?= implode("\n",$optionsHtml) ?>
                </div>
            </div>
        </div>
        <div class="review_statistic">
            <b><?= JText::_('COM_QUIZ_ANSWER'); ?></b><br/>
            <?= implode('; ',array_filter(array_map(function($option) use ($type, $review){
                $option = new \Joomla\Registry\Registry($option);
                return $option->get('picked')?$option->get('text'):false;
            },$options))) ?>
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