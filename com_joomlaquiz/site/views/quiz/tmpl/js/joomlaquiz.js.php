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


function ScrollToElement(theElement){
	var selectedPosX = 0;
	var selectedPosY = 0;

	while(theElement != null){
		try{
			selectedPosX += theElement.offsetLeft;
			selectedPosY += theElement.offsetTop;
			selectedPosY -= margin_top;
			theElement = theElement.offsetParent;
		} catch(e){
				// TODO: add error handling
		}
	}
	try{
		window.scrollTo(selectedPosX,selectedPosY-10);
	} catch(e){
		// TODO: add error handling
	}

}

var explicitFinishCalled = false;

function preload(arrayOfImages) {
	jq_jQuery(arrayOfImages).each(function(){
		jq_jQuery('<img/>')[0].src = this;
	});
}

var debug = true;
var quiz = {};
var used_keys = [];
var keys_to_hardstop_debug = ['quest_count'];
function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}

function setReactiveLogging(object, keys, holder){
	holder = holder || object;
	keys.map(function(key){
		holder.key = null;
		Object.defineProperty(object, key, {
			// writable: false,
			// configurable: false,
			get: function() {
				if(debug){
					if(keys_to_hardstop_debug.indexOf(key)!==-1){
						debugger;
					}
					used_keys.push(key);
					used_keys = used_keys.filter( onlyUnique );
					console.trace('get '+key, holder[key]);
				}
				return holder[key];
			},
			set: function(value) {
				if(debug){
					if(keys_to_hardstop_debug.indexOf(key)!==-1){
						debugger;
					}
					used_keys.push(key);
					used_keys = used_keys.filter( onlyUnique );
					console.trace('set '+key+' to', value, 'upon', holder[key]);
				}
				holder[key] = value;
			}
		});
	});
}
setReactiveLogging(window, [
		'reStartOption','reStartView','reStartID','quiz_id','stu_quiz_id','error_call_code','kol_drag_elems','drag_array','coord_left','coord_top','ids_in_cont','cont_for_ids','answ_ids','cont_index','last_drag_id','last_drag_id_drag','last_drag_quest_n','kol_main_elems','main_ids_array','mes_complete_this_part','mes_failed','mes_please_wait','mes_time_is_up','mes_quest_number','mes_quest_points','user_email_to','user_unique_id','cur_quest_type','saved_prev_quest_exec_quiz_script','saved_prev_quest_exec_quiz_script_data','saved_prev_quest_data','saved_prev_res_data','saved_prev_quest_id','saved_prev_quest_type','saved_prev_quest_score','cur_quest_id','cur_quest_score','cur_quest_num','quiz_count_quests','cur_impscale_ex','quest_type','prev_correct','allow_attempt','timer_sec','stop_timer','result_is_shown','max_quiz_time','timer_style','quiz_blocked','url_prefix','limit_time','quest_timer_sec','quest_timer','quest_timer_ticktack','circle','path_elems','mes_question_is_misconfigured','margin_top','qs','live_url','questions',
		'show_timer','slide'
], quiz);

{
	/** manual processings and review needed for code **/
	Object.defineProperty(window, 'quest_count', {
		configurable: false,
		get: function() {
			if(debug){
				console.trace('get quest_count', questions.length);
			}
			return questions.length;
		},
		set: function(value) {
			console.error('Should not set this property');
			console.trace('questions count is attempted to be set');
		}
	});
	/** STOP **/
}

// used for caching last response
var response;
Object.defineProperty(window, 'response', {
	// writable: false,
	// configurable: false,
	value: null,
	get: function() {
		console.error('trying to get response cache');
		return null;
	},
	set: function(value) {
		// caching of the last response is disabled in sake of memory/performance
		if(debug){
			this.value = value;
		}else{
			console.warn('trying to cache response');
		}
	}
});

reStartOption = '<?php echo $reStartOption; ?>';
reStartView = '<?php echo $reStartView; ?>';
reStartID = parseInt('<?php echo $reStartID; ?>');

quiz_id = parseInt('<?php echo $quiz->c_id;?>');
show_timer = parseInt('<?= $quiz->c_show_timer ?>');
slide = parseInt('<?= $quiz->c_slide ?>');
stu_quiz_id = 0;
error_call_code = '';
kol_drag_elems = 0;
drag_array = new Array(kol_drag_elems);
coord_left = new Array(kol_drag_elems);
coord_top = new Array(kol_drag_elems);
ids_in_cont = new Array(kol_drag_elems);
cont_for_ids = new Array(kol_drag_elems);
answ_ids = new Array(kol_drag_elems);
cont_index = 0;
last_drag_id = '';
last_drag_id_drag = '';
last_drag_quest_n = -1;
kol_main_elems = 0;
main_ids_array = new Array(kol_main_elems); //for likert quest
// *** MESSAGES ***
mes_complete_this_part = '<?php echo addslashes( JText::_('COM_MES_COMPLETE_THIS_PART'))?>';
mes_failed = '<?php echo addslashes( JText::_('COM_QUIZ_FAILED'))?>';
mes_please_wait = '<?php echo addslashes( JText::_('COM_MES_PLEASE_WAIT'))?>';
mes_time_is_up = '<?php echo addslashes(JText::_('COM_QUIZ_MES_TIMEOUT'))?>';
mes_quest_number = '<?php echo (($quiz->c_show_quest_pos) ? addslashes(JText::_('COM_QUIZ_QUESTION_NUMBER')) : "");?>';
mes_quest_points = '<?php echo (($quiz->c_show_quest_points) ? addslashes(JText::_('COM_QUIZ_QUESTION_POINTS')) : "");?>';
// *** some script variables ***
user_email_to = '';
user_unique_id = '';
cur_quest_type = '';
saved_prev_quest_exec_quiz_script = '';
saved_prev_quest_exec_quiz_script_data = '';
saved_prev_quest_data = '';
saved_prev_res_data = '';
saved_prev_quest_id = 0;
saved_prev_quest_type = 0;
saved_prev_quest_score = 0;
cur_quest_id = 0;
cur_quest_score = 0;
cur_quest_num = 0;
quiz_count_quests = 0;
cur_impscale_ex = 0;
quest_type;
prev_correct = 0;
allow_attempt = 0;
timer_sec = 0;
stop_timer = 0;
result_is_shown = 0;
max_quiz_time = <?php echo ($quiz->c_time_limit)?($quiz->c_time_limit * 60):3600000; ?>;
timer_style = <?php echo ($quiz->c_timer_style);?>;
quiz_blocked = 0;
url_prefix = 'index.php?option=com_joomlaquiz<?php echo JoomlaquizHelper::JQ_GetItemId();?>&tmpl=component&task=ajaxaction.procces';
limit_time = 0;
quest_timer_sec = 0;
quest_timer = 0;
quest_timer_ticktack = 0;
circle = null;
path_elems = new Array();
mes_question_is_misconfigured = '<?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_IS_CONFIGURED');?>';
margin_top = '<?php echo $margin_top; ?>';
qs = getParameter('qs');

<?php
$live_url = JURI::root().JUri::root(true);
?>

live_url = '<?php echo $live_url;?>';

questions = new Array();

function question_data() {
	var object = {};
	/* setReactiveLogging(object, ['cur_quest_type','cur_quest_id',
'cur_quest_score',
'quest_data',
'quest_data_user',
'exec_quiz_script',
'quiz_script_data',
'im_check',
'kol_drag_elems',
'drag_array',
'coord_left',
'coord_top',
'ids_in_cont',
'cont_for_ids',
'answ_ids',
'cont_index',
'disabled',
'attempts',
'is_prev',
'is_last',
'c_separator']); */

	object.cur_quest_type = '';
	object.cur_quest_id = 0;
	object.cur_quest_score = 0;
	object.quest_data = '';
	object.quest_data_user = '';
	object.exec_quiz_script = 0;
	object.quiz_script_data = '';
	object.im_check = 0;
	object.kol_drag_elems = 0;
	object.drag_array = new Array(object.kol_drag_elems);
	object.coord_left = new Array(object.kol_drag_elems);
	object.coord_top = new Array(object.kol_drag_elems);
	object.ids_in_cont = new Array(object.kol_drag_elems); // what div id's in containers
	object.cont_for_ids = new Array(object.kol_drag_elems); //in that container object.id
	object.answ_ids = new Array(object.kol_drag_elems);
	object.cont_index = 0;
	object.disabled = false;
	object.attempts = 0;
	object.is_prev = 0;
	object.is_last = 0;
	object.c_separator = 0;
	object.disable = false;
	return object;
}

function jq_attachE(obj,event,handler) {
	if(obj.addEventListener) {
		obj.addEventListener(event, handler, false);
	} else if(obj.attachEvent) {
		obj.attachEvent('on'+event, handler, false);
	}
}

function getKeyDataFromXML(xml, key, fn){
	fn = fn || null;
	var data = xml.getElementsByTagName(key)[0].firstChild.data;
	if(typeof fn === "function"){
		return fn(data);
	}else{
		if(fn!==null){
			console.warn('Callback passed, but it is not a function');
			console.trace(fn);
		}
		return data;
	}
}
function parseQuestionDataFromXML(question){
	return {
		cur_quest_type: getKeyDataFromXML(question,'quest_type'),
		cur_quest_id: getKeyDataFromXML(question,'quest_id'),
		cur_quest_score: getKeyDataFromXML(question,'quest_score'),
		exec_quiz_script: getKeyDataFromXML(question,'exec_quiz_script'),
		quiz_script_data: getKeyDataFromXML(question,'quiz_script_data'),
		cur_quest_num: getKeyDataFromXML(question,'quiz_quest_num'),
		c_separator: getKeyDataFromXML(question,'quest_separator',parseInt),
		im_check: getKeyDataFromXML(question,'quest_im_check',parseInt),

		quest_data: jq_jQuery(question).find(question,'quest_data').text(),
		quest_data_user: jq_jQuery(question).find(question,'quest_data_user').text(),
		get div_id(){
			return 'quest_div' + this.cur_quest_id
		}
		get response(){
			console.error('Need to avoid storing and using full xml response because of memory consumption');
			return ""+question;
		}
	}
}

function parseAndAssignQuestionDataFromXML(question, n){
	questions[n] = Object.assign(questions[n], parseQuestionDataFromXML(question));
}

function jq_GetQuestionData(question, n) {
	parseAndAssignQuestionDataFromXML(question, n);
}

function emptyContainer(){
	populateContainer();
}

function populateContainer(content){
	content = content || '';
	jq_getObj('jq_quiz_container').innerHTML = content;
}

function getContainerContent(){
	return jq_getObj('jq_quiz_container').innerHTML;
}

function jq_CreateQuestions(xml) {
	var count = getKeyDataFromXML(xml,'quest_count');

	var question_template = '';
	var question_delimeter = '<?php echo JoomlaQuiz_template_class::JQ_getQuestionDelimeter()?>';

	<?php if((preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name) || preg_match("/t3_bs3/", $quiz->template_name)) && $quiz->c_show_timer){?>
	// TODO: move to CSS
	jq_jQuery('.jq_time_tick_container').css('display', 'inline-block');
	<?php } ?>

	for (var i = 0; i < count; i++){
		var question = question_data();

		question_template = '<?php echo JoomlaQuiz_template_class::JQ_QuizBody()?>';

		question = Object.assign(question, parseQuestionDataFromXML(xml.getElementsByTagName('question_data')[i]));

		question.is_prev = getKeyDataFromXML(xml,'is_prev',parseInt);
		question.is_last = getKeyDataFromXML(xml,'is_last',parseInt);

		var div_quest_text = document.createElement("div");
		var div_inside = document.createElement("div");

		div_inside.id = question.div_id;
		div_inside.className = 'jq_question_inner';

		<?php
// TODO: move to CSS
if(!preg_match("/pretty_green/", $quiz->template_name)){
    echo "div_inside.style.position = 'relative';";
}
?>

		<?php if($quiz->c_show_quest_pos || $quiz->c_show_quest_points){ ?>
		if (question.cur_quest_type != 9) {
			progressbar_text = '<?php echo JoomlaQuiz_template_class::JQ_getQuestionInfo()?>'
			.replace("<!-- Z -->", (question.cur_quest_num/quiz_count_quests)*100).replace("<!-- Z -->", (question.cur_quest_num/quiz_count_quests)*100)
			.replace("<!-- QUESTION_X_Y -->", mes_quest_number.replace("{X}", question.cur_quest_num).replace("{Y}", quiz_count_quests))
			.replace("<!-- POINTS -->", mes_quest_points.replace("{X}", question.cur_quest_score ));
			progressbar_text = getProgressBar(question.cur_quest_num, quiz_count_quests, progressbar_text);
		}
		<?php } ?>

		div_inside.innerHTML += '<span class="error_messagebox_quest" id="error_messagebox_quest'+question.cur_quest_id+'"><!-- x --></span>';
		div_inside.innerHTML += question.quest_data;
		div_quest_text.appendChild(div_inside);

		if (question.cur_quest_type == 7) {
			// TODO investigate and simplify to make sure consistent flow
			var div_inside_hs = document.createElement("div");
			div_inside_hs.id = 'quest_div'+question.cur_quest_id+'_hs';
			div_inside_hs.style.position = 'relative';
			div_inside_hs.innerHTML =  question.quest_data_user;

			div_quest_text.appendChild(div_inside_hs);
			question.quest_data_user = '<!-- x -->';
		}

		question_template = question_template.replace(/\{QUESTION_TEXT\}/, div_quest_text.innerHTML).replace(/\{ANSWERS\}/, question.quest_data_user);

		var addToContainer = '<div <?php echo ((!preg_match("/pretty_green/", $quiz->template_name)) ? 'style="position: relative;"' : '');?> id="qcontainer'+question.cur_quest_id+'">' + question_template + (question.c_separator? question_delimeter:'') + '</div>';
		populateContainer(getContainerContent() + addToContainer);

		var question = response.getElementsByTagName('question_data')[i];
		var question_task = getKeyDataFromXML(question,'quest_task');

		if(['disabled', 'no_attempts'].indexOf(question_task)!==-1){
			jq_Disable_Question(i);
			if (question_task == 'no_attempts') {
				createPopupText(question.cur_quest_id, getKeyDataFromXML(question,'quest_message_box'), 'incorrect_answer' );
			}
		}

		if (question.exec_quiz_script+'' == '1') {
			eval(question.quiz_script_data);
		}
		<?php JoomlaquizHelper::getJavascriptIncludes('includes');?>

		questions.push(question);
	}

	// TODO: move to CSS
	<?php if(preg_match("/pretty_green/", $quiz->template_name) && !$quiz->c_show_timer) {?>
	jq_jQuery("#jq_question_info_container").css("margin-top", "0");
	<?php }?>
}

function getProgressBar(x, y, text) {
	percent = Math.round((100 / y) * x);
	html = '<div class="progress progress-striped active">'+text+'<div class="bar" style="width: '+percent+'%;"></div> </div>';
	return html;
}

function createPopupText(question_id, text, className) {
	var div = document.createElement("div");
	div.id = 'divv'+question_id;
	div.className = className;
	div.innerHTML = text;
	jq_getObj('qcontainer'+question_id).appendChild(div);
}

function removePopupText(question_id) {
	var divv = jq_getObj('divv'+question_id);
	if(divv){
		try {
			divv.parentNode.removeChild(divv);
		} catch(e){
			// TODO: add error handling
		}
	}
}

function createMiniPopupText(blank_id, is_correct) {

	var quest_blank = jq_getObj('blk_id_'+blank_id);
	var blank_value = jq_getObj('hid_blk_id_'+blank_id).value;

	jq_jQuery(quest_blank).removeClass('blank_correct');
	jq_jQuery(quest_blank).removeClass('blank_incorrect');
	jq_jQuery(quest_blank).removeClass('jq_blank_droppable');
	jq_jQuery(quest_blank).removeClass('ui-droppable');

	jq_jQuery(quest_blank).addClass((is_correct? 'blank_correct': 'blank_incorrect'));
	jq_jQuery(quest_blank).droppable( "destroy" );
	jq_jQuery(quest_blank).html(blank_value+'&nbsp;');
	createDD();

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
			} catch (e) {
				// TODO: add error handling
			}
		}
	}
	if (!http_request) {
		return false;
	}
	http_request.onreadystatechange = jq_showpage;
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
		}
	});
}

jq_jQuery(window).on( 'orientationchange resize', function(e){
	if (typeof _recalculateSize == 'function') {
		setTimeout(_recalculateSize(), 10);
	}
});

function jq_clean_amp(str) {
	var ampChar = String.fromCharCode(38);
	return str.replace(/\&amp;/gi, ampChar);
}

function jq_showpage(){
	if (this.readyState == 4) {
		if ((this.status == 200)) {
			if(this.responseXML.documentElement == null){
				try {
					this.responseXML.loadXML(this.responseText);
				} catch (e) {
					console.warn('Not able to load response text properly');
				}
			}

			try {
				jq_getObj('jq_feed_questions').innerHTML = getKeyDataFromXML(this.responseXML.documentElement,'quiz_statistic');

				try{
				// TODO: move to CSS
				<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
				jq_jQuery(".jq_feedback_question_header").toggle(function () {
					var curentContent = jq_jQuery(this).next();
					curentContent.removeClass('visible').addClass('hidden').slideUp(1000);
				},function () {
					var curentContent = jq_jQuery(this).next();
					curentContent.addClass('visible').removeClass('hidden').slideDown(1000);
				});
				<?php } ?>
				}catch(e){
					console.error('UI is broken, may be because of function change from handling parameter to this');
				}
			} catch (e) {
				console.warn('Not able to insert quiz statistic');
			}
		}
	}
}

function createRequest(){
	var http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				// TODO: add error handling
			}
		}
	}
	return http_request;
}

function jq_MakeRequest(url, do_clear, silent) {
	var do_silent = parseInt(silent);


	var http_request = createRequest();
	if (!http_request) {
		return false;
	}

	if (!do_silent){
		if (do_clear == 1){
			jq_UpdateTaskDiv('hide');
			jq_getObj('jq_quiz_container').style.display = 'none';
		}
		jq_jQuery('#jq_quiz_container1').css('opacity', 0.7);
		jq_jQuery('#jq_quiz_container1').addClass('jq_ajax_loader');
	}

	try {
		ScrollToElement(jq_getObj('jq_quiz_container_title'));
	} catch(e) {
		// TODO: add error handling
	}

	// TODO: should be "="?
	quiz_blocked == 1;

	http_request.onreadystatechange = jq_AnalizeRequest;

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

function setErrorMessage(content){
	content = content || '';
	// presumably if content than second param is always 1; and else 0;
	ShowMessage('error_messagebox', !!content, content);
}

function hideMessageBox(){
	setErrorMessage('')
	jq_jQuery('#jq_quiz_container1').css('opacity', 1);
	jq_jQuery('#jq_quiz_container1').removeClass('jq_ajax_loader');
}

function blockQuiz(){
	quiz_blocked = 1;
}

function blockForRefresh(timeout){
	timeout	= timeout || 400;
	blockQuiz();
	setTimeout(function(){
		jq_releaseBlock();
		try{
			ScrollToElement(jq_getObj('jq_quiz_container_title'));
		} catch(e) {
			// TODO: add error handling
		}
	}, timeout);
}

function hideContainer(){
	emptyContainer();
	jq_getObj('jq_quiz_container').style.display = '';
}
function setContainerFormAnd(content){
	jq_getObj('jq_quiz_container').innerHTML = '<form name=\'quest_form\'></form>'+content;
}

function stopTimer(state){
	if(typeof state === "undefined"){
		state = 1;
	}
	stop_timer = state;
}

function jq_AnalizeRequest() {
	if (this.readyState != 4) {
		return ;
	}

	if (this.status != 200){
		blockForRefresh();
		jq_UpdateTaskDiv('show');
		setErrorMessage('<?php echo addslashes(JText::_('COM_QUIZ_FAILED_REQUEST'))?>');
	} else {
		if(this.responseXML.documentElement == null){
			try {
				this.responseXML.loadXML(this.responseText);
			} catch (e) {
				// TODO: add error handling
			}
		}

		hideMessageBox();
		stopTimer(0)

		response  = this.responseXML.documentElement;
		var task = getKeyDataFromXML(response,'task');

		if(['finish','results','review_finish'].indexOf(task)>-1){
			quest_type=0;
		}else{
			// TODO: change to getKeyDataFromXML(response,'quest_type');
			quest_type = jq_jQuery(response).find('quest_type').text();
		}

		function updateTotalQuestionsCountFromXml(xml){
			try {
				quiz_count_quests = getKeyDataFromXML(xml,'quiz_count_quests');
			} catch (e) {
				console.warn('Unable to update attempt data from recieved response');
			}
		}

		function doCreateQuestions(xml){
			blockForRefresh();
			hideContainer();

			updateTotalQuestionsCountFromXml(xml);

			jq_CreateQuestions(xml);
		}

		function initiateQuestionsBatch(){
			try {
				user_unique_id = getKeyDataFromXML(response,'user_unique_id');
				stu_quiz_id = getKeyDataFromXML(response,'stu_quiz_id');
			} catch (e) {
				console.warn('Unable to update attempt data from recieved response');
			}

			doCreateQuestions(response);

			var is_prev = getKeyDataFromXML(response,'is_prev',parseInt);
			var is_last = getKeyDataFromXML(response,'is_last',parseInt);
			// TODO: change to getKeyDataFromXML(response,'skip_question');
			var skip_question = jq_jQuery(response).find('skip_question').text();

			if (is_prev && is_last) {
				jq_UpdateTaskDiv('prev_next_last', skip_question);
			} else if (is_last) {
				jq_UpdateTaskDiv('next_last', skip_question);
			} else if (is_prev) {
				jq_UpdateTaskDiv('prev_next', skip_question);
			} else {
				jq_UpdateTaskDiv('next', skip_question);
			}

			if(quiz.show_timer){
				var past_time = 0;
				try {
					past_time = getKeyDataFromXML(response,'quiz_past_time',parseInt);
				} catch(e){
					console.error('Unable to find quiz_past_time in response');
				}
				jq_Start_TickTack(past_time);
			}

			if(quiz.slide){
				try{
					jq_getObj('jq_quiz_result_container').innerHTML = getKeyDataFromXML(response,'quiz_panel_data');
					jq_getObj('jq_panel_link_container').style.visibility = 'visible';
				}catch(e){
					console.error('Trying to set panel information when there is no panel');
				}
			}
			ensureQuestionsRemoved();
		}

		function ensureQuestionsRemoved(){
			/** ensure rendered questions are removed **/
			if(jq_jQuery('#jq_total_memory_point')){
				jq_jQuery('#jq_total_memory_point').remove();
				jq_jQuery('#jq_current_memory_point').remove();
				jq_jQuery('#jq_penalty_memory_point').remove();
			}
		}
		function processFeedback(task, is_preview){
			is_preview = is_preview || 0;
			// TODO: change to getKeyDataFromXML(response,'skip_question');
			var skip_question = jq_jQuery(response).find('skip_question').text();
			jq_processFeedback(task, response, is_preview, skip_question);
		}

		switch (task) {
			case 'start':
			case 'seek_quest':
				initiateQuestionsBatch();
			break;

			case 'review_start':
			case 'review_next':
				doCreateQuestions(response);

				jq_updateHotspot();
				stopTimer();

				jq_UpdateTaskDiv('review_next');

				// TODO: move to CSS
				<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
				jq_jQuery(".jq_time_tick_container").css("visibility", "hidden");
				<?php } ?>
			break;

			case 'review_finish':
				// TODO: move blockForRefresh above switch (as a separate commit, for history)
				blockForRefresh();
				hideContainer();

				ensureQuestionsRemoved();

				jq_updateHotspot();
				stopTimer();

				jq_UpdateTaskDiv('finish');

				setContainerFormAnd(saved_prev_res_data);
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

			case 'next':
				// TODO: move blockForRefresh above switch (as a separate commit, for history)
				blockForRefresh();
				hideContainer();

				var is_prev = getKeyDataFromXML(response, 'is_prev',parseInt);

				if (is_prev){
					processFeedback('prev_next');
				}else{
					processFeedback('next');
				}
			break;

			case 'prev':
				doCreateQuestions(response);

				var is_prev = getKeyDataFromXML(response, 'is_prev',parseInt);

				if (is_prev){
					processFeedback('prev');
				} else{
					processFeedback('prev_first');
				}
			break;
			case 'quest_preview':
				// TODO: move blockForRefresh above switch (as a separate commit, for history)
				blockForRefresh();
				hideContainer();

				doCreateQuestions(response);

				jq_UpdateTaskDiv('next');
			break;
			case 'preview_finish':
				// TODO: move blockForRefresh above switch (as a separate commit, for history)
				blockForRefresh();
				hideContainer();

				processFeedback('preview_finish', 1);
			break;
			case 'email_results':
				// TODO: move blockForRefresh above switch (as a separate commit, for history)
				blockForRefresh();
				setErrorMessage(getKeyDataFromXML(response,'email_msg'));
			break;
			case 'time_is_up':
				// TODO: move blockForRefresh above switch (as a separate commit, for history)
				blockForRefresh();
				hideContainer();

				setContainerFormAnd(getKeyDataFromXML(response,'quiz_message_box'));

				setErrorMessage(mes_time_is_up);

				stopTimer();
				jq_UpdateTaskDiv('continue_finish');
				jq_getObj('jq_time_tick_container').innerHTML = mes_time_is_up;
			break;

			case 'finish':
				// TODO: move blockForRefresh above switch (as a separate commit, for history)
				blockForRefresh();
				hideContainer();

				processFeedback('finish');

				// TODO: move to CSS
				<?php if(preg_match("/pretty_green/", $quiz->template_name) && $quiz->c_show_timer){?>
				jq_jQuery(".jq_time_tick_container").css("visibility", "hidden");
				<?php } ?>
			break;
			case 'results':
				// TODO: move blockForRefresh above switch (as a separate commit, for history)
				blockForRefresh();
				hideContainer();

				<?php if($quiz->c_flag):?>
				jq_jQuery('#c_flag').unbind('click');
				document.getElementById('c_flag').checked = false;
				jq_jQuery('.jq_flagged_question').hide();
				<?php endif;?>

				jq_UpdateTaskDiv('finish');

				if(JQ_process_redirect(response)){
					return;
				}

				// TODO: consider change to getKeyDataFromXML(response,'quiz_results');
				saved_prev_res_data = jq_jQuery(response).find('quiz_results').text();

				setContainerFormAnd(saved_prev_res_data);


				jq_updateHotspot();
				stopTimer();

				// TODO: move to CSS
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
				blank_id = getKeyDataFromXML(response,'quest_blank_id');
				is_correct = getKeyDataFromXML(response,'is_correct',parseInt);
				createMiniPopupText(blank_id, is_correct);
			break;

			case 'failed':
				// TODO: move blockForRefresh above switch (as a separate commit, for history)
				blockForRefresh();
				setErrorMessage(mes_failed);
			break;
			default:
			break;
		}
	}
}

function JQ_process_redirect(xml) {
	try {
		if(getKeyDataFromXML(xml,'quiz_redirect')){
			var redirect_url = getKeyDataFromXML(xml,'quiz_redirect_url');
			if(redirect_url){
				getKeyDataFromXML(xml,'quiz_redirect_delay',function(delay){
					setTimeout(function(){
						JQ_do_redirect(redirect_url);
					},(parseInt(delay) || 0)*1000);
				});
				return true;
			}
		}
	} catch(e) {
		console.error('There is an issue in the redirect flow');
		console.trace(e);
	}
	return false;
}

function JQ_do_redirect(url) {
	if (!url){
		return false;
	}else{
		url = url+'';
	}

	if (url.indexOf('javascript:') === -1) {
		window.location.href = url;
	} else {
		eval(url.replace("javascript:", ""));
	}
	return true;
}

function jq_processFeedback(task, xml, is_preview, skip_question){

	var feed_task = '';
	var is_allow_attempt = 0;
	var is_do_feedback = 0;
	var show_flag = 0;

	var feedbacks = (xml.getElementsByTagName('feedback') || []);

	if(feedbacks.length){
		questions.map(function(question){
			removePopupText(question.cur_quest_id);
		});
	}

	feedbacks.map(function(feedback){
		var do_feedback = getKeyDataFromXML(feedback,'quest_feedback',parseInt);
		var feedback_quest_id = getKeyDataFromXML(feedback,'feedback_quest_id');
		var prev_correct = getKeyDataFromXML(feedback,'quiz_prev_correct');

		<?php if($quiz->c_flag):?>
		show_flag = parseInt(jq_jQuery(xml).find('feedback_show_flag').text());
		if(show_flag){
			jq_jQuery('.jq_flagged_question').show();
			jq_jQuery('#c_flag').bind('click', function(){
				setFlag(feedback_quest_id);
			});
		}
		<?php endif;?>

		if (do_feedback){
			if (feedback_quest_id) {
				is_do_feedback++;

				if(question = questions.find(function(question){
					return question.cur_quest_id == feedback_quest_id;
				})){
					var allow_attempt = 0;
					try {
						allow_attempt = getKeyDataFromXML(feedback,'quiz_allow_attempt');
					} catch(e) {
						console.warn('Was expecting for quiz_allow_attempt and did not get one');
					}
					question.attempts = allow_attempt;
					disableQuestion(question);
				}

				var feedback_quest_type = feedback.getElementsByTagName('feedback_quest_type')[0].firstChild.data;
				if (!jq_getObj('div_qoption'+feedback_quest_id)) {
					null;
				}else {
					var blank_fbd_count = 0;
					try {
						blank_fbd_count = feedback.getElementsByTagName('blank_fbd_count')[0].firstChild.data;
					} catch(e){
						// TODO: add error handling
					}
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
					if(jq_getObj('quest_result_'+feedback_quest_id)){
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

							allow_attempt = xml.getElementsByTagName('quiz_allow_attempt')[0].firstChild.data;
							if (allow_attempt == 1) {
								is_allow_attempt++;
							}
						}
					}
				}
			}
		} else {
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
	});

	if (task == 'finish')
		feed_task = 'continue_finish';
	else if (['start','next','prev_next'].indexOf(task)!==-1){
		feed_task = 'continue';
	}

	if (is_allow_attempt) {
		feed_task = 'back_'+feed_task;
	}

	if (!is_do_feedback) {
		if (task == 'finish')
			jq_QuizContinueFinish();
		else if (['start','next','prev_next','prev','prev_first'].indexOf(task)!==-1) {
			jq_QuizContinue();
		}
		return;
	}else{
		jq_UpdateTaskDiv(feed_task || task, skip_question || 0);
	}
}

function jq_releaseBlock() {
	quiz_blocked = 0;
}

function jq_Start_Question_TickTack(limit_time)
{
		if(quest_timer_sec <= 0 ){
			ShowMessage('error_messagebox', 1, 'Time for answering this question has run out');
			quest_count = response.getElementsByTagName('quest_count')[0].firstChild.data;
			for(var n=0; n < quest_count; n++) {
				questions[n].disabled = true;
			}
			alert('Time for answering this question has run out');
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
			jq_jQuery('.jq_quest_time_past').html('<strong>Time left to answer this question:</strong>&nbsp;' + time_str + ':' + quest_time_str2);
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
		jq_getObj('jq_time_tick_container').innerHTML = '00:01'+ ' <?php echo JText::_('COM_QUIZ_TIME_OF');?> ' + max_quiz_time/60 + ':00';
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

function disableQuestion(question){
	question.disabled = true;
	if((var n = questions.indexOf(question))!==-1){
		switch (question.cur_quest_type) {
			<?php JoomlaquizHelper::getJavascriptIncludes('disable');?>
		}
	}
	return;
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
			try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {
				// TODO: add error handling
			}
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
		try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {
				// TODO: add error handling
		}
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
	if(explicitFinishCalled){
		jq_QuizContinueFinish();
	}

	<?php if($quiz->c_flag):?>
	jq_jQuery('#c_flag').unbind('click');
	document.getElementById('c_flag').checked = false;
	jq_jQuery('.jq_flagged_question').hide();
	<?php endif;?>

	try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {
		// TODO: add error handling
	}
	emptyContainer();

	jq_CreateQuestions(xml);

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
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {
					// TODO: add error handling
					}
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
					try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {
					// TODO: add error handling
					}
					ShowMessage('error_messagebox', 1, '<?php echo addslashes(JText::_('COM_QUIZ_UNKNOWN_ERROR'))?>');
					setTimeout("jq_releaseBlock()", 1000);
				break;

			}
			url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]='+answer;
		} else {
			url = url + '&quest_id[]='+questions[n].cur_quest_id+'&answer[]=';
		}
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
	explicitFinishCalled = true;
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
	var quiz_next_text_const= '<?php echo addslashes(JText::_('COM_QUIZ_NEXT')); ?>';

	if(quest_type == 9) {
		quiz_next_text_const = '<?php echo addslashes(JText::_('COM_QUIZ_CONTINUE')); ?>';
	}
	var is_last = false;
	switch (task) {
		case 'start':
			task_container = jq_StartButton('jq_StartQuizOn()', '<?php echo addslashes(JText::_('COM_QUIZ_START'))?>');
		break;

		case 'prev_first':
		case 'next':
			task_container = jq_NextButton('jq_QuizNextOn()', quiz_next_text_const);
		break;

		case 'prev':
		case 'prev_next':
			<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name)){?>
			jq_jQuery('.error_messagebox_quest').css('visibility', 'hidden');
			<?php if ($quiz->c_enable_prevnext) {?>task_container = jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
			task_container = task_container + jq_NextButton('jq_QuizNextOn()', quiz_next_text_const);
			<?php } else {?>
			task_container = <?php if ($quiz->c_enable_prevnext) {?>jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>') + <?php }?>
			jq_NextButton('jq_QuizNextOn()', quiz_next_text_const)+'';
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
			task_container = jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
			<?php if ($quiz->c_enable_prevnext) {?> if (is_prev) task_container += jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
			<?php } else { ?>
			task_container = jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
			<?php if ($quiz->c_enable_prevnext) {?> if (is_prev) task_container += jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>');<?php }?>
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
			task_container = task_container + jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>');
			<?php } ?>

			jq_jQuery('.error_messagebox_quest').html("<?php echo addslashes(JText::_('COM_LAST_MESSAGE'))?>");
			jq_jQuery('.error_messagebox_quest').css('visibility', 'visible');
			jq_jQuery('.error_messagebox_quest').css('color', 'red');

			<?php } else { ?>

			<?php if ($quiz->c_enable_skip==1) { ?>
			task_container = jq_NextButton('jq_QuizNextOn()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>')+
			<?php if ($quiz->c_enable_prevnext) {?> jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>')+ <?php }?>'';

			<?php } else { ?>
			task_container = jq_NextButton('jq_QuizNextFinish()', '<?php echo addslashes(JText::_('COM_QUIZ_NEXT'))?>')+
			<?php if ($quiz->c_enable_prevnext) {?> jq_PrevButton('jq_QuizPrevQuestion()','<?php echo addslashes(JText::_('COM_QUIZ_PREV'))?>')+ <?php }?>'';
			<?php } ?>

			<?php } ?>
			is_last = true;
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
			} catch(e) {
				// TODO: add error handling
			}
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
	try{ ScrollToElement(jq_getObj('jq_quiz_container_title'));} catch(e) {
				// TODO: add error handling
	}
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

(function(quiz, $){

})(quiz, jq_jQuery);

//--><!]]>
</script>
<?php
$paths = JoomlaquizHelper::getJavascriptFunctions();
if(!empty($paths)){
    foreach($paths as $path){
        echo '<script type="text/javascript" src="'.$path.'"></script>';
    }
}
