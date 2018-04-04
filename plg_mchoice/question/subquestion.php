<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

jimport('question.Helper',JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
/** @var \Joomla\Registry\Registry $data */
$input = JFactory::getApplication()->input;
//$stated = \Joomplace\Quiz\Question\Mchoice\Helper::addResultStatistic($displayData,$input->get('stu_quiz_id'));
$data = new Joomla\Registry\Registry($displayData);

$options = $data->get('options',array());
$right_options = count(array_filter($options,function ($opt){
    return $opt->right > 0;
}));
$type = $right_options>1?'checkbox':'radio';
if($data->get('shuffle')){
    shuffle($options);
}

$session = JFactory::getSession();
$answer_session = $session->get('quiz.'.$input->get('stu_quiz_id',0).'.question.'.$data->get('parent_id',0));
$answers = array();
$disabled = false;
if($answer_session) {
    $answers_arr = json_decode($answer_session, true);
    $answers = isset($answers_arr[$data->get('id')]) ? $answers_arr[$data->get('id')] : array();
    $disabled_arr = json_decode($answer_session, true);
    $disabled = isset($disabled_arr[$data->get('id') . '_attempted']) ?
        $disabled_arr[$data->get('id') . '_attempted'] >= $data->get('attempts', 1000) : false;
}
$options = array_map(function($option) use ($type, /*$review,*/ $answers, $disabled){
    $option = new \Joomla\Registry\Registry($option);
    $option->set('type',$type);
    $option->set('picked',in_array($option->get('id'),$answers));
    $option->set('disabled',$disabled);
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