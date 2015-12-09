var dropped = false;
function createDD(){
	jq_jQuery(".jq_draggable_answer").draggable({
		containment:'parent',
		cursor: 'move',
		revert: true,
		revertDuration: 0,
		start: function(event, ui) {
			dropped = false;
		},
		stop: function(event, ui) {
			dropped = false;
		}
	});

	jq_jQuery('.jq_blank_draggable').draggable({
		cursor: 'move',
		revert: true,
		revertDuration: 0,
		start: function(event, ui) {
			dropped = false;
			if (jq_jQuery(this).hasClass( 'jq_blank_draggable' )) {
				jq_jQuery(this).addClass( 'jq_draggable_answer_span' );
			}
		},
		stop: function(event, ui) {
			if (jq_jQuery(this).hasClass( 'jq_blank_draggable' )) {
				jq_jQuery(this).removeClass( 'jq_draggable_answer_span' );
			}
			if (!dropped) {
				jq_getObj('hid'+this.id).value = '';
				jq_jQuery('.jq_draggable_answer[xid="'+jq_jQuery(this).attr("xid")+'"]').css('visibility','visible');
				jq_jQuery(this).remove();
			}
			dropped = false;
		}
	});

	jq_jQuery(".jq_blank_droppable").droppable({
		accept: '.jq_blank_draggable, .jq_draggable_answer',
		activeClass: 'jq_active',
		hoverClass: 'jq_hover',
		tolerance: 'pointer',
		drop: function(event, ui){
				dropped = true;
				var droppable_value = jq_getObj('hid_'+this.id).value;
				var xid = jq_jQuery(this).children('.jq_blank_draggable').attr('xid');

				jq_getObj('hid_'+this.id).value = ui.draggable.html();
				if (ui.draggable.hasClass("jq_blank_draggable") && ui.draggable.attr("id")) {
					jq_getObj('hid'+ui.draggable.attr("id")).value = '';
				}

				this.innerHTML = '<span class="jq_blank_draggable" id="_'+this.id+'" xid="'+ui.draggable.attr("xid")+'">'+ui.draggable.html()+'</span>&nbsp;';

				check_Blank(this.id.replace('blk_id_','') , ui.draggable.html());

				if (droppable_value && ui.draggable.hasClass("jq_blank_draggable")) {
					var tid = ""+ui.draggable.attr("id");
					jq_getObj('hid'+tid).value = droppable_value;
					jq_getObj(tid.substr(1)).innerHTML = '<span class="jq_blank_draggable" id="'+tid+'" '+(xid? 'xid="'+xid: '')+'">'+droppable_value+'</span>&nbsp;';
				} else if (droppable_value) {
					ui.draggable.css('visibility','hidden');
					jq_jQuery('.jq_draggable_answer[xid="'+xid+'"]').css('visibility','visible');
				} else {
					if (ui.draggable.hasClass("jq_draggable_answer")){
						ui.draggable.css('visibility','hidden');
						ui.draggable.attr('id', 'dd_'+this.id);
					} else {
						ui.draggable.remove();
					}
				}

				jq_jQuery('.jq_blank_draggable').draggable({
					cursor: 'move',
					revert: true,
					revertDuration: 0,
					start: function(event, ui) {
						dropped = false;
						if (jq_jQuery(this).hasClass( 'jq_blank_draggable' )) {
							jq_jQuery(this).addClass( 'jq_draggable_answer_span' );
						}
					},
					stop: function(event, ui) {
						if (jq_jQuery(this).hasClass( 'jq_blank_draggable' )) {
							jq_jQuery(this).removeClass( 'jq_draggable_answer_span' );
						}
						if (!dropped) {
							jq_getObj('hid'+this.id).value = '';
							jq_jQuery('.jq_draggable_answer[xid="'+jq_jQuery(this).attr("xid")+'"]').css('visibility','visible');
							jq_jQuery(this).remove();
						}
						dropped = false;
					}
				});

				jq_jQuery("body").css('cursor', 'default');
			}
	});
}