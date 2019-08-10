if (questions[i].cur_quest_type == 12) {
	createDD_imageMatch();
	var limit_time = jq_jQuery(response).find('quest_limit_time').text();
	if(parseInt(limit_time)) {
		quest_timer_sec = limit_time;
		jq_Start_Question_TickTack(limit_time);
		clearInterval(quest_timer);
		quest_timer = setInterval("jq_Start_Question_TickTack("+limit_time+")", 1000);
	}
}