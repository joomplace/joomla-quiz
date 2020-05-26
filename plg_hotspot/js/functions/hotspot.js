function getPosition_x(el){
	return 10;
	// var  left = 0, top = 0;
	// do {
	// 	left += el.offsetLeft || 0;
	// 	top += el.offsetTop || 0;
	// 	el = el.offsetParent;
	// } while (el);
	// return left;
}

function getPosition_y(el){
	return 10;
	// var  left = 0, top = 0;
	// do {
	// left += el.offsetLeft || 0;
	// 	top += el.offsetTop || 0;
	// 	el = el.offsetParent;
	// } while (el);
	// return top;
}

function getCirclePosition(el){
	var el = document.querySelector('.hotspot__circle');
	alert([el.style.left, el.style.right])
	return [el.style.left, el.style.right];
	// el = jq_jQuery(el[0]);
	// var scale = el.closest('svg').data('scale');
	// // return [el.attr('cx')*scale, el.attr('cy')*scale];
	// return [el.attr('cx'), el.attr('cy')];
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

// window.onresize = function(){
// 	setTimeout(_recalculateSize, 10);
// 	landscape = true;
// }
//
// if(!landscape){
// 	jQuery(window).bind( 'orientationchange', function(e){
// 		setTimeout(_recalculateSize, 10);
// 		landscape = true;
// 	});
// }

/*function _recalculateSize(){
	var hotspots = jq_jQuery('#foo > svg, .hotspot > svg');
    // console.log('in');

	if(hotspots.length){

		jq_jQuery(hotspots).each(function(){
			var svg = jq_jQuery(this);
            var wrapper = svg.parent();
			var img = svg.find('image');
			var src = img.attr('xlink:href');
            var circle = svg.find("circle");
			if(!src || src=='undefined'){
				src = img.attr('href');
			}
			// need to be removed after drawPolygons() will be refactored
            viewport_width = wrapper.width();
            var fullscaleimage = new Image();
            fullscaleimage.onload = function(){
                img_width_init = fullscaleimage.width;
                img_height_init = fullscaleimage.height;

                fullscaleimage.remove();

                var prev_svg_width = svg.width();
                var svg_width = svg.width();
                var wrapper_width = wrapper.width();

                if(svg_width < wrapper_width){
                    if(wrapper_width > img_width_init)
					svg_width = img_width_init;
				else{
                        svg_width = wrapper_width;
					}
			}

            var nwidth = svg_width;

			var ratio = img_height_init/img_width_init;
			var nheight = nwidth * ratio;

			svg.attr('width', nwidth);
                svg.attr('height', nheight);

                if(circle.data('scale')=="initial"){
                    // console.log('initial');
                    var cursor_adjust = nwidth/img_width_init;
                }else{
                    // console.log('prev');
                    var cursor_adjust = nwidth/img.attr('width');
                }
                // console.log(cursor_adjust);
                circle.data('scale','');

			img.attr('width', nwidth);
			img.attr('height', nheight);

                var cx = circle.attr('cx'); // horizontal percentage
                var cy = circle.attr('cy'); // vertical percentage

                var scale = img_width_init/nwidth;
                svg.data('scale',scale);

                circle.attr('cx', cx*cursor_adjust);
                circle.attr('cy', cy*cursor_adjust);

			scaleX = wOrigin / nwidth;
			scaleY = hOrigin / nheight;
			if(jq_jQuery('#foo > svg').length){
				svg.find("path").remove();
				drawPolygons();
			}

                prev_it_width = nwidth;
                prev_it_height = nheight;

			initial = 0;
            };
            fullscaleimage.src = src;
		});
	}
}*/
