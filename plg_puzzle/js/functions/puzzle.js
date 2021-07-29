jq_jQuery(function ($) {
    window.puzzle = window.puzzle || {};

    puzzle.setSize = function() {
        console.log($(this));
        $('.feedback-puzzle__img').each(function () {
            var puzzleW = $(this).width(),
                puzzleH = $(this).height(),
                difficulty = $(this).attr('data-difficulty');
            $(this).closest('.jq_puzzle_img').find('.feedback-puzzle__piece').each(function () {
                var pieceW = Math.floor(puzzleW/difficulty) - 0.1,
                    pieceH = Math.floor(puzzleH/difficulty) - 0.1;
                $(this).width(pieceW).height(pieceH);
            });
        });
    }

    window.addEventListener('resize', function (e) {
        if (!$('.jq_puzzle_fdb').length) {
            return;
        }
        puzzle.setSize();
    });
});


function startPuzzle(cur_quest_id, stu_quiz_id, button=false) {
    if(button) {
        jQuery(button).hide();
    }
    var task = jq_jQuery(parent.response).find('task').text();
    if(task != 'review_start' && task != 'review_next'){
        puzzle_cur_id = cur_quest_id;
        SqueezeBox.initialize({});
        options.forEach(function(item, i, arr) {
            if(item.c_id==cur_quest_id) {
                queezeOptions = item;
                return;
            }
        });
        SqueezeBox.fromElement('/index.php?option=com_joomlaquiz&task=ajaxaction.procces&ajax_task=ajax_plugin&plg_task=show&quest_type=puzzle&quest_id=' + puzzle_cur_id, queezeOptions);
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