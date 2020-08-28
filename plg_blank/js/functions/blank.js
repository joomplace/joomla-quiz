var dropped = false;
function createDD(){

	if(jq_jQuery('.jq_blank_wrap').height() > (document.documentElement.clientHeight * 0.7)){
		jq_jQuery('.jq_blank_wrap').height(document.documentElement.clientHeight * 0.7);
	}

	jq_jQuery(".jq_draggable_answer").draggable({
		containment:'parent',
		cursor: 'move',
		revert: true,
		revertDuration: 0,
		scroll: true,
		start: function(event, ui) {
			dropped = false;
			joomlaquizBlank.headerDemoSiteInpin(); //fix for demo-site to allow scrolling
		},
		stop: function(event, ui) {
			dropped = false;
			joomlaquizBlank.headerDemoSitePin();
		}
	});

	joomlaquizBlank.makeBlankDraggable();

	jq_jQuery(".jq_blank_droppable").droppable({
		accept: '.jq_blank_draggable, .jq_draggable_answer',
		activeClass: 'jq_active',
		hoverClass: 'jq_hover',
		tolerance: 'pointer',
		out: function(event, ui) {
			//When dragging an element across multiple fields, the 'out'-event will occur multiple times.
			joomlaquizBlank.prevBlankContainerIds.push(jq_jQuery(this).attr('id'));
		},
		drop: function(event, ui){
			dropped = true;
			var droppable_value = jq_getObj('hid_'+this.id).value;
			var xid = jq_jQuery(this).children('.jq_blank_draggable').attr('xid');

			jq_getObj('hid_'+this.id).value = ui.draggable.html();
			if (ui.draggable.hasClass("jq_blank_draggable") && ui.draggable.attr("id")) {
				jq_getObj('hid'+ui.draggable.attr("id")).value = '';
			}

			this.innerHTML = '<span class="jq_blank_draggable" id="_'+this.id+'" xid="'+ui.draggable.attr("xid")+'" draggable="true">'+ui.draggable.html()+'</span>&nbsp;';

			check_Blank(this.id.replace('blk_id_','') , ui.draggable.html());

			if (droppable_value && ui.draggable.hasClass("jq_blank_draggable")) {
				var tid = ""+ui.draggable.attr("id");
				jq_getObj('hid'+tid).value = droppable_value;
				jq_getObj(tid.substr(1)).innerHTML = '<span class="jq_blank_draggable" id="'+tid+'" '+(xid? 'xid="'+xid: '')+'" draggable="true">'+droppable_value+'</span>&nbsp;';
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

			joomlaquizBlank.makeBlankDraggable();
			joomlaquizBlank.blankFieldsRestoration();
			jq_jQuery("body").css('cursor', 'default');
		},
	});
}

jq_jQuery(function ($) {
	window.joomlaquizBlank = window.joomlaquizBlank || {};
	joomlaquizBlank.prevBlankContainerIds = [];

	joomlaquizBlank.makeBlankDraggable = function () {
		$('.jq_blank_draggable').draggable({
			cursor: 'move',
			revert: true,
			revertDuration: 0,
			scroll: true,
			start: function(event, ui) {
				dropped = false;
				if ($(this).hasClass( 'jq_blank_draggable' )) {
					$(this).addClass( 'jq_draggable_answer_span' );
				}
				joomlaquizBlank.headerDemoSiteInpin();
			},
			stop: function(event, ui) {
				if ($(this).hasClass( 'jq_blank_draggable' )) {
					$(this).removeClass( 'jq_draggable_answer_span' );
				}
				if (!dropped) {
					jq_getObj('hid'+this.id).value = '';
					$('.jq_draggable_answer[xid="'+$(this).attr("xid")+'"]').css('visibility','visible');
					$(this).remove();
				}
				dropped = false;
				joomlaquizBlank.blankFieldsRestoration();
				joomlaquizBlank.headerDemoSitePin();
			}
		});
	};

	joomlaquizBlank.blankFieldsRestoration = function () {
		joomlaquizBlank.prevBlankContainerIds.forEach(function(item, i, arr) {
			if(!$('#'+item).find('span.jq_blank_draggable').length) {
				$('#'+item).html('<span class="jq_blank_draggable ui-draggable" id="_'+item+'" style="position:relative;"></span>&nbsp;');
			}
		});
		joomlaquizBlank.prevBlankContainerIds = [];
	};

	joomlaquizBlank.headerDemoSitePin = function () {
		if($('#t3-mainnav.navbar-fixed-top').length) {
			$('#t3-mainnav.navbar-fixed-top').css({'position':'fixed'});
			$('body').css({'padding-top':'55px'});
		}
	};

	joomlaquizBlank.headerDemoSiteInpin = function () {
		if($('#t3-mainnav.navbar-fixed-top').length && document.documentElement.clientWidth <= 768) {
			$('#t3-mainnav.navbar-fixed-top').css({'position':'relative'});
			$('body').css({'padding-top':'0px'});
		}
	};

});
