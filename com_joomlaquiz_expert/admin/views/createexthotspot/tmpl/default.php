<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
$css = JFactory::getApplication()->input->get('t','');
				
$hs_message = '';
$hotspot = intval(JFactory::getApplication()->input->get('hotspot', 0));
if (!$hotspot) {
	echo JText::_('COM_JOOMLAQUIZ_NO_IMAGE');
	return;
}
$image_name = '';
$database = JFactory::getDBO();

$query = "SELECT `c_image` FROM `#__quiz_t_question` WHERE c_id = '".$hotspot."'";
$database->SetQuery( $query );
$image_name = $database->LoadResult();
if (!$image_name) {
	echo JText::_('COM_JOOMLAQUIZ_NO_IMAGE');
	return;
}

$image_path = "../images/joomlaquiz/images/".$image_name;

$hs_task = JFactory::getApplication()->input->get('hs_task', '');
if ($hs_task == 'save_hs') {
	
	$hs_areas = $_REQUEST['hs_areas'];
	$c_paths = json_encode($hs_areas);

	$query = "DELETE FROM `#__quiz_t_ext_hotspot` WHERE `c_question_id` = '".$hotspot."'";
	$database->SetQuery( $query );
	$database->query();
	
	$query = "INSERT INTO `#__quiz_t_ext_hotspot` (c_id, c_question_id, c_paths) "
	. "\n VALUES('', '".$hotspot."', '".$c_paths."')";
	$database->SetQuery( $query );
	$database->query();
	
}

$query = "SELECT * FROM `#__quiz_t_ext_hotspot` WHERE `c_question_id` = '".$hotspot."'";
$database->SetQuery( $query );
$row = $database->loadObject();

if ($row){
    $c_paths = $row->c_paths;  
} else {
    $c_paths = "";
}

$paths_array = json_decode($c_paths);

?>
<form method="post" action="index.php" name="HS_form"  id="HS_form">
	<input type="hidden" name="hotspot" value="<?php echo $hotspot;?>" />
	<input type="hidden" id="coord" size="100" style="width:100%" value="" />
	<input type="hidden" name="task" value="hotspot.saveexthotspot">
	<input type="hidden" name="option" value="com_joomlaquiz">
	<input type="hidden" name="tmpl" value="component">
	<input type="hidden" name="hs_task" value="save_hs">
	<input class="text_area" type="button" name="cr_hotspot" value="Save HotSpot" onclick="jq_SaveHotSpotArea();" />
	<input class="text_area" type="button" name="close_hotspot" value="Close Window" onclick="window.close();" />
</form>
<div id="foo"></div>
<script src="<?php echo JURI::root();?>administrator/components/com_joomlaquiz/assets/js/raphael.js" type="text/javascript"></script>
<script type="text/javascript">

	function jq_SaveHotSpotArea() {
		var form = document.HS_form;
		form.submit();
	}

	jQuery(document).ready(function() {

		jQuery('#coord').val('');

		var tmp_img = new Image();
		tmp_img.src = '<?php echo $image_path;?>';

		jQuery(tmp_img).load(function () {

			var paths = new Array();
			var h = tmp_img.height;
			var w = tmp_img.width;

			var circles = new Array();
			var path;
			var z = 0, ii = 0, n = 0;

			var paper = Raphael('foo', w, h);
			var img = paper.image('<?php echo $image_path;?>', 0, 0, w, h);
			var rect = paper.rect(0, 0, w, h).attr({fill: 'none'});

			img.click(function (event) {
				var event = event || window.event;
				if (!circles[z]) {
					circles[z] = new Array();

					path = paper.path();
					path.attr({stroke: 'white', 'stroke-width': 3, fill: '#147EDB', 'fill-opacity': 0.5});
					paths.push(path);
				}

				var qqq1 = event.pageY || (event.clientY + (document.documentElement.scrollTop || document.body.scrollTop));
				var qqq2 = event.pageX || (event.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));
				var base_y = getPosition_y('#foo') + 1;
				var base_x = getPosition_x('#foo') + 1;

				cx = Math.round(qqq2 - base_x);
				cy = Math.round(qqq1 - base_y);
				var r = 5;

				var c = paper.circle(cx, cy, r).attr({stroke: 'red', fill: 'orange'});
				ii++;
				c.number = ii;
				circles[z].push(c);

				mx = cx;
				my = cy;

				var oldpath = jQuery('#coord').val();
				if (!oldpath.match(/M/) || oldpath.match(/Z/)) {
					jQuery('#coord').val('M ' + mx + ' ' + my + ' ');
				} else {
					jQuery('#coord').val(oldpath + ' L ' + mx + ' ' + my);
				}
				redraw(path);

				bindMouseOnCircle(c, ii, path);
			});

			var drawPolygons = function () {
				var paths = new Array();
				<?php if(!empty($paths_array)):?>
				<?php foreach($paths_array as $path):?>
				paths.push('<?php echo $path;?>');
				<?php endforeach;?>
				<?php endif;?>

				if (paths.length) {
					for (var p = 0; p < paths.length; p++) {

						path = paper.path();
						path.attr({stroke: 'white', 'stroke-width': 3, fill: '#147EDB', 'fill-opacity': 0.5});
						path.attr({path: paths[p]});
						path.number = z;

						jQuery("<input type='hidden' name='hs_areas[]' id='hs_area_" + z + "' value='" + paths[p] + "'>").insertAfter("#coord");

						var regexp_m = /M\s*([0-9]*)\s*([0-9]*)/g;
						var m = paths[p].match(regexp_m);

						regexp_cmx = /([0-9]*)/g;
						var cm = m[0].match(regexp_cmx);
						cmx = parseInt(cm[2]);
						cmy = parseInt(cm[4]);

						if (!circles[z]) circles[z] = new Array();
						drawCircle(cmx, cmy, path);

						var regexp_l = /L\s*([0-9]*)\s*([0-9]*)/g;
						var l = paths[p].match(regexp_l);

						if (l.length) {
							for (var v in l) {
								regexp_clx = /([0-9]*)/g;
								var cl = l[v].match(regexp_clx);
								clx = parseInt(cl[2]);
								cly = parseInt(cl[4]);
								drawCircle(clx, cly, path);
							}
						}
						z++;
					}
				}

			}

			var drawCircle = function (cx, cy, path) {

				var r = 5;
				var c = paper.circle(cx, cy, r).attr({stroke: 'red', fill: 'orange'});
				ii++;
				c.number = ii;
				circles[z].push(c);
				bindMouseOnCircle(c, ii, path);

				return true;
			}

			var bindMouseOnCircle = function (c, b, path) {
				c.mouseover(function () {
					this.attr({fill: 'blue'});
				});

				c.mouseout(function () {
					this.attr({fill: 'orange'});
				});

				c.mousedown(function (event) {
					var event = event || window.event;

					if (event.button == 2) {

						removeLinesAndCircle(this, path);

						if (event.preventDefault)
							event.preventDefault();
						else
							event.returnValue = false;
						return false;
					} else if (event.button == 0) {
						var oldpath = jQuery('#coord').val();
						jQuery('#coord').val(oldpath + ' Z');
						redraw(path);

						jQuery("<input type='hidden' name='hs_areas[]' id='hs_area_" + z + "' value='" + jQuery('#coord').val() + "'>").insertAfter("#coord");
						jQuery('#coord').val('');
						z++;
					}
				});
			}

			var redraw = function (path) {
				path.attr({path: jQuery('#coord').val()});
				path.number = z;
			}

			var removeLinesAndCircle = function (circle, path) {

				cx = circle.attr('cx');
				cy = circle.attr('cy');

				var hs_area_input = jQuery('#hs_area_' + path.number);
				if (hs_area_input) {
					hs_area_input.remove();
				}

				var space = " ";
				var path_str = '';
				var coord = path.attr('path');
				for (var v in coord) {
					for (var b in coord[v]) {
						if (coord[v][b] == "Z") space = "";
						path_str += coord[v][b] + space;
					}
				}

				jQuery('#coord').val(path_str);
				var oldpath = jQuery('#coord').val();
				var regexp = new RegExp('(L\\s*' + cx + '\\s*' + cy + '\\s*)||(M\\s*' + cx + '\\s*' + cy + '\\s*)', 'gi');

				if (oldpath.match(regexp)) {
					oldpath = oldpath.replace(regexp, '');
					jQuery('#coord').val(oldpath);
					redraw(path);
				}

				delete(circle);
				circle.remove();

			}

			drawPolygons();
			jQuery(document).bind("contextmenu", function (e) {
				return false;
			});

			function getPosition_x (el) {
				var left = jQuery(el).offset().left;
				return left;
			}

			function getPosition_y(el) {
				var top = jQuery(el).offset().top;
				return top;
			}
		});
	});
</script>