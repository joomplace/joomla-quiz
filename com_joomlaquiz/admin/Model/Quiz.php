<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 22.06.2017
 * Time: 16:34
 */

namespace Joomplace\Component\Joomlaquiz\Administrator\Model;


class Quiz extends \JoomPlaceX\Model
{
    protected static $_fields = array(
        'published' => array(
            'mysql_type' => 'int(1) unsigned',
            'type' => 'radio',
            'class' => 'btn-group',
            'nullable' => false,
            'default' => 0,
            'option' => array(
                0 => 'JNO',
                1 => 'JYES',
            ),
            'hide_at' => array('list'),
        )
    );

    protected function determine()
    {
        $this->_table = '#__jquiz_quiz';
        $this->_context = 'com_joomlaquiz.quiz';
    }

}