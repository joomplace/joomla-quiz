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
			}
		}).disableSelection();
}