/*
	jQuery snapPuzzle v1.0.0
    Copyright (c) 2014 Hans Braxmeier / Simon Steinberger / Pixabay
    GitHub: https://github.com/Pixabay/jQuery-snapPuzzle
	License: http://www.opensource.org/licenses/mit-license.php
*/

(function($){
    jq_jQuery.fn.snapPuzzle = function(options){
        var o = jq_jQuery.extend({ pile: '', containment: 'document', rows: 5, columns: 5, onComplete: function(){}, onCorrect: function(elem){}, onDraggable: function(){} }, options);

        // public methods
        if (typeof options == 'string') {
            this.each(function(){
                var that = jq_jQuery(this),
                    o = that.data('options'),
                    pieceWidth = that.width() / o.columns,
                    pieceHeight = that.height() / o.rows,
                    pile = jq_jQuery(o.pile),
                    maxX = pile.width() - pieceWidth,
                    maxY = pile.height() - pieceHeight,
                    puzzle_offset = that.closest('span').offset(),
                    pile_offset = pile.offset();

                if (options == 'destroy') {
                    jq_jQuery('.'+o.puzzle_class).remove();
                    that.unwrap().removeData('options');
                    pile.removeClass('snappuzzle-pile');
                } else if (options == 'refresh') {
                    jq_jQuery('.snappuzzle-slot.'+o.puzzle_class).each(function(){
                        var x_y = jq_jQuery(this).data('pos').split('_'), x = x_y[0], y = x_y[1];
                        jq_jQuery(this).css({
                            width: pieceWidth,
                            height: pieceHeight,
                            left: y*pieceWidth,
                            top: x*pieceHeight
                        });
                    });
                    jq_jQuery('.snappuzzle-piece.'+o.puzzle_class).each(function(){
                        if (jq_jQuery(this).data('slot')) {
                            // placed on slot
                            var x_y = jq_jQuery(this).data('slot').split('_'), slot_x = x_y[0], slot_y = x_y[1],
                                x_y = jq_jQuery(this).data('pos').split('_'), pos_x = x_y[0], pos_y = x_y[1];;
                            jq_jQuery(this).css({
                                width: pieceWidth,
                                height: pieceHeight,
                                left: slot_y*pieceWidth+puzzle_offset.left-pile_offset.left,
                                top: slot_x*pieceHeight+puzzle_offset.top-pile_offset.top,
                                backgroundPosition: (-pos_y*pieceWidth)+'px '+(-pos_x*pieceHeight)+'px',
                                backgroundSize: that.width()
                            });
                        } else {
                            // placed anywhere else
                            var x_y = jq_jQuery(this).data('pos').split('_'), x = x_y[0], y = x_y[1];
                            jq_jQuery(this).css({
                                width: pieceWidth,
                                height: pieceHeight,
                                left: Math.floor((Math.random()*(maxX+1))),
                                top: Math.floor((Math.random()*(maxY+1))),
                                backgroundPosition: (-y*pieceWidth)+'px '+(-x*pieceHeight)+'px',
                                backgroundSize: that.width()
                            });
                        }
                    });
                }
            });
            return this;
        }

        function init(that){
            var puzzle_class = 'sp_'+new Date().getTime(),
                puzzle = that.wrap('<span class="snappuzzle-wrap"/>').closest('span'),
                src = that.attr('src'),
                pieceWidth = that.width() / o.columns,
                pieceHeight = that.height() / o.rows,
                pile = jq_jQuery(o.pile).addClass('snappuzzle-pile'),
                maxX = pile.width() - pieceWidth,
                maxY = pile.height() - pieceHeight;

            o.puzzle_class = puzzle_class;
            that.data('options', o);
			num = 1;
            for (var x=0; x<o.rows; x++) {
                for (var y=0; y<o.columns; y++) {
                    jq_jQuery('<div class="snappuzzle-piece '+puzzle_class+'" data-number="'+(num*100).toString(36)+'"/>').data('pos', x+'_'+y).css({
                        width: pieceWidth,
                        height: pieceHeight,
                        position: 'absolute',
                        left: Math.floor((Math.random()*(maxX+1))),
                        top: Math.floor((Math.random()*(maxY+1))),
                        zIndex: Math.floor((Math.random()*10)+1),
                        backgroundImage: 'url('+src+')',
                        backgroundPosition: (-y*pieceWidth)+'px '+(-x*pieceHeight)+'px',
                        backgroundSize: that.width()
                    }).draggable({
                        start: function(e, ui){ 
							jq_jQuery(this).removeData('slot'); 
							o.onDraggable(that);
						},
                        stack: '.snappuzzle-piece',
                        containment: o.containment
                    }).appendTo(pile).data('lastSlot', pile);

					num++;
					
                    jq_jQuery('<div class="snappuzzle-slot '+puzzle_class+'"/>').data('pos', x+'_'+y).css({
                        width: pieceWidth,
                        height: pieceHeight,
                        left: y*pieceWidth,
                        top: x*pieceHeight
                    }).appendTo(puzzle).droppable({
                        accept: '.'+puzzle_class,
                        hoverClass: 'snappuzzle-slot-hover',
                        drop: function(e, ui){
                            var slot_pos = jq_jQuery(this).data('pos');

                            // prevent dropping multiple pieces on one slot
                            jq_jQuery('.snappuzzle-piece.'+puzzle_class).each(function(){
                                if (jq_jQuery(this).data('slot') == slot_pos) slot_pos = false;
                            });
                            if (!slot_pos) return false;

                            ui.draggable.data('lastSlot', jq_jQuery(this)).data('slot', slot_pos);
                            ui.draggable.position({ of: jq_jQuery(this), my: 'left top', at: 'left top' });
                            if (ui.draggable.data('pos')==slot_pos) {
                                ui.draggable.addClass('correct');
								o.onCorrect(ui.draggable);
								//console.log(ui.draggable.data);
                                // fix piece
                                // jq_jQuery(this).droppable('disable').fadeIn().fadeOut();
                                jq_jQuery(this).droppable('disable').css('opacity', 1);
                                ui.draggable.css({opacity: 0, cursor: 'default'}).draggable('disable');
                                if (jq_jQuery('.snappuzzle-piece.correct.'+puzzle_class).length == o.rows*o.columns) o.onComplete(that);
                            }
                        }
                    });
                }
            }
        }

        return this.each(function(){
            if (this.complete) init(jq_jQuery(this));
            else jq_jQuery(this).load(function(){ init(jq_jQuery(this)); });
        });
    };
}(jQuery));
