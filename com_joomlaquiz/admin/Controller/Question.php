<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 22.06.2017
 * Time: 12:24
 */

namespace Joomplace\Component\Joomlaquiz\Administrator\Controller;

class Question extends \JoomPlaceX\Controller
{
    protected $_default_action = 'index';

    public function save2copy(array $jform){
        \JPluginHelper::importPlugin('joomlaquiz');
        $dispatcher = \JEventDispatcher::getInstance();
        $result = $dispatcher->trigger('onQuestionBeforeSave2Copy', array(&$jform));
        \JFactory::getApplication()->input->set('jform',$jform);
        $this->apply($jform, 'save2copy');
    }

}