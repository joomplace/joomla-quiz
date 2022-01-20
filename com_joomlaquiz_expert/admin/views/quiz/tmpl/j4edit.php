<?php
/**
* Joomlaquiz Deluxe Component for Joomla 4
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
//HTMLHelper::_('formbehavior.chosen', 'select');

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('jquery');

$app = JFactory::getApplication();
$input = $app->input;

?>
<script>
jQuery(document).ready(function(){
    window.counter = Number("<?php echo(count($this->jq_pool_cat));?>");

    var poolRandDiv = jQuery('#jform_pool_rand').closest('.control-group'),
        headCatDiv = jQuery('#head_cat').closest('.control-group'),
        catPoolDiv = jQuery('#cat_pool_with_head').closest('.control-group');

    jQuery("#jform_c_email_chk1").click(function () {
      jQuery("#jform_c_emails").removeAttr("disabled");
    });
    jQuery("#jform_c_email_chk0").click(function () {
      jQuery("#jform_c_emails").attr("disabled", true);
    });
    jQuery("#jform_c_pool0").click(function () {
		jQuery("#jform_pool_rand").attr("disabled", true);
		jQuery("#head_cat").attr("disabled", true);
		jQuery("#cat_pool_with_head input").attr("disabled", true);

        jQuery(poolRandDiv).hide('slow');
        jQuery(headCatDiv).hide('slow');
        jQuery(catPoolDiv).hide('slow');
    });
    jQuery("#jform_c_pool1").click(function () {
		jQuery("#jform_pool_rand").attr("disabled", false);
		jQuery("#head_cat").attr("disabled", true);
		jQuery("#cat_pool_with_head input").attr("disabled", true);

        jQuery(poolRandDiv).show('slow');
        jQuery(headCatDiv).hide('slow');
        jQuery(catPoolDiv).hide('slow');
    });
    jQuery("#jform_c_pool2").click(function () {
		jQuery("#jform_pool_rand").attr("disabled", true);
		jQuery("#head_cat").attr("disabled", false);
		jQuery("#cat_pool_with_head input").attr("disabled", false);

        jQuery(poolRandDiv).hide('slow');
        jQuery(headCatDiv).show('slow');
        jQuery(catPoolDiv).show('slow');
    });
    jQuery("#jform_c_ismetadescr1").click(function () {
        jQuery("#jform_c_metadescr").removeAttr("disabled");
    });
    jQuery("#jform_c_iskeywords1").click(function () {
        jQuery("#jform_c_keywords").removeAttr("disabled");
    });
    jQuery("#jform_c_ismetatitle1").click(function () {
        jQuery("#jform_c_metatitle").removeAttr("disabled");
    });
    jQuery("#jform_c_ismetadescr0").click(function () {
        jQuery("#jform_c_metadescr").attr("disabled", true);
    });
    jQuery("#jform_c_iskeywords0").click(function () {
        jQuery("#jform_c_keywords").attr("disabled", true);
    });
    jQuery("#jform_c_ismetatitle0").click(function () {
        jQuery("#jform_c_metatitle").attr("disabled", true);
    });

    <?php if(!empty($this->item->head_cat)) { ?>
    showHideCategories(document.getElementById('head_cat'));
    <?php } ?>
});

window.onload = function (){
	var checked1 = document.getElementById('jform_c_email_chk1').getAttribute('checked');
    var checked2 = document.getElementById('jform_c_pool0').getAttribute('checked');
    var checked3 = document.getElementById('jform_c_pool1').getAttribute('checked');
    var checked4 = document.getElementById('jform_c_pool2').getAttribute('checked');
    var checked5 = document.getElementById('jform_c_ismetadescr1').getAttribute('checked');
    var checked6 = document.getElementById('jform_c_iskeywords1').getAttribute('checked');
    var checked7 = document.getElementById('jform_c_ismetatitle1').getAttribute('checked');
    var counter = Number("<?php echo(count($this->jq_pool_cat));?>");

    var poolRandDiv = jQuery('#jform_pool_rand').closest('.control-group'),
        headCatDiv = jQuery('#head_cat').closest('.control-group'),
        catPoolDiv = jQuery('#cat_pool_with_head').closest('.control-group');

    if(checked1!=null){
	    document.getElementById('jform_c_emails').disabled = false;
	} else{
	    document.getElementById('jform_c_emails').disabled = true;
	}

    if(checked2!=null){
		jQuery("#jform_pool_rand").attr("disabled", true);
		jQuery("#head_cat").attr("disabled", true);
		jQuery("#cat_pool_with_head input").attr("disabled", true);

        jQuery(poolRandDiv).hide('slow');
        jQuery(headCatDiv).hide('slow');
        jQuery(catPoolDiv).hide('slow');
    }

    if(checked3!=null){
		jQuery("#jform_pool_rand").attr("disabled", false);
		jQuery("#head_cat").attr("disabled", true);
		jQuery("#cat_pool_with_head input").attr("disabled", true);

        jQuery(poolRandDiv).show('slow');
        jQuery(headCatDiv).hide('slow');
        jQuery(catPoolDiv).hide('slow');
    }

    if(checked4!=null){
		jQuery("#jform_pool_rand").attr("disabled", true);
		jQuery("#head_cat").attr("disabled", false);
		jQuery("#cat_pool_with_head input").attr("disabled", false);

        jQuery(poolRandDiv).hide('slow');
        jQuery(headCatDiv).show('slow');
        jQuery(catPoolDiv).show('slow');
    }

    if(checked5!=null){
        document.getElementById('jform_c_metadescr').disabled = false;
    } else{
        document.getElementById('jform_c_metadescr').disabled = true;
    }

    if(checked6!=null){
        document.getElementById('jform_c_keywords').disabled = false;
    } else{
        document.getElementById('jform_c_keywords').disabled = true;
    }

    if(checked7!=null){
        document.getElementById('jform_c_metatitle').disabled = false;
    } else{
        document.getElementById('jform_c_metatitle').disabled = true;
    }

}

</script>
<?php echo $this->loadTemplate('j4menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=edit&c_id='.(int) $this->item->c_id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="quiz-form" class="form-validate">
    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'quiz-details', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'quiz-details', Text::_('COM_JOOMLAQUIZ_QUIZ_DETAILS')); ?>
        <div class="row">
            <div class="col-lg-4">
                <div>
                    <fieldset class="adminform">
                        <?php echo $this->form->renderField('c_title'); ?>
                        <?php echo $this->form->renderField('c_author'); ?>
                        <?php echo $this->form->renderField('c_show_author'); ?>
                        <?php echo $this->form->renderField('c_category_id'); ?>
                        <?php echo $this->form->renderField('paid_check'); ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label data-content="<?php echo JText::_('COM_JOOMLAQUIZ_TEMPLATE_DESC');?>" data-original-title="<?php echo Text::_('COM_JOOMLAQUIZ_TEMPLATE_DESC');?>" class="control-label hasPopover" for="jformc_skin" id="jformc_skin-lbl"><?php echo Text::_('COM_JOOMLAQUIZ_TEMPLATE_LABEL');?></label>
                            </div>
                            <div class="controls">
                                <?php echo $this->jq_templates; ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label data-content="<?php echo Text::_('COM_JOOMLAQUIZ_CERTIFICATE_DESC');?>" data-original-title="<?php echo Text::_('COM_JOOMLAQUIZ_CERTIFICATE_DESC');?>" class="control-label hasPopover" for="jformc_c_certificate" id="jformc_c_certificate-lbl"><?php echo Text::_('COM_JOOMLAQUIZ_CERTIFICATE_LABEL');?></label>
                            </div>
                            <div class="controls">
                                <?php echo $this->c_certificates; ?>
                            </div>
                        </div>
                        <?php echo $this->form->renderField('c_autostart'); ?>
                        <?php echo $this->form->renderField('c_pagination'); ?>
                        <?php echo $this->form->renderField('c_allow_continue'); ?>
                        <?php echo $this->form->renderField('c_show_timer'); ?>
                        <?php echo $this->form->renderField('c_time_limit'); ?>
                        <?php echo $this->form->renderField('c_timer_style'); ?>
                        <?php echo $this->form->renderField('c_number_times'); ?>
                        <?php echo $this->form->renderField('c_min_after'); ?>
                        <?php echo $this->form->renderField('c_once_per_day'); ?>
                        <?php echo $this->form->renderField('c_passing_score'); ?>
                        <?php echo $this->form->renderField('one_time'); ?>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'quiz-description', Text::_('COM_JOOMLAQUIZ_QUIZ_DESCRIPTION')); ?>
        <div class="row">
            <div class="col-lg-12">
                <div>
                    <fieldset class="adminform">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('c_description'); ?>
                            </div>
                            <div class="controls">
                                <?php echo Text::_('COM_JOOMLAQUIZ_YOU_CAN_DEFINE');?>
                                <?php echo $this->form->getInput('c_description'); ?>
                            </div>
                        </div>
                        <?php echo $this->form->renderField('c_short_description'); ?>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'additional-option', Text::_('COM_JOOMLAQUIZ_ADDITIONAL_OPTIONS')); ?>
        <div class="row">
            <div class="col-lg-6">
                <div>
                    <fieldset class="adminform">
                        <?php echo $this->form->renderField('c_random'); ?>
                        <?php echo $this->form->renderField('c_enable_skip'); ?>
                        <?php echo $this->form->renderField('c_show_result'); ?>
                        <?php echo $this->form->renderField('c_enable_review'); ?>
                        <?php echo $this->form->renderField('c_enable_prevnext'); ?>
                        <?php echo $this->form->renderField('c_enable_print'); ?>
                        <?php echo $this->form->renderField('c_email_to'); ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('c_email_chk'); ?>
                            </div>
                            <div class="controls">
                                <div style="float:left;"><?php echo $this->form->getInput('c_email_chk'); ?></div>
                                <div style="float:left;margin-left:10px;"><?php echo $this->form->getInput('c_emails'); ?></div>
                            </div>
                        </div>
                        <?php echo $this->form->renderField('c_slide'); ?>
                        <?php echo $this->form->renderField('c_resbycat'); ?>
                        <?php echo $this->form->renderField('c_show_quest_pos'); ?>
                        <?php echo $this->form->renderField('c_show_quest_points'); ?>
                        <?php echo $this->form->renderField('c_redirect_after'); ?>
                        <div class="control-group">
                            <div style="float:left;">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('c_redirect_link'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('c_redirect_link'); ?>
                                </div>
                            </div>
                            <div style="float:left;margin-left:12px;">
                                <?php echo $this->form->getLabel('c_redirect_linktype'); ?>
                                <div class="controls">
                                    <?php echo $this->form->getInput('c_redirect_linktype'); ?>
                                </div>
                            </div>
                        </div>
                        <?php echo $this->form->renderField('c_redirect_delay'); ?>
                        <?php echo $this->form->renderField('c_grading'); ?>
                        <?php echo $this->form->renderField('c_flag'); ?>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'feedback-option', Text::_('COM_JOOMLAQUIZ_FEEDBACK_OPTIONS')); ?>
        <div class="row">
            <div class="col-lg-12">
                <div>
                    <fieldset class="adminform">
                        <?php echo $this->form->renderField('c_feedback'); ?>
                        <?php echo $this->form->renderField('c_feedback_pdf'); ?>
                        <?php echo $this->form->renderField('c_show_qfeedback'); ?>
                        <?php echo $this->form->renderField('c_right_message'); ?>
                        <?php echo $this->form->renderField('c_wrong_message'); ?>
                    </fieldset>
                    <fieldset class="adminform">
                        <legend><?php echo Text::_('COM_JOOMLAQUIZ_QUIZ_FEEDBACK_OPT');?></legend>
                        <?php echo $this->form->renderField('c_share_buttons'); ?>
                        <?php echo $this->form->renderField('c_image'); ?>
                        <?php echo $this->form->renderField('c_statistic'); ?>
                        <?php echo $this->form->renderField('c_hide_feedback'); ?>
                        <?php echo $this->form->renderField('c_feed_option'); ?>
                        <div class="control-group">
                            <label class="hasTip control-label"></label>
                            <div class="controls">
                                <table cellpadding="10" cellspacing="10" id="feed_toins" <?php if($this->item->c_feed_option == 0) echo 'style="display:none;"';?>>
                                    <?php
                                    if (!empty($this->feed_opres)) {
                                        foreach ($this->feed_opres as $fopt) {
                                            echo '<tr>';
                                            echo '<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="'.JURI::root().'administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a></td>';
                                            echo '<td><input type="text" name="from_percent[]" maxlength="30" value="'.$fopt->from_percent.'" /></td>';
                                            echo '<td><input type="text" name="to_percent[]" maxlength="30" value="'.$fopt->to_percent.'" /></td>';
                                            echo '<td><textarea name="feed_by_percent[]" row="5" cols="50">'.stripslashes($fopt->fmessage).'</textarea></td>';
                                            echo '</tr>';
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                            <table cellpadding="0" id="by_score_id" <?php if($this->item->c_feed_option == 0) echo 'style="display:none;"';?>>
                                <tr>
                                    <td>
                                        <?php echo Text::_('COM_JOOMLAQUIZ_IF_RECEIVED');?><input type="text" id="q_rfrom" name="q_rfrom" maxlength="30" style="margin-left:15px;"/>
                                    </td>
                                    <td>
                                        &nbsp;<?php echo Text::_('COM_JOOMLAQUIZ_TO');?>&nbsp;<input type="text" id="q_rto" name="q_rto" maxlength="30" />
                                    </td>
                                    <td>
                                        <input type="button" value="Add" onclick="if(validate_numeric(document.adminForm.q_rfrom) && validate_numeric(document.adminForm.q_rto)) InsertFrange('feed_toins'); else return false; " class="btn btn-small" style="margin-left:15px;"/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php echo $this->form->renderField('c_pass_message'); ?>
                        <?php echo $this->form->renderField('c_unpass_message'); ?>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'question-pool-option', Text::_('COM_JOOMLAQUIZ_QUESTION_POOL_OPTIONS')); ?>
        <div class="row">
            <div class="col-lg-6">
                <div>
                    <fieldset class="adminform">
                        <?php echo $this->form->renderField('c_pool'); ?>
                        <?php echo $this->form->renderField('c_auto_breaks'); ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('pool_rand'); ?>
                            </div>
                            <div class="controls">
                                <input type="text" size="10" value="<?php echo $this->item->c_pool==1 ? $this->q_count : '';?>" id="jform_pool_rand" name="jform[pool_rand]" class="" aria-invalid="false">
                            </div>
                        </div>
                        <div class="control-group">
                            <label data-content="<?php echo Text::_('COM_JOOMLAQUIZ_HEAD_CATEGORY_DESC');?>"
                                   data-original-title="<?php echo Text::_('COM_JOOMLAQUIZ_HEAD_CATEGORY_DESC');?>"
                                   class="control-label hasPopover" for="head_cat" id="jform_pool_rand-lbl" aria-invalid="false">
                                <?php echo Text::_('COM_JOOMLAQUIZ_HEAD_CATEGORY_LABEL');?>
                            </label>
                            <div class="controls">
                                <?php echo $this->head_cat;?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div id="cat_pool_with_head">
                                <?php
                                foreach ($this->jq_pool_cat as $listcat)
                                {
                                    $is_num_cat = '';
                                    if( $this->item->c_pool==2 && isset($this->if_pool))
                                    {
                                        foreach($this->if_pool as $poolz)
                                        {
                                            if ( $poolz->q_cat == $listcat->value )
                                            {
                                                $is_num_cat = $poolz->q_count;
                                            }
                                        }
                                    }
                                    echo '<div style="display:hidden" class="head_category_' . $listcat->head_category . '">';
                                    echo '<table width="100%"><tr>';
                                    echo '<div class="control-label"><td align="left" style="width:24%">'.$listcat->text.'</td></div>';
                                    echo '<br/>';
                                    echo '<div class="controls"><td><input type="hidden" name="pool_cats[]" value="'.$listcat->value.'"><input id="cat_field_'.$listcat->value.'" type="text" name="pnumber_'.$listcat->value.'" value="'.$is_num_cat.'"></td></div>';
                                    echo '</tr></table>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'metadata-information', Text::_('COM_JOOMLAQUIZ_METADATA_INFORMATION')); ?>
        <div class="row">
            <div class="col-lg-6">
                <div>
                    <fieldset class="adminform">
                        <?php echo $this->form->renderField('c_ismetadescr'); ?>
                        <div class="control-group">
                            <div class="controls">
                                <?php echo $this->form->getInput('c_metadescr'); ?>
                            </div>
                        </div>
                        <?php echo $this->form->renderField('c_iskeywords'); ?>
                        <div class="control-group">
                            <div class="controls">
                                <?php echo $this->form->getInput('c_keywords'); ?>
                            </div>
                        </div>
                        <?php echo $this->form->renderField('c_ismetatitle'); ?>
                        <div class="control-group">
                            <div class="controls">
                                <?php echo $this->form->getInput('c_metatitle'); ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'quiz-permission', Text::_('COM_JOOMLAQUIZ_QUIZ_PERMISSION')); ?>
        <div class="row">
            <div class="col-lg-12">
                <div>
                    <fieldset class="adminform">
                        <?php echo $this->form->renderField('rules'); ?>
                        <?php echo $this->form->renderField('c_quiz_access_message'); ?>
                        <?php echo $this->form->renderField('c_quiz_certificate_access_message'); ?>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="jform[c_id]" value="<?php echo $this->item->c_id;?>" />
        <input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
        <?php echo $this->form->getInput('asset_id'); ?>
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
<script>
	var head_cats = new Array;
	function showHideCategories(el) {
		var divs = document.getElementsByTagName('div');
		var class_name = el.options[el.selectedIndex].value;
		for(var i=0; i<divs.length; i++) {
			if(divs[i].parentNode.id == 'cat_pool_with_head') {
				if(class_name == '' || divs[i].className == class_name) {
					divs[i].style.display = 'block';
				} else {
					divs[i].style.display = 'none';
				}
			}
		}
	}

	document.getElementById('jform_c_feed_option').onchange = function()
	{
		if(document.getElementById('jform_c_feed_option').value == 0 ) {document.getElementById('feed_toins').style.display = 'none';document.getElementById('by_score_id').style.display = 'none'; } else { document.getElementById('feed_toins').style.display = 'block';document.getElementById('by_score_id').style.display = 'block';}
	}

	function Delete_tbl_row(element) {
		var del_index = element.parentNode.parentNode.sectionRowIndex;
		var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
		element.parentNode.parentNode.parentNode.deleteRow(del_index);
	}

	function InsertFrange(table_id)
	{
		var tbl_elem = document.getElementById(table_id);
		var row = tbl_elem.insertRow(tbl_elem.rows.length);
		var cell1 = document.createElement("td");
		var cell2 = document.createElement("td");
		var cell3 = document.createElement("td");
		var cell4 = document.createElement("td");
		var from_range = document.createElement("input");
		from_range.type = "text";
		from_range.name = "from_percent[]";
		from_range.setAttribute('maxlength',30);
		from_range.value = document.adminForm.q_rfrom.value;
		var to_range = document.createElement("input");
		to_range.type = "text";
		to_range.name = "to_percent[]";
		to_range.setAttribute('maxlength',30);
		to_range.value = document.adminForm.q_rto.value;

		cell2.appendChild(from_range);
		cell1.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
		cell3.appendChild(to_range);
		var inc_text = 	document.createElement("textarea");
		inc_text.name = "feed_by_percent[]";
		inc_text.value = "feedback notes";
		inc_text.row = 5;
		inc_text.cols = 50;
		cell4.appendChild(inc_text);
		row.appendChild(cell1);
		row.appendChild(cell2);
		row.appendChild(cell3);
		row.appendChild(cell4);

	}

	function validate_numeric(thfield)
	{
		var reas = new RegExp('[0-9]{1,3}');
		if (!reas.test(thfield.value))
		{
			alert(thfield.value + '<?php echo addslashes( JText::_('COM_JOOMLAQUIZ_IS_NOT_NUMERIC'));?>');
			return false;
		}
		if(thfield.value>100 && jQuery('#jform_c_feed_option').val()==1)
		{
			alert(thfield.value + '<?php echo JText::_('COM_JOOMLAQUIZ_MORE_THAN');?>');
			return false;
		}
		return true;
	}

	Joomla.submitbutton = function(task)
	{
		if (task == 'quiz.cancel' || document.formvalidator.isValid(document.getElementById('quiz-form'))) {
			<?php //echo $this->form->getField('c_description')->save(); ?>
			<?php //echo $this->form->getField('c_short_description')->save(); ?>
			<?php //echo $this->form->getField('c_right_message')->save(); ?>
			<?php //echo $this->form->getField('c_wrong_message')->save(); ?>

			Joomla.submitform(task, document.getElementById('quiz-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

	/* dumb code here as we`ve not renamed our columns still */
	var initialGetElementById = document.getElementById;
	document.getElementById = function(){
		if(arguments[0] == 'jform_title'){
			return initialGetElementById.apply(document, ['jform_c_title']);
		}else{
			return initialGetElementById.apply(document, arguments);
		}
	}
	var initialgetUrlParam = getUrlParam;
	var getUrlParam = function(){
		if(arguments[0] == 'id'){
			return initialgetUrlParam.apply(document, ['c_id']);
		}else{
			return initialgetUrlParam.apply(document, arguments);
		}
	}
</script>
