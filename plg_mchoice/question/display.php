<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

/** @var \Joomla\Registry\Registry $data */
$data = $displayData;
$params = new Joomla\Registry\Registry($data->get('params'));

$question_data = json_decode(JLayoutHelper::render('question.json.subquestions', $data->get('c_id'), JPATH_SITE.'/plugins/joomlaquiz/mchoice/'));
if($data->get('c_random',0)){
    shuffle($question_data);
}


/*
 * GOVNOKOD_ENABLED
 */
$quit = false;
/*
 * GOVNOKOD_DISABLED
 */
$sub_questions = array_map(function($question) use (&$quit, $params){
//    return JLayoutHelper::render('question.subquestion', $question, JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
    if($quit){
        return false;
    }
    /*
     * GOVNOKOD_ENABLED
     */
    $lay = JLayoutHelper::render('question.subquestion', $question, JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
    if(!strpos($lay,'disabled') && $params->get('onebyone',0)){
        $quit = true;
    }
    return $lay;
    /*
     * GOVNOKOD_DISABLED
     */
},$question_data);
$sub_questions = array_filter($sub_questions);
?>
<style>
    .not-filled{
        box-shadow: 0px 0px 3px red;
    }
</style>
<div id="question-<?= $data->get('c_id'); ?>" class="question_content">
    <?php if($data->get('c_question')){
        ?>
        <div class="question_description">
            <?= $data->get('c_question','') ?>
        </div>
        <?php
    } ?>
    <div class="sub_questions" data-total="<?= count($question_data) ?>">
        <?= implode("\n",$sub_questions) ?>
    </div>
</div>
