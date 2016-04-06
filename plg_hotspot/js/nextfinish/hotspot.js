case '7':
	var cr = 0;
	var sx = circle.attr('cx');
	var sy = circle.attr('cy');

	if(path_elems.length){
		for(var j=0;j<path_elems.length;j++){
			if(path_elems[j].isPointInside(sx, sy)){
				cr = 1;
			}
		}
	}

	var answer = sx + ',' + sy + ',' + cr;
	null;

break;