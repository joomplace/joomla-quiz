<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.languagecode
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use \Joomplace\Component\Joomlaquiz\Administrator\Model\Question;
use \Joomplace\Plugin\Joomlaquiz\Choice\Model\Choice;

class PlgJoomlaquizChoice extends JPlugin
{
    /*
     * TODO: move to interface
     * move to interface start
     */
    public function __construct($subject, array $config = array())
    {
        parent::__construct($subject, $config);
        JLoader::registerNamespace('Joomplace\Plugin\Joomlaquiz\\'.ucfirst($this->getType()), dirname(__FILE__), false, false, 'psr4');
    }

    protected function typeStrict($type){
        if($type!=$this->getType()){
            return false;
        }else{
            return true;
        }
    }

    protected function renderLayout($layoutId, $data = array(), $path = null, $component = 'com_joomlaquiz'){
        $html = \JoomPlaceX\Helper::renderLayout($layoutId,$data,$path,$component,2);
        return $html;
    }

    protected function render($questionData = array()){
        return $this->renderLayout('quiz.question.'.$this->getType(), $questionData,dirname(__FILE__).DIRECTORY_SEPARATOR.'layouts');
    }

    // TODO: make abstract
    protected function getType(){
        return 'choice';
    }
    /*
     * move to interface stop
     */

    public function onQuestionPrepareForm(JForm $form,array $data,Joomla\Registry\Registry $params){
        if($this->typeStrict($data['type'])){
            jimport('joomla.form.formfield');
            $form->loadFile(dirname(__FILE__).DIRECTORY_SEPARATOR.'form'.DIRECTORY_SEPARATOR.'question.xml');
            $choiceModel = new Choice();
            $bind = new Joomla\Registry\Registry();
            $options = $choiceModel->getList(false, false, array('question'=>$data['id']),'stdClass');
            $bind->set('data.options',$options,'.');
            $form->bind($bind);
        }
        return true;
    }

    public function onQuestionBeforeStore(Question $question, array $data,Joomla\Registry\Registry $params){

        return true;
    }

    public function onQuestionAfterStore(Question $question, array $data,Joomla\Registry\Registry $params){
        if($this->typeStrict($data['type'])) {
            $form = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
            $options = json_decode($form['data']['options']);
            $questionId = $question->id;
            array_map(function ($option) use ($questionId) {
                if (!isset($option->id)) {
                    $option->id = null;
                }
                $option->question = $questionId;
                $choice           = new Choice($option->id);
                $choice->bind($option);
                $choice->store();
            }, $options);
            // TODO: add delete
        }

        return true;
    }

    public function onQuestionRender(Question $question){
        if($this->typeStrict($question->type)) {
            $choiceModel = new Choice();
            $options     = $choiceModel->getList(false, false,
                array('question' => $question->id), 'stdClass');
            $question = new \Joomla\Registry\Registry($question->getProperties());
            $question->set('options', $options);
            return $this->render($question);
        }
        return '';
    }
}
