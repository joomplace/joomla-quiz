if (questions[i].cur_quest_type == 14) {	
	var task = jq_jQuery(response).find('task').text();

	if(jq_jQuery(response).find('sq_delayed').text()) {
		sq_delayed = jq_jQuery(response).find('sq_delayed').text();
	}

	img_width = jq_jQuery(response).find('img_width').text();
	img_height = jq_jQuery(response).find('img_height').text();
	
	dc_w = Math.floor(img_width/5);
	dc_h = Math.floor(img_height/5);

	var dalliclickImgRatio = jq_jQuery(response).find('img_ratio').text();

	window.addEventListener('resize', function(e) {
		setDalliclickScreenClass();
		setDalliclickImageSize();
	});

	function setDalliclickScreenClass(){
		jq_jQuery('.dc_layout').toggleClass('dc_pda_ver', document.documentElement.clientWidth <= 768);
	}

	function setDalliclickImageSize() {
		if(!jq_jQuery('.dc_layout').length) {
			return;
		}

		var w = 184,
			is_feedback = false;

		if(jq_jQuery('.jq_question_answers_cont').length) {
			w = jq_jQuery('.jq_question_answers_cont').css({'width':'100%'}).width();
		}
		else if (jq_jQuery('.jq_feedback_question_content').length) { //feedback or review
			is_feedback = true;
			w = jq_jQuery('#jq_quiz_container').width();
		}

		w = w * 0.7;

		if(jq_jQuery('.dc_layout').hasClass('dc_pda_ver')) {
			jq_jQuery('.dc_layout').css({'margin':'0 auto'});
		} else {
			jq_jQuery('.dc_layout').css({'margin':'0'});
		}

		var ratio = dalliclickImgRatio * 1,
			h = Math.round(w/ratio),
			cover = jq_jQuery('.cover').eq(0);

		jq_jQuery(cover).width(w).height(h)
			.find('canvas').width(w-1).height(h);

		dc_w = Math.floor(w/5);
		dc_h = Math.floor(h/5);

		jq_jQuery(cover).find('.square').each(function() {
			jq_jQuery(this).width('20%').height(dc_h+1);
		});

		if(is_feedback) {
			jq_jQuery(cover).find('img').width(w).height(h);
		}
	}

	setDalliclickScreenClass();
	setDalliclickImageSize();

	if(task != 'review_start' && task != 'review_next') {
		Dalliclick_init();
	}
}
