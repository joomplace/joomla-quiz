var pics = new Array();
var m_ids = new Array();
var map = new Array();
var user = new Array();
var temparray = new Array();
var clickarray = new Array(0, 0);
var ctr = 0;
var oktoclick = true;
var finished = 0;
var memory_time = 0;
var memory_timer;
var quest_timer_sec;
var quest_timer;

function scramble(s) {
	for (z = 0; z < s-1; z++) {
		for (x = 0; x <= (s*2 - 1); x++) {
			temparray[0] = Math.floor(Math.random()*(s * 2));
			temparray[1] = map[temparray[0]];
			temparray[2] = map[x];
			map[x] = temparray[1];
			map[temparray[0]] = temparray[2];
		}
	}
	
	var quest_id = jq_jQuery(response).find('quest_id').text();
	jq_jQuery.ajax({
		type: "POST",
		url: live_url + "index.php?option=com_joomlaquiz&task=ajaxaction.procces",
		data: "ajax_task=ajax_plugin&plg_task=add_points&action=start&quest_type=memory&quiz_id="  + quiz_id + "&quest_id="+ quest_id +"&stu_quiz_id=" + stu_quiz_id
	});
}

function showimage(but) {
	var quest_task = jq_jQuery(response).find('quest_task').text();
	if(quest_task != 'no_attempts'){
		if (oktoclick) {
			oktoclick = false;
			jq_jQuery('#quest_form img[name=img'+ but +']').attr('src', live_url + 'images/joomlaquiz/images/memory/' + pics[map[but]]);
			if (ctr == 0) {
				ctr++;
				clickarray[0] = but;
				oktoclick = true;
			} else {
				clickarray[1] = but;
				ctr = 0;
				setTimeout('returntoold()', 600);
			}
		}
	}
}

function returntoold() {
	var c_img_cover = jq_jQuery(response).find('c_img_cover').text();
	var quest_id = jq_jQuery(response).find('quest_id').text();
	
	if ((clickarray[0] == clickarray[1]) && (!user[clickarray[0]])) {
		jq_jQuery('#quest_form img[name=img'+ clickarray[0] +']').attr('src', live_url + '/images/joomlaquiz/images/memory/' + c_img_cover);
		oktoclick = true;
	} else {
		if (map[clickarray[0]] != map[clickarray[1]]) {
			if (user[clickarray[0]] == 0) {
				jq_jQuery('#quest_form img[name=img'+ clickarray[0] +']').attr('src', live_url + 'images/joomlaquiz/images/memory/' + c_img_cover);
			}
			if (user[clickarray[1]] == 0) {
				jq_jQuery('#quest_form img[name=img'+ clickarray[1] +']').attr('src', live_url + 'images/joomlaquiz/images/memory/' + c_img_cover);
			}
		}
		if (map[clickarray[0]] == map[clickarray[1]]) {
			if (user[clickarray[0]] == 0&&user[clickarray[1]] == 0) { finished++; }
			user[clickarray[0]] = 1;
			user[clickarray[1]] = 1;
			jq_jQuery('#quest_form img[name=img'+ clickarray[0] +']').animate({'opacity': 0}, 'slow');
			jq_jQuery('#quest_form img[name=img'+ clickarray[1] +']').animate({'opacity': 0}, 'slow');
			var mid = m_ids[map[clickarray[0]]];
			clearInterval(memory_timer);
			var mem_time = memory_time;
			memory_time = 0;
			memory_timer = setInterval(MemoryTimer, 1000);
			
			jq_jQuery.ajax({
				type: "POST",
				url: live_url + "index.php?option=com_joomlaquiz&task=ajaxaction.procces",
				data: "ajax_task=ajax_plugin&plg_task=add_points&quest_type=memory&quiz_id=" + quiz_id + "&quest_id="+ quest_id +"&m_id="+mid+"&stu_quiz_id="+stu_quiz_id + "&mem_time=" + mem_time,
				success: function(xml){
					var t_score = jq_jQuery(xml).find('t_score').text();
					var c_score = jq_jQuery(xml).find('c_score').text();
					var p_score = jq_jQuery(xml).find('p_score').text();
					
					jq_jQuery('#result_point_' + quest_id).html(t_score);
					jq_jQuery('#jq_total_memory_point span').text(t_score);
					jq_jQuery('#jq_current_memory_point span').text(c_score);
					jq_jQuery('#jq_penalty_memory_point span').text(p_score);
				}
			});
			
		}
		if (finished >= count_pairs)
		{
			jq_jQuery('<div id="divv1" class="correct_answer"></div>').insertAfter('.jq_question_answers_cont');
			ShowMessage('divv1', 1, wellDone);
			setTimeout("jq_QuizNextOn()", 2000);
			clearInterval(quest_timer);
			jq_jQuery('.jq_quest_time_past').html('');
			if(jq_jQuery('#jq_total_memory_point')){
				jq_jQuery('#jq_total_memory_point').remove();
				jq_jQuery('#jq_current_memory_point').remove();
				jq_jQuery('#jq_penalty_memory_point').remove();
			}
		} else {
			oktoclick = true;
		}
	}
}

function initMemory()
{
	count_pairs = jq_jQuery(response).find('count_pairs').text();
	scramble(count_pairs);
	clearInterval(memory_timer);
	memory_timer = setInterval(MemoryTimer, 1000);
}

function MemoryTimer()
{
	memory_time++;
}