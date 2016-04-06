case '6':
	var blank_count = parseInt(eval('document.quest_form'+questions[n].cur_quest_id+'.blnk_cnt.value'));
	for(i=1;i<blank_count+1;i++){
		var blank_item = eval('document.quest_form'+questions[n].cur_quest_id+'["quest_blank_"+'+i+']');
		blank_item.disabled = 'disabled';
	}
	jq_jQuery(".jq_draggable_answer").draggable("destroy");
	jq_jQuery('.jq_blank_draggable').draggable("destroy");
	jq_jQuery(".jq_blank_droppable").droppable("destroy");
break;