<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

/** @var \Joomla\Registry\Registry $data */
$data = $displayData;
$data->set('show_correct',true);
$data->set('disabled',true);
echo JLayoutHelper::render('question.option.'.$data->get('type','radio'), $data);