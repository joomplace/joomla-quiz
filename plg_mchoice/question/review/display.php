<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

/** @var \Joomla\Registry\Registry $data */
list($data, $result_id) = $displayData;
$params = new Joomla\Registry\Registry($data->get('params'));

$question_data = json_decode(JLayoutHelper::render('question.json.subquestions', $data->get('c_id'), JPATH_SITE.'/plugins/joomlaquiz/mchoice/'));
if($data->get('c_random',0)){
    shuffle($question_data);
}

$sub_questions = $question_data;

$sub_questions = array_map(function($question)use($result_id){
    $question->review = true;
    $question->result_id = $result_id;
    return JLayoutHelper::render('question.review.subquestion', $question, JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
},$sub_questions);

$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('COUNT(*)')
    ->from($db->qn('#__quiz_r_student_question'))
    ->where($db->qn('c_question_id').'='.$db->q($data->get('c_id')));
$answered_times = $db->setQuery($query)->loadResult();
?>
<style>
    label span.stat {
        width: 2em;
        display: inline-block;
        margin-right: 1em;
        vertical-align: baseline;
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
    <div class="">
        <?= JText::_('COM_QUIZ_RST_PPAST').' '.$answered_times.' '.JText::_('COM_QUIZ_RST_PPAST_TIMES') ?>
    </div>
</div>
