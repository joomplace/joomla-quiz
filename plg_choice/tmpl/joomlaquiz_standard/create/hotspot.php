<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaquiz Deluxe class
 */
class JoomlaquizViewCreateHotspot
{
	public static function getQuestionContent($hotspot, $data){
		
		$live_site = JURI::root();
		$count_hotspot = count($hotspot);
		
		$hotspot['c_select_x'] = (isset($hotspot['c_select_x'])? $hotspot['c_select_x']: 0);
		$hotspot['c_select_y'] = (isset($hotspot['c_select_y'])? $hotspot['c_select_y']: 0);
		
		$jq_tmpl_html = <<<HTMLEND

		var is_clickable{$data['q_data']->c_id} = true;
		
		function getPosition_x(el) {
			var left = jq_jQuery(el).offset().left;
			return left;
		}	
			
		function getPosition_y(el) {
			var top = jq_jQuery(el).offset().top;
			return top;
		}
		
		var quiz_cont_add = jq_getObj('quest_div{$data['q_data']->c_id}_hs');
		var im_td2 = jq_getObj('im_td{$data['q_data']->c_id}');						
		var quiz_cont = jq_getObj('jq_quiz_container'); 
		var hs_gg_div1 = document.createElement("div");
		var hs_gg_div2 = document.createElement("div");
		hs_gg_div1.id="im_div{$data['q_data']->c_id}";
		hs_gg_div1.style.position = "relative";
		hs_gg_div1.style.border = "1px solid #000000";
		
		var hs_gg_img1 = document.createElement("img");
		hs_gg_img1.src = '{$live_site}images/joomlaquiz/images/{$data['q_data']->c_image}';
		/*hs_gg_img1.style.position = 'relative';*/
		hs_gg_img1.id = 'img_hotspot{$data['q_data']->c_id}';
		hs_gg_img1.alt = 'img_hotspot';
		hs_gg_div1.appendChild(hs_gg_img1);
		
		var hs_gg_img2 = document.createElement("img");
		hs_gg_img2.src = '{$live_site}components/com_joomlaquiz/views/templates/tmpl/{$data['cur_template']}/images/hs_round.png';
		hs_gg_img2.style.width = '12px';
		hs_gg_img2.style.height = '12px';
		
		hs_gg_div2.id = 'hs_label{$data['q_data']->c_id}_div';
		hs_gg_div2.style.visibility = 'hidden';
		hs_gg_div2.style.display = 'none';
		hs_gg_div2.style.position = 'absolute';
		hs_gg_div2.appendChild(hs_gg_img2);
		
		var hs_gg_form = document.createElement("form");
		hs_gg_form.name = 'quest_form{$data['q_data']->c_id}';
		
		var hs_gg_input1 = document.createElement('input');
		hs_gg_input1.setAttribute('type', 'hidden');
		hs_gg_input1.setAttribute('name', 'hotspot_x');
		hs_gg_input1.setAttribute('value', "{$hotspot['c_select_x']}");
						
		var hs_gg_input2 = document.createElement('input');
		hs_gg_input2.setAttribute('type', 'hidden');
		hs_gg_input2.setAttribute('name', 'hotspot_y');
		hs_gg_input2.setAttribute('value', "{$hotspot['c_select_y']}");
			
		hs_gg_form.appendChild(hs_gg_input1);
		hs_gg_form.appendChild(hs_gg_input2);
		
		var quiz_cont_uu = document.createElement("div");
		quiz_cont_uu.id = "qwert_div{$data['q_data']->c_id}";
		hs_gg_div1.appendChild(hs_gg_div2);
		quiz_cont_uu.appendChild(hs_gg_div1);
		im_td2.appendChild(quiz_cont_uu);

		jq_jQuery('img#img_hotspot{$data['q_data']->c_id}').bind('click',
		function(e) {
			var qid = {$data['q_data']->c_id}; 
			for (var i=0; i < quest_count; i++) {
				if (questions[i].cur_quest_id == qid) {
					n = i;
					break;
				}
			} 
			
			if (questions[n].disabled){
				return;
			}
			
			if (!e) { 
				e = window.event;
			}
			
			var targ=e.target? e.target: e.srcElement;
			var hs_img = jq_getObj('img_hotspot{$data['q_data']->c_id}');
			var hs_label_div = jq_getObj('hs_label{$data['q_data']->c_id}_div');
			var qqq1 = e.pageY || (e.clientY + (document.documentElement.scrollTop || document.body.scrollTop));
			var qqq2 = e.pageX || (e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));
			var base_y = getPosition_y(jq_getObj('qwert_div{$data['q_data']->c_id}')) + 1;
			var base_x = getPosition_x(jq_getObj('qwert_div{$data['q_data']->c_id}')) + 1;
			var img_y =  getPosition_y(jq_getObj('img_hotspot{$data['q_data']->c_id}'));
			var img_x = getPosition_x(jq_getObj('img_hotspot{$data['q_data']->c_id}'));
			var valueT = 0; 
			var valueL = 0;
			var valueL2 = 0; 
			var kol2 = 0; 
			var element1 = hs_img; 
			
			do {
				valueT += element1.scrollTop || 0; 
				valueL += element1.scrollLeft || 0; 
				kol2++; 
				valueL2 += element1.offsetLeft || 0; 
				element1 = element1.parentNode; 
			} while (element1);
			
			var valueT2 = 0; 
			var valueL2 = 0; 
			var element2 = hs_label_div; 
			
			do {
				valueT2 += element2.scrollTop || 0; 
				valueL2 += element2.scrollLeft || 0; 
				element2 = element2.parentNode; 
			} while (element2);
			
			hs_label_div.style.top = qqq1 - base_y -  6 + 'px';
			hs_label_div.style.left = qqq2 - base_x -  6 + 'px';//-valueL2
			hs_label_div.style.visibility = 'visible';
			hs_label_div.style.display = 'block';
			hs_label_div.style.position = 'absolute';
			document.quest_form{$data['q_data']->c_id}.hotspot_x.value = parseInt(qqq2 - base_x);
			document.quest_form{$data['q_data']->c_id}.hotspot_y.value = parseInt(qqq1 - base_y);
		}
		);

		if ({$count_hotspot}) {
			jq_getObj('hs_label{$data['q_data']->c_id}_div').style.left = {$hotspot['c_select_x']} - 6 + 'px';
			jq_getObj('hs_label{$data['q_data']->c_id}_div').style.top = {$hotspot['c_select_y']} - 6 + 'px';
			jq_getObj('hs_label{$data['q_data']->c_id}_div').style.visibility = '';
			jq_getObj('hs_label{$data['q_data']->c_id}_div').style.display = '';
			document.quest_form{$data['q_data']->c_id}.hotspot_x.value = "{$hotspot['c_select_x']}";
			document.quest_form{$data['q_data']->c_id}.hotspot_y.value = "{$hotspot['c_select_y']}";
		}
HTMLEND;
		
		return $jq_tmpl_html;
	}
}
?>