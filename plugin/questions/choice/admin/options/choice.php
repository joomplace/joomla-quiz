<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 *
 * @package   Joomlaquiz Deluxe
 * @author    JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
if (JComponentHelper::getParams('com_joomlaquiz')->get('wysiwyg_options', true)) {
    $editor = JEditor::getInstance(JFactory::getConfig()->get('editor', 'none'));
} else {
    $editor = JEditor::getInstance('none');
}
if($editor->get('_name') == 'none'){
    JHtml::_('behavior.core');
}
?>
<style type="text/css">
    .mce-edit-area iframe {
        width: 100% !important;
        height: 150px !important;
    }

    .js-editor-none .pull-left {
        float: none !important;
    }
</style>
<table class="table table-striped" id="qfld_tbl" cellpadding="10">
    <tr>
        <th width="20px"
            align="center"><?php echo JText::_('COM_JOOMLAQUIZ_SHARP'); ?></th>
        <th width="60px"
            align="center"><?php echo JText::_('COM_JOOMLAQUIZ_CHECK_CORRECT_CHOICE'); ?></th>
        <th class="title"
            width="200px"><?php echo JText::_('COM_JOOMLAQUIZ_OPTION_TEXT'); ?></th>
        <th width="20px" align="center"
            class="title"><?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?></th>
        <th width="20px" align="center"
            class="title"><?php echo JText::_('COM_JOOMLAQUIZ_MOVEUP'); ?></th>
        <th width="20px" align="center"
            class="title"><?php echo JText::_('COM_JOOMLAQUIZ_MOVEDOWN'); ?></th>
        <th width="200px" align="center"
            class="title"><?php echo JText::_('COM_JOOMLAQUIZ_POINTS2'); ?></th>
    </tr>

    <?php
    $k        = 0;
    $ii       = 0;
    $ind_last = count($return['choices']);
    foreach ($return['choices'] as $frow) {
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td rowspan="<?= ($q_om_type == 1) ? 2 : 1 ?>" align="center"
                valign="top"><?php echo $ii + 1 ?></td>
            <td valign="top"
                align="<?php echo($q_om_type != 10 ? 'center' : 'left') ?>">
                <?php
                if ($q_om_type != 10) {
                    ?>
                    <input <?php echo($frow->c_right ? 'checked' : ''); ?>
                            type="radio" name="jq_checked[]"
                            value="<?php echo $ii ?>"
                            onClick="jq_UnselectCheckbox2(event);"/>
                    <?php
                } else {
                    $random = rand(100000, 1000000);
                    ?>
                    <span>
					<label><input name="jq_radio_<?php echo $random; ?>"
                                  <?php echo(!$frow->c_right ? ' checked '
                                      : ''); ?>type="radio" value="0"
                                  onClick="jq_SetHidden('jq_checked_<?php echo $random; ?>', 0);"/><?php echo JText::_('COM_JOOMLAQUIZ_FALSE2'); ?></label><br/>
                    <label><input name="jq_radio_<?php echo $random; ?>"
                                  <?php echo($frow->c_right ? ' checked '
                                      : ''); ?>type="radio" value="1"
                                  onClick="jq_SetHidden('jq_checked_<?php echo $random; ?>', 1);"/><?php echo JText::_('COM_JOOMLAQUIZ_TRUE2'); ?></label>
                    <input name="jq_checked[]" type="hidden"
                           value="<?php echo $frow->c_right; ?>"
                           id="jq_checked_<?php echo $random; ?>"/>
					</span>
                    <?php
                }
                ?>
            </td>
            <td valign="top" align="left">
                <?php
                if($editor->get('_name') == 'none'){
                    echo $editor->display('jq_hid_fields[' . $ii . ']', $frow->c_choice, 300, 170, '20', '10', false);
                } else {
                    echo $editor->display('jq_hid_fields[' . $ii . ']', $frow->c_choice, 300, 170, '20', '10', true, 'editor'. $ii, null, null, array('pagebreak', 'readmore'));
                }
                ?>
                <input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id ?>"/>
            </td>
            <td align="center" valign="top"><a href="javascript: void(0);"
                                               onClick="javascript:Delete_tbl_row(this); return false;"
                                               title="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?>"><img
                            src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"
                            border="0"
                            alt="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?>"></a>
            </td>
            <td valign="top"><?php if ($ii > 1) { ?><a
                    href="javascript: void(0);"
                    onClick="javascript:Up_tbl_row(this); return false;"
                    title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP'); ?>">
                    <img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"
                         border="0"
                         alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP'); ?>">
                    </a><?php } ?></td>
            <td valign="top"><?php if ($ii < $ind_last) { ?><a
                    href="javascript: void(0);"
                    onClick="javascript:Down_tbl_row(this); return false;"
                    title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN'); ?>">
                    <img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"
                         border="0"
                         alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN'); ?>">
                    </a><?php } ?></td>
            <td align="left" valign="top"><input type="text"
                                                 name="jq_a_points[]"
                                                 value="<?php echo $frow->a_point; ?>"
                                                 maxlength="10"/></td>
        </tr>
        <?php if ($q_om_type == 1) { ?>
            <tr>
                <td><?php echo JText::_('COM_JOOMLAQUIZ_FEEDBACK_OPTION'); ?></td>
                <td colspan="5">
                    <?php
                    $config = array(
//                        'configuration'=> array(
//                            'toolbars' => (object) array(
//                                '0'=> (object) array(
//                                    'menu' => array(),
//                                    'toolbar1' => array(
//                                        "bold",
//                                        "italic",
//                                        "underline",
//                                        "strikethrough",
//                                        "|",
//                                        "alignleft",
//                                        "aligncenter",
//                                        "alignright",
//                                        "alignjustify",
//                                        "|",
//                                        "formatselect",
//                                        "fontselect",
//                                        "fontsizeselect",
//                                        "|",
//                                        "bullist",
//                                        "numlist",
//                                        "indent",
//                                        "|",
//                                        "undo",
//                                        "|",
//                                        "link",
//                                        "unlink",
//                                        "image",
//                                        "|",
//                                        "code",
//                                        "|",
//                                        "forecolor",
//                                        "backcolor",
//                                        "|",
//                                        "subscript",
//                                        "superscript",
//                                        "|",
//                                        "charmap",
//                                        "|",
//                                        "blockquote",
//                                        "|"
//                                    )
//                                )
//                            ),
//                            'setoptions' => (object) array(
//                                '0'=> (object) array(
//                                    'inline' => true,
//                                    'html_height' => 200
//                                )
//                            )
//                        )
                    );
                    if($editor->get('_name') == 'none'){
                        echo $editor->display('jq_incorrect_feed[' . $ii . ']', $frow->c_incorrect_feed, 500, 170, '20', '10', false);
                    } else {
                        // fix Tinymce: add prefix '0' to key
                        // see explanation in /plugins/joomlaquiz/choice/choice.php in onAdminSaveOptions()
                        echo $editor->display('jq_incorrect_feed[0' . $ii . ']', $frow->c_incorrect_feed, 500, 170, '20', '10', true, 'feed'. $ii, null, null, $config);
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
        <?php
        $k = 1 - $k;
        $ii++;
    } ?>
</table>
<hr>
<table class="adminlist" style="margin-top:15px;">
    <legend>
        <?php echo JText::_('COM_JOOMLAQUIZ_ADD_NEW_OPTION'); ?>
    </legend>
    <tr>
        <th align="left"><?php echo JText::_('COM_JOOMLAQUIZ_OPTION_TEXT2'); ?></th>
        <th align="left"><?php echo JText::_('COM_JOOMLAQUIZ_OPTION_POINTS'); ?></th>
    </tr>
    <tr>
        <td align="left" valign="top" width="700">
            <div style="margin-right: 50px;">
                <?php
                if($editor->get('_name') == 'none'){
                    echo $editor->display('new_field', '', 600, 170, '20', '10', false);
                } else {
                    echo $editor->display('new_field', '', 600, 170, '20', '10', true, null, null, null, array('pagebreak', 'readmore'));
                }
                ?>
            </div>
        </td>
        <td align="left" valign="top" width="280">

            <div>
                <input id="new_field_points" class="text_area" type="text"
                       name="new_field_points" maxlength="10"/>
            </div>
        </td>
        <td align="left" rowspan="1" valign="top" width="auto">
            <div>
                <input class="modal-button btn" type="button"
                       name="add_new_field" style="width:70px;margin-left:10px;"
                       value="Add"
                       onClick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'jq_hid_fields[]');"/>
            </div>
        </td>
        <td></td>
    </tr>
    <?php if ($q_om_type === 1) { ?>
        <tr>
            <td colspan="4" align="left" valign="top">
                <br/>
                <b><?php echo JText::_('COM_JOOMLAQUIZ_FEEDBACK_MESSAGE'); ?></b>
                <br/>
                <br/>
                <div id="new_editor">
                    <?php
                    if($editor->get('_name') == 'none'){
                        echo $editor->display('new_incorrect_feed', '', 500, 170, '20', '10', '5', false);
                    } else {
                        echo $editor->display('new_incorrect_feed', '', 500, 170, '20', '10');
                    }
                    ?>
                </div>
            </td>
        </tr>
    <?php } ?>
    <?php echo JHtml::_('form.token'); ?>
</table>