<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

?>
<table width="100%" class="table table-striped" id="qfld_tbl">
	<tr>
		<th><?php echo JText::_('COM_JOOMLAQUIZ_SHARP');?></th>
		<th><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_OPTIONS2');?></th>
		<th class="title"></th>
		<th class="title"></th>
		<th class="title"></th>
		<th class="title"></th>
		<th><?php echo JText::_('COM_JOOMLAQUIZ_POINTS2');?></th>
	</tr>
	<?php
	$k = 0; $ii = 1; $ind_last = count($row->matching);
	foreach ($row->matching as $frow) { ?>
	<tr class="<?php echo "row$k"; ?>">
		<td><?php echo $ii?></td>
		<td>
			<img src="<?php echo JURI::root();?>images/joomlaquiz/images/resize/<?php echo htmlspecialchars(stripslashes($frow->c_left_text))?>" width="100" height="100" />
			<input type="hidden" name="jq_hid_fields_left[]" value="<?php echo htmlspecialchars(stripslashes($frow->c_left_text))?>" />
			<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id?>" />
		</td>
		<td>
			<img src="<?php echo JURI::root();?>images/joomlaquiz/images/resize/<?php echo htmlspecialchars(stripslashes($frow->c_right_text))?>" width="100" height="100" />
			<input type="hidden" name="jq_hid_fields_right[]" value="<?php echo htmlspecialchars(stripslashes($frow->c_right_text))?>" />
		</td>
		<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE');?>"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE');?>"></a></td>
		<td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP');?>"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP');?>"></a><?php } ?></td>
		<td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN');?>"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN');?>"></a><?php } ?></td>
		<td><input class="text_area" size="5" type="text" name="jq_hid_fields_points[]" value="<?php echo $frow->a_points?>"  /></td>
	</tr>
	<?php
	$k = 1 - $k; $ii ++;
	} ?>
	</table><br>
	<table class="table table-striped" style="width:100%">
	<tr>
		<th class="title" align="left"><?php echo JText::_('COM_JOOMLAQUIZ_ADD_NEW_OPTIONS');?></th>
	</tr>
	<tr>
	<td>
	<div>
		<div style="float:left">
			<div style="height:140px;">
				<img src="<?php echo JURI::root();?>images/joomlaquiz/images/resize/tnnophoto.jpg" id="imagelib_left" width="100" height="100" />
			</div>
			<br/>
			<?php echo $lists['imagelist_left'];?>&nbsp;&nbsp;
		</div style="float:left">
		<div style="float:left">
			<div style="height:140px;">
				<img src="<?php echo JURI::root();?>images/joomlaquiz/images/resize/tnnophoto.jpg" id="imagelib_right" width="100" height="100" />
			</div>	
			<br/>
			<?php echo $lists['imagelist_right'];?>&nbsp;&nbsp;
		</div>
		<div style="float:left;margin-top:157px;">
			<?php echo JText::_('COM_JOOMLAQUIZ_POINTS2');?>:&nbsp;<input id="new_field_points" class="text_area" size="5" type="text" name="new_field_points"  />&nbsp;&nbsp;
			<input class="btn" type="button" name="add_new_field" style="width:70px " value="Add" onclick="javascript:Add_new_tbl_field('picture_left', 'picture_right', 'new_field_points', 'qfld_tbl', 'jq_hid_fields_left[]', 'jq_hid_fields_right[]', 'jq_hid_fields_points[]');" />
		</div>
	</div>
	<div style="clear:both;"></div>
	<div style="margin-top:35px;">
		Upload new image:
		<input type="file" name="Filedata" class="btn btn-small" id="Filedata" />
		<input class="btn" type="button" name="add_new_field" value="Upload Image" onclick="jq_uploadImage();" />
		<iframe style="display:none" src="javascript:void(0);" name="brkFrame" id="brkFrame"></iframe>
	</div>
	<input type="hidden" name="plgtask" value="" id="plgtask"/>
	</td>
</tr>
</table>