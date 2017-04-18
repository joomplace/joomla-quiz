<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

/** @var \Joomla\Registry\Registry $data */

$quiz_result = $displayData['quiz_result_id'];
$data = new Joomla\Registry\Registry($displayData['question']);
$params = new Joomla\Registry\Registry($data->get('params'));

$question_data = json_decode(JLayoutHelper::render('question.json.subquestions', $data->get('c_id'), JPATH_SITE.'/plugins/joomlaquiz/mchoice/'));
?>
<div class="question">
    <?php
    /*
     * diplayed already somehow
    ?>
    <div class="description">
        <?= $data->get('c_question','') ?>
    </div>
    */
    ?>
    <?php
        array_map(function ($question) use ($quiz_result){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('*')
                ->from($db->qn('#__quiz_r_student_question'))
                ->where($db->qn('c_stu_quiz_id').' = '.$db->q($quiz_result))
                ->where($db->qn('c_question_id').' = '.$db->q($question->id));
            $result = $db->setQuery($query)->loadObject();
            $query->clear()
                ->select($db->qn('c_choice_id'))
                ->from($db->qn('#__quiz_r_choice'))
                ->where($db->qn('c_sq_id').' = '.$db->q($result->c_id));
            $answers = $db->setQuery($query)->loadColumn();
            $query->clear()
                ->select($db->qn('c_id'))
                ->from($db->qn('#__quiz_r_student_question'))
                ->where($db->qn('c_question_id').' = '.$db->q($question->id));
            $statistic = array();
            $statistic['res_ids'] = $db->setQuery($query)->loadColumn();
            $statistic['total'] = count($statistic['res_ids']);

            $query->clear()
                ->select('COUNT(*) AS `picked`')
                ->select($db->qn('c_choice_id'))
                ->from($db->qn('#__quiz_r_choice'))
                ->where($db->qn('c_sq_id').' IN ('.implode(',', $statistic['res_ids']).')')
                ->group($db->qn('c_choice_id'));
            $statistics = $db->setQuery($query)->loadObjectList('c_choice_id');
            foreach ($statistics as $stat){
                $stat->total = $statistic['total']?$statistic['total']:1;
                $stat->percentage = round($stat->picked/$stat->total * 100);
            }
            ?>
        <div class='jq_feedback_question_content'>
            <?= $question->text ?>
            <table class="jq_feedback_question_content_inner">
                <tr class='jq_feedback_question_content_header'>
                    <td class='jq_feedback_question_content_col_wide'><?= JText::_('COM_JQ_POSSIBLE_ANSWERS') ?></td>
                    <td class='jq_feedback_question_content_col_narrow'><?= JText::_('COM_QUIZ_CORRECT') ?></td>
                    <td class='jq_feedback_question_content_col_narrow'><?= JText::_('COM_JQ_YOUR_CHOICE') ?></td>
                    <td class='jq_feedback_question_content_col_narrow'><?= JText::_('COM_JQ_PEOPLE_STATISTIC') ?></td>
                </tr>
                <?php
            array_map(function($option) use ($answers, $statistics){
                $data = new Joomla\Registry\Registry($option);
                $data->set('stats', $statistics[$data->get('id')]);
                if(in_array($data->get('id'),$answers)){
                    $data->set('picked', true);
                }
                echo JLayoutHelper::render('question.results.option', $data, JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
            }, $question->options);
            ?>
                <tr>
                    <td colspan='4' class='review_statistic'><?= JText::_('COM_QUIZ_RST_PANSW')." ".$statistic['total']." ".JText::_('COM_QUIZ_RST_PANSW_TIMES') ?></td>
                </tr>
                <tr>
                    <td colspan='4' valign='top'><br /><strong><?= JText::_('COM_QUIZ_RES_MES_SCORE')." ".$result->c_score ?></strong><br /></td>
                </tr>
            </table>
        </div>
            <?php

        },$question_data);
    ?>
</div>