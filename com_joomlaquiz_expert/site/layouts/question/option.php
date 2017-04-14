<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

/** @var \Joomla\Registry\Registry $data */
$data = $displayData;

echo JLayoutHelper::render('question.option.'.$data->get('type','radio'), $data);