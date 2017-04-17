<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 14.04.2017
 * Time: 16:21
 */

$data = JLayoutHelper::render('question.json.subquestions', $displayData->question_id, JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
$data = json_decode($data);
$db = JFactory::getDbo();
$query = $db->getQuery(true);

$query->select($db->qn('q.c_question_id','q_id'))
    ->select($db->qn('q.c_score'))
    ->select($db->qn('q.is_correct'))
    ->select($db->qn('c.c_choice_id','choice'))
    ->from($db->qn('#__quiz_r_student_question','q'))
    ->leftJoin($db->qn('#__quiz_r_choice','c').' ON '.$db->qn('q.c_id').' = '.$db->qn('c.c_sq_id'))
    ->where($db->qn('q.c_question_id').' IN ('.implode(',',array_map(function($q)use($db){return $db->q($q->id);},$data)).')')
    ->where($db->qn('c_stu_quiz_id').'='.$db->q($displayData->c_stu_quiz_id));
$answers = $db->setQuery($query)->loadObjectList();
$data = array_map(function($question) use ($answers){
    $qanswers = array_filter($answers, function($ans) use (&$question){
        if($question->id == $ans->q_id){
            $question->is_correct = $ans->is_correct;
            $question->score = $ans->c_score;
            return true;
        }
        return false;
    });
    $choices = array_map(function($ans){
        return $ans->choice;
    },$qanswers);
    $question->options = array_map(function($option) use ($choices){
        $option->picked = in_array($option->id, $choices);
        return $option;
    },$question->options);
    return $question;
},$data);

echo json_encode($data);