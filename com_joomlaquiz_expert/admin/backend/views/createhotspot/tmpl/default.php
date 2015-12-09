<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$css = JFactory::getApplication()->input->get('t','');
				
$hs_message = '';
$hotspot = intval(JFactory::getApplication()->input->get('hotspot', 0));
if (!$hotspot) {
	echo JText::_('COM_JOOMLAQUIZ_NO_IMAGE');
	return;
}
$image_name = '';
$database = JFactory::getDBO();
$query = "SELECT c_image FROM #__quiz_t_question WHERE c_id = '".$hotspot."'";
$database->SetQuery( $query );
$image_name = $database->LoadResult();
if (!$image_name) {
	echo JText::_('COM_JOOMLAQUIZ_NO_IMAGE');
	return;
}
$hs_task = JFactory::getApplication()->input->get('hs_task', '');
if ($hs_task == 'save_hs') {
	$c_start_x = intval(JFactory::getApplication()->input->get('c_start_x', 0));
	$c_start_y = intval(JFactory::getApplication()->input->get('c_start_y', 0));
	$c_end_x = intval(JFactory::getApplication()->input->get('c_end_x', 0));
	$c_end_y = intval(JFactory::getApplication()->input->get('c_end_y', 0));
	$c_width = $c_end_x - $c_start_x;
	if ($c_width < 0) $c_width = 0;
	$c_height = $c_end_y - $c_start_y;
	if ($c_height < 0) $c_height = 0;
	$query = "DELETE FROM #__quiz_t_hotspot WHERE c_question_id = '".$hotspot."'";
	$database->SetQuery( $query );
	$database->execute();
	$query = "INSERT INTO #__quiz_t_hotspot (c_question_id, c_start_x, c_start_y, c_width, c_height) "
	. "\n VALUES('".$hotspot."', '".$c_start_x."', '".$c_start_y."', '".$c_width."', '".$c_height."')";
	$database->SetQuery( $query );
	$database->execute();
	$hs_message = JText::_('COM_JOOMLAQUIZ_HOTSPOT_AREA');
}
	$query = "SELECT * FROM #__quiz_t_hotspot WHERE c_question_id = '".$hotspot."'";
	$database->SetQuery( $query );
	$hotspot_data = $database->LoadObjectList();
	$hs_lefttop_x = 0;
	$hs_lefttop_y = 0;
	$hs_rightbottom_x = 0;
	$hs_rightbottom_y = 0;
	if (isset($hotspot_data[0])) {
		$hs_lefttop_x = $hotspot_data[0]->c_start_x;
		$hs_lefttop_y = $hotspot_data[0]->c_start_y;
		$hs_rightbottom_x = $hotspot_data[0]->c_start_x + $hotspot_data[0]->c_width;
		$hs_rightbottom_y = $hotspot_data[0]->c_start_y + $hotspot_data[0]->c_height;
	}
	$directory = 'joomlaquiz';
	$css = JFactory::getApplication()->input->get('t','');
	if (defined('_ISO')) {
		$iso = explode( '=', _ISO );
	}
?>
<script language="javascript" type="text/javascript">
	<!--
	var hs_step = 1;
	var hs_begin_x = 0;
	var hs_begin_y = 0;
	var hs_lefttop_x = <?php echo $hs_lefttop_x?>;
	var hs_lefttop_y = <?php echo $hs_lefttop_y?>;
	var hs_rightbottom_x = <?php echo $hs_rightbottom_x?>;
	var hs_rightbottom_y = <?php echo $hs_rightbottom_y?>;
	function getObj(name) {
		if (document.getElementById)  {  return document.getElementById(name);  }
		else if (document.all)  {  return document.all[name];  }
		else if (document.layers)  {  return document.layers[name];  }
	}
	function JQ_img_click_handler(hs_event) {
		if(!hs_event) { var hs_event = window.event }
		var hs_div = getObj('div_hotspot_rec');
		var hs_img = getObj('img_hotspot');
		if (hs_step == 1) {
			hs_div.style.left = hs_event.clientX+"px";
			hs_begin_x = hs_event.clientX
			hs_div.style.top = hs_event.clientY+"px";
			hs_begin_y = hs_event.clientY;
			hs_div.style.width = 0;
			hs_div.style.height = 0;
			hs_lefttop_x = hs_event.clientX - hs_img.offsetLeft;
			hs_lefttop_y = hs_event.clientY - hs_img.offsetTop;
		}
		if (hs_step == 2) {
			hs_div.style.width = (((hs_event.clientX - hs_begin_x) > 0)?(hs_event.clientX - hs_begin_x):0)+"px";
			hs_div.style.height = (((hs_event.clientY - hs_begin_y) > 0)?(hs_event.clientY - hs_begin_y):0)+"px";
			hs_rightbottom_x = hs_event.clientX - hs_img.offsetLeft;
			hs_rightbottom_y = hs_event.clientY - hs_img.offsetTop;
		}
		if (hs_step == 1) {hs_step = 2;} else { hs_step = 1;}
			getObj('div_log').innerHTML = "<?php echo JText::_('COM_JOOMLAQUIZ_LEFTTOP');?>X = "+hs_lefttop_x+"; Y = "+hs_lefttop_y+"<br><?php echo JText::_('COM_JOOMLAQUIZ_RIGHT_BOTTOM');?>X = "+hs_rightbottom_x+"; Y = "+hs_rightbottom_y;
		}
		function JQ_img_move_handler(hs_event) {
			if(!hs_event) { var hs_event = window.event }
			if (hs_step == 2) {
				var hs_img = getObj('img_hotspot');
				var hs_div = getObj('div_hotspot_rec');
				hs_div.style.width = (((hs_event.clientX - hs_begin_x) > 0)?(hs_event.clientX - hs_begin_x):0)+"px";
				hs_div.style.height = (((hs_event.clientY - hs_begin_y) > 0)?(hs_event.clientY - hs_begin_y):0)+"px";
				hs_rightbottom_x = hs_event.clientX - hs_img.offsetLeft;
				hs_rightbottom_y = hs_event.clientY - hs_img.offsetTop;
				getObj('div_log').innerHTML = "<?php echo JText::_('COM_JOOMLAQUIZ_LEFTTOP');?>X = "+hs_lefttop_x+"; Y = "+hs_lefttop_y+"<?php echo JText::_('COM_JOOMLAQUIZ_RIGHT_BOTTOM');?>X = "+hs_rightbottom_x+"; Y = "+hs_rightbottom_y;
			}
		}
		function jq_SaveHotSpotArea() {
			if ((hs_lefttop_x == 0) && (hs_lefttop_y == 0) && (hs_rightbottom_x == 0) && (hs_rightbottom_y == 0)) {
				alert("<?php echo JText::_('COM_JOOMLAQUIZ_HOTSPOT_AREA_NOT');?>");return false;
			} else {
				var form = document.HS_form;
				form.c_start_x.value = hs_lefttop_x;

				form.c_start_y.value = hs_lefttop_y;
				form.c_end_x.value = hs_rightbottom_x;
				form.c_end_y.value = hs_rightbottom_y;
				form.submit();
			}
		}
//-->
</script>
		
		
<form method="post" action="index.php" name="HS_form"  id="HS_form">			
	<div id="div_log"><?php echo "Left-Top corner: X = ".$hs_lefttop_x."; Y = ".$hs_lefttop_y."<br>Right-Bottom corner: X = ".$hs_rightbottom_x."; Y = ".$hs_rightbottom_y;?></div>
	<input type="hidden" name="hotspot" value="<?php echo $hotspot;?>" />
	<input type="hidden" name="t" value="<?php echo $css?>" />
	<input type="hidden" name="c_start_x" value="<?php echo $hs_lefttop_x?>">
	<input type="hidden" name="c_start_y" value="<?php echo $hs_lefttop_y?>">
	<input type="hidden" name="c_end_x" value="<?php echo $hs_rightbottom_x?>">
	<input type="hidden" name="c_end_y" value="<?php echo $hs_rightbottom_y?>">
	<input type="hidden" name="hs_task" value="save_hs">
	<input class="text_area" type="button" name="cr_hotspot" value="Save HotSpot" onclick="jq_SaveHotSpotArea();" />
	<input type="hidden" name="task" value="hotspot.savehotspot">
	<input type="hidden" name="option" value="com_joomlaquiz">
	<input type="hidden" name="tmpl" value="component">
</form>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" height="600px"><tr><td align="left" valign="top">
		<tr><td valign="bottom" align="center">
			<div class="message"><?php echo $hs_message?></div>
		</td></tr></table>		
		<div id="div_hotspot_rec" style="background-color:#FFFFFF; <?php if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) { echo "filter:alpha(opacity=50);";}?> -moz-opacity:.50; opacity:.50; border:1px solid #000000; position:absolute; left:<?php echo $hs_lefttop_x+10?>px; top:<?php echo $hs_lefttop_y+100?>px; width:<?php echo ($hs_rightbottom_x - $hs_lefttop_x)?>px; height:<?php echo ($hs_rightbottom_y - $hs_lefttop_y)?>px; z-index:10; " onMouseDown="JQ_img_click_handler(event)" onmousemove="JQ_img_move_handler(event)"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/blank.png" border="0" width="1" height="1"></div>
		<img id="img_hotspot" src="<?php echo JURI::root();?>/images/joomlaquiz/images/<?php echo $image_name?>" onclick="JQ_img_click_handler(event)" onMouseMove="JQ_img_move_handler(event)" style="position:absolute; left:10px;top:100px; z-index:1;" />