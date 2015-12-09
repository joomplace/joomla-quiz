var dc_w = 0;
var dc_h = 0;
var dc_html = '';
var dc_c = 1;
var squares = new Array();
var dc_timer = 0;
var dc_timer2 = 0;
var timer_pause = 0;
var dc_seconds = 0;
var dc_counter = 3;
var dc_answer = 0;
var q_type;
var img_width;
var img_height;

var Base64 = {

	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},
	
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

}

function Dalliclick_init()
{
	var quest_task = jq_jQuery(response).find('quest_task').text();
	dc_html = '';
	dc_c = 1;
	squares = new Array();
	dc_seconds = 0;
	dc_counter = 3;
	dc_answer = 0;
	clearInterval(dc_timer);
	clearInterval(dc_timer2);
	clearInterval(timer_pause);
	dc_timer = 0;
	dc_timer2 = 0;
	timer_pause = 0;
	for (var i=1; i<=5; i++){
		for(var j=1; j<=5; j++){
			squares.push(dc_c);
			dc_html += '<div class="square" id="sq_'+dc_c+'" style="width:20%;height:'+(dc_h+1)+'px;"></div>';
			++dc_c;
		}
		dc_html += '<div class="dc_clear"><!--x--></div>';
	}
	jq_jQuery('.cover').prepend(dc_html);
	if(quest_task == 'no_attempts'){
		jq_jQuery('.square').css('opacity', 0);
	}
	var imgSrc = Base64.decode(jq_jQuery('#imgSrc').val());
	var dc_image = document.getElementById("dc_image");
	if((jq_jQuery.browser.msie && jq_jQuery.browser.version < 9) || jq_jQuery.browser.opera){
		jq_jQuery("<img src='"+imgSrc+"' class='dc_image' width='"+img_width+"' height='"+img_height+"' />").insertBefore(jq_jQuery('#imgSrc'));
	} else {
		var ctx = dc_image.getContext('2d');
		var pic = new Image();
		pic.src = imgSrc;
		pic.onload = function() {
			ctx.drawImage(pic, 0, 0, img_width, img_height);
		}
	}
	if (quest_task != 'no_attempts'){
		timer_pause = setInterval(readyCounter, 1000);
	}
	if (quest_task == 'no_attempts'){
		jq_jQuery('.dc_button').attr('disabled', 'disabled');
	}
}

function readyCounter()
{
	jq_jQuery('.pause').css('font-size', '115px').css('opacity', 1);
	jq_jQuery('.pause').html(dc_counter);
	jq_jQuery('.pause').animate({fontSize:0, opacity:'0'}, 700);
	dc_counter--;
	if(dc_counter < 0){
		clearInterval(timer_pause);
		jq_jQuery('.pause').fadeOut();
		dc_timer = setInterval(clearSquare, sq_delayed * 1000);
		dc_timer2 = setInterval(calcSeconds, 1000);					
	}
}

function calcSeconds()
{
	dc_seconds++;
	jq_jQuery('.dc_time').html(elapsedTime + " " + dc_seconds + " sec");
}

function clearSquare()
{
	var sq_len = squares.length;	
	var rnd = getRandomInt(sq_len-1);
	var rnd_id = squares[rnd];
	var sq_id = 'sq_' + rnd_id;
	jq_jQuery('#'+sq_id).animate({opacity:0}, (sq_delayed * 1000) - 500);	
	delete squares[rnd];
	var tmp_squares = new Array();
	for(var dc_n=0; dc_n <=sq_len-1; dc_n++){
		if(typeof(squares[dc_n]) != 'undefined'){
			tmp_squares.push(squares[dc_n]);
		}
	}
	squares = tmp_squares;
	var post_len = squares.length;
	if(post_len <= 0){
		clearInterval(dc_timer);
		clearInterval(dc_timer2);
		var cur_quest_id = jq_jQuery(".error_messagebox_quest").attr("id");
		ShowMessage(cur_quest_id, 1, timeHasRunOut);
	}
}

function getRandomInt(max)
{
	return Math.floor(Math.random() * max);
}

function clearSquaresStop(element)
{
	if(!dc_timer && !dc_timer2) return;
	clearInterval(dc_timer);
	clearInterval(dc_timer2);
	dc_timer = 0;
	dc_timer2 = 0;
	var quest_val = jq_jQuery(element).attr('id');
	var qid = parseInt(quest_val.replace(/quest_choice_/, ''));
	var quest_id = jq_jQuery(response).find('quest_id').text();
	var open_squares = dc_c - squares.length - 1;
	
	jq_jQuery.ajax({
		type: "POST",
		url: live_url + "index.php?option=com_joomlaquiz&task=ajaxaction.procces",
		data: "ajax_task=ajax_plugin&plg_task=check_right&quest_type=dalliclick&quiz_id=" + quiz_id + "&quest_id="+ quest_id +"&check_id="+qid+"&o_sq="+open_squares+"&stu_quiz_id="+stu_quiz_id + "&elapsed_time="+dc_seconds,
		success: function(xml){
			var c_score = jq_jQuery(xml).find('c_score').text();
			var is_correct = jq_jQuery(xml).find('is_correct').text();
			dc_answer = 1;
			jQuery('.square').animate({opacity:0}, 500);
			if(is_correct == 1){
				jq_jQuery(element).css('background', '#93C162');	
			} else if(is_correct == 0) {
				jq_jQuery(element).css('background', '#FF3333');
			}
			jq_jQuery(element).css('color', 'white');
			jq_jQuery('.dc_button').attr('disabled', 'disabled');
			
			jq_jQuery('#result_point_' + quest_id).html(c_score);
		}
	});
}