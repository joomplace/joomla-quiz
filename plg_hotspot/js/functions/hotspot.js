function getCirclePosition(el){
    el = jq_jQuery(el[0]);
    var scale = el.closest('svg').data('scale');
    return [el.attr('cx')*scale, el.attr('cy')*scale];
}

var wOrigin = 0,
	hOrigin = 0,
	ratio = 1,
	scaleX = 0,
	scaleY = 0,
	paper = null,
	drawPolygons = null,
	landscape = false;

window.onresize = function(){
	setTimeout(_recalculateSize, 10);
	landscape = true;
}

if(!landscape){
	jQuery(window).bind('orientationchange', function(e){
		setTimeout(_recalculateSize, 10);
		landscape = true;
	});
}

function _recalculateSize(){
	var hotspots = jq_jQuery('#foo > svg, .hotspot > svg');
	if(hotspots.length){
		jq_jQuery(hotspots).each(function(){
			var svg = jq_jQuery(this),
				wrapper = svg.parent(),
				img = svg.find('image'),
				src = img.attr('href'),
            	circle = svg.find("circle");

			if(!src || src === 'undefined'){
				src = img.attr('xlink:href');
			}

            var fullscaleimage = new Image();
			fullscaleimage.src = src;

            fullscaleimage.onload = function(){
				wOrigin = fullscaleimage.width;
				hOrigin = fullscaleimage.height;
				ratio = wOrigin / hOrigin;

                var svg_width = svg.width(),
                	wrapper_width = wrapper.width();

				if(svg_width < wrapper_width){
					if(wrapper_width > wOrigin) {
						svg_width = wOrigin;
					} else {
						svg_width = wrapper_width;
					}
				}

            	var nwidth = svg_width,
					nheight = nwidth / ratio;

				scaleX = wOrigin / nwidth;
				scaleY = hOrigin / nheight;

				svg.attr('width', nwidth);
				svg.attr('height', nheight);
				svg.data('scale', scaleX);

				var cursor_adjust;
                if(circle.data('scale') == 'initial'){
                    cursor_adjust = nwidth / wOrigin;
                }else{
                    cursor_adjust = nwidth / img.attr('width');
                }
                circle.data('scale','');

				img.attr('width', nwidth);
				img.attr('height', nheight);

                var cx = circle.attr('cx');
                var cy = circle.attr('cy');

                circle.attr('cx', cx*cursor_adjust);
                circle.attr('cy', cy*cursor_adjust);

				if(jq_jQuery('#foo > svg').length){
					drawPolygons();
					svg.find('path').remove();
					var rect = svg.find('rect');
					rect.attr('width', nwidth);
					rect.attr('height', nheight);
				}

				fullscaleimage = null;
            };
		});
	}
}
