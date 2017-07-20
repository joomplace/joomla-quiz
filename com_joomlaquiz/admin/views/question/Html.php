<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 22.06.2017
 * Time: 15:02
 */

namespace Joomplace\Component\Joomlaquiz\Administrator\views\question;

use JoomPlaceX\Sidebar;
use JoomPlaceX\View;
use Joomplace\Component\Joomlaquiz\Administrator\Model\Quiz;

class Html extends View
{
    public function render($tpl = null)
    {
        switch ($this->getLayout()){
            case 'edit':
                break;
            default:
//                Sidebar::addEntry('Quizzes','index.php?option=com_joomlaquiz&view=quiz');
//                Sidebar::addEntry('Quizzes | Categories','index.php?option=com_categories&extension=com_joomlaquiz');
//                Sidebar::addEntry('Quizzes | Questions','index.php?option=com_joomlaquiz&view=question',true);
//                Sidebar::addEntry('Quizzes | Questions | Categories','index.php?option=com_categories&extension=com_joomlaquiz.question');

                $quiz_model = new Quiz();
                $quizzes = $quiz_model->getList(0,0, array(), 'stdClass');
                $pool_option = new \stdClass;
                $pool_option->id = 0;
                $pool_option->title = 'Questions Pool';
                array_unshift($quizzes, $pool_option);
                Sidebar::addFilter('-- Select quiz --','filter[quiz]', \JHtmlSelect::options($quizzes, 'id', 'title', isset($this->filter['quiz'])?$this->filter['quiz']:null));
                $this->sidebar = Sidebar::render();
        }
        return parent::render($tpl);
    }

}