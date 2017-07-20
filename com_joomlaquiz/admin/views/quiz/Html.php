<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 22.06.2017
 * Time: 15:02
 */

namespace Joomplace\Component\Joomlaquiz\Administrator\views\quiz;

use JoomPlaceX\Sidebar;
use JoomPlaceX\View;

class Html extends View
{
    public function render($tpl = null)
    {
        switch ($this->getLayout()){
            case 'edit':
                break;
            default:
//                Sidebar::addEntry('Quizzes','index.php?option=com_joomlaquiz&view=quiz',true);
//                Sidebar::addEntry('Quizzes | Categories','index.php?option=com_categories&extension=com_joomlaquiz');
//                Sidebar::addEntry('Quizzes | Questions','index.php?option=com_joomlaquiz&view=question');
                $this->sidebar = Sidebar::render();
        }
        return parent::render($tpl);
    }

}