<?php

namespace Joomplace\Component\Joomlaquiz\Administrator;

use JoomPlaceX\Sidebar;

class Dispatcher extends \JoomPlaceX\Dispatcher
{
    protected $namespace = __NAMESPACE__;
    protected $default_view = 'quiz';

    protected function addMustHaveButtons()
    {
        jimport('helpers.joomlaquiz',dirname(__FILE__));
        \JoomlaQuizHelper::addSubmenu($this->input->get('view'));
        parent::addMustHaveButtons();
    }


}