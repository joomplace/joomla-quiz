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
<form class="form-horizontal" assesmentTest>
    <?php
        $categories = array();
        echo implode("\n", array_map(function($question)use(&$categories){
            $categories[] = $question->catid;
            \JPluginHelper::importPlugin('joomlaquiz');
            $dispatcher = \JEventDispatcher::getInstance();
            $layout = implode("\n",$dispatcher->trigger('onQuestionRender', array($question)));
            return $layout;
        }, $questions));
        $categories = array_unique($categories);
    ?>
</form>
<hr/>
<div assesmentResults>
    <?php
        array_map(function($catid){
            $cat = JTable::getInstance('Category');
            $cat->load($catid);
        ?>
            <div style="border-bottom: 1px dashed #ddd; padding: 20px 0px 10px" categoryBlock="<?= $cat->id ?>">
                <b class="row-fluid">
                    <div class="col-xs-12 col-sm-8 span8">
                        <?= $cat->title ?>:
                    </div>
                    <div class="col-xs-12 col-sm-2 span2" categoryPoints>

                    </div>
                </b>
                <?= $cat->description ?>
            </div>
        <?php
        },$categories);
    ?>
</div>
<script>
    jQuery(document).ready(function($){
        $('[assesmentTest]').on('change','select',function(e){
            recalculateResults();
        });
        function recalculateResults(){
            var results = {};
            $('[assesmentTest] select option:selected').each(function(i,el){
                var cv = parseInt(results[$(this).closest('select').data('category')]);
                results[$(this).closest('select').data('category')] = (cv?cv:0)+parseInt($(this).data('points'));
            });
            $.each(Object.keys(results),function(i,el){
                $('[assesmentResults] [categoryBlock="'+el+'"] [categoryPoints]').html(results[el]);
            })
        }
    });
</script>
