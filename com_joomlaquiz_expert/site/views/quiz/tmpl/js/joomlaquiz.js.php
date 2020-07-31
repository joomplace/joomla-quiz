<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

JHTML::_('behavior.modal');

$jinput = JFactory::getApplication()->input;
$reStartOption = $jinput->get('option','');
$reStartView = $jinput->get('view','');
$reStartID = $jinput->getInt('id',0);

//content plugin in article?
$margin_top = $this->get('_name') == 'quiz' ? $this->margin_top : JComponentHelper::getParams('com_joomlaquiz')->get('margin_top');
?>
<script language="JavaScript" type="text/javascript">
<!--//--><![CDATA[//><!--

if(typeof jq_jQuery == 'undefined' && jQuery){
	var jq_jQuery = jQuery;
}

function preload(arrayOfImages) {
	jq_jQuery(arrayOfImages).each(function(){
		jq_jQuery('<img/>')[0].src = this;
	});
}

function ScrollToElement(theElement){
	var selectedPosX = 0;
	var selectedPosY = 0;

	while(theElement != null){
		try{
			selectedPosX += theElement.offsetLeft;
			selectedPosY += theElement.offsetTop;
			selectedPosY -= margin_top;
			theElement = theElement.offsetParent;
		} catch(e){}
	}
	try{
		window.scrollTo(selectedPosX,selectedPosY-10);
	} catch(e){}

}

var reStartOption = '<?php echo $reStartOption; ?>';
var reStartView = '<?php echo $reStartView; ?>';
var reStartID = '<?php echo $reStartID; ?>';

var penaltyPoint = '<?php echo JText::_('COM_JOOMLAQUIZ_PENALTY_POINT')?>';
var currentPoint = '<?php echo JText::_('COM_JOOMLAQUIZ_CURRENT_POINT')?>';
var totalPoint = '<?php echo JText::_('COM_JOOMLAQUIZ_TOTAL_POINT')?>';
var elapsedTime = '<?php echo JText::_('COM_QUIZ_ELAPSED_TIME')?>';
var timeHasRunOut = '<?php echo JText::_('COM_QUIZ_TIME_FOR_ANSWERING_HAS_RUN_OUT')?>';
var wellDone = '<?php echo JText::_('COM_QUIZ_WELL_DONE')?>';
var confirm_notstarted_message = '<?= JText::_('COM_JOOMLAQUIZ_MEMORY_NOTSTARTED') ?>';

var quiz_id = <?php echo $quiz->c_id;?>;
var stu_quiz_id = 0;
var error_call_code = '';
var kol_drag_elems = 0;
var drag_array = new Array(kol_drag_elems);
var coord_left = new Array(kol_drag_elems);
var coord_top = new Array(kol_drag_elems);
var ids_in_cont = new Array(kol_drag_elems);
var cont_for_ids = new Array(kol_drag_elems);
var answ_ids = new Array(kol_drag_elems);
var cont_index = 0;
var last_drag_id = '';
var last_drag_id_drag = '';
var last_drag_quest_n = -1;
var kol_main_elems = 0;
var main_ids_array = new Array(kol_main_elems); //for likert quest
// *** MESSAGES ***
var mes_complete_this_part = '<?php echo addslashes( JText::_('COM_MES_COMPLETE_THIS_PART'))?>';
var mes_failed = '<?php echo addslashes( JText::_('COM_QUIZ_FAILED'))?>';
var mes_please_wait = '<?php echo addslashes( JText::_('COM_MES_PLEASE_WAIT'))?>';
var mes_time_is_up = '<?php echo addslashes(JText::_('COM_QUIZ_MES_TIMEOUT'))?>';
var mes_quest_number = '<?php echo (($quiz->c_show_quest_pos) ? addslashes(JText::_('COM_QUIZ_QUESTION_NUMBER')) : "");?>';
var mes_quest_points = '<?php echo (($quiz->c_show_quest_points) ? addslashes(JText::_('COM_QUIZ_QUESTION_POINTS')) : "");?>';
// *** some script variables ***
var user_email_to = '';
var user_unique_id = '';
var cur_quest_type = '';
var saved_prev_quest_exec_quiz_script = '';
var saved_prev_quest_exec_quiz_script_data = '';
var saved_prev_quest_data = '';
var saved_prev_res_data = '';
var saved_prev_quest_id = 0;
var saved_prev_quest_type = 0;
var saved_prev_quest_score = 0;
var cur_quest_id = 0;
var cur_quest_score = 0;
var cur_quest_num = 0;
var quiz_count_quests = 0;
var cur_impscale_ex = 0;
var response;
var quest_type;
var prev_correct = 0;
var allow_attempt = 0;
var timer_sec = 0;
var stop_timer = 0;
var result_is_shown = 0;
var max_quiz_time = <?php echo ($quiz->c_time_limit)?($quiz->c_time_limit * 60):3600000; ?>;
var timer_style = <?php echo ($quiz->c_timer_style);?>;
var quiz_blocked = 0;
var url_prefix = 'index.php?option=com_joomlaquiz<?php echo JoomlaquizHelper::JQ_GetItemId();?>&tmpl=component&task=ajaxaction.procces';
var limit_time = 0;
var quest_timer_sec = 0;
var quest_timer = 0;
var quest_timer_ticktack = 0;
var circle = null;
var path_elems = new Array();
var mes_question_is_misconfigured = '<?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_IS_CONFIGURED');?>';
var margin_top = '<?php echo $margin_top; ?>';
var qs = getParameter('qs');

<?php
$live_url = JURI::root();
?>
var live_url = '<?php echo $live_url;?>';
var quest_count = 1;
var questions = new Array(quest_count);

function question_data() {
	this.cur_quest_type = '';
	this.cur_quest_id = 0;
	this.cur_quest_score = 0;
	this.quest_data = '';
	this.quest_data_user = '';
	this.exec_quiz_script = 0;
	this.quiz_script_data = '';
	this.im_check = 0;
	this.kol_drag_elems = 0;
	this.drag_array = new Array(this.kol_drag_elems);
	this.coord_left = new Array(this.kol_drag_elems);
	this.coord_top = new Array(this.kol_drag_elems);
	this.ids_in_cont = new Array(this.kol_drag_elems); // what div id's in containers
	this.cont_for_ids = new Array(this.kol_drag_elems); //in that container this id
	this.answ_ids = new Array(this.kol_drag_elems);
	this.cont_index = 0;
	this.disabled = false;
	this.attempts = 0;
	this.is_prev = 0;
	this.is_last = 0;
	this.c_separator = 0;
}

function jq_attachE(obj,event,handler) {
	if(obj.addEventListener) {
		obj.addEventListener(event, handler, false);
	} else if(obj.attachEvent) {
		obj.attachEvent('on'+event, handler, false);
	}
}

function jq_GetQuestionData(question, n) {
	questions[n].cur_quest_type = question.getElementsByTagName('quest_type')[0].firstChild.data
	questions[n].cur_quest_id = question.getElementsByTagName('quest_id')[0].firstChild.data;
	questions[n].c_separator = parseInt(question.getElementsByTagName('quest_separator')[0].firstChild.data);
	questions[n].cur_quest_score = question.getElementsByTagName('quest_score')[0].firstChild.data;
	questions[n].quest_data = jq_jQuery(question).find('quest_data').text();
	questions[n].quest_data_user = jq_jQuery(question).find('quest_data_user').text();
	questions[n].exec_quiz_script = question.getElementsByTagName('exec_quiz_script')[0].firstChild.data;
	questions[n].quiz_script_data = question.getElementsByTagName('quiz_script_data')[0].firstChild.data;
	questions[n].cur_quest_num = question.getElementsByTagName('quiz_quest_num')[0].firstChild.data;
	questions[n].im_check = parseInt(question.getElementsByTagName('quest_im_check')[0].firstChild.data);
	questions[n].div_id = 'quest_div' + questions[n].cur_quest_id;
	questions[n].response = question;
}

function jq_CreateQuestions() {

	var question_template = '';
	var question_delimeter = quest_count > 1? '<?php echo JoomlaQuiz_template_class::JQ_getQuestionDelimeter()?>': '';

	<?php if((preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name) || preg_match("/t3_bs3/", $quiz->template_name)) && $quiz->c_show_timer){?>
		jq_jQuery('.jq_time_tick_container').css('display', 'inline-block');
	<?php } ?>

	var question_info = '';
	jq_getObj('jq_quiz_container').innerHTML = '';

	for (var i = 0; i < quest_count; i++){
		questions[i] = new question_data();
		if (questions[i].cur_quest_type == 9) {
			question_info = '';
		} else {
			question_info = '<?php echo JoomlaQuiz_template_class::JQ_getQuestionInfo()?>';
		}

		question_template = '<?php echo JoomlaQuiz_template_class::JQ_QuizBody()?>';
		jq_GetQuestionData(response.getElementsByTagName('question_data')[i], i);
		questions[i].is_prev = parseInt(response.getElementsByTagName('is_prev')[0].firstChild.data);
		questions[i].is_last = parseInt(response.getElementsByTagName('is_last')[0].firstChild.data);
		var div_quest_text = document.createElement("div");
		var div_inside = document.createElement("div");
		div_inside.id = questions[i].div_id;
		<?php
			if(!preg_match("/pretty_green/", $quiz->template_name)){
				echo "div_inside.style.position = 'relative';";
			}
		?>
		div_inside.className = 'jq_question_inner';

		<?php if ($quiz->c_show_quest_pos && !$quiz->c_pagination  ) { ?>
			<?php
				if(preg_match("/t3_bs3/", $quiz->template_name) || preg_match("/pretty_green/", $quiz->template_name)){
				?>
					question_info = question_info.replace("<!-- Z -->", (questions[i].cur_quest_num/quiz_count_quests)*100).replace("<!-- Z -->", (questions[i].cur_quest_num/quiz_count_quests)*100);
					question_info = question_info.replace("<!-- QUESTION_X_Y -->", mes_quest_number.replace("{X}", questions[i].cur_quest_num).replace("{Y}", quiz_count_quests));
					question_info = getProgressBar(questions[i].cur_quest_num, quiz_count_quests, question_info);
				<?php
				}else{
				?>
					question_info = question_info.replace("<!-- QUESTION_X_Y -->", mes_quest_number.replace("{X}", questions[i].cur_quest_num).replace("{Y}", quiz_count_quests));
				<?php
				}
			?>
		<?php }

		if ($quiz->c_show_quest_points) { ?>
		if (questions[i].cur_quest_type == 9) {
		} else {
			question_info = question_info.replace("<!-- POINTS -->", mes_quest_points.replace("{X}", questions[i].cur_quest_score ));
		}
		<?php }
		if (!$quiz->c_show_quest_pos && !$quiz->c_show_quest_points) {?>
		question_info = '';
		<?php }?>

		if (questions[i].cur_quest_type == 7) {
			jq_jQuery(div_quest_text).append(question_info + '<span class="error_messagebox_quest" id="error_messagebox_quest'+questions[i].cur_quest_id+'"><!-- x --></span>');
			var div_inside_hs = document.createElement("div");
			div_inside_hs.id = 'quest_div'+questions[i].cur_quest_id+'_hs';
			div_inside_hs.style.position = 'relative';
			div_inside_hs.innerHTML =  questions[i].quest_data_user;
		} else {
			div_inside.innerHTML = question_info + '<span class="error_messagebox_quest" id="error_messagebox_quest'+questions[i].cur_quest_id+'"><!-- x --></span>';
		}

		div_inside.innerHTML += questions[i].quest_data;
		div_quest_text.appendChild(div_inside);

		if (questions[i].cur_quest_type == 7) {
			div_quest_text.appendChild(div_inside_hs);
			questions[i].quest_data_user = '<!-- x -->';
		}
		question_template = question_template.replace(/\{QUESTION_TEXT\}/, div_quest_text.innerHTML);
		question_template = question_template.replace(/\{ANSWERS\}/, questions[i].quest_data_user);

		jq_getObj('jq_quiz_container').innerHTML = jq_getObj('jq_quiz_container').innerHTML + '<div <?php echo ((!preg_match("/pretty_green/", $quiz->template_name)) ? 'style="position: relative;"' : '');?> id="qcontainer'+questions[i].cur_quest_id+'">' + question_template + (questions[i].c_separator? question_delimeter:'') + '</div>';

		questions[i].disabled = false;

		var question = response.getElementsByTagName('question_data')[i];
		if (question.getElementsByTagName('quest_task')[0].firstChild.data == 'no_attempts') {
			jq_Disable_Question(i);
			var text = question.getElementsByTagName('quest_message_box')[0].firstChild.data;
			createPopupText(questions[i].cur_quest_id, text, 'incorrect_answer' );
		} else if (question.getElementsByTagName('quest_task')[0].firstChild.data == 'disabled') {
			jq_Disable_Question(i);
		}

	}
	<?php if(preg_match("/pretty_green/", $quiz->template_name) && !$quiz->c_show_timer) {?>
	jq_jQuery("#jq_question_info_container").css("margin-top", "0");
	<?php }?>

	for (var i = 0; i < quest_count; i++){
		if (questions[i].exec_quiz_script == 1 || questions[i].exec_quiz_script == '1' ) {
			eval(questions[i].quiz_script_data);
		}

		<?php JoomlaquizHelper::getJavascriptIncludes('includes');?>
	}
}

function getProgressBar(x, y, text) {
	percent = Math.round((100 / y) * x);

	html = '<div class="progress progress-striped active">'+text+'<div class="bar" style="width: '+percent+'%;"></div> </div>';

	return html;
}

function createPopupText(question_id, text, className) {

	var div_quest = jq_getObj('qcontainer'+question_id);
	var divv = document.createElement("div");
	divv.id = 'divv'+question_id;
	divv.innerHTML = text;
	div_quest.appendChild(divv);
	divv.className = className;
}

function createMiniPopupText(blank_id, is_correct) {

	var quest_blank = jq_getObj('blk_id_'+blank_id);
	var blank_value = jq_getObj('hid_blk_id_'+blank_id).value;

	jq_jQuery(quest_blank).removeClass('blank_correct');
	jq_jQuery(quest_blank).removeClass('blank_incorrect');
	jq_jQuery(quest_blank).addClass((is_correct? 'blank_correct': 'blank_incorrect'));
	jq_jQuery(quest_blank).removeClass('jq_blank_droppable');
	jq_jQuery(quest_blank).removeClass('ui-droppable');
	jq_jQuery(quest_blank).droppable( "destroy" );
	jq_jQuery(quest_blank).html(blank_value+'&nbsp;');
	createDD();

}

function removePopupText(question_id) {
	var divv = jq_getObj('divv'+question_id);
	if(divv){
		try {
			divv.parentNode.removeChild(divv);
		} catch(e){}
	}
}

function pagination_go(page_num) {
	var http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try { http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try { http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!http_request) {
		return false;
	}
	http_request.onreadystatechange = function() { jq_showpage(http_request); };
	var url_prefix2 = jq_clean_amp('&lang=<?php echo _JQ_JF_LANG;?>&user_unique_id=' + user_unique_id);
	var url = jq_clean_amp('&stu_quiz_id='+stu_quiz_id+'&ajax_task=showpage&quest_per_page='+jq_getObj('quest_per_page').value+'&limitstart='+page_num+'&quiz=<?php echo $quiz->c_id?>');

	lp_url_prefix2 = jq_clean_amp("tmpl=component&option=com_joomlaquiz<?php echo JoomlaquizHelper::JQ_GetItemId();?>&task=ajaxaction.procces" + url_prefix2);
    var post_target = jq_clean_amp('<?php echo JUri::root(true) ?>/index.php?tmpl=component&option=com_joomlaquiz<?php echo JoomlaquizHelper::JQ_GetItemId();?>&task=ajaxaction.procces');
	http_request.open("POST", jq_clean_amp(post_target), true);
	http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_request.send(jq_clean_amp(lp_url_prefix2 + url));
	var div_inside = jq_getObj('jq_feed_questions');
	div_inside.align = 'center';
}

function jq_clean_amp(str) {
	var ampChar = String.fromCharCode(38);
	return str.replace(/\&amp;/gi, ampChar);
}

function jq_showpage(http_request){
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			if(http_request.responseXML.documentElement == null){
				try {
					http_request.responseXML.loadXML(http_request.responseText);
				} catch (e) {
				}
			}
			var div_inside = jq_getObj('jq_feed_questions');
			var response  = http_request.responseXML.documentElement;
			try {
				var $quiz_statistic = jq_jQuery(response).find('quiz_statistic').text();
				div_inside.innerHTML = $quiz_statistic;
				<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
				jq_jQuery(".jq_feedback_question_header").toggle(function () {
					var curentContent = jq_jQuery(this).next();
					curentContent.removeClass('visible').addClass('hidden').slideUp(1000);
				},function () {
					var curentContent = jq_jQuery(this).next();
					curentContent.addClass('visible').removeClass('hidden').slideDown(1000);
				});
				<?php } ?>
			} catch (e) {}
		}
	}
}

function jq_MakeRequest(url, do_clear, silent) {
	var do_silent = parseInt(silent);

	if (!do_silent)
	if (do_clear == 1) jq_UpdateTaskDiv('hide');

	var http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try { http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try { http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!http_request) {
		return false;
	}
	if (do_clear == 1 && !do_silent) {
		jq_getObj('jq_quiz_container').style.display = 'none';
	}
	try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}

	if (!do_silent){
		jq_jQuery('#jq_quiz_container1').css('opacity', 0.7);
		jq_jQuery('#jq_quiz_container1').addClass('jq_ajax_loader');
	}

	quiz_blocked == 1;
	http_request.onreadystatechange = function() { jq_AnalizeRequest(http_request); };
	<?php if ($is_preview) { ?>
	var url_prefix2 = '&preview_id=<?php echo $preview_id?>';
	<?php } else { ?>
	var url_prefix2 = '&user_unique_id=' + user_unique_id;
	<?php } ?>

	lp_url_prefix2 = jq_clean_amp("tmpl=component&lang=<?php echo _JQ_JF_LANG;?>&option=com_joomlaquiz<?php echo JoomlaquizHelper::JQ_GetItemId();?>&task=ajaxaction.procces" + url_prefix2);
	var post_target = jq_clean_amp('<?php echo JUri::root(true) ?>/index.php?tmpl=component&option=com_joomlaquiz<?php echo JoomlaquizHelper::JQ_GetItemId();?>&task=ajaxaction.procces');
	http_request.open("POST", post_target, true);
	http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_request.send(jq_clean_amp(lp_url_prefix2 + url));
	stop_timer = 2;
}

var globalImageWidth = [],
	globalImageHeight = [],
	globalScaleImageWidth = [],
	globalScaleImageHeight = [],
	globalCircleX = [],
	globalCircleY = [];

function jq_updateHotspot(){

	jq_jQuery(document).ready(function($){
		if (typeof _recalculateSize == 'function') {
			_recalculateSize();
			$(window).on( 'orientationchange resize', function(e){
				setTimeout(_recalculateSize(), 10);
			});
		}
	});

}

function jq_AnalizeRequest(http_request) {
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			if(http_request.responseXML.documentElement == null){
				try {
					http_request.responseXML.loadXML(http_request.responseText);
				} catch (e) {
				}
			}

			response  = http_request.responseXML.documentElement;
			if (response.getElementsByTagName('task')[0].firstChild.data!='finish' && response.getElementsByTagName('task')[0].firstChild.data!='results' && response.getElementsByTagName('task')[0].firstChild.data!='review_finish') {
				quest_type = jq_jQuery(response).find('quest_type').text();
			} else {
				quest_type=0;
			}

			var task = response.getElementsByTagName('task')[0].firstChild.data;
			ShowMessage('error_messagebox',0,'');
			jq_jQuery('#jq_quiz_container1').css('opacity', 1);
			jq_jQuery('#jq_quiz_container1').removeClass('jq_ajax_loader');

			stop_timer = 0;
			switch (task) {
				case 'start':
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 1000);
						try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
						user_unique_id = response.getElementsByTagName('user_unique_id')[0].firstChild.data;
						stu_quiz_id = response.getElementsByTagName('stu_quiz_id')[0].firstChild.data;
						var skip_question = jq_jQuery(response).find('skip_question').text();
						var limit_time = jq_jQuery(response).find('quest_limit_time').text();

						quiz_count_quests = response.getElementsByTagName('quiz_count_quests')[0].firstChild.data;
						jq_getObj('jq_quiz_container').innerHTML = '';
						jq_getObj('jq_quiz_container').style.display = '';

						quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
						jq_CreateQuestions();

						var is_last = parseInt(response.getElementsByTagName('is_last')[0].firstChild.data);
						if (is_last){
							jq_UpdateTaskDiv('next_last', skip_question);
						} else {
							jq_UpdateTaskDiv('next', skip_question);
						}
                        /*
						if(quest_type == 13){
							quest_timer_sec = 0;
						}
                         */
						if(limit_time != 0 && (quest_type < 11 || quest_type > 14)){
							quest_timer_sec = limit_time;
							var min_str = (limit_time < 10) ? '0' + limit_time : limit_time;
							jq_jQuery('.jq_quest_time_past').html('<strong><?php echo JText::_('COM_QUIZ_TIME_LEFT');?></strong>&nbsp;' + min_str + ':00');
							jq_Start_Question_TickTack(limit_time);
							quest_timer = setInterval("jq_Start_Question_TickTack("+limit_time+")", 1000);
						}

						<?php if ($quiz->c_show_timer) { ?>
							var past_time = 0;
							try {
								past_time = parseInt(response.getElementsByTagName('quiz_past_time')[0].firstChild.data);
							} catch(e){}
							jq_Start_TickTack(past_time);
						<?php }
						if ($quiz->c_slide) { ?>
							jq_getObj('jq_quiz_result_container').innerHTML = response.getElementsByTagName('quiz_panel_data')[0].firstChild.data;
							jq_getObj('jq_panel_link_container').style.visibility = 'visible';
						<?php } ?>
					break;

				case 'seek_quest':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					var req_user_unique_id = null;
					var req_stu_quiz_id = null;
					var req_quiz_count_quests = null;
					try {
						req_user_unique_id = response.getElementsByTagName('user_unique_id')[0].firstChild.data;
						req_stu_quiz_id = response.getElementsByTagName('stu_quiz_id')[0].firstChild.data;
						req_quiz_count_quests = response.getElementsByTagName('quiz_count_quests')[0].firstChild.data;
					} catch (e) {}

					if (req_user_unique_id && req_stu_quiz_id) {
						user_unique_id = req_user_unique_id;
						stu_quiz_id = req_stu_quiz_id;
						quiz_count_quests = req_quiz_count_quests;
					}

					jq_getObj('jq_quiz_container').innerHTML = '';
					jq_getObj('jq_quiz_container').style.display = '';

					quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
					jq_CreateQuestions();

					var is_prev = parseInt(response.getElementsByTagName('is_prev')[0].firstChild.data);
					var is_last = parseInt(response.getElementsByTagName('is_last')[0].firstChild.data);
					var skip_question = jq_jQuery(response).find('skip_question').text();

					var limit_time = jq_jQuery(response).find('quest_limit_time').text();
					/*
                    if(quest_type == 13){
						quest_timer_sec = 0;
					}
                    */
					if(limit_time != 0 && (quest_type < 11 || quest_type > 14)){
						clearInterval(quest_timer);
						quest_timer_sec = limit_time;
						var min_str = (limit_time < 10) ? '0' + limit_time : limit_time;
						jq_jQuery('.jq_quest_time_past').html('<strong><?php echo JText::_('COM_QUIZ_TIME_LEFT');?></strong>&nbsp;' + min_str + ':00');
						jq_Start_Question_TickTack(limit_time);
						quest_timer = setInterval("jq_Start_Question_TickTack("+limit_time+")", 1000);
					} else {
						clearInterval(quest_timer);
						jq_jQuery('.jq_quest_time_past').html('');
					}

					if (is_prev && is_last) {
						jq_UpdateTaskDiv('prev_next_last', skip_question);
					} else if (is_last) {
						jq_UpdateTaskDiv('next_last', skip_question);
					} else if (is_prev) {
						jq_UpdateTaskDiv('prev_next', skip_question);
					} else {
						jq_UpdateTaskDiv('next', skip_question);
					}
					if (req_user_unique_id && req_stu_quiz_id) {
					<?php if ($quiz->c_show_timer) { ?>
							var past_time = 0;
							try {
								past_time = parseInt(response.getElementsByTagName('quiz_past_time')[0].firstChild.data);
							} catch(e){}
							jq_Start_TickTack(past_time);
					<?php }
					if ($quiz->c_slide) { ?>
						jq_getObj('jq_quiz_result_container').innerHTML = response.getElementsByTagName('quiz_panel_data')[0].firstChild.data;
						jq_getObj('jq_panel_link_container').style.visibility = 'visible';
					<?php } ?>
					}

					if(jq_jQuery('#jq_total_memory_point')){
						jq_jQuery('#jq_total_memory_point').remove();
						jq_jQuery('#jq_current_memory_point').remove();
						jq_jQuery('#jq_penalty_memory_point').remove();
					}
				break;

				case 'review_start':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					quiz_count_quests = response.getElementsByTagName('quiz_count_quests')[0].firstChild.data;
					jq_getObj('jq_quiz_container').innerHTML = '';
					jq_getObj('jq_quiz_container').style.display = '';

					quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
					jq_CreateQuestions();

					jq_UpdateTaskDiv('review_next');
					<?php if (false && $quiz->c_slide) { ?>
						jq_getObj('jq_panel_link_container').style.visibility = 'visible';
					<?php } ?>
					<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
					jq_jQuery(".jq_time_tick_container").css("visibility", "hidden");
					<?php } ?>
					jq_updateHotspot();
                    stop_timer = 1;
				break;

				case 'review_next':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					jq_getObj('jq_quiz_container').innerHTML = '';
					jq_getObj('jq_quiz_container').style.display = '';

					quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
					jq_CreateQuestions();

					jq_UpdateTaskDiv('review_next');
					jq_updateHotspot();
                    stop_timer = 1;
				break;

				case 'review_finish':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					jq_UpdateTaskDiv('finish');
					var quiz_cont = jq_getObj('jq_quiz_container');
					jq_getObj('jq_quiz_container').style.display = '';
					quiz_cont.innerHTML = '<form name=\'quest_form\'></form>'+saved_prev_res_data;
					<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
					jq_jQuery(".jq_feedback_question_header").toggle(function () {
							var curentContent = jq_jQuery(this).next();
							curentContent.removeClass('visible').addClass('hidden').slideUp(1000);
						},function () {
							var curentContent = jq_jQuery(this).next();
							curentContent.addClass('visible').removeClass('hidden').slideDown(1000);
					});
					<?php } ?>
					if(jq_jQuery('#jq_total_memory_point')){
						jq_jQuery('#jq_total_memory_point').remove();
						jq_jQuery('#jq_current_memory_point').remove();
						jq_jQuery('#jq_penalty_memory_point').remove();
					}
					jq_updateHotspot();
                    stop_timer = 1;
				break;

				case 'next':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					var quiz_cont = jq_getObj('jq_quiz_container');
					jq_getObj('jq_quiz_container').style.display = '';
					var skip_question = jq_jQuery(response).find('skip_question').text();
					var limit_time = jq_jQuery(response).find('quest_limit_time').text();

					if(limit_time != 0 && (quest_type < 11 || quest_type > 14)){
							clearInterval(quest_timer);
							quest_timer_sec = limit_time;
							var min_str = (limit_time < 10) ? '0' + limit_time : limit_time;
							jq_jQuery('.jq_quest_time_past').html('<strong><?php echo JText::_('COM_QUIZ_TIME_LEFT');?></strong>&nbsp;' + min_str + ':00');
							jq_Start_Question_TickTack(limit_time);
							quest_timer = setInterval("jq_Start_Question_TickTack("+limit_time+")", 1000);
						} else {
							clearInterval(quest_timer);
							jq_jQuery('.jq_quest_time_past').html('');
						}

					var is_prev = parseInt(response.getElementsByTagName('is_prev')[0].firstChild.data);
					if (is_prev)
						jq_processFeedback('prev_next', 0, skip_question);
					else
						jq_processFeedback('next', 0, skip_question);
				break;

				case 'prev':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					var quiz_cont = jq_getObj('jq_quiz_container');
					quiz_cont.innerHTML = '';
					quiz_cont.style.display = '';
					quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
					jq_CreateQuestions();

					var skip_question = jq_jQuery(response).find('skip_question').text();
					var limit_time = jq_jQuery(response).find('quest_limit_time').text();
					var quest_task = jq_jQuery(response).find('quest_task').text();

					if(limit_time!=0 && quest_task != 'no_attempts' && (quest_type < 11 || quest_type > 14)){
							clearInterval(quest_timer);
							quest_timer_sec = limit_time;
							var min_str = (limit_time < 10) ? '0' + limit_time : limit_time;
							jq_jQuery('.jq_quest_time_past').html('<strong><?php echo JText::_('COM_QUIZ_TIME_LEFT');?></strong>&nbsp;' + min_str + ':00');
							jq_Start_Question_TickTack(limit_time);
							quest_timer = setInterval("jq_Start_Question_TickTack("+limit_time+")", 1000);
						} else {
							clearInterval(quest_timer);
							jq_jQuery('.jq_quest_time_past').html('');
						}

					var is_prev = parseInt(response.getElementsByTagName('is_prev')[0].firstChild.data);
					if (is_prev){
						jq_processFeedback('prev', 0, skip_question);
					} else{
						jq_processFeedback('prev_first', 0, skip_question);
					}
				break;
				<?php if ($is_preview) { ?>
				case 'quest_preview':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					quiz_count_quests = response.getElementsByTagName('quiz_count_quests')[0].firstChild.data;
					jq_getObj('jq_quiz_container').innerHTML = '';
					jq_getObj('jq_quiz_container').style.display = '';

					quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
					jq_CreateQuestions();

					jq_UpdateTaskDiv('next');
				break;
				case 'preview_finish':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					var quiz_cont = jq_getObj('jq_quiz_container');
					jq_getObj('jq_quiz_container').style.display = '';
					jq_processFeedback('preview_finish', 1);

				break;
				<?php } ?>

				case 'email_results':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					var email_msg = response.getElementsByTagName('email_msg')[0].firstChild.data;
					ShowMessage('error_messagebox', 1, email_msg);
				break;

				case 'time_is_up':
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					var quiz_cont = jq_getObj('jq_quiz_container');
					var children = quiz_cont.childNodes;
					for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
					var qmb = response.getElementsByTagName('quiz_message_box')[0].firstChild.data;
					quiz_cont.innerHTML = '<form name=\'quest_form\'></form>';
					quiz_cont.innerHTML = '<form name=\'quest_form\'></form>'+qmb;
					jq_getObj('jq_time_tick_container').innerHTML = mes_time_is_up;
					ShowMessage('error_messagebox', 1, mes_time_is_up);
					stop_timer = 1;

					jq_UpdateTaskDiv('continue_finish');
					jq_getObj('jq_time_tick_container').innerHTML = mes_time_is_up;
					stop_timer = 1;
				break;

				case 'finish':
					<?php if(preg_match("/pretty_green/", $quiz->template_name) && $quiz->c_show_timer){?>
					jq_jQuery(".jq_time_tick_container").css("visibility", "hidden");
					<?php } ?>
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					var quiz_cont = jq_getObj('jq_quiz_container');
					jq_getObj('jq_quiz_container').style.display = '';
					jq_processFeedback('finish', 0);
				break;


				case 'results':
					<?php if($quiz->c_flag):?>
					jq_jQuery('#c_flag').unbind('click');
					document.getElementById('c_flag').checked = false;
					jq_jQuery('.jq_flagged_question').hide();
					<?php endif;?>

					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 100);
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					jq_getObj('jq_quiz_container').style.display = '';
					var quiz_cont = jq_getObj('jq_quiz_container');
					var children = quiz_cont.childNodes;
					for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
					quiz_cont.innerHTML = '<form name=\'quest_form\'></form>';
					stop_timer = 1;

					jq_UpdateTaskDiv('finish');

					var quiz_redirect = 0;
					try {
						quiz_redirect = parseInt(response.getElementsByTagName('quiz_redirect')[0].firstChild.data);
						if (quiz_redirect && JQ_process_redirect(response)) {
							return;
						}
					} catch(e){}

					var quiz_results = jq_jQuery(response).find('quiz_results').text();

					var quiz_cont = jq_getObj('jq_quiz_container');
					quiz_cont.innerHTML = '<form name=\'quest_form\'></form>' + quiz_results;

					jq_updateHotspot();

					saved_prev_res_data = quiz_results;
					<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>

					jq_jQuery(".jq_feedback_question_header").toggle(function () {
							var curentContent = jq_jQuery(this).next();
							curentContent.removeClass('visible').addClass('hidden').slideUp(1000);
						},function () {
							var curentContent = jq_jQuery(this).next();
							curentContent.addClass('visible').removeClass('hidden').slideDown(1000);
					});

					<?php } ?>
				break;

				case 'blank_feedback':
					var blank_id = 0;
					var is_correct = '';
					blank_id = response.getElementsByTagName('quest_blank_id')[0].firstChild.data;
					is_correct = parseInt(response.getElementsByTagName('is_correct')[0].firstChild.data);

					createMiniPopupText(blank_id, is_correct);
				break;

				case 'failed':
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					ShowMessage('error_messagebox', 1, mes_failed);
					quiz_blocked = 1;
					setTimeout("jq_releaseBlock()", 1000);
				break;
				default:
				break;
			}
		} else {
			quiz_blocked = 1;
			setTimeout("jq_releaseBlock()", 1000);
			jq_UpdateTaskDiv('show');
			ShowMessage('error_messagebox', 1, '<?php echo addslashes(JText::_('COM_QUIZ_FAILED_REQUEST'))?>');
		}
	}
}

var redirect_delay = 0;
var redirect_url = '';

function JQ_process_redirect(response) {
	try {
		redirect_delay = parseInt(response.getElementsByTagName('quiz_redirect_delay')[0].firstChild.data);
		redirect_url = response.getElementsByTagName('quiz_redirect_url')[0].firstChild.data;
	} catch(e) {}

	if (redirect_delay && redirect_url) {
		setTimeout("JQ_do_redirect()", redirect_delay*1000);
		return false;
	} else if (redirect_url){
		return JQ_do_redirect();
	}

	return false;
}

function JQ_do_redirect() {
	if (!redirect_url) return false;
	redirect_url = redirect_url+'';
	if (redirect_url.indexOf('javascript:') === -1) {
		window.location.href = redirect_url;
	} else {
		redirect_url = redirect_url.replace("javascript:", "");
		eval(redirect_url);
	}
	return true;
}

function jq_processFeedback(task, is_preview, skip_question){

	skip_question = (skip_question) ? skip_question : 0;
	var feedback_count = 0;
	try {
		feedback_count = parseInt(response.getElementsByTagName('feedback_count')[0].firstChild.data);
	} catch(e){}

	var feedback = null;
	var feedback_quest_id = 0;
	var do_feedback = 0;
	var prev_correct = 0;
	var feed_task = '';
	var is_allow_attempt = 0;
	var is_do_feedback = 0;
	var show_flag = 0;

	if (feedback_count) {
		for(var n=0; n < quest_count; n++) {
			removePopupText(questions[n].cur_quest_id);
		}

		for(var i=0; i < feedback_count; i++){
			feedback = response.getElementsByTagName('feedback')[i];
			do_feedback = parseInt(feedback.getElementsByTagName('quest_feedback')[0].firstChild.data);
			feedback_quest_id = feedback.getElementsByTagName('feedback_quest_id')[0].firstChild.data;
			prev_correct = feedback.getElementsByTagName('quiz_prev_correct')[0].firstChild.data;

			<?php if($quiz->c_flag):?>
			show_flag = jq_jQuery(response).find('feedback_show_flag').text();

			if(~~show_flag){

				jq_jQuery('.jq_flagged_question').show().css({'display':'inline-block'});
				jq_jQuery('#c_flag').bind('click', function(){
					setFlag(feedback_quest_id);
				});
			}
			<?php endif;?>

			if (feedback_quest_id) {
				for (var j=0; j<quest_count; j++) {
					if (questions[j].cur_quest_id == feedback_quest_id) {
						var allow_attempt = 0;
						try {
							allow_attempt = feedback.getElementsByTagName('quiz_allow_attempt')[0].firstChild.data;
						} catch(e) {}
						questions[j].attempts = allow_attempt;
						jq_Disable_Question(j);
						break;
					}
				}
			}

			if (do_feedback){
				if (feedback_quest_id) {
					is_do_feedback++;

					var feedback_quest_type = feedback.getElementsByTagName('feedback_quest_type')[0].firstChild.data;
					if (!jq_getObj('div_qoption'+feedback_quest_id)) {
						null;
					}else {
						var blank_fbd_count = 0;
						try {
							blank_fbd_count = feedback.getElementsByTagName('blank_fbd_count')[0].firstChild.data;
						} catch(e){}
						if (blank_fbd_count) {
							for(var ff=0; ff<blank_fbd_count; ff++){
								var blank_id = feedback.getElementsByTagName('quest_blank_id')[ff].firstChild.data;
								var is_correct = parseInt(feedback.getElementsByTagName('is_correct')[ff].firstChild.data);

								createMiniPopupText(blank_id, is_correct);
							}
						} else {
							var ftext = feedback.getElementsByTagName('quiz_message_box')[0].firstChild.data;
							var fclassName = prev_correct == '1'? 'correct_answer': 'incorrect_answer';
							createPopupText(feedback_quest_id, ftext, fclassName);
						}
					}

					if (task == 'preview_finish') {
						feed_task = 'preview_back';
					} else {
                        /* IE11 fix */
                        //<if(jq_getObj('quest_result_'+feedback_quest_id)){
                            if (prev_correct == '1') { // correct answer
                                <?php if ($quiz->c_slide) { ?>
                                    <?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
                                        jq_getObj('quest_result_'+feedback_quest_id).innerHTML = '<img src="<?php echo JURI::root(true)?>/components/com_joomlaquiz/assets/images/result_panel_true.png" border=0>';
                                    <?php } else {?>
                                        jq_getObj('quest_result_'+feedback_quest_id).innerHTML = '<img src="<?php echo JURI::root(true)?>/components/com_joomlaquiz/assets/images/tick.png" border=0>';
                                    <?php }?>
                                <?php } ?>
                            } else { // incorrect answer
                                <?php if ($quiz->c_slide) { ?>
                                    <?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
                                        jq_getObj('quest_result_'+feedback_quest_id).innerHTML = '<img src="<?php echo JURI::root(true)?>/components/com_joomlaquiz/assets/images/result_panel_false.png" border=0>';
                                    <?php } else {?>
                                        jq_getObj('quest_result_'+feedback_quest_id).innerHTML = '<img src="<?php echo JURI::root(true)?>/components/com_joomlaquiz/assets/images/publish_x.png" border=0>';
                                    <?php }?>
                                <?php } ?>

                                allow_attempt = response.getElementsByTagName('quiz_allow_attempt')[0].firstChild.data;
                                if (allow_attempt == 1) {
                                    is_allow_attempt++;
                                }
                            }
						//}

					}
				}
			}// if do_feedback
			else {
				if (prev_correct == '1') {
					<?php if ($quiz->c_slide) { ?>
						<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
							jq_getObj('quest_result_'+feedback_quest_id).innerHTML = '<img src="<?php echo JURI::root(true)?>/components/com_joomlaquiz/assets/images/result_panel_true.png" border=0>';
						<?php } else {?>
							jq_getObj('quest_result_'+feedback_quest_id).innerHTML = '<img src="<?php echo JURI::root(true)?>/components/com_joomlaquiz/assets/images/tick.png" border=0>';
						<?php } ?>
					<?php } ?>
				} else {
					<?php if ($quiz->c_slide) { ?>
						<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
							jq_getObj('quest_result_'+feedback_quest_id).innerHTML = '<img src="<?php echo JURI::root(true)?>/components/com_joomlaquiz/assets/images/result_panel_false.png" border=0>';
						<?php } else {?>
							jq_getObj('quest_result_'+feedback_quest_id).innerHTML = '<img src="<?php echo JURI::root(true)?>/components/com_joomlaquiz/assets/images/publish_x.png" border=0>';
						<?php } ?>
					<?php } ?>
				}
			}
		}//for...

		if (!is_do_feedback) {
			if (task == 'finish')
				jq_QuizContinueFinish();
			else if (task == 'start' || task == 'next' || task == 'prev_next' || task == 'prev' || task == 'prev_first') {
				jq_QuizContinue();
			}
			return;
		}

		if (is_allow_attempt) {
			if (task == 'finish')
				feed_task = 'back_continue_finish';
			else if (task == 'start' || task == 'next' || task == 'prev_next'){
				feed_task = 'back_continue';
			}
		} else {
			if (task == 'finish')
				feed_task = 'continue_finish';
			else if (task == 'start' || task == 'next' || task == 'prev_next'){
				feed_task = 'continue';
			}
		}

	} // if feedback count

	if (!is_do_feedback) {
		if (task == 'finish')
			jq_QuizContinueFinish();
		else if (task == 'start' || task == 'next' || task == 'prev_next' || task == 'prev' || task == 'prev_first') {
			jq_QuizContinue();
		}
	}

	if (feed_task == '') {
		feed_task = task;
	}

	switch (feed_task) {
		case 'start':
			jq_UpdateTaskDiv('start', skip_question);
		break;

		case 'prev_next':
			jq_UpdateTaskDiv('prev_next', skip_question);
		break;

		case 'next':
			jq_UpdateTaskDiv('next', skip_question);
		break;

		case 'next_last':
			jq_UpdateTaskDiv('next_last', skip_question);
		break;

		case 'prev_next_last':
			jq_UpdateTaskDiv('next_last', skip_question);
		break;

		case 'prev':
			jq_UpdateTaskDiv('prev', skip_question);
		break;

		case 'prev_first':
			jq_UpdateTaskDiv('prev_first', skip_question);
		break;

		case 'back_continue':
			jq_UpdateTaskDiv('back_continue');
		break;

		case 'back_continue_finish':
			jq_UpdateTaskDiv('back_continue_finish');
		break;

		case 'continue':
			jq_UpdateTaskDiv('continue');
		break;

		case 'continue_finish':
			jq_UpdateTaskDiv('continue_finish');
		break;

		case 'finish':
			jq_UpdateTaskDiv('finish');
		break;

		case 'back':
			jq_UpdateTaskDiv('back');
		break;

		case 'review_next':
			jq_UpdateTaskDiv('review_next');
		break;

		case 'preview_back':
			jq_UpdateTaskDiv('preview_back');
		break;
	}

}

function jq_releaseBlock() {
	quiz_blocked = 0;
}

function jq_Start_Question_TickTack(limit_time)
{
    console.log(limit_time, quest_timer_sec);
    if(quest_timer_sec <= 0 ){
        ShowMessage('error_messagebox', 1, '<?php echo JText::_('COM_QUIZ_TIME_FOR_ANSWERING_HAS_RUN_OUT');?>');
        quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
        for(var n=0; n < quest_count; n++) {
            questions[n].disabled = true;
        }
        alert('<?php echo JText::_('COM_QUIZ_TIME_FOR_ANSWERING_HAS_RUN_OUT');?>');
        setTimeout("jq_QuizNextOn()", 300);
        clearInterval(quest_timer);
        jq_jQuery('.jq_quest_time_past').html('');
        if(jq_jQuery('#jq_total_memory_point')){
            jq_jQuery('#jq_total_memory_point').remove();
            jq_jQuery('#jq_current_memory_point').remove();
            jq_jQuery('#jq_penalty_memory_point').remove();
        }
        return;
    } else {
        var quest_timer_sec_tmp = quest_timer_sec;
        var quest_timer_min = parseInt(quest_timer_sec_tmp/60);
        var plus_sec = quest_timer_sec_tmp - (quest_timer_min*60);
        if (quest_timer_min < 0) { quest_timer_min = quest_timer_min*(-1); }
        if (plus_sec < 0) { plus_sec = plus_sec*(-1); }
        var time_str = quest_timer_min + '';
        if (time_str.length == 1) time_str = '0'+time_str;
        quest_time_str2 = plus_sec + '';
        if (quest_time_str2.length == 1) quest_time_str2 = '0'+quest_time_str2;
        jq_jQuery('.jq_quest_time_past').html('<strong><?php echo JText::_('COM_QUIZ_TIME_LEFT');?></strong>&nbsp;' + time_str + ':' + quest_time_str2);
        quest_timer_sec--;
    }
}

function jq_Start_TickTack(past_time) {
	clearInterval(quest_timer_ticktack);
	timer_sec = 1;
	if (parseInt(past_time)) {
		timer_sec = past_time;
	}
	if (max_quiz_time < timer_sec) {
		jq_getObj('jq_time_tick_container').innerHTML = mes_time_is_up;
		jq_getObj('jq_time_tick_container').style.visibility = "visible";
		setTimeout("jq_QuizContinueFinish()", 1000);
		return;
	}

	if (timer_style == 2 && max_quiz_time > 0 && max_quiz_time != 3600000) {
		jq_getObj('jq_time_tick_container').innerHTML = '00:01' + ' <?php echo JText::_('COM_QUIZ_TIME_OF');?> ' + max_quiz_time/60 + ':00';
	} else if (timer_style == 1 && max_quiz_time > 0 && max_quiz_time != 3600000) {
		jq_getObj('jq_time_tick_container').innerHTML = max_quiz_time/60 + ':00';
	} else {
		jq_getObj('jq_time_tick_container').innerHTML = '00:01';
	}

	if (timer_sec > 1) {
		if (timer_style == 1 && max_quiz_time > 0 && max_quiz_time != 3600000) {
			var timer_sec_tmp = max_quiz_time-timer_sec;
		} else {
			var timer_sec_tmp = timer_sec;
		}
		var timer_min = parseInt(timer_sec_tmp/60);
		var plus_sec = timer_sec_tmp - (timer_min*60);
		if (timer_min < 0) { timer_min = timer_min*(-1); }
		if (plus_sec < 0) { plus_sec = plus_sec*(-1); }
		var time_str = timer_min + '';
		if (time_str.length == 1) time_str = '0'+time_str;
		time_str2 = plus_sec + '';
		if (time_str2.length == 1) time_str2 = '0'+time_str2;
		if (timer_style == 2 && max_quiz_time > 0 && max_quiz_time != 3600000) {
			jq_getObj('jq_time_tick_container').innerHTML = time_str + ':' + time_str2 + ' <?php echo JText::_('COM_QUIZ_TIME_OF');?> ' + max_quiz_time/60 + ':00';
		} else {
			jq_getObj('jq_time_tick_container').innerHTML = time_str + ':' + time_str2;
		}
	}

	jq_getObj('jq_time_tick_container').style.visibility = "visible";
	//setTimeout("jq_Continue_TickTack()", 1000);
	quest_timer_ticktack = setInterval("jq_Continue_TickTack()", 1000);
}

function jq_Continue_TickTack() {
	if (stop_timer == 1) {
		jq_getObj('jq_time_tick_container').style.visibility = "hidden";
	} else if (stop_timer == 2) {
	//pause
		jq_getObj('jq_time_tick_container').style.textDecoration = "blink";
		//setTimeout("jq_Continue_TickTack()", 1000);
	} else {
		jq_getObj('jq_time_tick_container').style.textDecoration = "none";
		timer_sec ++;
		<?php
		if ($quiz->c_pagination) {
		?>
		if (timer_sec > max_quiz_time - 3) {
			setTimeout("jq_QuizSaveNext()", 1000);
		}
		<?php } ?>
		if (timer_sec > max_quiz_time) {
			jq_getObj('jq_time_tick_container').innerHTML = mes_time_is_up;
			setTimeout("jq_QuizContinueFinish()", 1000);
			return;
		} else {
			if (timer_style == 1 && max_quiz_time > 0 && max_quiz_time != 3600000) {
				var timer_sec_tmp = max_quiz_time-timer_sec;
			} else {
				var timer_sec_tmp = timer_sec;
			}
			var timer_min = parseInt(timer_sec_tmp/60);
			var plus_sec = timer_sec_tmp - (timer_min*60);
			if (timer_min < 0) { timer_min = timer_min*(-1); }
			if (plus_sec < 0) { plus_sec = plus_sec*(-1); }
			var time_str = timer_min + '';
			if (time_str.length == 1) time_str = '0'+time_str;
			time_str2 = plus_sec + '';
			if (time_str2.length == 1) time_str2 = '0'+time_str2;
			if (timer_style == 2 && max_quiz_time > 0 && max_quiz_time != 3600000) {
				jq_getObj('jq_time_tick_container').innerHTML = time_str + ':' + time_str2 + ' <?php echo JText::_('COM_QUIZ_TIME_OF');?> ' + max_quiz_time/60 + ':00';
			} else {
				jq_getObj('jq_time_tick_container').innerHTML = time_str + ':' + time_str2;
			}
			//setTimeout("jq_Continue_TickTack()", 1000);
		}
	}
}

function jq_QuizSaveNext() {
	<?php if ($is_preview) { ?>
	var jq_task = 'next_preview';
	<?php } else { ?>
	var jq_task = 'next';
	<?php } ?>
	var answer = '';
	var url = '&ajax_task=' + jq_task + '&quiz=<?php echo $quiz->c_id?>'+'&stu_quiz_id='+stu_quiz_id;
	for(var n=0; n < quest_count; n++) {
		answer = '';
		if (!questions[n].disabled) {
			switch (questions[n].cur_quest_type) {
				<?php JoomlaquizHelper::getJavascriptIncludes('savenext');?>
				case '9':
					answer = 0;
				break;
			}
			url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]='+answer;
		} else {
			url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]=';
		}
	}

	jq_MakeRequest(url, 1);
}

function jq_validateEmail(){
	var email = document.getElementById('jq_user_email').value;
	var re = /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/;
	return re.test(email);
}

function jq_StartQuizOn() {

	if(document.getElementById('jq_user_name') && document.getElementById('jq_user_name').value == ''){
		alert('<?php echo JText::_('COM_JOOMLAQUIZ_DEFINE_USERNAME_PLEASE',true);?>');
		return false;
	}

	if(document.getElementById('jq_user_surname') && document.getElementById('jq_user_surname').value == ''){
		alert('<?php echo JText::_('COM_JOOMLAQUIZ_DEFINE_USERSURNAME_PLEASE');?>');
		return false;
	}

	if(document.getElementById('jq_user_email') && document.getElementById('jq_user_email').value == ''){
		alert('<?php echo JText::_('COM_JOOMLAQUIZ_DEFINE_EMAIL');?>');
		return false;
	}

	if(document.getElementById('jq_user_email') && document.getElementById('jq_user_email').value != ''){
		if(!jq_validateEmail()){
			alert('<?php echo JText::_('COM_JOOMLAQUIZ_DEFINE_VALID_EMAIL');?>');
			return false;
		}
	}

	if (!quiz_blocked) {
		jq_jQuery('#jq_quiz_container1').css('opacity', 0.7);
		jq_jQuery('#jq_quiz_container1').addClass('jq_ajax_loader');
		timerID = setTimeout("jq_StartQuiz()", 300);
	} else {
		<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
		var qid = jq_jQuery('.error_messagebox_quest').attr('id');
		ShowMessage(qid, 1, mes_please_wait);
		<?php } else { ?>
		ShowMessage('error_messagebox', 1, mes_please_wait);
		<?php } ?>
	}
}
function jq_StartQuiz() {

	var uname = (document.getElementById('jq_user_name') && document.getElementById('jq_user_name').value != '') ? document.getElementById('jq_user_name').value : '';
	var usurname = (document.getElementById('jq_user_surname') && document.getElementById('jq_user_surname').value != '') ? document.getElementById('jq_user_surname').value : '';
	var uemail = (document.getElementById('jq_user_email') && document.getElementById('jq_user_email').value != '') ? document.getElementById('jq_user_email').value : '';
	var custom_info = '';
	<?php
		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onQuizCustomFieldsRenderJS');
	?>

	uname = encodeURIComponent(uname);
	usurname = encodeURIComponent(usurname);
	uemail = encodeURIComponent(uemail);

	if (qs) {
		custom_info = custom_info+'&qs='+qs;
	}

	jq_MakeRequest('&ajax_task=start&quiz=<?php echo $quiz->c_id?>&uname=' + uname + '&uemail=' + uemail + '&usurname=' + usurname + custom_info, 1);
}

function JQ_gotoQuestionOn(qid) {

	clearInterval(quest_timer);
	jq_jQuery('.jq_quest_time_past').html('');
	if(jq_jQuery('#jq_total_memory_point')){
		jq_jQuery('#jq_total_memory_point').remove();
		jq_jQuery('#jq_current_memory_point').remove();
		jq_jQuery('#jq_penalty_memory_point').remove();
	}

	if (!quiz_blocked) {
		jq_jQuery('#jq_quiz_container1').css('opacity', 0.7);
		jq_jQuery('#jq_quiz_container1').addClass('jq_ajax_loader');
		timerID = setTimeout("JQ_gotoQuestion("+qid+")", 300);
	} else {
		<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
		var qid = jq_jQuery('.error_messagebox_quest').attr('id');
		ShowMessage(qid, 1, mes_please_wait);
		<?php } else { ?>
		ShowMessage('error_messagebox', 1, mes_please_wait);
		<?php } ?>
		setTimeout("jq_releaseBlock()", 1000);
	}
}
function JQ_gotoQuestion(qid) { jq_MakeRequest('&ajax_task=goto_quest&quiz=<?php echo $quiz->c_id?>&stu_quiz_id='+stu_quiz_id+'&seek_quest_id='+qid, 1 ); }
function jq_emailResultsUser()
{
	var jq_email_cont = jq_getObj('jq_user_email');
	var re_email = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/;
	if (!re_email.test(jq_email_cont.value)) {
		alert("Please enter a correct e-mail address");
		return;
	}
	user_email_to = jq_email_cont.value;
	jq_MakeRequest('&ajax_task=email_results&quiz=<?php echo $quiz->c_id?>&stu_quiz_id='+stu_quiz_id<?php echo ($quiz->c_email_to == 2)?"+'&email_address='+user_email_to":''; ?>,0);

}
function jq_emailResults() {
	if (!quiz_blocked) {
		<?php if($quiz->c_email_to == 2) { ?>
			if(!user_email_to)
			{
				if(jq_getObj('jq_user_email'))
				{
					var jq_email_cont = jq_getObj('jq_user_email');
					var re_email = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/;
					if (!re_email.test(jq_email_cont.value)) {
						alert("Please enter a correct e-mail address");
						return;
					}
					user_email_to = jq_email_cont.value;
					jq_MakeRequest('&ajax_task=email_results&quiz=<?php echo $quiz->c_id?>&stu_quiz_id='+stu_quiz_id<?php echo ($quiz->c_email_to == 2)?"+'&email_address='+user_email_to":''; ?>,0);
				}
				else
				jq_MakeRequest('&ajax_task=email_results&quiz=<?php echo $quiz->c_id?>&ent_em=1&stu_quiz_id='+stu_quiz_id<?php echo ($quiz->c_email_to == 2)?"+'&email_address='+user_email_to":''; ?>,0);
			}
			else
			jq_MakeRequest('&ajax_task=email_results&quiz=<?php echo $quiz->c_id?>&stu_quiz_id='+stu_quiz_id<?php echo ($quiz->c_email_to == 2)?"+'&email_address='+user_email_to":''; ?>,0);
		<?php }else{ ?>

		jq_MakeRequest('&ajax_task=email_results&quiz=<?php echo $quiz->c_id?>&stu_quiz_id='+stu_quiz_id<?php echo ($quiz->c_email_to == 2)?"+'&email_address='+user_email_to":''; ?>,0);
		<?php }?>
	}
}

function jq_startReview() {
	if (!quiz_blocked) {
		jq_MakeRequest('&ajax_task=review_start&quiz=<?php echo $quiz->c_id?>&stu_quiz_id='+stu_quiz_id, 1);
	} else {
		<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
		var qid = jq_jQuery('.error_messagebox_quest').attr('id');
		ShowMessage(qid, 1, mes_please_wait);
		<?php } else { ?>
		ShowMessage('error_messagebox', 1, mes_please_wait);
		<?php } ?>
		setTimeout("jq_releaseBlock()", 1000);
	}
}
function jq_QuizReviewNext() {
	if (!quiz_blocked) {
		var url = '&ajax_task=review_next&quiz=<?php echo $quiz->c_id?>'+'&stu_quiz_id='+stu_quiz_id;
		for(var n=0; n < quest_count; n++) {
			if (questions[n].disabled) {
				continue;
			}
			url = url + '&quest_id[]='+questions[n].cur_quest_id;
		}
		jq_MakeRequest(url, 1);
	} else {
		<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
		var qid = jq_jQuery('.error_messagebox_quest').attr('id');
		ShowMessage(qid, 1, mes_please_wait);
		<?php } else { ?>
		ShowMessage('error_messagebox', 1, mes_please_wait);
		<?php } ?>
		setTimeout("jq_releaseBlock()", 1000);
	}
}

function jq_Disable_Question(n){
	questions[n].disabled = true;
	switch (questions[n].cur_quest_type) {
		<?php JoomlaquizHelper::getJavascriptIncludes('disable');?>
	}
	return;
}

function jq_Check_valueItem(item_name, form_name) {

	<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
	selItem = jq_jQuery("form[name=" + form_name + "]").find('.jq_hidden_match');
	<?php } else { ?>
	selItem = eval('document.'+form_name+'.'+item_name);
	<?php } ?>
	var rrr = '';
	if (selItem) {
		if (selItem.length) { var i;
			for (i = 0; i<selItem.length; i++) {
				if (selItem[i].value == '{0}') return '';
				rrr = rrr + selItem[i].value + '```';
			}
			rrr = rrr.substring(0, rrr.length - 3);
		} else { rrr = rrr + selItem.value;	}}

	return rrr;
}

function jq_QuizNextOn() { // Two steps CHECK (delete this func in the future)
	for(var n=0; n < quest_count; n++) {
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 0, '');
	   // jq_QuizContinueFinish
	}
	for(var n=0; n < quest_count; n++) {
		if (questions[n].disabled) {
			continue;
		}
		switch (questions[n].cur_quest_type) {
			<?php JoomlaquizHelper::getJavascriptIncludes('next');?>
		}
		if (quiz_blocked) {
			try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
			<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
			var qid = jq_jQuery('.error_messagebox_quest').attr('id');
			ShowMessage(qid, 1, mes_please_wait);
			<?php } else { ?>
			ShowMessage('error_messagebox', 1, mes_please_wait);
			<?php } ?>
			setTimeout("jq_releaseBlock()", 1000);
			return;
		}
	}
	if (!quiz_blocked) {
		quiz_blocked = 1;
		timerID = setTimeout("jq_QuizNext()", 300);
	} else {
		try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
		<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
		var qid = jq_jQuery('.error_messagebox_quest').attr('id');
		ShowMessage(qid, 1, mes_please_wait);
		<?php } else { ?>
		ShowMessage('error_messagebox', 1, mes_please_wait);
		<?php } ?>
		setTimeout("jq_releaseBlock()", 1000);
	}

}

function jq_QuizContinue() {

	<?php if($quiz->c_flag):?>
	jq_jQuery('#c_flag').unbind('click');
	document.getElementById('c_flag').checked = false;
	jq_jQuery('.jq_flagged_question').hide();
	<?php endif;?>

	try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
	jq_getObj('jq_quiz_container').innerHTML = '';
	quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
	jq_CreateQuestions();

	var skip_question = jq_jQuery(response).find('skip_question').text();
	var is_prev = parseInt(response.getElementsByTagName('is_prev')[0].firstChild.data);
	var is_last = parseInt(response.getElementsByTagName('is_last')[0].firstChild.data);
	if (is_prev && is_last)
		jq_UpdateTaskDiv('prev_next_last', skip_question);
	else if (is_last)
		jq_UpdateTaskDiv('next_last', skip_question);
	else if (is_prev)
		jq_UpdateTaskDiv('prev_next', skip_question);
	else
		jq_UpdateTaskDiv('next', skip_question);
}

function jq_QuizContinueFinish() {
    var reStartString='';
    if(reStartOption && reStartOption!='com_joomlaquiz') {
        reStartString = '?';
        reStartString += 'option='+reStartOption+'&';
        if(reStartView) {
            reStartString += 'view='+reStartView+'&';
        }
        if(reStartID!=0) {
            reStartString += 'id='+reStartID;
        }
        reStartString = encodeURIComponent(reStartString);
    }
	jq_MakeRequest('&ajax_task=finish_stop&quiz=<?php echo $quiz->c_id?>'+'&stu_quiz_id='+stu_quiz_id + '&reStartString='+reStartString, 1);
}

function jq_QuizCallCode() {
	var call_code = jq_getObj('call_code').value;
	if(call_code == '') {
		ShowMessage('error_messagebox', 1, '<?php echo JText::_('COM_QUIZ_ENTER_A_CODE');?>');
		return;
	}
	jq_MakeRequest('&ajax_task=finish_stop&quiz=<?php echo $quiz->c_id?>'+'&stu_quiz_id='+stu_quiz_id+'&call_code='+call_code, 1);
}

function jq_QuizBack() {

	for (var n=0; n<quest_count; n++) {
		if (questions[n].attempts > 0) {
			questions[n].disabled = false;
			switch (questions[n].cur_quest_type) {
				<?php JoomlaquizHelper::getJavascriptIncludes('quizback');?>
			}

			removePopupText(questions[n].cur_quest_id);
		} else {
			if (jq_getObj('divv'+questions[n].cur_quest_id)) {
				removePopupText(questions[n].cur_quest_id);
				createPopupText(questions[n].cur_quest_id, '<?php echo addslashes(JText::_('COM_MES_NO_ATTEMPTS'))?>', 'incorrect_answer');
			}
		}
	}

	if (questions[0].is_prev && questions[0].is_last)
		jq_UpdateTaskDiv('prev_next_last');
	else if (questions[0].is_last)
		jq_UpdateTaskDiv('next_last');
	else if (questions[0].is_prev)
		jq_UpdateTaskDiv('prev_next');
	else
		jq_UpdateTaskDiv('next');
	return;
}
function URLencode(sStr) {
	return encodeURIComponent(sStr);
	return escape(sStr).replace(/\+/g, '%2B').replace(/\"/g,'%22').replace(/\'/g, '%27').replace(/\//g,'%2F');
}

function TRIM_str(sStr) {
	if (sStr) {
	  return (sStr.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
	} else {
		return '';
	}
}

function jq_QuizPrevQuestion(){
	<?php if ($quiz->c_enable_prevnext) {?>

	var answer = '';
	var no_answer = false;
	var url = '&ajax_task=prev&quiz=<?php echo $quiz->c_id?>'+'&stu_quiz_id='+stu_quiz_id;
	for(var n=0; n < quest_count; n++) {
		answer = '';
		if (!questions[n].disabled) {
			switch (questions[n].cur_quest_type) {
				<?php JoomlaquizHelper::getJavascriptIncludes('prev');?>
				case '9':
					answer = 0;
				break;
				default:
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					ShowMessage('error_messagebox', 1, '<?php echo addslashes(JText::_('COM_QUIZ_UNKNOWN_ERROR'))?>');
					setTimeout("jq_releaseBlock()", 1000);
				break;

			}
			if (!no_answer) {
				url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]='+answer;
			} else {
				url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]=~~~';
			}
		} else {
			url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]=~~~';
		}
	}

	jq_MakeRequest(url, 1);
	<?php } else echo 'null;'?>
}

function jq_Escape(txt) {
	var text = txt;

	//text = escape(txt);
	//if (text.indexOf( '%u', 0 ) >= 0)
	return encodeURIComponent(txt);

	return text;
}

function jq_QuizNext() { //send 'TASK = next'
	<?php if ($is_preview) { ?>
	var jq_task = 'next_preview';
	<?php } else { ?>
	var jq_task = 'next';
	<?php } ?>
	var answer = '';
	var url = '&ajax_task=' + jq_task + '&quiz=<?php echo $quiz->c_id?>'+'&stu_quiz_id='+stu_quiz_id;
	for(var n=0; n < quest_count; n++) {
		answer = '';
		if (!questions[n].disabled) {
			switch (questions[n].cur_quest_type) {
				<?php JoomlaquizHelper::getJavascriptIncludes('quiznext');?>
				case '9':
					answer = 0;
				break;
				default:
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
					ShowMessage('error_messagebox', 1, '<?php echo addslashes(JText::_('COM_QUIZ_UNKNOWN_ERROR'))?>');
					setTimeout("jq_releaseBlock()", 1000);
				break;

			}
			url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]='+answer;
		} else {
			url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]=';
		}
	}
		if(quest_timer_sec){
			url = url + '&timer=' + quest_timer_sec;
		}

	jq_MakeRequest(url, 1);
}

function setFlag(qid){

	var f_checked = (document.getElementById('c_flag').checked) ? 1 : 0;
	jq_jQuery.ajax({
		type: "POST",
		url: "index.php?option=com_joomlaquiz&task=ajaxaction.flag_question&tmpl=component",
		data: { quiz_id: <?php echo $quiz->c_id;?>, quest_id: qid, stu_quiz_id: stu_quiz_id, flag_quest: f_checked},
		success: function(data){

		}
	});

	return true;
}

function jq_QuizNextFinish() { //send 'TASK = next'
<?php if($quiz->c_enable_skip==2){ ?>
    <?php if ($is_preview) { ?>
	var jq_task = 'next_preview';
	<?php } else { ?>
	//new code
    var feedback = jq_jQuery(response).find('feedback');
    if(feedback){
        var q_feedback = jq_jQuery(feedback).find('quest_feedback').text();
        if(q_feedback == '1'){
            var jq_task = 'next';
        }else{
            var jq_task = 'nextFinish';
        }
    }

    //old code
//    var jq_task = 'nextFinish';
		var jq_task2 = ''
	<?php } ?>
	var answer = '';
	var url = '&ajax_task=' + jq_task + '&quiz=<?php echo $quiz->c_id?>'+'&stu_quiz_id='+stu_quiz_id;
	for(var n=0; n < quest_count; n++) {
		ShowMessage('error_messagebox_quest'+questions[n].cur_quest_id, 0, '');
	   // jq_QuizContinueFinish
	}

	/*
	for(var n=0; n < quest_count; n++) {
		if (questions[n].disabled) {
			continue;
		}
		switch (questions[n].cur_quest_type) {
			<?php JoomlaquizHelper::getJavascriptIncludes('nextfinish');?>
		}
		if (quiz_blocked) {
			try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
			<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
			var qid = jq_jQuery('.error_messagebox_quest').attr('id');
			ShowMessage(qid, 1, mes_please_wait);
			<?php } else { ?>
			ShowMessage('error_messagebox', 1, mes_please_wait);
			<?php } ?>
			setTimeout("jq_releaseBlock()", 1000);
			return;
		}
	}
	*/
	for(var n=0; n < quest_count; n++) {
		answer = '';
		if (!questions[n].disabled) {
			switch (questions[n].cur_quest_type) {
				<?php JoomlaquizHelper::getJavascriptIncludes('nextfinish');?>
				case '9':
					answer = 0;
				break;
			}
			url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]='+answer;
		} else {
			url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]=';
		}
	}
	jq_MakeRequest(url, 1);
	/*
	if (!quiz_blocked) {
		quiz_blocked = 1;
		timerID = setTimeout("jq_QuizNext()", 300);
	} else {
		try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
		<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
		var qid = jq_jQuery('.error_messagebox_quest').attr('id');
		ShowMessage(qid, 1, mes_please_wait);
		<?php } else { ?>
		ShowMessage('error_messagebox', 1, mes_please_wait);
		<?php } ?>
		setTimeout("jq_releaseBlock()", 1000);
	}
	*/
<?php }else{ ?>
     jq_QuizNext();
<?php } ?>
}

function jq_UpdateTaskDiv(task, skip_question) {

	skip_question = (skip_question && skip_question != 0) ? skip_question : null;
	var task_container = '';
	jq_jQuery('.jq_quiz_task_container').show(1);
	var skip_type=0;
	var last_quest_warning_message = '<?php echo JText::_('COM_LAST_MESSAGE') ?>';
	var is_last = false;
	switch (task) {
		case 'start':
			task_container = jq_StartButton('jq_StartQuizOn()', '<?php echo addslashes(JText::_('COM_QUIZ_START'))?>');
		break;

		case 'prev':
		case 'prev_next':
			<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
			jq_jQuery('.error_messagebox_quest').css('visibility', 'hidden');
			<?php if ($quiz->c_enable_prevnext) {?>task_container = jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
			task_container = task_container + jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
			<?php } else {?>
			task_container = <?php if ($quiz->c_enable_prevnext) {?>jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>') + <?php }?>
			jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>')+'';
			<?php } ?>
		break;

		case 'next_last':
			var is_prev = parseInt(response.getElementsByTagName('is_prev')[0].firstChild.data);
			<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>

			<?php if ($quiz->c_enable_skip==1) { ?>
			<?php if ($quiz->c_enable_prevnext) {?>if (is_prev) task_container = jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
			task_container = task_container + jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
			<?php } else { ?>
			<?php if ($quiz->c_enable_prevnext) {?>if (is_prev) task_container = jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
			task_container = task_container + jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
			<?php } ?>

			var qid = jq_jQuery('.error_messagebox_quest').attr('id');
			ShowMessage(qid, 1, last_quest_warning_message);           //Last question message

			<?php } else {?>
			ShowMessage('error_messagebox', 1, last_quest_warning_message);           //Last question message

			<?php if ($quiz->c_enable_skip==1) { ?>
			<?php if ($quiz->c_enable_prevnext) {?> if (is_prev) task_container += jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
			task_container += jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
			<?php } else { ?>
			<?php if ($quiz->c_enable_prevnext) {?> if (is_prev) task_container += jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
			task_container += jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
			<?php } ?>

			<?php } ?>
			is_last = true;
		break;

		case 'prev_next_last':
			<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>

			task_container = '';
			<?php if ($quiz->c_enable_skip==1) { ?>
			<?php if ($quiz->c_enable_prevnext) {?> task_container = jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
			task_container = task_container + jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
			<?php } else { ?>
			<?php if ($quiz->c_enable_prevnext) {?> task_container = jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
			task_container = task_container + jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?>');
			<?php } ?>

			jq_jQuery('.error_messagebox_quest').html("<?php echo addslashes(JText::_('COM_LAST_MESSAGE'))?>");
			jq_jQuery('.error_messagebox_quest').css('visibility', 'visible');
			jq_jQuery('.error_messagebox_quest').css('color', 'red');

			<?php } else { ?>

			<?php if ($quiz->c_enable_skip==1) { ?>
			task_container = jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>')+
			<?php if ($quiz->c_enable_prevnext) {?> jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>')+ <?php }?>'';

			<?php } else { ?>
			<?php if ($quiz->c_enable_prevnext) {?> task_container += jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>')+ <?php }?>'';
			task_container += jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?>');
			<?php } ?>

			<?php } ?>
			is_last = true;
		break;

		case 'prev_first':
		case 'next':
			task_container = jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
		break;

		case 'back_continue':
			task_container = jq_ContinueButton('jq_QuizContinue()', '<?php echo addslashes(JText::_('COM_QUIZ_CONTINUE'))?>')+jq_PrevButton('jq_QuizBack()', '<?php echo addslashes(JText::_('COM_QUIZ_BACK'))?>');
		break;

		case 'back_continue_finish':
			task_container = jq_SubmitButton('jq_QuizContinueFinish()', '<?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?>')+jq_PrevButton('jq_QuizBack()', '<?php echo addslashes(JText::_('COM_QUIZ_BACK'))?>');
			is_last = true;
		break;

		case 'continue':
			task_container = jq_ContinueButton('jq_QuizContinue()', '<?php echo addslashes(JText::_('COM_QUIZ_CONTINUE'))?>');
		break;

		case 'continue_finish':
			task_container = jq_SubmitButton('jq_QuizContinueFinish()', '<?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?>');
		break;

		case 'hide':
			jq_jQuery('.jq_quiz_task_container').hide(1);
			return;
		break;

		case 'show':
			jq_jQuery('.jq_quiz_task_container').show(1);
			return;
		break;

		case 'finish':
			jq_jQuery('#jq_panel_link_container').hide(1);
			jq_jQuery('.jq_question_info_container').hide(1);
		case 'clear':
			try {
			<?php if ($quiz->c_show_quest_pos) { ?>
			if(jq_getObj('jq_quest_num_container')){
				jq_getObj('jq_quest_num_container').style.visibility = "hidden";
			}
			<?php } ?>
			<?php if ($quiz->c_show_quest_points) { ?>
			if(jq_getObj('jq_points_container')){
				jq_getObj('jq_points_container').style.visibility = "hidden";
			}
			<?php } ?>
			} catch(e) {}
		break;

		case 'call_code':
			task_container = jq_SubmitButton('jq_QuizCallCode()', '<?php echo addslashes(JText::_('COM_QUIZ_CONTINUE'))?>');
		break;

		case 'review_next':
			task_container = jq_ContinueButton('jq_QuizReviewNext()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
		break;

		<?php if ($is_preview) { ?>
		case 'preview_back':
			task_container = jq_PrevButton('JQ_previewQuest()', '<?php echo addslashes(JText::_('COM_QUIZ_BACK'))?>');
		break;
		<?php } ?>
	}
	<?php if ($quiz->c_enable_skip==2) { ?>
		skip_type = 1;

		<?php } ?>
	skip_type_finish = 0;
	<?php if ($quiz->c_enable_skip==1) { ?>
		skip_type_finish = 1;
		<?php } ?>


	if (!quest_type) {
		quest_type = 0;
	}
	<?php if(!preg_match("/pretty_green/", $quiz->template_name) && !preg_match("/pretty_blue/", $quiz->template_name)){?>
        if (skip_question && !is_last && skip_type && quest_type == 9) {
            task_container = task_container + '<div onclick="javascript:jq_QuizNextFinish()" id="jq_finish_link_container"><div id="jq_quiz_task_link_container" class="jq_back_button"><' + 'a class="btn btn-primary" title="<?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?>" href="javascript: void(0)"><?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?></a></div></div>';
        } else if (skip_question && !is_last && skip_type_finish && quest_type==9) {
            task_container = task_container;
        } else if (skip_question && !is_last && skip_type && quest_type!=9) {
            task_container = '<div onclick="javascript:JQ_gotoQuestionOn(' + skip_question + ')" id="jq_continue_link_container"><div id="jq_quiz_task_link_container" class="jq_back_button"><' + 'a class="btn btn-primary" title="<?php echo addslashes(JText::_('COM_QUIZ_SKIP'))?>" href="javascript: void(0)"><?php echo addslashes(JText::_('COM_QUIZ_SKIP'))?></a></div></div>' + task_container + '<div onclick="javascript:jq_QuizNextFinish()" id="jq_finish_link_container"><div id="jq_quiz_task_link_container" class="jq_back_button"><' + 'a class="btn btn-primary" title="<?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?>" href="javascript: void(0)"><?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?></a></div></div>';
	    } else if(quiz_count_quests == 1 && task == 'next' && !is_last && skip_type  && quest_type!=9 ){
            task_container = task_container + '<div onclick="javascript:jq_QuizNextFinish()" id="jq_finish_link_container"><div id="jq_quiz_task_link_container" class="jq_back_button"><' + 'a class="btn btn-primary" title="<?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?>" href="javascript: void(0)"><?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?></a></div></div>';
	    } else if (skip_question && !is_last && !skip_type) {
            task_container = '<div onclick="javascript:JQ_gotoQuestionOn(' + skip_question + ')" id="jq_continue_link_container"><div id="jq_quiz_task_link_container" class="jq_back_button"><' + 'a class="btn btn-primary" title="<?php echo addslashes(JText::_('COM_QUIZ_SKIP'))?>" href="javascript: void(0)"><?php echo addslashes(JText::_('COM_QUIZ_SKIP'))?></a></div></div>' + task_container;
        }
	<?php } else {?>
        if(skip_question && !is_last && !skip_type){
            task_container = '<a onclick="javascript: JQ_gotoQuestionOn(' + skip_question + ')" id="jq_skip_link" title="<?php echo addslashes(JText::_('COM_QUIZ_SKIP'))?>"><?php echo addslashes(JText::_('COM_QUIZ_SKIP'))?></a>' + task_container;
        } else if (skip_question && !is_last && skip_type  && quest_type!=9){
            task_container = '<a onclick="javascript:jq_QuizNextFinish()" id="jq_finish_link_container" title="<?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?>"><?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?></a><a onclick="javascript: JQ_gotoQuestionOn(' + skip_question + ')" id="jq_skip_link" title="<?php echo addslashes(JText::_('COM_QUIZ_SKIP'))?>"><?php echo addslashes(JText::_('COM_QUIZ_SKIP'))?></a>' + task_container;
        } else if( quiz_count_quests == 1 && task == 'next' && !is_last && skip_type  && quest_type!=9 ){
            task_container = '<a onclick="javascript:jq_QuizNextFinish()" id="jq_finish_link_container" title="<?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?>"><?php echo addslashes(JText::_('COM_QUIZ_FINISH'))?></a>' + task_container;
        }
	<?php } ?>

	jq_jQuery('.jq_quiz_task_container').html(task_container);

	<?php if ($quiz->c_slide) { ?>
	if (result_is_shown == 1) { jq_ShowPanel(); }
	<?php } ?>
	if (task == 'finish') {
		var obj_plc = jq_getObj('jq_panel_link_container');
		if (obj_plc) obj_plc.style.visibility = 'hidden';
	}
    jq_updateHotspot();
}

function jq_NextButton(task, text) {
	<?php if(!preg_match("/pretty_green/", $quiz->template_name) && !preg_match("/pretty_blue/", $quiz->template_name)){?>
	return "<div id=\"jq_next_link_container\" onClick=\""+task+"\"><div class=\"jq_back_button\" id=\"jq_quiz_task_link_container\"><"+"a class=\"btn btn-primary\" href=\"javascript: void(0)\" title=\""+text+"\">"+text+"</a></div></div>";
	<?php } else {?>
	return "<a class=\"jq_next_link\" title=\""+text+"\" onclick=\""+task+"\" id=\"jq_next_link\" href=\"javascript: void(0)\">"+text+"<i class=\"jq_next_arrow\">&rsaquo;</i></a>";
	<?php }?>
}

function jq_SubmitButton(task, text) {
	<?php if(!preg_match("/pretty_green/", $quiz->template_name) && !preg_match("/pretty_blue/", $quiz->template_name)){?>
	return "<div id=\"jq_submit_link_container\" onClick=\""+task+"\"><div class=\"jq_back_button\" id=\"jq_quiz_task_link_container\"><"+"a class=\"btn btn-primary\" href=\"javascript: void(0)\" title=\""+text+"\">"+text+"</a></div></div>";
	<?php } else {?>
	return "<a class=\"jq_submit_link_container\" title=\""+text+"\" onclick=\""+task+"\" id=\"jq_submit_link_container\" href=\"javascript: void(0)\">"+text<?php if($quiz->template_name != 'joomlaquiz_pretty_blue'){?>+"<i class=\"jq_next_arrow\"></i>"<?php }?> +"</a>";
	<?php }?>
}

function jq_ContinueButton(task, text) {
	<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
	return "<a title='" + text + "' onclick='"+task+"' id='jq_skip_link' href='javascript: void(0)'>"+text+"</a>";
	<?php } else {?>
	return "<div id=\"jq_continue_link_container\" onClick=\""+task+"\"><div class=\"jq_back_button\" id=\"jq_quiz_task_link_container\"><"+"a class=\"btn btn-primary\" href=\"javascript: void(0)\" title=\""+text+"\">"+text+"</a></div></div>";
	<?php }?>
}

function jq_StartButton(task, text) {
	<?php if(!preg_match("/pretty_green/", $quiz->template_name)){?>
	return "<div id=\"jq_start_link_container\" onClick=\""+task+"\"><div class=\"jq_back_button\" id=\"jq_quiz_task_link_container\"><"+"a class=\"btn btn-primary\" href=\"javascript: void(0)\" title=\""+text+"\">"+text+"</a></div></div>";
	<?php } else {?>
	return "<a class=\"jq_start_link_container\" title=\""+text+"\" onclick=\""+task+"\" id=\"jq_start_link_container\" href=\"javascript: void(0)\">"+text+"<i class=\"jq_start_arrow\"></i></a>";
	<?php }?>
}

function jq_PrevButton(task, text) {
	<?php if(!preg_match("/pretty_green/", $quiz->template_name) && !preg_match("/pretty_blue/", $quiz->template_name)){?>
	return "<div id=\"jq_back_link_container\" onClick=\""+task+"\"><div class=\"jq_back_button\" id=\"jq_quiz_task_link_container\"><"+"a class=\"btn btn-primary\" href=\"javascript: void(0)\" title=\""+text+"\">"+text+"</a></div></div>";
	<?php } else {?>
	return "<a class=\"jq_back_link\" title=\""+text+"\" onclick=\""+task+"\" id=\"jq_back_link\" href=\"javascript: void(0)\"><i class=\"jq_back_arrow\">&lsaquo;</i>"+text+"</a>";
	<?php }?>
}

function check_Blank(id, value){
	if (questions[0].im_check) {
		var answer = '&bid='+id+'&text='+value;
		if(!value || !id) {
			return;
		}
		removePopupText(questions[0].cur_quest_id);

		jq_MakeRequest('&ajax_task=check_blank&quiz=<?php echo $quiz->c_id?>'+'&stu_quiz_id='+stu_quiz_id+'&='+answer, 1, 1);
	}
}

function jq_ShowPanel_go() {
	var jq_quiz_c_cont = jq_getObj('jq_quiz_container');
	if (jq_quiz_c_cont) { jq_quiz_c_cont.style.visibility = 'hidden'; jq_quiz_c_cont.style.display = 'none';}
	var jq_quiz_r_c = jq_getObj('jq_quiz_result_container');
	if (jq_quiz_r_c) { jq_quiz_r_c.style.visibility = 'visible'; jq_quiz_r_c.style.display = 'block';}
}

function jq_HidePanel_go() {
	var jq_quiz_r_c = jq_getObj('jq_quiz_result_container');
	if (jq_quiz_r_c) { jq_quiz_r_c.style.visibility = 'hidden'; jq_quiz_r_c.style.display = 'none';}
	var jq_quiz_c_cont = jq_getObj('jq_quiz_container');
	if (jq_quiz_c_cont) { jq_quiz_c_cont.style.visibility = 'visible'; jq_quiz_c_cont.style.display = 'block';}
}

function jq_ShowPanel() {
<?php if ($quiz->c_slide) { ?>
	try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {}
	if (result_is_shown == 1) { jq_HidePanel_go(); result_is_shown = 0;	}
	else { jq_ShowPanel_go();	result_is_shown = 1; }
<?php } ?>
}

<?php if ($is_preview) { ?>
function JQ_previewQuest() {
	jq_jQuery('#jq_quiz_container1').css('opacity', 0.7);
	jq_jQuery('#jq_quiz_container1').addClass('jq_ajax_loader');
	jq_MakeRequest('&ajax_task=preview_quest&quiz=<?php echo $quiz->c_id?>'+'&preview_id=<?php echo $preview_id?>&quest_id=<?php echo $preview_quest?>', 1);
}
<?php } ?>

function getParameter(paramName) {
	var searchString = window.location.search.substring(1),
	i, val, params = searchString.split("&");

	for (i=0;i<params.length;i++) {
		val = params[i].split("=");
		if (val[0] == paramName) {
			return val[1];
		}
	}
	return null;
}

//--><!]]>
</script>
<?php
	$paths = JoomlaquizHelper::getJavascriptFunctions();
	if(count($paths)){
		foreach($paths as $path){
			echo '<script type="text/javascript" src="'.$path.'"></script>';
		}
	}
?>
