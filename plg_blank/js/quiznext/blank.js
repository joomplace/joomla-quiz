case '6':
	var blank_count = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.blnk_cnt.value'));
	var answer = '';

	var fact_blank_count = jq_jQuery('.jq_blank.q'+questions[n].cur_quest_id).length;

	for(i=1;i<blank_count+1;i++){
		var blank_item = eval('document.quest_form'+questions[n].cur_quest_id+'["quest_blank_"+'+i+']');
		var res = TRIM_str(blank_item.value);
		answer = answer + jq_Escape(res) + '```';
	}
	if(answer)	{
		answer = answer.substring(0, answer.length - 3);
	}
break;