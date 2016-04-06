case '13':
	clearInterval(quest_timer);
	jq_jQuery('.jq_quest_time_past').html('');
	if(jq_jQuery('#jq_total_memory_point')){
		jq_jQuery('#jq_total_memory_point').remove();
		jq_jQuery('#jq_current_memory_point').remove();
		jq_jQuery('#jq_penalty_memory_point').remove();
	}
break;