function getPosition_x(el){
	return 10;
	var  left = 0, top = 0;
	do {
		left += el.offsetLeft || 0;
		top += el.offsetTop || 0;
		el = el.offsetParent;
	} while (el);
	return left;
}

function getPosition_y(el){
	return 10;
	var  left = 0, top = 0;
	do {
	left += el.offsetLeft || 0;
		top += el.offsetTop || 0;
		el = el.offsetParent;
	} while (el);
	return top;
}

var img_width_init = 0,
	wOrigin = 0,
	hOrigin = 0,
	viewport_width = 0,
	img_height_init = 0,
	scaleX = 0,
	scaleY = 0,
	initial = 1,
	drawPolygons = null,
	paper = null,
	getNewPath = null,
	landscape = false;
	prev_it_width = 0;
	prev_it_height = 0;

window.onresize = function(){
	setTimeout(_recalculateSize, 10);
	landscape = true;
}

if(!landscape){
	jQuery(window).bind( 'orientationchange', function(e){
		setTimeout(_recalculateSize, 10);
		landscape = true;
	});
}

function _recalculateSize(){
	var hotspots = jq_jQuery('#foo > svg, .hotspot > svg');
	
	if(hotspots.length){	
		jq_jQuery(hotspots).each(function(){
			var foo = jq_jQuery(this).parent();
			var svg = jq_jQuery(this);
			var img = svg.find('image');
			var src = img.attr('xlink:href');
			if(!src || src=='undefined'){
				src = img.attr('href');
			}
			// need to be removed after drawPolygons() will be refactored
			viewport_width = foo.width();
			var nimg = new Image();
			nimg.src = src;
			
			img_width_init = nimg.width;
			img_height_init = nimg.height;
			nimg.remove();
			
			svg_width = svg.width();
			foo_width = foo.width();
			
			if(svg_width < foo_width){
				if(foo_width > img_width_init)
					svg_width = img_width_init;
				else{
					svg_width = foo_width;
					}
			}

			nwidth = svg_width;
			
			var ratio = img_height_init/img_width_init;
			var nheight = nwidth * ratio;

			svg.attr('width', nwidth);
			img.attr('width', nwidth);

			svg.attr('height', nheight);
			img.attr('height', nheight);

			if(prev_it_width && prev_it_height){
				svg.find("circle").attr('cx', svg.find("circle").attr('cx')*nwidth/prev_it_width);
				svg.find("circle").attr('cy', svg.find("circle").attr('cy')*nheight/prev_it_height);
			}
			
			scaleX = wOrigin / nwidth;
			scaleY = hOrigin / nheight;
			if(jq_jQuery('#foo > svg').length){
				svg.find("path").remove();
				drawPolygons();
			}
			
			prev_it_width = nwidth;
			prev_it_height = nheight;

			initial = 0;
		});
	}
}