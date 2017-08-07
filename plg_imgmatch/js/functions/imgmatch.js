var quest_timer;
var els = new Array();
function createDD_imageMatch(){
		var els_str = jq_jQuery(els.toString());
		jq_jQuery(els_str).sortable(
		{
			items: '.groupItem',
			handle: 'div.headerItem',
			revert: 'true',
			connectWith: els,
			tolerance: 'intersect',
			placeholder: 'sortHelper',
			forcePlaceholderSize: 'true',
			stop: function(e,ui){
				if(jq_jQuery(ui.item).prev()){
					jq_jQuery(ui.item).prev().val('true');
				}	
			},
            update: function (event, ui) {
                var td_id = jq_jQuery(ui.item).parent().attr('id');
                var number_of_items = jq_jQuery('#'+td_id).children('.groupItem').length;
                if (number_of_items > 1) {
                    var sender_id = jq_jQuery(ui.sender).attr('id');
                    jq_jQuery('#'+sender_id).sortable("cancel")
                }
            }
		}).disableSelection();
}