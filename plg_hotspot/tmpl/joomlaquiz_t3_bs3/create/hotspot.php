<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaquiz Deluxe class
 */
class JoomlaquizViewCreateHotspot
{
	public static function getQuestionContent($hotspot, $data){
		
		$live_site = JURI::root();
		$count_hotspot = count($hotspot);
		
		$hotspot['c_select_x'] = (isset($hotspot['c_select_x'])? $hotspot['c_select_x']: 0);
		$hotspot['c_select_y'] = (isset($hotspot['c_select_y'])? $hotspot['c_select_y']: 0);
		
		if($hotspot['c_select_x'] && $hotspot['c_select_y']){
			$circle = "circle = paper.circle(".$hotspot['c_select_x'].", ".$hotspot['c_select_y'].", 5).attr({stroke:'red', fill:'orange'});";
		} else {
			$circle = "circle = null;";
		}
		
		if(count($data['hs_data_array'])){
			foreach($data['hs_data_array'] as $path){
				$path_str .= "paths.push('".$path."')"."\n\t\t\t\t";
			}
		}
		
		$imagesizes = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image);
		$w = $imagesizes[0];
		$h = $imagesizes[1];
		
		$jq_tmpl_html = <<<HTMLEND
		
		function getPosition_x(el) {
			var left = jq_jQuery(el).offset().left;
			return left;
		}	
			
		function getPosition_y(el) {
			var top = jq_jQuery(el).offset().top;
			return top;
		}
		
		var h = {$h};
		var w = {$w};
		var l = getPosition_x('#foo');
		var t = getPosition_y('#foo');
		
		var container_width = jQuery('#jq_quiz_container').width();
		var proportion = h / w;
		
		var new_w = container_width;
		var new_h = container_width * proportion;
		
		var rect_propotion = new_h / h;	
		
		var paper = Raphael('foo', new_w, new_h);
		var img = paper.image('{$live_site}images/joomlaquiz/images/{$data['q_data']->c_image}', 0, 0, new_w, new_h);
		var rect = paper.rect(0, 0, new_w, new_h).attr({fill:'none'});
		
		var drawPolygons = function(){
				var paths = new Array();
				path_elems = new Array();
				path_params = new Array();
				{$path_str}

				if(paths.length){
					for(var p = 0;p < paths.length;p++){					
						path = paper.path();
						path.attr({fill: 'none', 'stroke': 'none'});
						path.attr({path: paths[p]});
						
						path_params = path.attr('path');

						/* Resize click area */
						for (var j = 0; j < path_params.length; j++) {

							if (path_params[j][0] == 'L' || path_params[j][0] == 'M') {

								if (Number.isInteger(path_params[j][1])) {
									path_params[j][1] = path_params[j][1] * rect_propotion;
								}
								if (Number.isInteger(path_params[j][2])) path_params[j][2] = path_params[j][2] * rect_propotion;

							}
						}
						
						path.attr('path', path_params);
						path_elems.push(path);
					}
				}
		}
		drawPolygons();
		
		{$circle}
		img.click(function(event){
			var event = event || window.event;
			var x = event.clientX;
			var y = event.clientY;
			var r = 5;
			
			var qqq1 = event.pageY || (event.clientY + (document.documentElement.scrollTop || document.body.scrollTop));
			var qqq2 = event.pageX || (event.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));
			var base_y = getPosition_y('#foo') + 1;
			var base_x = getPosition_x('#foo') + 1;
			
			cx = Math.round(qqq2 - base_x);
			cy = Math.round(qqq1 - base_y);
			
			if (circle == null){
				circle = paper.circle(cx, cy, r).attr({stroke:'red', fill:'orange'});
			} else {
				circle.attr({cx: cx, cy: cy, r: r});
			}
		});		
HTMLEND;
		
		return $jq_tmpl_html;
	}
}
?>