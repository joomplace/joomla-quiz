jq_jQuery(function ($) {
    window.puzzle = window.puzzle || {};

    puzzle.setSize = function() {
        $('.feedback-puzzle__img').each(function () {
            var puzzleW = $(this).width(),
                puzzleH = $(this).height();
            $(this).closest('.jq_puzzle_img').find('.feedback-puzzle__piece').each(function () {
                $(this).width(puzzleW/3).height(puzzleH/3);
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