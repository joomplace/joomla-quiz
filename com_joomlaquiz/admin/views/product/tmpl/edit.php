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
JHTML::_('behavior.calendar');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$relations = $this->lists['relation'];
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<style>
	label
	{
		display:inline;
	}
</style>
<?php echo $this->loadTemplate('menu');?>
<script>
    jQuery(document).ready(function(){

        jQuery("#q_access_0_2").click(function () {
            jQuery('#period1').text('<?php echo JText::_("COM_JOOMLAQUIZ_ACCESS_DAYS")?>');
        });
        jQuery("#q_access_1_2").click(function () {
            jQuery('#period1').text('<?php echo JText::_("COM_JOOMLAQUIZ_ACCESS_PERIOD")?>');
        });
        jQuery("#l_access_0_3").click(function () {
            jQuery('#period2').text('<?php echo JText::_("COM_JOOMLAQUIZ_ACCESS_DAYS")?>');
        });
        jQuery("#l_access_1_3").click(function () {
            jQuery('#period2').text('<?php echo JText::_("COM_JOOMLAQUIZ_ACCESS_PERIOD")?>');
        });
    });

</script>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&layout=edit&pid='.(int) $this->item->pid); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="product-form" class="form-validate">
	<div id="j-main-container" class="span10 form-horizontal">
		<fieldset class="adminform">
			<div class="control-group form-inline">
				<label class="control-label" for="jform_product_id" id="jform_product_id-lbl"><?php echo JText::_('COM_JOOMLAQUIZ_PRODUCT_NAME2')?></label>
				<div class="controls">
					<input type="text" size="30" value="<?php echo @$this->lists['name']?>" id="jform_name" name="jform[name]">
				</div>
			</div>
            <?php if(strlen($this->lists['products'])!=0):?>
                <div class="control-group form-inline">
                    <label class=" control-label" for="jform_product_id" id="jform_product_id-lbl"><?php echo JText::_('COM_JOOMLAQUIZ_OR_SELECT_VM')?></label>
                    <div class="controls">
                        <?php if (!$this->lists['no_virtuemart']) {?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_OR_SELECT_VM');?><?php echo $this->lists['products'];  } else {?><input  type="hidden" name="product_id" value="<?php echo $this->lists['product_id'];?>" /><?php  } ?>
                    </div>
                </div>
            <?php endif;?>
            <ul class="nav nav-tabs" id="questionTabs" style="width:110%">
                <li class="active"><a href="#quizzes" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_ADMINISTRATION_QUIZZES_LIST');?></a></li>
                <li><a href="#lpaths" data-toggle="tab"><?php echo  JText::_('COM_JOOMLAQUIZ_ADMINISTRATION_LPATHS_LIST');?></a></li>
            </ul>
            <div class="tab-content" style="width:110%">
                <div class="tab-pane active" id="quizzes">
			        <table class="adminform">
                    <?php if(!empty($this->lists['quiz'])){ ?>
							<table class="table table-striped">
                                <thead>
                                <tr>
                                    <th width=5%>
                                        <?php echo JText::_('COM_JOOMLAQUIZ_ACTIVATE'); ?>
                                    </th>
                                    <th width="25%">
                                        <?php echo JText::_('COM_JOOMLAQUIZ_TITLE2'); ?>
                                    </th>
                                    <th width="20%">
                                        <?php echo JText::_('COM_JOOMLAQUIZ_ACCESS_TYPE'); ?>
                                    </th>
                                    <th id="period1" width="25%">
                                        <?php echo JText::_('COM_JOOMLAQUIZ_ACCESS_DAYS'); ?>
                                    </th>
                                    <th width="20%">
                                        <?php echo JText::_('COM_JOOMLAQUIZ_ATTEMPTS_'); ?>
                                    </th>
                                </tr>
                                </thead>
							<?php
							foreach($this->lists['quiz'] as $quiz) {
								$checked = (!empty($relations['q']) && is_array($relations['q']) && array_key_exists($quiz->value, $relations['q'])) ? true : false;
								$period = ($checked && (($relations['q'][$quiz->value]['period_start'] && $relations['q'][$quiz->value]['period_start'] != '0000-00-00') || ($relations['q'][$quiz->value]['period_end'] && $relations['q'][$quiz->value]['period_end'] != '0000-00-00'))) ? true : false;
							?>
							<tr>
								<td class="nowrap center">
									<input type="checkbox" name="q_ids[]" id="q_ids_<?php echo $quiz->value; ?>" value="<?php echo $quiz->value; ?>" onclick="disablerow(this, 'q')" <?php echo ($checked ? 'checked ' : '');?>/>
								</td>
								<td class="has-context">
                                    <span><?php echo $quiz->text; ?></span>
								</td>
								<td class="has-context">
									<?php echo JText::_('COM_JOOMLAQUIZ_ACCESS_TO_COURSE');?>
									<input type="radio" name="q_access_<?php echo $quiz->value; ?>" id="q_access_0_<?php echo $quiz->value; ?>" value="0" onclick="viewaccessarea(this, <?php echo $quiz->value; ?>, 'q');" <?php echo ($checked ? '' : 'disabled ').(!$period ? 'checked ': ''); ?> />
									<label for="q_access_0_<?php echo $quiz->value; ?>"><?php echo JText::_('COM_JOOMLAQUIZ_X_DAYS');?></label>
									<?php echo JText::_('COM_JOOMLAQUIZ_OR2');?>
									<input type="radio" name="q_access_<?php echo $quiz->value; ?>" id="q_access_1_<?php echo $quiz->value; ?>" value="1" onclick="viewaccessarea(this, <?php echo $quiz->value; ?>, 'q');" <?php echo ($checked ? '' : 'disabled ').($period ? 'checked ': ''); ?> />
									<label for="q_access_1_<?php echo $quiz->value; ?>"><?php echo JText::_('COM_JOOMLAQUIZ_PERIOD');?></label>.
								</td>
								<td class="has-context">
									<span id="q_area_0_<?php echo $quiz->value; ?>" style="display: <?php echo ( $period ? 'none' : 'inline'); ?>;">
										<?php echo JText::_('COM_JOOMLAQUIZ_NUMBER_OF_DAYS');?>
										<input class="input-small" type="text" size="5" maxlength="5" name="q_xdays_<?php echo $quiz->value; ?>" id="q_xdays_<?php echo $quiz->value; ?>" value="<?php echo (@$relations['q'][$quiz->value]['xdays'] ? $relations['q'][$quiz->value]['xdays'] : 0); ?>" <?php echo ($checked ? '' : 'disabled '); ?> />
									</span>
									<span id="q_area_1_<?php echo $quiz->value; ?>" style="display: <?php echo ( $period ? 'inline' : 'none'); ?>;">
										<?php echo JText::_('COM_JOOMLAQUIZ_PERIOD_FROM');?>
																				
										<?php echo JHTML::_('calendar', (@$relations['q'][$quiz->value]['period_start'] ? $relations['q'][$quiz->value]['period_start'] : '0000-00-00'), 'q_period_start_'.$quiz->value, 'q_period_start_'.$quiz->value, '%Y-%m-%d', array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'10') );?>
										
										<?php echo JText::_('COM_JOOMLAQUIZ_TO1');?>
																				
										<?php echo JHTML::_('calendar', (@$relations['q'][$quiz->value]['period_end'] ? $relations['q'][$quiz->value]['period_end'] : '0000-00-00'), 'q_period_end_'.$quiz->value, 'q_period_end_'.$quiz->value, '%Y-%m-%d', array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'10') );?>
									</span>
								</td>
								<td class="has-context">
                                    <span>
									    <?php echo JText::_('COM_JOOMLAQUIZ_NUMBER_OF_ATTEMPTS');?>
									    <input class="input-small" type="text" size="5" maxlength="3" name="q_attempts_<?php echo $quiz->value; ?>" id="q_attempts_<?php echo $quiz->value; ?>" value="<?php echo (@$relations['q'][$quiz->value]['attempts'] ? $relations['q'][$quiz->value]['attempts'] : 0); ?>" <?php echo ($checked ? '' : 'disabled '); ?> />
								    </span>
                                </td>
							</tr>
							<?php
							} } else { ?>
                                    <tr>
                                        <td>
                                            <?php echo JText::_('COM_JOOMLAQUIZ_NOQUIZZESCREATED'); ?>
                                            <a href="<?php echo JRoute::_('index.php?option=com_joomlaquiz&task=quiz.add'); ?>" >
                                                <?php echo JText::_('COM_JOOMLAQUIZ_CREATEANEWONE'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
							</table>
                    </table>
                </div>
                <div class="tab-pane" id="lpaths">
                        <table class="adminform">
                            <?php if(!empty($this->lists['lpath'])){ ?>
							<table class="table table-striped">
                                <thead>
                                <tr>
                                    <th width="5%">
                                    <?php echo JText::_('COM_JOOMLAQUIZ_ACTIVATE'); ?>
                                    </th>
                                    <th width="25%">
                                        <?php echo JText::_('COM_JOOMLAQUIZ_TITLE2'); ?>
                                    </th>
                                    <th width="20%">
                                        <?php echo JText::_('COM_JOOMLAQUIZ_ACCESS_TYPE'); ?>
                                    </th>
                                    <th id="period2" width="25%">
                                        <?php echo JText::_('COM_JOOMLAQUIZ_ACCESS_DAYS'); ?>
                                    </th>
                                    <th width="20%">
                                        <?php echo JText::_('COM_JOOMLAQUIZ_ATTEMPTS_'); ?>
                                    </th>
                                </tr>
                                </thead>
							<?php
							foreach($this->lists['lpath'] as $lpath) {
								$checked = (!empty($relations['l']) && is_array($relations['l']) && array_key_exists($lpath->value, $relations['l'])) ? true : false;
								$period = ($checked && (($relations['l'][$lpath->value]['period_start'] && $relations['l'][$lpath->value]['period_start'] != '0000-00-00')|| ($relations['l'][$lpath->value]['period_end'] && $relations['l'][$lpath->value]['period_end'] != '0000-00-00')))? true : false;
							?>
							<tr>
								<td class="nowrap center">
									<input type="checkbox" name="l_ids[]" id="l_ids_<?php echo $lpath->value; ?>" value="<?php echo $lpath->value; ?>" onclick="disablerow(this, 'l')" <?php echo ($checked ? 'checked ' : '');?>>
								</td>
								<td class="has-context">
                                    <span><?php echo $lpath->text; ?></span>
								</td>
								<td class="has-context">
									<?php echo JText::_('COM_JOOMLAQUIZ_ACCESS_TO_COURSE');?>
									<input type="radio" name="l_access_<?php echo $lpath->value; ?>" id="l_access_0_<?php echo $lpath->value; ?>" value="0" onclick="viewaccessarea(this, <?php echo $lpath->value; ?>, 'l');" <?php echo ($checked ? '' : 'disabled ').(!$period ? 'checked ': ''); ?> />
									<label for="l_access_0_<?php echo $lpath->value; ?>"><?php echo JText::_('COM_JOOMLAQUIZ_X_DAYS');?></label>
									<?php echo JText::_('COM_JOOMLAQUIZ_OR2');?>
									<input type="radio" name="l_access_<?php echo $lpath->value; ?>" id="l_access_1_<?php echo $lpath->value; ?>" value="1" onclick="viewaccessarea(this, <?php echo $lpath->value; ?>, 'l');" <?php echo ($checked ? '' : 'disabled ').($period ? 'checked ': ''); ?> />
									<label for="l_access_0_<?php echo $lpath->value; ?>"><?php echo JText::_('COM_JOOMLAQUIZ_PERIOD');?></label>.
								</td>
								<td class="has-context">
									<span id="l_area_0_<?php echo $lpath->value; ?>" style="display: <?php echo ( $period ? 'none' : 'inline'); ?>;">
										<?php echo JText::_('COM_JOOMLAQUIZ_NUMBER_OF_DAYS');?>
										<input class="input-small" type="text" size="5" maxlength="5" name="l_xdays_<?php echo $lpath->value; ?>" id="l_xdays_<?php echo $lpath->value; ?>" value="<?php echo (@$relations['l'][$lpath->value]['xdays'] ? $relations['l'][$lpath->value]['xdays'] : 0); ?>" <?php echo ($checked ? '' : 'disabled '); ?> />
									</span>
									<span id="l_area_1_<?php echo $lpath->value; ?>" style="display: <?php echo ( $period ? 'inline' : 'none'); ?>;">
										<?php echo JText::_('COM_JOOMLAQUIZ_PERIOD_FROM');?>
																				
										<?php echo JHTML::_('calendar', (@$relations['l'][$lpath->value]['period_start'] ? $relations['l'][$lpath->value]['period_start'] : '0000-00-00'), 'l_period_start_'.$lpath->value, 'l_period_start_'.$lpath->value, '%Y-%m-%d', array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'10') );?>
										
										<?php echo JText::_('COM_JOOMLAQUIZ_TO1');?>
																				
										<?php echo JHTML::_('calendar', (@$relations['l'][$lpath->value]['period_end'] ? $relations['l'][$lpath->value]['period_end'] : '0000-00-00'), 'l_period_end_'.$lpath->value, 'l_period_end_'.$lpath->value, '%Y-%m-%d', array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'10') );?>
									</span>
									
								</td>
								<td class="has-context">
                                    <span>
									    <?php echo JText::_('COM_JOOMLAQUIZ_NUMBER_OF_ATTEMPTS');?>
									    <input class="input-small" type="text" size="5" maxlength="3" name="l_attempts_<?php echo $lpath->value; ?>" id="l_attempts_<?php echo $lpath->value; ?>" value="<?php echo (@$relations['l'][$lpath->value]['attempts'] ? $relations['l'][$lpath->value]['attempts'] : 0); ?>" <?php echo ($checked ? '' : 'disabled '); ?> />
								    </span>
                                </td>
							</tr>
							<?php
							} }
                                else { ?>
                                    <tr>
                                        <td>
                                            <?php echo JText::_('COM_JOOMLAQUIZ_NOLPATHCREATED'); ?>
                                            <a href="<?php echo JRoute::_('index.php?option=com_joomlaquiz&task=lpath.add'); ?>" >
                                                <?php echo JText::_('COM_JOOMLAQUIZ_CREATEANEWONE'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
							</table>
						</table>
					</div>
			</table>
            </div>
		</fieldset>
	    
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>	
	</div>
</form>
<script type="text/javascript">

function viewaccessarea(el, val, type) {
	document.getElementById(type + '_area_' + el.value + '_' + val).style.display = 'inline';
	document.getElementById(type + '_area_' + new Number(1-el.value) + '_' + val).style.display = 'none';
}

function disablerow(el, type) {
	document.getElementById(type + '_access_0_' + el.value).disabled = !document.getElementById(type + '_access_0_' + el.value).disabled;
	document.getElementById(type + '_access_1_' + el.value).disabled = !document.getElementById(type + '_access_1_' + el.value).disabled;
	document.getElementById(type + '_xdays_' + el.value).disabled = !document.getElementById(type + '_xdays_' + el.value).disabled;
	document.getElementById(type + '_attempts_' + el.value).disabled = !document.getElementById(type + '_attempts_' + el.value).disabled;
}

Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('product-form'));
	}

</script>
