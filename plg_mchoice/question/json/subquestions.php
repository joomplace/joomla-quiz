<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 11.04.2017
 * Time: 14:02
 */

$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select($db->qn('c_id','id'))
    ->select($db->qn('c_random','shuffle'))
    ->select($db->qn('c_question','text'))
    ->select($db->qn('c_point','points'))
    ->select($db->qn('c_partial','partial'))
    ->select($db->qn('c_attempts','attempts'))
    ->select($db->qn('c_feedback','feedback'))
    ->select($db->qn('c_right_message','feedback_correct'))
    ->select($db->qn('c_wrong_message','feedback_incorrect'))
    ->select($db->qn('c_partially_message','feedback_partial'))
    ->from($db->qn('#__quiz_t_question', 'q'))
    ->where($db->qn('parent_id').' = '.$db->q($displayData));
$sub_quests = $db->setQuery($query)->loadObjectList();
$query->clear()
    ->select('*')
    ->from($db->qn('#__quiz_options', 'o'));
$sub_quests = array_map(function($quest) use($db,$query){
    $query->clear('where')
        ->where($db->qn('question').' = '.$db->q($quest->id));
    $quest->shuffle = (bool)$quest->shuffle;
    $quest->attempts = (int)$quest->attempts;
    $quest->points = (float)$quest->points;
    $quest->partial = (bool)$quest->partial;
    $quest->feedback = (bool)$quest->feedback;
    $quest->options = array_map(function($option){;
        $option->points = (float)$option->points;
        $option->right = $option->right?true:false;
        return $option;
    }, $db->setQuery($query)->loadObjectList());
    return $quest;
},$sub_quests);
echo json_encode($sub_quests);