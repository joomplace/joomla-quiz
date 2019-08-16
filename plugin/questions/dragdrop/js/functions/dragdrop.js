function setDrnDnAnswers(n) {
	var ans_count = 0;
	try {
		ans_count = questions[n].response.getElementsByTagName('ans_count')[0].firstChild.data;
	} catch(e){}
	var i = 0;
	var j = 0;
	if (ans_count > 0) {
		var lfield_id = 0;
		var rfield_id = 0;
		for (i = 1; i <= ans_count; i++) {
			lfield_id = questions[n].response.getElementsByTagName('lfield_id')[i-1].firstChild.data;
			rfield_id = questions[n].response.getElementsByTagName('rfield_id')[i-1].firstChild.data;

			if ( lfield_id > 0 && rfield_id > 0) {
				an_div = jq_getObj('cdiv'+questions[n].cur_quest_id+'_' + lfield_id);
				targ = jq_getObj('ddiv'+questions[n].cur_quest_id+'_' + rfield_id);
				targ.style.left	= parseInt((targ.offsetLeft - an_div.offsetLeft) / -2) + 'px';
				targ.style.top	= parseInt((an_div.offsetLeft - targ.offsetLeft) + 10) + 'px';
				last_drag_id = 'ddiv'+questions[n].cur_quest_id+'_'+rfield_id;
				last_drag_quest_n = n;
				questions[n].cont_index = lfield_id;
				stopDrag();
			}
		}
	}
}

function getCoords(elem) {
    var box = elem.getBoundingClientRect();
    coord = {
        top: box.top + pageYOffset,
        left: box.left + pageXOffset
    };
    coord.bottom = coord.top*1 + elem.clientHeight*1;
    coord.right = coord.left*1 + elem.clientWidth*1;
    return coord;
}

var dragLimits = [];

function startDrag(e){
	if(!e){var e=window.event};
	var targ=e.target?e.target:e.srcElement;
	if (targ.id.substring(0, 4) != 'ddiv') {return;}
	if (last_drag_id_drag != '') {
		if (last_drag_id_drag != targ.id) {return;}
	}
	var qid = parseInt(targ.id.substring(targ.id.lastIndexOf("ddiv")+4));

	for (var i=0; i<quest_count; i++) {
		if (questions[i].cur_quest_id == qid) {
			n = i;
			break;
		}
	}

	if (questions[n].disabled){return;}

	last_drag_quest_n = n;

	for (i=1; i<=questions[n].kol_drag_elems; i++) {
		an_div	= jq_getObj('ddiv'+questions[n].cur_quest_id+'_' + i);
		an_div.style.zIndex = 500;
	}
	targ.className = 'jq_draggable_div';
	targ.style.zIndex = 1000;
	targ.style.position = 'relative';
	last_drag_id = targ.id;
	last_drag_id_drag = targ.id;
	offsetX=e.clientX;
	offsetY=e.clientY;
	if(!targ.style.left){targ.style.left='0px'};
	if(!targ.style.top){targ.style.top='0px'};
	coordX=parseInt(targ.style.left);
	coordY=parseInt(targ.style.top);
	drag=true;

	questions[n].cont_index = 0;

	if( typeof dragLimits[targ.id] === "undefined" ){
        var parentAnswers = document.querySelector('.jq_question_answers_cont'),
            parentAnswersCoord = getCoords(parentAnswers),
            dragElemStartCoord = getCoords(targ);
        dragLimits[targ.id] = {
            top: (parentAnswersCoord.top - dragElemStartCoord.top),           // < 0
            bottom: (parentAnswersCoord.bottom - dragElemStartCoord.bottom),
            left:  (parentAnswersCoord.left - dragElemStartCoord.left),       // < 0
            right:  (parentAnswersCoord.right - dragElemStartCoord.right)
        };
    }

	document.onmousemove=dragDiv;
}

function dragDiv(e){
	var n = last_drag_quest_n;

	if(!drag){return};
	if(!e){var e = window.event};
	var targ = e.target?e.target:e.srcElement;
	if (last_drag_id_drag != '') {
		if (last_drag_id_drag != targ.id) {
			var ddd = jq_getObj(last_drag_id_drag);

            ddd.style.left = coordX+e.clientX-offsetX + 'px';
            if(parseInt(ddd.style.left, 10) < dragLimits[ddd.id].left*1){
                ddd.style.left = dragLimits[ddd.id].left + 'px';
            }
            if(parseInt(ddd.style.left, 10) > dragLimits[ddd.id].right*1){
                ddd.style.left = dragLimits[ddd.id].right + 'px';
            }

            ddd.style.top = coordY+e.clientY-offsetY + 'px';
            if(parseInt(ddd.style.top, 10) < dragLimits[ddd.id].top*1){
                ddd.style.top = dragLimits[ddd.id].top + 'px';
            }
            if(parseInt(ddd.style.top, 10) > dragLimits[ddd.id].bottom*1){
                ddd.style.top = dragLimits[ddd.id].bottom + 'px';
            }

			return;
		}
	}
	if (targ.id.substring(0, 4) != 'ddiv') {return;}

    targ.style.left = coordX+e.clientX-offsetX + 'px';
    if(parseInt(targ.style.left, 10) < dragLimits[targ.id].left*1){
        targ.style.left = dragLimits[targ.id].left + 'px';
    }
    if(parseInt(targ.style.left, 10) > dragLimits[targ.id].right*1){
        targ.style.left = dragLimits[targ.id].right + 'px';
    }

    targ.style.top = coordY+e.clientY-offsetY + 'px';
    if(parseInt(targ.style.top, 10) < dragLimits[targ.id].top*1){
        targ.style.top = dragLimits[targ.id].top + 'px';
    }
    if(parseInt(targ.style.top, 10) > dragLimits[targ.id].bottom*1){
        targ.style.top = dragLimits[targ.id].bottom + 'px';
    }

	var is_on_cont = false;
	for (i=1; i<=questions[n].kol_drag_elems; i++) {
		an_div = jq_getObj('cdiv'+questions[n].cur_quest_id+'_' + i);
		FDIV_RightX = an_div.offsetLeft + an_div.offsetWidth;
		SDIV_LeftX = targ.offsetLeft;
		FDIV_TopY = an_div.offsetTop;
		FDIV_DownY = an_div.offsetTop + an_div.offsetHeight;
		SDIV_MiddleY = targ.offsetTop + parseInt(targ.offsetHeight/2);
		if ( ((parseInt(FDIV_RightX) + 10) > (parseInt(SDIV_LeftX))) && ((parseInt(FDIV_DownY) + 10) > (parseInt(SDIV_MiddleY))) && ((parseInt(FDIV_TopY) - 10) < (parseInt(SDIV_MiddleY))) ) {
			an_div.className = 'jq_highlight_drag_div';
			questions[n].cont_index = i;
			is_on_cont = true;
		}
		else {
			an_div.className = 'jq_cont_drag_div';
		}
	}
	var dr_number = parseInt(last_drag_id.substring(last_drag_id.lastIndexOf("_")+1));

	for (i=1; i<=questions[n].kol_drag_elems; i++) {
		if (i != dr_number) {
			an_div = jq_getObj('ddiv'+questions[n].cur_quest_id+'_' + i);
			if ( (questions[n].coord_left[i]) && (questions[n].coord_left[i] != '') ) { an_div.style.left = questions[n].coord_left[i]; }
			if ( (questions[n].coord_top[i]) && (questions[n].coord_top[i] != '') ) { an_div.style.top = questions[n].coord_top[i]; }
		}
	}
	if (!is_on_cont) { questions[n].cont_index = 0; }

	return false;
}

function stopDrag(e){
	var n = last_drag_quest_n;
	if (n < 0) { return; }

	var dr_obj = jq_getObj(last_drag_id);
	var is_all_cont = 1;
	var is_all_ids = 1;
    var offset = 0;
	if (dr_obj) {
		var dr_number = parseInt(last_drag_id.substring(last_drag_id.indexOf('_')+1));
		dr_obj.className = 'jq_draggable_div';

        //Offset between two different blocks may vary depending on css
        if (questions[n].kol_drag_elems > 1) {
            var divId_1 = "cdiv" + questions[n].cur_quest_id + "_1";
            var divId_2 = "cdiv" + questions[n].cur_quest_id + "_2";
            divElem_1 = document.querySelector("#" + divId_1);
            divElem_2 = document.querySelector("#" + divId_2);
            offset = divElem_2.offsetTop - divElem_1.offsetTop;
        } else {
            offset = 1;
        }

        if (questions[n].cont_index) {
			dr_obj.style.position = 'relative';
			dr_obj.style.left = '-57px';

            dr_obj.style.top = parseInt((questions[n].cont_index - 1) * offset - (offset * (dr_number - 1)) + 7) + 'px';

			questions[n].ids_in_cont[questions[n].cont_index - 1] = dr_number;

			dr_obj.className = 'jq_draggable_stop_div';
		}

		questions[n].cont_for_ids[dr_number - 1] = questions[n].cont_index;
		questions[n].coord_left[dr_number] = dr_obj.style.left;
		questions[n].coord_top[dr_number] = dr_obj.style.top;

		dr_obj.style.zIndex = 499;
	}
	last_drag_id_drag = '';
	for (i=1; i<=questions[n].kol_drag_elems; i++) {
		an_div	= jq_getObj('cdiv'+questions[n].cur_quest_id+'_' + i);
		an_div.className = 'jq_cont_drag_div';
	}
	last_drag_quest_n = -1;

	drag=false;
}