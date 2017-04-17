<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 14.04.2017
 * Time: 16:21
 */

$data = new stdClass();
$data->question_id = $displayData->c_id;
$data->c_stu_quiz_id = $displayData->c_stu_quiz_id;

$data = JLayoutHelper::render('question.json.report', $data, JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
$data = json_decode($data);

array_map(function($question){
    ?>
    <div>
        <label>
            <b>Question text:</b>
        </label>
        <?= $question->text ?>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th colspan="2">
                    Option data
                </th>
                <th>
                    User answer
                </th>
            </tr>
        </thead>
        <tbody>
    <?php
    array_map(function($option){
        ?>
        <tr>
            <td width="20px"><?= $option->right?'<i class="icon-checkmark"></i>':'' ?></td>
            <td width="30%"><?= $option->text ?></td>
            <td><i class="icon-checkbox<?= $option->picked?'':'-unchecked'  ?>"> </i> </td>
        </tr>
        <?php
    },$question->options);
    ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">
                    Result: <?= $question->is_correct?'Correct':'' ?>
                </th>
            </tr>
        </tfoot>
    </table>
    <?php
},$data);