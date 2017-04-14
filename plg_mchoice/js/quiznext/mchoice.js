case '15':
	var question_container = jq_jQuery('#question-'+questions[n].cur_quest_id);
	var subs_answers = {};
	var clear_sky = true;
	question_container.find('.sub_questions [id*="subquestion-"]').each(function (i) {
		var sub_id = jq_jQuery(this).data('id');
		jq_jQuery(this).removeClass('not-filled');
		subs_answers[sub_id] = [];
		jq_jQuery(this).find('input[name*="'+jq_jQuery(this).attr('id')+'"]:checked').each(function (oi) {
			subs_answers[sub_id].push(jq_jQuery(this).val());
		});
		if(!subs_answers[sub_id].length){
			jq_jQuery(this).addClass('not-filled');
			delete subs_answers[sub_id];
			try{ ScrollToElement(jq_jQuery(this));} catch(e) {}
			ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 1, mes_complete_this_part);
			clear_sky = false;
		}else{
			jq_jQuery(this).find('input[name*="'+jq_jQuery(this).attr('id')+'"]:checked')
				.each(function (oi) {
					jq_jQuery(this).prop('disabled', true);
				});
		}
	});
	answer = JSON.stringify(subs_answers);
break;