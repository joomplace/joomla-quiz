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
	
	if ((sx != 0) && (sy != 0)) {
		var answer = sx + ',' + sy + ',' + cr;
		null;
	} else {
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
		setTimeout("jq_releaseBlock()", 1000);
		return false;
	}
break;