<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

/** @var \Joomla\Registry\Registry $data */
$data = $displayData;
/** @var JForm $form */
$form = $data->get('form');
$item = $data->get('item');
echo JHtml::_('bootstrap.addTab', $data->get('TabSet'), 'question-extended', JText::_('COM_JOOMLAQUIZ_QUESTION_EXTENDED'));
$params = new \Joomla\Registry\Registry($item->params);
?>
    <div class="row-fluid">
        <div class="span6">
            <fieldset>
                <div class="control-group">
                    <div class="control-label">
                        <?php
                            $field_data = array(
                                'name' => 'jform[params][onebyone]',
                                'id' => 'jform_params_onebyone',
                                'label' => 'One by one',
                                'text' => 'One by one',
                                'description' => 'Sub questions will be shown one after another after answer to previous',
                                'class' => 'btn-group btn-group-yesno',
                                'value' => $params->get('onebyone', 0),
                                'options' => array(
                                    (object) array('text'=>JText::_('JYES'),'value'=>'1'),
                                    (object) array('text'=>JText::_('JNO'),'value'=>'0'),
                                )
                            );
                            echo JLayoutHelper::render('joomla.form.renderlabel',$field_data)
                        ?>
                    </div>
                    <div class="controls">
                        <?php
                            $field_data = array(
                                'name' => 'jform[params][onebyone]',
                                'id' => 'jform_params_onebyone',
                                'label' => 'One by one',
                                'text' => 'One by one',
                                'description' => 'Sub questions will be shown one after another after answer to previous',
                                'class' => 'btn-group btn-group-yesno',
                                'value' => $params->get('onebyone', 0),
                                'options' => array(
                                    (object) array('text'=>JText::_('JYES'),'value'=>'1'),
                                    (object) array('text'=>JText::_('JNO'),'value'=>'0'),
                                )
                            );
                            echo JLayoutHelper::render('joomla.form.field.radio',$field_data)
                        ?>
                    </div>
                </div>
            </fieldset>
        </div>
        <div  class="span6">

        </div>
    </div>
<?php
echo JHtml::_('bootstrap.endTab');