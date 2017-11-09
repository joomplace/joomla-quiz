<?php
/**
 * Created by PhpStorm.
 * User: shvets_a
 * Date: 03.11.2017
 * Time: 18:37
 */
echo $this->loadTemplate('menu');
JHtml::_('jquery.framework');
?>
<script type="text/javascript">

    Joomla.submitbutton = function(task)
    {
        if (task != '') {
            Joomla.submitform(task, document.getElementById('cleaning-form'), true);
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
        }
    }
</script>
<div id="j-main-container" class="span12 form-horizontal">
    <div class="tab-content">
        <form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz'); ?>"
              enctype="multipart/form-data" method="post" name="adminForm" id="cleaning-form" class="form-validate">
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('cleaning_from'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('cleaning_from'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('cleaning_to'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('cleaning_to'); ?>
                </div>
            </div>
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </form>

    </div>
</div>
