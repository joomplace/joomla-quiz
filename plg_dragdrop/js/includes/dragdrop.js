if (questions[i].cur_quest_type == 4) {
	//Drag and drop

	questions[i].kol_drag_elems = eval('kol_drag_elems'+questions[i].cur_quest_id);
	questions[i].drag_array = new Array(questions[i].kol_drag_elems);
	questions[i].coord_left = new Array(questions[i].kol_drag_elems);
	questions[i].coord_top = new Array(questions[i].kol_drag_elems);
	questions[i].ids_in_cont = new Array(questions[i].kol_drag_elems); // what div id's in containers
	questions[i].cont_for_ids = new Array(questions[i].kol_drag_elems); //in that container this id
	questions[i].answ_ids = new Array(questions[i].kol_drag_elems);
	questions[i].answ_ids = eval('answ_ids'+questions[i].cur_quest_id);
	questions[i].cont_index = 0;

	for(var j =1; j <= questions[i].kol_drag_elems; j++) {
		jq_jQuery('div#ddiv'+questions[i].cur_quest_id+'_'+j).mouseup(function(event){
			stopDrag(event);
		}).mousedown(function(event){
			startDrag(event);
		});

		//for Firefox
		document.getElementById('ddiv'+questions[i].cur_quest_id+'_'+j).ondragstart = function() {
			return false;
		};
	}

	setDrnDnAnswers(i);

	jQuery(function ($) {
		var dragdropTable = $('.dragdrop_table');
		if($(dragdropTable).length) {
			jpEqualHeight($(dragdropTable).find('td'));
			jpEqualHeight($(dragdropTable).find('td>div'));
		}
	});

}