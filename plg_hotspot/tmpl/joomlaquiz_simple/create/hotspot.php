<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaquiz Deluxe class
 */
class JoomlaquizViewCreateHotspot
{
	public static function getQuestionContent($hotspot, $data)
    {
        $live_site = JURI::root();

        $hotspot['c_select_x'] = (isset($hotspot['c_select_x'])? $hotspot['c_select_x']: 0);
        $hotspot['c_select_y'] = (isset($hotspot['c_select_y'])? $hotspot['c_select_y']: 0);

        if($hotspot['c_select_x'] && $hotspot['c_select_y']){
            $circle = "circle = paper.circle(".$hotspot['c_select_x'].", ".$hotspot['c_select_y'].", 5).attr({stroke:'red', fill:'orange'});";
        } else {
            $circle = "circle = null;";
        }

        $path_str = '';
        if(!empty($data['hs_data_array'])){
            foreach($data['hs_data_array'] as $path){
                $path_str .= "paths.push('".$path."')"."\n\t\t\t\t";
            }
        }

        $imagesizes = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$data['q_data']->c_image);
        $w = $imagesizes[0];
        $h = $imagesizes[1];
        $koef = round($w/$h, 5);

        $jq_tmpl_html = <<<HTMLEND
		
		function getPosition_x(el) {
			var left = jq_jQuery(el).offset().left;
			return left;
		}	
			
		function getPosition_y(el) {
			var top = jq_jQuery(el).offset().top;
			return top;
		}

        wOrigin = {$w};
		hOrigin = {$h};
        ratio = {$koef};
        
		jq_jQuery('#foo').css({'width':'100% !important'});
		var w = jq_jQuery('#foo').width();
		
		var viewport_width = jq_jQuery('body').width();
		if(viewport_width < w){
		    w = viewport_width - 50;
		}
		
		var h = w / ratio;
		
		scaleX = wOrigin / w;
		scaleY = hOrigin / h;
		
		paper = Raphael('foo', w, h);
		var img = paper.image('{$live_site}images/joomlaquiz/images/{$data['q_data']->c_image}', 0, 0, w, h);
		var rect = paper.rect(0, 0, w, h).attr({fill:'none'});
        
        path_elems.length=0;
        
		drawPolygons = function(){
			var paths = new Array();
			{$path_str}
			if(paths.length){
				for(var p = 0;p < paths.length;p++){
					
					var pX = [];
					var pY = [];

					var ps = paths[p].split('L');
					if(ps.length){
						for(var j = 0, len = ps.length; j < len; j += 1){
							ps[j] = ps[j].replace('M', '');
							ps[j] = ps[j].replace('Z', '');

							var coords = ps[j].split(' ');
							if(coords.length){
								var tX = scaleX > 1 ? (coords[1] / scaleX) : (coords[1] * scaleX);
								var tY = scaleY > 1 ? (coords[2] / scaleY) : (coords[2] * scaleY);
								pX.push(tX);
								pY.push(tY);
							}
						}
					}

					if(pX.length && pY.length){
						tPath = '';
						for(var n = 0, len = pX.length; n < len; n += 1){
							if(!n){
								tPath += 'M ' + pX[n] + ' ' + pY[n] + ' ';
							} else {
								tPath += 'L ' + pX[n] + ' ' + pY[n] + ' ';
								if(n == (len - 1) ){
									tPath += 'Z';
								}
							}
						}
					}

					paths[p] = tPath;
					path = paper.path();
					path.attr({fill: 'none', 'stroke': 'none'});
					path.attr({path: paths[p]});
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