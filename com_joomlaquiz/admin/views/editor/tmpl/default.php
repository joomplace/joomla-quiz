<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

$editor = JFactory::getEditor();
$id = JFactory::getApplication()->input->get('id', '');
?>
<script language="javascript" type="text/javascript">
    function getObj_frame(name) {
        if (parent.document.getElementById) {
            return parent.document.getElementById(name);
        }
        else if (parent.document.all) {
            return parent.document.all[name];
        }
        else if (parent.document.layers) {
            return parent.document.layers[name];
        }
    }

    function getObj(name) {
        if (document.getElementById) {
            return document.getElementById(name);
        }
        else if (document.all) {
            return document.all[name];
        }
        else if (document.layers) {
            return document.layers[name];
        }
    }

    function get_content() {
        var qwerty = getObj_frame('test_<?php echo $id;?>').innerHTML;
        WFEditor.setContent('cdescription', qwerty);
    }

    function save_content() {
        getObj_frame('test_<?php echo $id;?>').innerHTML =    <?php echo $editor->getContent('cdescription');    ?>;

        try {
            getObj_frame('ta_<?php echo $id;?>').innerHTML = getObj_frame('test_<?php echo $id;?>').innerHTML;
        } catch (e) {
        }
        try {
            getObj_frame('ta_<?php echo $id;?>').value = getObj_frame('test_<?php echo $id;?>').innerHTML;
        } catch (e) {
        }
        parent.tb_remove();
    }
</script>

<?php JoomlaquizControllerQuestion::JQ_editorArea('cdescription', '', 'cdescription', '100%;', '250', '75', '20'); ?>

<style type="text/css">
    input.button2 {
        font-size: 1.1em;
        background: #FAFAFA none repeat-x scroll center top;
        color: #000000;
        font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
        padding-bottom: 1px;
        padding-top: 1px;
        width: auto !important;
        border: 1px solid #666666;
        cursor: pointer;
        background-color: #FAFAFA;
        background-image: url(<?php echo JURI::root();?>administrator/components/com_joomlaquiz/images/bg_button.gif);
        color: #000000;
    }

    input.button2:hover, input.button3:hover {
        background-position: 0 100%;
        border: 1px solid #BCBCBC;
        border-color: #BC2A4D;
        color: #BC2A4D;
    }
</style>
<div style="width:100%; text-align:right;">
    <input type="button" value="<?php echo JText::_('COM_JOOMLAQUIZ_CANCEL'); ?>" class="button2"
           onclick="javascript: parent.tb_remove();"/>
    <input type="button" value="<?php echo JText::_('COM_JOOMLAQUIZ_RESET'); ?>" class="button2"
           onclick="javascript: get_content();"/>&nbsp;&nbsp;

    <input type="button" value="<?php echo JText::_('COM_JOOMLAQUIZ_SAVE'); ?>" class="button2"
           onclick="javascript: save_content();"/>
</div>
<script language="javascript" type="text/javascript">
    window.addEvent('domready', function () {
        var qwerty = getObj_frame('test_<?php echo $id;?>').innerHTML;
        <?php
        echo $editor->setContent('cdescription', '');
        ?>
        timerID2 = setTimeout("get_content()", 300);
    });

    window.onload = get_content;
    timerID = setTimeout("get_content()", 300);
</script>