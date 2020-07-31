jq_jQuery(function ($) {
    window.puzzle = window.puzzle || {};

    puzzle.setSize = function() {
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