<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 19.07.2017
 * Time: 14:43
 */

class JFormFieldOptions extends JFormField {

    protected $type = 'options';

    public function renderField($options = array()) {
        return JLayoutHelper::render('options',$this,dirname(__FILE__).DIRECTORY_SEPARATOR.'layouts');
    }
}