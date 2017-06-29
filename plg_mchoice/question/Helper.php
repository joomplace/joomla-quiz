<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 16.05.2017
 * Time: 12:04
 */

namespace Joomplace\Quiz\Question\Mchoice;


class Helper
{

    public static function addResultStatistic($question, $quiz_result){
        $db = \JFactory::getDbo();
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
        if(!$statistic['res_ids']){
            $statistic['res_ids'] = array(0);
        }
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

        $question->options = array_map(function($option) use ($answers, $statistics){
            $data = new \Joomla\Registry\Registry($option);
            $data->set('stats', $statistics[$data->get('id')]?$statistics[$data->get('id')]:false);
            if(in_array($data->get('id'),$answers)){
                $data->set('picked', true);
            }
            return $data->toObject();
        }, $question->options);

        return $question;
    }

}