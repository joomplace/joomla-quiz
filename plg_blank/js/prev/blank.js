case '6':
	var blank_count = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.blnk_cnt.value'));
	var answer = '';

	var fact_blank_count = jq_jQuery('.jq_blank').length;
	if(fact_blank_count != blank_count){
		try{ ScrollToElement(jq_getObj('quest_div'+questions[n].cur_quest_id));} catch(e) {}
			ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_question_is_misconfigured);
			return false;
	}
	
	for(i=1;i<blank_count+1;i++){
		var blank_item = eval('document.quest_form'+questions[n].cur_quest_id+'["quest_blank_"+'+i+']');
		var res = TRIM_str(blank_item.value);
		if (false && res == '') {
			no_answer = true;
		} else {
			answer = answer + jq_Escape(res) + '```';
		}
	}
	if(answer)	{
		answer = answer.substring(0, answer.length - 3);
	} else {
		no_answer = true;
	}
break;