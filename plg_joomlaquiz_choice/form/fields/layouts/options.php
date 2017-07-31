<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 19.07.2017
 * Time: 15:04
 */

/** @var JFormField $displayData */
JFactory::getDocument()->addScript('https://unpkg.com/vue');
JFactory::getDocument()->addScript('https://unpkg.com/sortablejs@1.4.2');
JFactory::getDocument()->addScript('https://unpkg.com/vue-sortable@0.1.3');
$options = new \Joomla\Registry\Registry;
$options->set('relative', true);
$options->set('pathOnly', true);
$file = 'quiz/question/form/choice.js';
$path = \JHtml::script($file,$options->toArray());
if(!$path){
    $path = dirname(__FILE__).DIRECTORY_SEPARATOR.'options.js';
    $path = str_replace(JPATH_SITE,'',$path);
}
JFactory::getDocument()->addScript($path);

//hide 'correct' field on question edit page
JFactory::getDocument()->addStyleDeclaration('.table thead th:nth-child(3),
                                              .table tr td:nth-child(3) {
                                                    display:none;
                                              }'
                                         );

$data = new \Joomla\Registry\Registry();
$data->set('options', $displayData->value?$displayData->value:array());
$data->set('deleteOptions', array());
$data->set('newOption', (object)array('text'=>'','right'=>($data->get('options')?false:true),'points'=>''));
?>
<script>
    var questionData = <?= $data->toString(); ?>;
</script>
<div id="choiceOptions" assignEnterHit="#addOption">
    <input type="hidden" v-bind:value="printData" name="<?= $displayData->name ?>" />
    <input type="hidden" v-bind:value="printDelete" name="<?= str_replace('options','deleteOptions',$displayData->name) ?>" />
    <table class="table table-striped">
        <thead>
            <tr>
                <th>
                    #
                </th>
                <th>
                    Option
                </th>
                <th>
                    Correct
                </th>
                <th>
                    Points
                </th>
                <th>
                </th>
            </tr>
        </thead>
        <tbody v-sortable="{onEnd: reorder}">
            <tr v-for="(option, i) in options" :key="option.ordering">
                <td>
                    {{i+1}}
                </td>
                <td>
                    <?php
                    // TODO: add content editable
                    ?>
                    {{option.text}}
                </td>
                <td>
                    {{option.right}}
                </td>
                <td>
                    {{parseFloat(option.points).toFixed(2)}}
                </td>
                <td>
                    <button type="button" class="btn btn-small" @click="deleteOption(i)">Delete</button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>
                </td>
                <td>
                    <input type="text" v-model="newOption.text" placeholder="new option"/>
                </td>
                <td>
                    <input type="checkbox" v-model="newOption.right"/>
                </td>
                <td>
                    <input type="text" v-model="newOption.points" placeholder="0"/>
                </td>
                <td>
                    <button type="button" class="btn btn-small" id="addOption" @click="addOption($event)">Add</button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>