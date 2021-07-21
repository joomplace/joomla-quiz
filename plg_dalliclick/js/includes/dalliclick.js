if (questions[i].cur_quest_type == 14) {
	if(questions.length==1) {
		startDalliclick(questions[i].cur_quest_id,stu_quiz_id);
	} else {
		jQuery('#qcontainer'+questions[i].cur_quest_id+' .jq_question_answers_cont').hide();
		jQuery('#qcontainer'+questions[i].cur_quest_id).append('<button onClick="startDalliclick('+questions[i].cur_quest_id+','+stu_quiz_id+')">Start' +
			' Dalliclick question</button>');
	}
}
