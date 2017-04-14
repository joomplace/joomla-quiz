<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 14:47
 */

/** @var \Joomla\Registry\Registry $data */
$data = $displayData;
/** @var JForm $form */
$form = $data->get('form');
$item = $data->get('item');
echo JHtml::_('bootstrap.addTab', $data->get('TabSet'), 'question-details', JText::_('COM_JOOMLAQUIZ_QUESTION'));
?>
<div class="row-fluid">
    <div class="span12">
        <fieldset>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $form->getLabel('c_question'); ?>
                </div>
                <div class="controls">
                    <?php echo $form->getInput('c_question'); ?>
                </div>
            </div>
        </fieldset>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <fieldset>
            <?php if($this->is_reportname):?>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $form->getLabel('report_name'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $form->getInput('report_name'); ?>
                    </div>
                </div>
            <?php endif;?>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $form->getLabel('published'); ?>
                </div>
                <div class="controls">
                    <?php echo $form->getInput('published'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $form->getLabel('c_show_timer'); ?>
                </div>
                <div class="controls">
                    <?php echo $form->getInput('c_show_timer'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $form->getLabel('c_time_limit'); ?>
                </div>
                <div class="controls">
                    <?php echo $form->getInput('c_time_limit'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $form->getLabel('c_separator'); ?>
                </div>
                <div class="controls">
                    <?php echo $form->getInput('c_separator'); ?>
                </div>
            </div>
            <?php
            if(count($add_form)){
                foreach($add_form as $for => $item){?>
                    <div class="control-group">
                        <?php if ($for=='c_qform')
                            echo $item['label'];
                        else {?>
                            <label class=" control-label" for="<?php echo $for;?>" id="<?php echo $for;?>-lbl" style="width:156px;"><?php echo $item['label']?></label>
                        <?php }?>
                        <div class="controls">
                            <?php echo $item['input']?>
                        </div>
                    </div>
                <?php }
            }
            ?>
        </fieldset>
    </div>
    <div  class="span6">
        <fieldset>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $form->getLabel('c_quiz_id'); ?>
                </div>
                <div class="controls">
                    <?php echo $form->getInput('c_quiz_id'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $form->getLabel('c_ques_cat'); ?>
                </div>
                <div class="controls">
                    <?php echo $form->getInput('c_ques_cat'); ?>
                </div>
            </div>
            <?php if($this->is_points):?>
                <div class="control-group">
                    <div class="control-class">
                        <?php echo $form->getLabel('c_point'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $form->getInput('c_point'); ?>
                    </div>
                </div>
            <?php endif;?>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $form->getLabel('c_attempts'); ?>
                </div>
                <div class="controls">
                    <?php echo $form->getInput('c_attempts'); ?>
                </div>
            </div>
            <?php if($this->is_penalty):?>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $form->getLabel('c_penalty'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $form->getInput('c_penalty'); ?>
                    </div>
                </div>
            <?php endif;?>
            <div class="control-group">
                <div class="control-label">
                    <?php echo JText::_('COM_JOOMLAQUIZ_ORDERING');?>
                </div>
                <div class="controls">
                    <?php echo $item->ordering_list;?>
                </div>
            </div>
        </fieldset>
    </div>
</div>
<?php
echo JHtml::_('bootstrap.endTab');