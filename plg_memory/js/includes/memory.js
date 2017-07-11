if(questions[i].cur_quest_type == 13){
	jq_jQuery('.jq_time_tick_container').prepend('<div id="jq_total_memory_point">' + totalPoint + '&nbsp;<span>0</span></div>');
	jq_jQuery('.jq_time_tick_container').prepend('<div id="jq_current_memory_point">' + currentPoint + '&nbsp;<span>0</span></div>');
	jq_jQuery('.jq_time_tick_container').prepend('<div id="jq_penalty_memory_point">' + penaltyPoint + '&nbsp;<span>0</span></div>');
	initMemory();
	var limit_time = jq_jQuery(response).find('quest_limit_time').text();
	if(parseInt(limit_time)) {
			quest_timer_sec = limit_time;
			jq_Start_Question_TickTack(limit_time);
			clearInterval(quest_timer);
			quest_timer = setInterval("jq_Start_Question_TickTack("+limit_time+")", 1000);
	}
} else {
	jq_jQuery('.jq_quest_time_past').html('');
	if(jq_jQuery('#jq_total_memory_point')){
		jq_jQuery('#jq_total_memory_point').remove();
		jq_jQuery('#jq_current_memory_point').remove();
		jq_jQuery('#jq_penalty_memory_point').remove();
	}
}