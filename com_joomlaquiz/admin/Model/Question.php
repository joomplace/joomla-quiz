<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 22.06.2017
 * Time: 16:34
 */

namespace Joomplace\Component\Joomlaquiz\Administrator\Model;


use Joomla\Registry\Registry;

class Question extends \JoomPlaceX\Model
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
        ),
        'quiz' => array(
            'mysql_type' => 'int(11) unsigned',
            'type' => 'sql',
            'query' => 'SELECT id, title FROM #__jquiz_quiz',
            'key_field' => 'id',
            'value_field' => 'title',
        )
    );

    protected function determine()
    {
        $this->_table = '#__jquiz_question';
        $this->_context = 'com_joomlaquiz.question';
    }

    protected function preprocessForm(\JForm &$form)
    {
        $params = new Registry();

        \JPluginHelper::importPlugin('joomlaquiz');
        $dispatcher = \JEventDispatcher::getInstance();
        $dispatcher->trigger('onQuestionPrepareForm', array($form, $this->getProperties(), $params));

        parent::preprocessForm($form);
    }

    public function store($updateNulls = false)
    {
        $params = new Registry();

        \JPluginHelper::importPlugin('joomlaquiz');
        $dispatcher = \JEventDispatcher::getInstance();
        $result = $dispatcher->trigger('onQuestionBeforeStore', array($this, $this->getProperties(), $params));
        if(!array_filter($result,function($resp){return !$resp;})){
            if(parent::store($updateNulls)){
                $result = $dispatcher->trigger('onQuestionAfterStore', array($this, $this->getProperties(), $params));
                if(!array_filter($result,function($resp){return !$resp;})){
                    return true;
                }
            }
        }

        return false;
    }


}