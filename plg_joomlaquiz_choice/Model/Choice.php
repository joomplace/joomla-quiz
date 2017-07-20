<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 22.06.2017
 * Time: 16:34
 */

namespace Joomplace\Plugin\Joomlaquiz\Choice\Model;


class Choice extends \JoomPlaceX\Model
{

    protected function determine()
    {
        $this->_table = '#__jquiz_question_choice';
        $this->_context = 'joomla.question';
    }

}