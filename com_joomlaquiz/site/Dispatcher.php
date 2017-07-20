<?php

namespace Joomplace\Component\Joomlaquiz\Site;

class Dispatcher extends \JoomPlaceX\Dispatcher
{
    protected $namespace = __NAMESPACE__;
    protected $default_view = 'quiz';

    public function dispatch($task = null)
    {
        parent::dispatch($task);
    }

}