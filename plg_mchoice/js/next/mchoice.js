case '15':
	var question_container = jq_jQuery('#question-'+questions[n].cur_quest_id);
	var total_subs = question_container.find('.sub_questions').data('total');
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
            jq_jQuery(this).find('input[name*="'+jq_jQuery(this).attr('id')+'"]')
			.each(function (oi) {
				jq_jQuery(this).prop('disabled', true);
			});
		}
    });
	if(!clear_sky){
		return clear_sky;
	}

	// add loading
	question_container.addClass('question-loading');

	var next_block = jq_jQuery('<div class="loading-next" />');
	var question_finished = question_container.data('finished');
	if(!question_finished){
        next_block.load(
            live_url+'index.php?option=com_ajax&ignoreMessages&plugin=mchoiceAnswerRenderSubquestion&group=joomlaquiz&stu_quiz_id='+stu_quiz_id+'&question='+questions[n].cur_quest_id+'&format=json',
            {'answers':subs_answers},
            function (response, status, xhr) {
                question_finished = true;
                response = JSON.parse(response);
                console.log(response);
                var feedbacks = response.messages;
                if(feedbacks){
                    Object.keys(feedbacks).map(function(quest, index) {
                        var msgs = feedbacks[quest];
                        if(msgs){
                            msgs.map(function(msg){
                                question_container.find('.sub_questions #subquestion-'+quest+' .feedback-section').html(msg);
                            });
                            clear_sky = false;
                        }
                    });
                }
                if(response.data){
                    var html = response.data[0];
                    html.map(function(obj, i) {
                        if(obj.again !== false){
                            if(obj.again === true){
                                question_container.find('input[name*="'+obj.id+'"]')
                                    .each(function (oi) {
                                        jq_jQuery(this).prop('disabled', false);
                                    });
                                clear_sky = false;
                            }else if(obj.html && obj.again === null){
                                question_container.find('.sub_questions').append(obj.html);
                                clear_sky = false;
                            }
                            question_finished = false;
                        }
                    });
                }
                question_container.data('finished', question_finished);

                if(clear_sky){
                	console.log('Still needed // seems like for all together');
                    quiz_blocked = 1;
                    timerID = setTimeout("jq_QuizNext()", 300);
                }else{
                    question_container.removeClass('question-loading');
                }
            }
        );

        return false;
	}
break;