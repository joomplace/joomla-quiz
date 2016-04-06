if (questions[i].cur_quest_type == 14) {	
	var task = jq_jQuery(response).find('task').text();
	if(jq_jQuery(response).find('sq_delayed').text()){
		sq_delayed = jq_jQuery(response).find('sq_delayed').text();
	}
	img_width = jq_jQuery(response).find('img_width').text();
	img_height = jq_jQuery(response).find('img_height').text();
	
	dc_w = Math.floor(img_width/5);
	dc_h = Math.floor(img_height/5);
}

if(questions[i].cur_quest_type == 14 && task != 'review_start' && task != 'review_next'){
	
	Dalliclick_init();
}

