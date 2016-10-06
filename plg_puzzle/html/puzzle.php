<!DOCTYPE html>
<html>
<head>
    <title></title>
	<!--[if IE]><script type="text/javascript" src="<?php echo JURI::root();?>plugins/joomlaquiz/puzzle/html/assets/js/excanvas.js"></script><![endif]-->
	<style>
		body {
			width: 90%;
			margin: 0 auto;
		}
		.snappuzzle-wrap { position: relative; display: block; }
		.snappuzzle-pile { position: relative; }
		.snappuzzle-piece { cursor: move; }
		.snappuzzle-slot { position: absolute; background: #fff; border: 1px solid black; opacity: 0.4; transition: 0.2s; }
		.snappuzzle-slot-hover { background: #eee; }

		.puzzle_image_container {
			width: 100%;
			position: relative;
		}
		.puzzle_image_container #puzzle_area, .puzzle_image_container #puzzle_parts {
			width: 50%;
		}
		#puzzle_area img {
			width: 100%;
		}
		.correct {
			opacity: 1 !important;
		}
	</style>
	<script type="text/javascript" src="<?php echo JURI::root();?>components/com_joomlaquiz/assets/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="<?php echo JURI::root();?>components/com_joomlaquiz/assets/js/jquery-ui-1.9.2.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo JURI::root();?>components/com_joomlaquiz/assets/js/jquery.ui.touch-punch.min.js"></script>

<!--	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>-->
	<!--<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>-->




	<script type="text/javascript" src="<?php echo JURI::root();?>plugins/joomlaquiz/puzzle/html/assets/js/jquery.snap-puzzle.min.js"></script>
    <script>
        var PUZZLE_DIFFICULTY;
        var PUZZLE_HOVER_TINT = '#009900';
		var PUZZLE_HOVER_TINT_OUT = '#990000';

        var _stage;
        var _canvas;

        var _img;
        var _pieces;
        var _puzzleWidth;
        var _puzzleHeight;
        var _pieceWidth;
        var _pieceHeight;
        var _currentPiece;
        var _currentDropPiece;
		var quest_timer_sec;
		var quest_timer;
        var _mouse;
		var point;
		var scores = 0;
		var quest_id;
		var stu_quiz_id;
		var c_image;
		var c_quiz_id;
		var start = false;

		function addEvent(elem, type, handler){
			if (elem.addEventListener){
				elem.addEventListener(type, handler, false)
			} else {
				elem.attachEvent("on"+type, handler)
			}
		}

		function start_puzzle(x){
			jq_jQuery('#source_image').snapPuzzle({
				rows: x,
				columns: x,
				pile: '#puzzle_parts',
				onComplete: function(){
					setTimeout(gameOver,500);
				},
				onCorrect: function (elem) {
					sendAjaxSuccess(elem.data('number'));
				},
				onDraggable: function () {
					imageRemove();
					action_start();
				}
			});
		}
		
        function init(){
			
			if(jq_jQuery.browser.msie && parseInt(jq_jQuery.browser.version) <= 7){
				jq_jQuery("body").html("<div id='jq_message_box'></div>");
				jq_jQuery("#jq_message_box").css('color', 'red').html("<?php echo JText::_('COM_QUIZ_UPDATE_YOUR_BROWSER');?>");
				jq_jQuery("#jq_message_box").fadeIn();
				return true;
			}
			
			
			if(parent == null){
				jq_jQuery("body").html("<div id='jq_message_box'></div>");
				jq_jQuery("#jq_message_box").css('color', 'red').html("Can not open the puzzle!");
				jq_jQuery("#jq_message_box").fadeIn();
				return true;
			}
			
			quest_id = jq_jQuery(parent.response).find('quest_id').text();
			task = jq_jQuery(parent.response).find('task').text();
			stu_quiz_id = parent.stu_quiz_id;
			
			if((!quest_id || !stu_quiz_id) && task != 'quest_preview'){
				jq_jQuery("body").html("<div id='jq_message_box'></div>");
				jq_jQuery("#jq_message_box").css('color', 'red').html("Can not open the puzzle!");
				jq_jQuery("#jq_message_box").fadeIn();
				return true;
			}
						
			jq_jQuery.ajax({
					type: "POST",
					url: "<?php echo JURI::root(); ?>index.php?option=com_joomlaquiz&task=ajaxaction.procces",
					data: "ajax_task=ajax_plugin&plg_task=getdata&quest_type=puzzle" + "&qid=" + quest_id + "&stu_quiz_id=" + stu_quiz_id,
					success: function(xml){
						//console.log(xml);
						var c_attempts = jq_jQuery(xml).find('c_attempts').text();
						if(!parseInt(c_attempts)){
							jq_jQuery("body").html("<div id='jq_message_box'></div>");
							jq_jQuery("#jq_message_box").css('color', 'red').html("No attempts");
							jq_jQuery("#jq_message_box").fadeIn();
							return true;
						}
						PUZZLE_DIFFICULTY = getPuzzleDifficulty(xml);
						quest_timer_sec = parseInt(jq_jQuery(xml).find('quest_time').text());
						point = jq_jQuery(xml).find('c_point').text();
						c_image = jq_jQuery(xml).find('c_image').text();
						c_quiz_id = jq_jQuery(xml).find('c_quiz_id').text();
						c_quest_text = jq_jQuery(xml).find('c_quest_text').text();
						jq_jQuery("#jq_quest_conteiner").html(c_quest_text);
						
						 _img = new Image();
						//addEvent(_img, "load", onImage);
						_img.src = "<?php echo JURI::root();?>images/joomlaquiz/images/" + c_image;
						_img.id = "source_image";
						_img.classList.add("pure-img");
						jq_jQuery('#puzzle_area').append(_img);

						start_puzzle(PUZZLE_DIFFICULTY);
					}
			});
		}
		
		function imageRemove () {
			jq_jQuery('#source_image').attr('src', '');
		}

		function sendAjaxSuccess(num) {
			num = (parseInt(num, 36)) / 100;
			jq_jQuery.ajax({
				type: "POST",
				url: "<?php echo JURI::root(); ?>index.php?option=com_joomlaquiz&task=ajaxaction.procces",
				data: "ajax_task=ajax_plugin&plg_task=addpoints&quest_type=puzzle" + "&quest_id=" + quest_id + "&stu_quiz_id=" + stu_quiz_id + "&quiz_id=" + c_quiz_id + "&ltime=" + quest_timer_sec + "&piece=" + num,
				success: function(){
					scores += parseInt(point);
					jq_jQuery("#jq_points").html("<?php echo JText::_('COM_JQ_YOUR_MEMORY_POINTS');?>: " + scores);
					if(parent.jq_getObj('result_point_'+quest_id)) parent.jq_getObj('result_point_'+quest_id).innerHTML = scores;
				}
			});
		}
        
		function gameOver(){
            document.onmousedown = null;
            document.onmousemove = null;
            document.onmouseup = null;
            clearInterval(quest_timer);
			jq_jQuery('#jq_message_box').html("<?php echo JText::_('COM_QUIZ_WELL_DONE');?>");
			jq_jQuery('#jq_message_box').fadeIn();
			setTimeout("jq_Close_PuzzleWindow()", 1500);
        }

		function action_start() {
			if (!start) {
				start = true;
				if (quest_timer_sec && parseInt(quest_timer_sec) > 1) setInterval("timerStep()", 1000);
				jq_jQuery.ajax({
					type: "POST",
					url: "<?php echo JURI::root(); ?>index.php?option=com_joomlaquiz&task=ajaxaction.procces",
					data: "ajax_task=ajax_plugin&plg_task=addpoints&quest_type=puzzle" + "&quest_id=" + quest_id + "&stu_quiz_id=" + stu_quiz_id + "&quiz_id=" + c_quiz_id + "&action=start"
				});
			}
		}

		function timerStep()
		{
			quest_timer_sec = parseInt(quest_timer_sec);
			if(quest_timer_sec <= 0 ){
				clearInterval(quest_timer);
				jq_jQuery('#jq_message_box').fadeIn();
				setTimeout("jq_Close_PuzzleWindow()", 1500);
				return;
			}

			if (quest_timer_sec > 0) {
				var quest_timer_sec_tmp = quest_timer_sec;
				var quest_timer_min = parseInt(quest_timer_sec_tmp/60);
				var plus_sec = quest_timer_sec_tmp - (quest_timer_min*60);
				if (quest_timer_min < 0) { quest_timer_min = quest_timer_min*(-1); }
				if (plus_sec < 0) { plus_sec = plus_sec*(-1); }
				var time_str = quest_timer_min + '';
				if (time_str.length == 1) time_str = '0'+time_str;
				quest_time_str2 = plus_sec + '';
				if (quest_time_str2.length == 1) quest_time_str2 = '0'+quest_time_str2;
				jq_jQuery('#jq_quest_time_past').html('<strong><?php echo JText::_('COM_QUIZ_TIME_LEFT');?></strong>&nbsp;' + time_str + ':' + quest_time_str2);
				quest_timer_sec--;
			}
		}
		
		function jq_Close_PuzzleWindow()
		{
			parent.jq_QuizNextOn();
			parent.SqueezeBox.close();
		}

		function getPuzzleDifficulty(xml) {
			PUZZLE_DIFFICULTY = parseInt(jq_jQuery(xml).find('puzzle_difficulty').text());
			if (PUZZLE_DIFFICULTY) return PUZZLE_DIFFICULTY;
			else return 4;
		}


    </script>
</head>

<!--   <body onload="init();">  -->
<body onload="init();">
	<div id="jq_quest_conteiner"><!--x--></div>
	<div style="clear:both;"><!--x--></div>
	<div id="jq_quest_time_past" style="font-size:17px;display:block;float:left;"></div>
	<div id="jq_message_box" style="font-size:17px;display:none;float:left;color:red;margin-left:40px;"><?php echo JText::_('COM_QUIZ_TIME_FOR_ANSWERING_HAS_RUN_OUT');?></div>
	<div id="jq_close_button" style="display:block;float:right;" onclick="clearInterval(quest_timer); jq_Close_PuzzleWindow();"><input type="button" value="<?php echo JText::_('COM_JQ_PUZZLE_CLOSE');?>" style="width:150px;height:35px;background:#CCDDEE;border:1px solid #CCCCCC;" /></div>
	<div id="jq_points" style="display:block;font-size:18px;float:right;margin-right:25px;"><?php echo JText::_('COM_JQ_YOUR_MEMORY_POINTS');?>: 0</div>

	<table class="puzzle_image_container">
		<tr>
			<td id="puzzle_area">

			</td>
			<td id="puzzle_parts">

			</td>
		</tr>
	</table>

</body>

</html>