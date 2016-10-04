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




	<script type="text/javascript" src="<?php echo JURI::root();?>plugins/joomlaquiz/puzzle/html/assets/js/jquery.snap-puzzle.js"></script>
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

		function addEvent(elem, type, handler){
			if (elem.addEventListener){
				elem.addEventListener(type, handler, false)
			} else {
				elem.attachEvent("on"+type, handler)
			}
		}

		function start_puzzle(x){
			jq_jQuery('#puzzle_solved').hide();
			jq_jQuery('#source_image').snapPuzzle({
				rows: x, columns: x,
				pile: '#pile',
				containment: '#puzzle-containment',
				onComplete: function(){
					jq_jQuery('#source_image').fadeOut(150).fadeIn();
					jq_jQuery('#puzzle_solved').show();
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
						PUZZLE_DIFFICULTY = jq_jQuery(xml).find('puzzle_difficulty').text();
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

						jq_jQuery('#source_image').snapPuzzle({
							rows: 3,
							columns: 3,
							pile: '#puzzle_parts',
							onComplete: function(){
								setTimeout(gameOver,500);
							},
							onCorrect: function (elem) {
								setScore();
								sendAjaxSuccess(elem.data('number'));
							},
							onDraggable: function () {
								imageRemove();
							}
						});
					}
			});
		}

		function setScore() {
			scores += parseInt(point);
			jq_jQuery("#jq_points").html("<?php echo JText::_('COM_JQ_YOUR_MEMORY_POINTS');?>: " + scores);
			if(parent.jq_getObj('result_point_'+quest_id)) parent.jq_getObj('result_point_'+quest_id).innerHTML = scores;
		}
		
		function imageRemove () {
			jq_jQuery('#source_image').attr('src', '');
		}

		function sendAjaxSuccess(num) {
			num = (parseInt(num, 36)) / 100;
			console.log(num);
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

        function onImage(e){
            _pieceWidth = Math.floor(_img.width / PUZZLE_DIFFICULTY);
            _pieceHeight = Math.floor(_img.height / PUZZLE_DIFFICULTY);
            _puzzleWidth = _pieceWidth * PUZZLE_DIFFICULTY;
            _puzzleHeight = _pieceHeight * PUZZLE_DIFFICULTY;
            setCanvas();
            initPuzzle();
        }
        function setCanvas(){
            _canvas = document.getElementById('canvas');
            _stage = _canvas.getContext('2d');
            _canvas.width = _puzzleWidth * 2;
            _canvas.height = _puzzleHeight;
            _canvas.style.border = "1px solid #cccccc";
        }
        function initPuzzle(){
            _pieces = [];
            _mouse = {x:0,y:0};
            _currentPiece = null;
            _currentDropPiece = null;
            _stage.drawImage(_img, 0, 0, _puzzleWidth, _puzzleHeight, 0, 0, _puzzleWidth, _puzzleHeight);
            createTitle("<?php echo JText::_('COM_QUIZ_CLICK_TO_START_PUZZLE');?>");
            buildPieces();
        }
        function createTitle(msg){
            _stage.fillStyle = "#000000";
            _stage.globalAlpha = .4;
            _stage.fillRect(100,_puzzleHeight - 40,_puzzleWidth - 200,40);
            _stage.fillStyle = "#FFFFFF";
            _stage.globalAlpha = 1;
            _stage.textAlign = "center";
            _stage.textBaseline = "middle";
            _stage.font = "20px Arial";
            if(!jq_jQuery.browser.msie){
				_stage.fillText(msg,_puzzleWidth / 2,_puzzleHeight - 20);
			}
        }
        function buildPieces(){
            var i;
            var piece;
            var xPos = 0;
            var yPos = 0;
            for(i = 0;i < PUZZLE_DIFFICULTY * PUZZLE_DIFFICULTY;i++){
                piece = {};
                piece.sx = xPos;
                piece.sy = yPos;
				piece.cx = xPos + _puzzleWidth;
				piece.cy = yPos;
				piece.fixed = 0;
				piece.position = i + 1;
                _pieces.push(piece);
                xPos += _pieceWidth;
                if(xPos >= _puzzleWidth){
                    xPos = 0;
                    yPos += _pieceHeight;
                }
            }
            document.onmousedown = shufflePuzzle;
        }
        function shufflePuzzle(){
			quest_timer = setInterval("jq_Start_Question_TickTack()", 1000);
			jq_jQuery.ajax({
					type: "POST",
					url: "<?php echo JURI::root(); ?>index.php?option=com_joomlaquiz&task=ajaxaction.procces",
					data: "ajax_task=ajax_plugin&plg_task=addpoints&quest_type=puzzle" + "&quest_id=" + quest_id + "&stu_quiz_id=" + stu_quiz_id + "&quiz_id=" + c_quiz_id + "&action=start"
			});
			
            _pieces = shuffleArray(_pieces);
            _stage.clearRect(0,0,_puzzleWidth,_puzzleHeight);
            var i;
            var piece;
            var xPos = 0;
            var yPos = 0;
            for(i = 0;i < _pieces.length;i++){
                piece = _pieces[i];
                piece.xPos = xPos;
                piece.yPos = yPos;
                _stage.drawImage(_img, piece.sx, piece.sy, _pieceWidth, _pieceHeight, xPos, yPos, _pieceWidth, _pieceHeight);
                xPos += _pieceWidth;
                if(xPos >= _puzzleWidth){
                    xPos = 0;
                    yPos += _pieceHeight;
                }
            }
			
			xPos = parseInt(_puzzleWidth);
			yPos = 0;
			for(i = 0;i < PUZZLE_DIFFICULTY * PUZZLE_DIFFICULTY;i++){
                piece = {};
                piece.sx = -1;
                piece.sy = -1;
				piece.xPos = xPos;
				piece.yPos = yPos;
				piece.border = 1;
                _pieces.push(piece);
				_stage.strokeRect(xPos, yPos, _pieceWidth,_pieceHeight);
                xPos += _pieceWidth;
                if(xPos >= _puzzleWidth * 2){
                    xPos = _puzzleWidth;
                    yPos += _pieceHeight;
                }
            }
						
            document.onmousedown = onPuzzleClick;
        }
        function onPuzzleClick(e){
		
            var mouse = mouseGetCoords(e);
			_mouse.x = mouse.mx - _canvas.offsetLeft;
			_mouse.y = mouse.my - _canvas.offsetTop;
			
            _currentPiece = checkPieceClicked();
            if(_currentPiece != null && !_currentPiece.fixed){
                _stage.clearRect(_currentPiece.xPos,_currentPiece.yPos,_pieceWidth,_pieceHeight);
				
                _stage.save();
                _stage.globalAlpha = .9;
                _stage.drawImage(_img, _currentPiece.sx, _currentPiece.sy, _pieceWidth, _pieceHeight, _mouse.x - (_pieceWidth / 2), _mouse.y - (_pieceHeight / 2), _pieceWidth, _pieceHeight);
                _stage.restore();
                document.onmousemove = updatePuzzle;
                document.onmouseup = pieceDropped;
            }
        }
        function checkPieceClicked(){
            var i;
            var piece;
            for(i = 0;i < _pieces.length;i++){
                piece = _pieces[i];
				if(piece.sx != -1){
					if(_mouse.x < piece.xPos || _mouse.x > (piece.xPos + _pieceWidth) || _mouse.y < piece.yPos || _mouse.y > (piece.yPos + _pieceHeight)){
						//PIECE NOT HIT
					}
					else{
						return piece;
					}
				}
            }
            return null;
        }
        function updatePuzzle(e){
            _currentDropPiece = null;
			
			var mouse = mouseGetCoords(e);
			_mouse.x = mouse.mx - _canvas.offsetLeft;
			_mouse.y = mouse.my - _canvas.offsetTop;
			
            _stage.clearRect(0,0,_puzzleWidth * 2,_puzzleHeight);
            var i;
            var piece;
            for(i = 0;i < _pieces.length;i++){
                piece = _pieces[i];
                if(piece == _currentPiece){
                    continue;
                }
				
				if(piece.sx != -1){
					_stage.drawImage(_img, piece.sx, piece.sy, _pieceWidth, _pieceHeight, piece.xPos, piece.yPos, _pieceWidth, _pieceHeight);
				} else {
					_stage.fillStyle = "#FFFFFF";
					_stage.fillRect(piece.xPos, piece.yPos, _pieceWidth,_pieceHeight);
				}
				
				if (i >= _pieces.length / 2){
					if(piece.border){
						_stage.strokeRect(piece.xPos, piece.yPos, _pieceWidth,_pieceHeight);
					}
				}
                if(_currentDropPiece == null){
                    if(_mouse.x < piece.xPos || _mouse.x > (piece.xPos + _pieceWidth) || _mouse.y < piece.yPos || _mouse.y > (piece.yPos + _pieceHeight)){
                        //NOT OVER
                    }
                    else{
                        _currentDropPiece = piece;
                        _stage.save();
                        _stage.globalAlpha = .4;
						if(_currentDropPiece.fixed){
							_stage.fillStyle = PUZZLE_HOVER_TINT_OUT;
						} else {
							_stage.fillStyle = PUZZLE_HOVER_TINT;
						}
                        _stage.fillRect(_currentDropPiece.xPos,_currentDropPiece.yPos,_pieceWidth, _pieceHeight);
                        _stage.restore();
                    }
                }
            }
            _stage.save();
            _stage.globalAlpha = .6;
            _stage.drawImage(_img, _currentPiece.sx, _currentPiece.sy, _pieceWidth, _pieceHeight, _mouse.x - (_pieceWidth / 2), _mouse.y - (_pieceHeight / 2), _pieceWidth, _pieceHeight);
            _stage.restore();
            _stage.strokeRect( _mouse.x - (_pieceWidth / 2), _mouse.y - (_pieceHeight / 2), _pieceWidth,_pieceHeight);
        }
        function pieceDropped(e){
            document.onmousemove = null;
            document.onmouseup = null;
			
			var mouse = mouseGetCoords(e);
			_mouse.x = mouse.mx - _canvas.offsetLeft;
			_mouse.y = mouse.my - _canvas.offsetTop;
			
            if(_currentDropPiece != null){
				if(!_currentDropPiece.fixed){
					if(_currentPiece.xPos < _puzzleWidth){
						_currentDropPiece.border = 0;
					}
					
					var tmp = {xPos:_currentPiece.xPos,yPos:_currentPiece.yPos};
					_currentPiece.xPos = _currentDropPiece.xPos;
					_currentPiece.yPos = _currentDropPiece.yPos;
					_currentDropPiece.xPos = tmp.xPos;
					_currentDropPiece.yPos = tmp.yPos;
				}
				
				if(_mouse.x > _currentPiece.cx && _mouse.x < _currentPiece.cx + _pieceWidth && _mouse.y > _currentPiece.cy &&  _mouse.y < _currentPiece.cy + _pieceHeight){
					 for(i = 0;i < _pieces.length;i++){
						if(_pieces[i].sx == _currentPiece.sx && _pieces[i].sy == _currentPiece.sy){
							_pieces[i].fixed = 1;
							jq_jQuery.ajax({
								type: "POST",
								url: "<?php echo JURI::root(); ?>index.php?option=com_joomlaquiz&task=ajaxaction.procces",
								data: "ajax_task=ajax_plugin&plg_task=addpoints&quest_type=puzzle" + "&quest_id=" + quest_id + "&stu_quiz_id=" + stu_quiz_id + "&quiz_id=" + c_quiz_id + "&piece=" + _pieces[i].position + "&ltime=" + quest_timer_sec,
								success: function(){
									scores += parseInt(point);
									jq_jQuery("#jq_points").html("<?php echo JText::_('COM_JQ_YOUR_MEMORY_POINTS');?>: " + scores);
									if(parent.jq_getObj('result_point_'+quest_id)) parent.jq_getObj('result_point_'+quest_id).innerHTML = scores;
								}
							});
							break;
						}
					 }
				}
            }
            resetPuzzleAndCheckWin();
        }
        function resetPuzzleAndCheckWin(){
            _stage.clearRect(0,0,_puzzleWidth,_puzzleHeight);
            var gameWin = true;
            var i;
            var piece;
            for(i = 0;i < _pieces.length;i++){
                piece = _pieces[i];
				if(piece.sx != -1){
					_stage.drawImage(_img, piece.sx, piece.sy, _pieceWidth, _pieceHeight, piece.xPos, piece.yPos, _pieceWidth, _pieceHeight);
				} else {
					_stage.fillStyle= "#FFFFFF";
					_stage.fillRect(piece.xPos, piece.yPos, _pieceWidth,_pieceHeight);
				}
				
				if (i >= _pieces.length / 2){
						if(piece.border){
							_stage.strokeRect(piece.xPos, piece.yPos, _pieceWidth,_pieceHeight);
						}
				}
                if(piece.sx != -1 && (piece.xPos != piece.cx || piece.yPos != piece.cy)){
                    gameWin = false;
                }
            }
            if(gameWin){
                setTimeout(gameOver,500);
            }
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
        function shuffleArray(o){
            for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
            return o;
        }
		
		function mouseGetCoords(e){
	    e = e || window.event
	 
	    if (e.pageX == null && e.clientX != null ) {
	        var html = document.documentElement
	        var body = document.body
	     
	        e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
	        e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
	    }
	 
	    return {mx:e.pageX, my:e.pageY};
		}
		
		function jq_Start_Question_TickTack()
		{
			
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


    </script>
</head>

<!--   <body onload="init();">  -->
<body onload="init();">
	<div id="jq_quest_conteiner"><!--x--></div>
	<div style="clear:both;"><!--x--></div>

	<table class="puzzle_image_container">
		<tr>
			<td id="puzzle_area">

			</td>
			<td id="puzzle_parts">

			</td>
		</tr>
	</table>

	<div id="jq_quest_time_past" style="font-size:17px;display:block;float:left;"></div>
	<div id="jq_message_box" style="font-size:17px;display:none;float:left;color:red;margin-left:40px;"><?php echo JText::_('COM_QUIZ_TIME_FOR_ANSWERING_HAS_RUN_OUT');?></div>
	<div id="jq_close_button" style="display:block;float:right;" onclick="clearInterval(quest_timer); jq_Close_PuzzleWindow();"><input type="button" value="<?php echo JText::_('COM_JQ_PUZZLE_CLOSE');?>" style="width:150px;height:35px;background:#CCDDEE;border:1px solid #CCCCCC;" /></div>
	<div id="jq_points" style="display:block;font-size:18px;float:right;margin-right:25px;"><?php echo JText::_('COM_JQ_YOUR_MEMORY_POINTS');?>: 0</div>




</body>

</html>