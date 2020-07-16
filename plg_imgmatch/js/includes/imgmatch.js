if (questions[i].cur_quest_type == 12) {
	createDD_imageMatch();
	var limit_time = jq_jQuery(response).find('quest_limit_time').text();
	if(parseInt(limit_time)) {
		quest_timer_sec = limit_time;
		jq_Start_Question_TickTack(limit_time);
		clearInterval(quest_timer);
		quest_timer = setInterval("jq_Start_Question_TickTack("+limit_time+")", 1000);
	}

	jq_jQuery(function ($) {
		window.imageMatch = window.imageMatch || {};
		imageMatch.wOrigin = $(response).find('img_width').text() ? $(response).find('img_width').text() : 100;
		imageMatch.hOrigin = $(response).find('img_height').text() ? $(response).find('img_height').text() : 100;
		imageMatch.ratio = $(response).find('img_ratio').text() ? $(response).find('img_ratio').text() : 1;

		imageMatch.setScreenClass = function() {
			$('.imagematch-answers').toggleClass('imagematch-pda', document.documentElement.clientWidth <= 768);
		}

		imageMatch.setSize = function() {
			if(!$('.imagematch-pda').length) {
				return;
			}

			var w = $('#jq_quiz_container').width(),
				blockW = w / 3,
				blockH = blockW / imageMatch.ratio,
				ddtable = $('#dd_table');

			$(ddtable).find('td').css({'width':blockW,'height':blockH})
				.removeAttr('width')
				.removeAttr('height');
			$(ddtable).find('div').not('.imagematch-target').css({'width':blockW,'height':blockH});
			$(ddtable).find('.imagematch-target').css({'width':blockW});
		}

		imageMatch.setScreenClass();
		imageMatch.setSize();

		window.addEventListener('resize', function(e) {
			imageMatch.setScreenClass();
			imageMatch.setSize();
		});
	});

}
