<?php

ob_start();
/*
 * TODO: move to the plugin file
 */
//$options = \Joomplace\Quiz\Plugin\Truefalse\Question()
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*')
    ->from($db->qn('#__quiz_t_choice'))
    ->where($db->qn('c_question_id').' = '.$db->q($displayData->get('c_id')));
$options = $db->setQuery($query)->loadObjectList();
?>
<div style="clear:both;">
    <?= $displayData->get('c_question','') ?>
</div>
<div class="jq_question_answers_cont">
    <div style="width:100%;clear:both;" id="div_qoption<?= $displayData->get('c_id') ?>">
        <form onsubmit="javascript: return false;" name="quest_form<?= $displayData->get('c_id') ?>">
            <input type="hidden" value="0" name="c_qform">
            <table class="jq_mchoice">
                <tbody>
                    <?php
                    array_map(function($option)use($displayData){
                        ?>
                        <tr>
                            <td class="jq_input_pos">
                                <input id="quest_choice_<?= $option->c_id ?>"
                                                    name="quest_choice"
                                                    value="<?= $option->c_id ?>"
                                                    type="radio"><label
                                class="quest_pos"
                                for="quest_choice_<?= $option->c_id ?>"><?= $option->c_choice ?></label></td>
                        </tr>
                        <?php
                    },$options);
                    ?>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php
$markup = ob_get_contents();
ob_end_clean();

$displayData->set('markup',$markup);

echo JLayoutHelper::render('question.container',$displayData);
