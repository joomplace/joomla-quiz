case '12':
	var jq_left = jq_jQuery('.jq_left_text');
	var jq_right = jq_jQuery('.jq_right_text');
	var jq_complete = jq_jQuery('.jq_complete');
	var answer = '';
	var complete = true;
	
	jq_jQuery(jq_left).each(function(i){
		if(jq_complete[i].value == 'false'){
			complete = false;
		} else {
			answer +=  jq_left[i].value + '|||' + jq_right[i].value + '```';								
		}
	});
							
	if (complete) {
		answer = URLencode(answer.substring(0, answer.length - 3));
		clearInterval(quest_timer);
		jq_jQuery('.jq_quest_time_past').html('');
		null;
	}
	
break;