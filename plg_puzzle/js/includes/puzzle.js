if(questions[i].cur_quest_type == 11){

	var task = jq_jQuery(parent.response).find('task').text();
	if(task != 'review_start' && task != 'review_next'){
		puzzle_cur_id = questions[i].cur_quest_id;
		SqueezeBox.initialize({});
		SqueezeBox.fromElement('<?php echo JUri::root();?>index.php?option=com_joomlaquiz&task=ajaxaction.procces&ajax_task=ajax_plugin&plg_task=show&quest_type=puzzle', options);
		jq_jQuery('#sbox-overlay, #sbox-btn-close, #jq_close_button').click(function(){
			clearInterval(quest_timer);
			jq_jQuery.ajax({
				type: "POST",
				url: "index.php?option=com_joomlaquiz&task=ajaxaction.procces",
				data: "ajax_task=ajax_plugin&plg_task=addpoints&quest_type=puzzle" + "&quest_id=" + puzzle_cur_id + "&stu_quiz_id=" + stu_quiz_id + "&quiz_id=" + quiz_id + "&action=start"
			});
			setTimeout("jq_QuizNextOn()", 500);
		});
	}

	//feedback on the page of quiz results
	var puzzleObserverTarget = document.getElementById('jq_quiz_container'),
		puzzleObserverConfig = {
			childList: true,
			subtree: true
		},
		puzzleObserverCallback = function(mutationsList, observer) {
			for (var mutation of mutationsList) {
				for(var node of mutation.addedNodes) {
					if (!(node instanceof HTMLElement)) continue;
					//if (node.matches('td[id="jq_feed_questions"]')) {
					//    puzzle.setSize();
					//}
					for(var elem of node.querySelectorAll('table[class="jq_puzzle_fdb"]')) {
						puzzle.setSize();
					}
				}
			}
		},
		puzzleObserverObserver = new MutationObserver(puzzleObserverCallback);
	puzzleObserverObserver.observe(puzzleObserverTarget, puzzleObserverConfig);

}