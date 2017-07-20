<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 18.07.2017
 * Time: 16:12
 */

/** @var \Joomplace\Component\Joomlaquiz\Administrator\Model\Quiz $quiz */
$quiz = $this->quiz;
/** @var \Joomplace\Component\Joomlaquiz\Administrator\Model\Question[] $questions */
$questions = $this->questions;

?>
<h2>
    <?= $quiz->title ?>
</h2>
<div>
    <?php
        echo implode('<hr/>', array_map(function($question){
            \JPluginHelper::importPlugin('joomlaquiz');
            $dispatcher = \JEventDispatcher::getInstance();
            $layout = implode("\n",$dispatcher->trigger('onQuestionRender', array($question)));
            return $layout;
        }, $questions));
    ?>
</div>
