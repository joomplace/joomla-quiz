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
<style>
	#sample_area
	{
		height:150px;
	}
</style>
<table width="100%" class="table table-striped" id="qfld_tbl">
	<tr>
		<th><?php echo JText::_('COM_JOOMLAQUIZ_SHARP');?></th>
		<th class="title"><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_OPTIONS2');?></th>
		<th class="title"></th>
		<th><?php echo JText::_('COM_JOOMLAQUIZ_POINTS2');?></th>
		<th></th>
	</tr>
	<?php
	$k = 0; $ii = 1; $ind_last = (isset($row->memory_data)) ? count($row->memory_data) : 0;
	if(count($row->memory_data)){
	foreach ($row->memory_data as $frow) { ?>
	<tr class="<?php echo "row$k"; ?>">
		<td><?php echo $ii?></td>
		<td>
			<img src="<?php echo JURI::root();?>images/joomlaquiz/images/memory/<?php echo $frow->c_img?>" height="100" />
			<input type="hidden" name="jq_hid_fields[]" value="<?php echo $frow->c_img;?>" />
			<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->m_id;?>" />
		</td>
		<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE');?>"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE');?>"></a></td>
		
		<td><input class="text_area" size="5" type="text" name="jq_hid_fields_points[]" value="<?php echo $frow->a_points?>"  /></td>
		<td><input class="text_area pairs" size="5" type="hidden" name="jq_hid_fields_pairs[]" value="<?php echo $frow->a_pairs?>"  /></td>
	</tr>
	<?php
	$k = 1 - $k; $ii ++;
	} 
	}?>
	</table><br>
	<table class="table table-striped">
	<tr>
		<th class="title"><?php echo JText::_('COM_JOOMLAQUIZ_ADD_NEW_OPTIONS');?></th>
	</tr>
	<tr>
	<td>
	<div>
		<div style="float:left;">
			<div style="height:100px;margin-bottom:20px;">
				<img style="height:100px" src="<?php echo JURI::root();?>images/joomlaquiz/images/memory/tnnophoto.jpg" id="imagelib" />
			</div>
			<?php echo $lists['imagelist'];?>&nbsp;&nbsp;
		</div>
		<div style="float:left;margin-top:120px;">
			<?php echo JText::_('COM_JOOMLAQUIZ_POINTS2');?>:&nbsp;<input id="new_field_points" class="text_area" size="5" type="text" name="new_field_points"  />&nbsp;&nbsp;
			<input id="new_field_pairs" class="text_area" size="5" type="hidden" name="new_field_pairs"  value="1"/>
			<input class="btn" type="button" name="add_new_field" value="Add" onclick="javascript:Add_new_tbl_field('picture', 'new_field_points', 'new_field_pairs', 'qfld_tbl', 'jq_hid_fields[]', 'jq_hid_fields_points[]', 'jq_hid_fields_pairs[]');" />
			
		</div>
		<div style="clear:both;"></div>
		<div align="left" style="margin-top:82px;">
				Upload new image:
				<input type="file" name="Filedata" class="btn btn-small" id="Filedata" />
				<input type="button" name="add_new_field" value="Upload Image" class="btn" onclick="jq_uploadMemoryImage();" />
				<iframe style="display:none" src="javascript:void(0);" name="brkFrame" id="brkFrame"></iframe>
				<input type="hidden" name="plgtask" value="" id="plgtask" />
		</div>
	</div>
	</td>
</tr>
<tr>
	<th class="title" align="left"><?php echo JText::_('COM_JOOMLAQUIZ_SAMPLE_AREA');?></th>
</tr>
<tr>	
	<td>
		<div id="sample_area"></div>
	</td>
</tr>
<tr>
	<th class="title" align="left"><?php echo JText::_('COM_JOOMLAQUIZ_ADD_NEW_COVER');?></th>
</tr>
<tr>
	<td>
		<div>
			<div>
				<div style="height:100px;margin-bottom:20px;">
					<img style="height:100px" src="<?php echo JURI::root();?>images/joomlaquiz/images/memory/<?php echo ((isset($row->c_img_cover)) ? $row->c_img_cover : 'tnnophoto.jpg');?>" id="imagelib_cover"/>
				</div>
				<?php echo $lists['imagelist_cover'];?>&nbsp;&nbsp;
				<input type="hidden" name="c_img_cover" value="<?php echo ((isset($row->c_img_cover)) ? $row->c_img_cover : 'tnnophoto.jpg');?>" id="c_img_cover" />
			</div>
		</div>
	</td>
</tr>
</table>
<script type="text/javascript">
	jQuery(document).ready(function(){
		var c_pairs = jQuery('.pairs');
		var summ_pairs = 0;
		var output = '';
		var bkg = '';
		
		jQuery(c_pairs).each(function(i, obj){
			summ_pairs += parseInt(obj.value);
		});
		
		if(summ_pairs!=0){
			var cols = parseInt(<?php echo ((isset($row->c_column)) ? $row->c_column : 0); ?>);
			var sq = summ_pairs * 2;
			var rows = (cols) ? Math.ceil(sq / cols) : 1;
			var cc = 1;
			for(var m=1; m <= rows; m++){
				for(var k=1; k <= cols; k++){
					bkg = (cc > summ_pairs * 2) ? 'red' : 'green';
					output += '<div style="display:block;float:left;width:25px;height:25px;background:'+bkg+';border:1px solid #cccccc;"></div>';
					cc++;
				}
				output += '<div style="clear:both;"></div>';
			}
		}
		
		jQuery('#sample_area').html(output);
	});
</script>