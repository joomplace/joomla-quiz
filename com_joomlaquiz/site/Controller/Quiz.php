<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 18.07.2017
 * Time: 13:38
 */

namespace Joomplace\Component\Joomlaquiz\Site\Controller;


use Joomplace\Component\Joomlaquiz\Administrator\Model\Question;
use JoomPlaceX\Controller;

class Quiz extends Controller
{
    public function take($id){
        $quiz = $this->getModel(\Joomplace\Component\Joomlaquiz\Administrator\Model\Quiz::class, $id);
        if(!$quiz->id){
            \JFactory::getApplication()->enqueueMessage('Quiz is not exists','error');
            return false;
        }
        if(!$quiz->published){
            \JFactory::getApplication()->enqueueMessage('Quiz is not published','error');
            return false;
        }

        /** @var Question[] $questions */
        $questions = $this->getModel(Question::class)->getList(null, null, array('quiz'=>$quiz->id,'published'=>1));

        $vars = array(
            'quiz' => $quiz,
            'questions' => $questions,
        );

        $layout = $this->input->get('layout','quiz');
        $this->display('quiz.'.$layout, $vars);

        return true;
    }
}