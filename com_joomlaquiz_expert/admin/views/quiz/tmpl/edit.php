<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$app = JFactory::getApplication();
$input = $app->input;
?>
<script>
jQuery(document).ready(function(){
window.counter = Number("<?php echo(count($this->jq_pool_cat));?>");
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
    });
jQuery("#jform_c_pool1").click(function () {
		jQuery("#jform_pool_rand").attr("disabled", false);
		jQuery("#head_cat").attr("disabled", true);
		jQuery("#cat_pool_with_head input").attr("disabled", true);
   });
jQuery("#jform_c_pool2").click(function () {
		jQuery("#jform_pool_rand").attr("disabled", true);
		jQuery("#head_cat").attr("disabled", false);
		jQuery("#cat_pool_with_head input").attr("disabled", false);
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

    if(checked1!=null){
	document.getElementById('jform_c_emails').disabled = false;
	} else{ 
	document.getElementById('jform_c_emails').disabled = true;
	}

    if(checked2!=null){
		jQuery("#jform_pool_rand").attr("disabled", true);
		jQuery("#head_cat").attr("disabled", true);
		jQuery("#cat_pool_with_head input").attr("disabled", true);
    }

    if(checked3!=null){
		jQuery("#jform_pool_rand").attr("disabled", false);
		jQuery("#head_cat").attr("disabled", true);
		jQuery("#cat_pool_with_head input").attr("disabled", true);
    }

    if(checked4!=null){
		jQuery("#jform_pool_rand").attr("disabled", true);
		jQuery("#head_cat").attr("disabled", false);
		jQuery("#cat_pool_with_head input").attr("disabled", false);
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
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=edit&c_id='.(int) $this->item->c_id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="quiz-form" class="form-validate">
<div id="j-main-container" class="span12 form-horizontal">
	<ul class="nav nav-tabs" id="quizTabs">
	    <li class="active"><a href="#quiz-details" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_QUIZ_DETAILS');?></a></li>
	    <li><a href="#quiz-description" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_QUIZ_DESCRIPTION');?></a></li>
	    <li><a href="#additional-option" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_ADDITIONAL_OPTIONS');?></a></li>
		<li><a href="#feedback-option" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_FEEDBACK_OPTIONS');?></a></li>
		<li><a href="#question-pool-option" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_QUESTION_POOL_OPTIONS');?></a></li>
		<li><a href="#metadata-information" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_METADATA_INFORMATION');?></a></li>
		<li><a href="#quiz-permission" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_QUIZ_PERMISSION');?></a></li>
	</ul>
	<div class="tab-content">
	    <div class="tab-pane active" id="quiz-details">
		<fieldset class="adminform">
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_title'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_title'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_author'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_author'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_show_author'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_show_author'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_category_id'); ?>
                </div>
				<div class="controls">
					 <?php echo $this->form->getInput('c_category_id'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('paid_check'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('paid_check'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <label data-content="<?php echo JText::_('COM_JOOMLAQUIZ_TEMPLATE_DESC');?>" data-original-title="<?php echo JText::_('COM_JOOMLAQUIZ_TEMPLATE_DESC');?>" class="control-label hasPopover" for="jformc_skin" id="jformc_skin-lbl"><?php echo JText::_('COM_JOOMLAQUIZ_TEMPLATE_LABEL');?></label>
                </div>
				<div class="controls">
					<?php echo $this->jq_templates; ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <label data-content="<?php echo JText::_('COM_JOOMLAQUIZ_CERTIFICATE_DESC');?>" data-original-title="<?php echo JText::_('COM_JOOMLAQUIZ_CERTIFICATE_DESC');?>" class="control-label hasPopover" for="jformc_c_certificate" id="jformc_c_certificate-lbl"><?php echo JText::_('COM_JOOMLAQUIZ_CERTIFICATE_LABEL');?></label>
                </div>
				<div class="controls">
					<?php echo $this->c_certificates; ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_autostart'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_autostart'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_pagination'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_pagination'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_allow_continue'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_allow_continue'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_show_timer'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_show_timer'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_time_limit'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_time_limit'); ?>
				</div>
			</div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_timer_style'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('c_timer_style'); ?>
                </div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_number_times'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_number_times'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_min_after'); ?>
				</div>
                <div class="controls">
					<?php echo $this->form->getInput('c_min_after'); ?>
				</div>				
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_once_per_day'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_once_per_day'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('c_passing_score'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('c_passing_score'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('one_time'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('one_time'); ?>
				</div>
			</div>
		</fieldset>
	    </div>
	    <div class="tab-pane" id="quiz-description">
		<fieldset class="adminform">
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_description'); ?>
                </div>
				<div class="controls">
					<?php echo JText::_('COM_JOOMLAQUIZ_YOU_CAN_DEFINE');?>
					<?php echo $this->form->getInput('c_description'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_short_description'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_short_description'); ?>
				</div>
			</div>
		</fieldset>
	    </div>
	    <div class="tab-pane" id="additional-option">
		<fieldset class="adminform">
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_random'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_random'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_enable_skip'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_enable_skip'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_show_result'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_show_result'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_enable_review'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_enable_review'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_enable_prevnext'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_enable_prevnext'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_enable_print'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_enable_print'); ?>
				</div>
			</div>
			<div class="control-group">
                    <div class="control-label">
				        <?php echo $this->form->getLabel('c_email_to'); ?>
                    </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_email_to'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_email_chk'); ?>
                </div>
				<div class="controls">
					<div style="float:left;"><?php echo $this->form->getInput('c_email_chk'); ?></div>
					<div style="float:left;margin-left:10px;"><?php echo $this->form->getInput('c_emails'); ?></div>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_slide'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_slide'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label"">
                    <?php echo $this->form->getLabel('c_resbycat'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_resbycat'); ?>
				</div>
			</div>
            <?php echo $this->form->renderField('c_show_quest_pos'); ?>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_show_quest_points'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_show_quest_points'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_redirect_after'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_redirect_after'); ?>
				</div>
			</div>
			
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
			<div class="control-group">
                <div class="control-label">
			    	<?php echo $this->form->getLabel('c_redirect_delay'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_redirect_delay'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_grading'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_grading'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_flag'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_flag'); ?>
				</div>
			</div>
		</fieldset>
	    </div>
		<div class="tab-pane" id="feedback-option">
		<fieldset class="adminform">
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_feedback'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_feedback'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_feedback_pdf'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_feedback_pdf'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_show_qfeedback'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_show_qfeedback'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_right_message'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_right_message'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_wrong_message'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_wrong_message'); ?>
				</div>
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLAQUIZ_QUIZ_FEEDBACK_OPT');?></legend>	
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_share_buttons'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_share_buttons'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('c_image'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('c_image'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_statistic'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_statistic'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('c_hide_feedback'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_hide_feedback'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_feed_option'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_feed_option'); ?>
				</div>
			</div>
			<div class="control-group">
					<label class="hasTip control-label"></label>
					<div class="controls">
					<table cellpadding="10" cellspacing="10" id="feed_toins" <?php if($this->item->c_feed_option == 0) echo 'style="display:none;"';?>>
					<?php			
						if(@count($this->feed_opres))
						{
							foreach($this->feed_opres as $fopt)
							{
								echo '<tr>';
								echo '<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="'.JURI::root().'administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a></td>';
								echo '<td><input type="text" name="from_percent[]" maxlength="3" value="'.$fopt->from_percent.'" /></td>';
								echo '<td><input type="text" name="to_percent[]" maxlength="3" value="'.$fopt->to_percent.'" /></td>';
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
								<?php echo JText::_('COM_JOOMLAQUIZ_IF_RECEIVED');?><input type="text" id="q_rfrom" name="q_rfrom" maxlength="3" style="margin-left:15px;"/>
							</td>
							<td>
								&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_TO');?>&nbsp;<input type="text" id="q_rto" name="q_rto" maxlength="3" />
							</td>
							<td>
								<input type="button" value="Add" onclick="if(validate_numeric(document.adminForm.q_rfrom) && validate_numeric(document.adminForm.q_rto)) InsertFrange('feed_toins'); else return false; " class="btn btn-small" style="margin-left:15px;"/>
							</td>
						</tr>
					</table>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_pass_message'); ?>
				</div>
                 <div class="controls">
					<?php echo $this->form->getInput('c_pass_message'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_unpass_message'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_unpass_message'); ?>
				</div>
			</div>
		</fieldset>
	    </div>
		<div class="tab-pane" id="question-pool-option">
		<fieldset class="adminform">
			<div class="control-group">
				<?php echo $this->form->getLabel('c_pool'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('c_pool'); ?>
				</div>
			</div>
			<div class="control-group">
				<?php echo $this->form->getLabel('c_auto_breaks'); ?>
				<div class="controls">
					<?php echo $this->form->getInput('c_auto_breaks'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
                    <?php echo $this->form->getLabel('pool_rand'); ?>
                </div>
				<div class="controls">
					<input type="text" size="10" value="<?php echo $this->item->c_pool==1 ? $this->q_count : '';?>" id="jform_pool_rand" name="jform[pool_rand]" class="" aria-invalid="false">
				</div>
			</div>
			<div class="control-group">
                <label data-content="<?php echo JText::_('COM_JOOMLAQUIZ_BY_CATEGORIES_DESC');?>" data-original-title="<?php echo JText::_('COM_JOOMLAQUIZ_BY_CATEGORIES_DESC');?>" class="control-label hasPopover" for="head_cat" id="jform_pool_rand-lbl" aria-invalid="false"><?php echo JText::_('COM_JOOMLAQUIZ_TEMPLATE_LABEL');?></label>
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
		<div class="tab-pane" id="metadata-information">
		<fieldset class="adminform">
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_ismetadescr'); ?>
				</div>
                <div class="controls">
					<?php echo $this->form->getInput('c_ismetadescr'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<?php echo $this->form->getInput('c_metadescr'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_iskeywords'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_iskeywords'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<?php echo $this->form->getInput('c_keywords'); ?>
				</div>
			</div>
			<div class="control-group">
                <div class="control-label">
				    <?php echo $this->form->getLabel('c_ismetatitle'); ?>
                </div>
				<div class="controls">
					<?php echo $this->form->getInput('c_ismetatitle'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<?php echo $this->form->getInput('c_metatitle'); ?>
				</div>
			</div>
		</fieldset>
	    </div>
	    <div class="tab-pane" id="quiz-permission">
			<fieldset class="adminform">
				<div class="control-group">
	                <div class="control-label">
					    <?php echo $this->form->getLabel('rules'); ?>
	                </div>
					<div class="controls">
						<?php echo $this->form->getInput('rules'); ?>
					</div>
				</div>
			</fieldset>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_quiz_access_message'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('c_quiz_access_message'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('c_quiz_certificate_access_message'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('c_quiz_certificate_access_message'); ?>
                </div>
            </div>

		</div>
	</div>
</div>
<input type="hidden" name="task" value="" />
<input type="hidden" name="jform[c_id]" value="<?php echo $this->item->c_id;?>" />
<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
<?php echo $this->form->getInput('asset_id'); ?>
<?php echo JHtml::_('form.token'); ?>
</div>
</form>
<script type="text/javascript">
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
		from_range.setAttribute('maxlength',3);
		from_range.value = document.adminForm.q_rfrom.value;
		var to_range = document.createElement("input");
		to_range.type = "text";
		to_range.name = "to_percent[]";
		to_range.setAttribute('maxlength',3);
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
		if (task == 'quiz.cancel' || document.formvalidator.isValid(document.id('quiz-form'))) {
			<?php echo $this->form->getField('c_description')->save(); ?>
			<?php echo $this->form->getField('c_short_description')->save(); ?>
			<?php echo $this->form->getField('c_right_message')->save(); ?>
			<?php echo $this->form->getField('c_wrong_message')->save(); ?>
			
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
	getUrlParam = function(){
		if(arguments[0] == 'id'){
			return initialgetUrlParam.apply(document, ['c_id']);
		}else{
			return initialgetUrlParam.apply(document, arguments);
		}
	}
</script>