<?php
/**
* JoomlaQuiz component for Joomla
* @version $Id: jp_template.php 2009-11-16 17:30:15
* @package JoomlaQuiz
* @subpackage jp_template.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

interface JoomlaQuizTemplateInterface {
    static function JQ_getTemplateName();
}

abstract class JoomlaQuizTemplateClass implements JoomlaQuizTemplateInterface {
	
	public static function JQ_MainScreen($descr) {
		
		$document 	= JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/views/templates/tmpl/'.JoomlaQuiz_template_class::JQ_getTemplateName().'/css/jq_template.css');		
		$document->addScript(JURI::root()."components/com_joomlaquiz/assets/js/jquery-1.9.1.min.js"); 
		$document->addScript(JURI::root()."components/com_joomlaquiz/assets/js/raphael.js");

		$live_site = JURI::root();
		if (JoomlaquizHelper::jq_substr($_SERVER['HTTP_HOST'],0,4) == 'www.') {
			if (strpos($live_site, 'www.') !== false){
				// nothing to do
			} else {
				$live_site = str_replace(JoomlaquizHelper::jq_substr($_SERVER['HTTP_HOST'],4), $_SERVER['HTTP_HOST'], $live_site);
			}
		} else { 
			if (strpos($live_site, 'www.') !== false) 
				$live_site = str_replace('www.'.$_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST'], $live_site);
		}
		
		$live_site_parts = parse_url($live_site); 
	
		$live_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on'? 'https':'http').'://'.$live_site_parts['host'].(isset($live_site_parts['port'])?':'.$live_site_parts['port']:'').(isset($live_site_parts['path'])?$live_site_parts['path']:'/');
		
		if ( JoomlaquizHelper::jq_substr($live_url, strlen($live_url)-1, 1) !== '/')
			$live_url .= '/';
		
		$hide_result_panel = JText::_('COM_QUIZ_HIDE_RESULT_PANEL');
		$show_result_panel = JText::_('COM_QUIZ_SHOW_RESULT_PANEL');
		$flag_question = JText::_('COM_QUIZ_FLAG_QUESTION');
		$jq_tmpl_html = <<<EOFTMPL
		
<script language="JavaScript" type="text/javascript">
<!--//--><![CDATA[//><!--
//variables for fading message

var fd_startR = 250;
var fd_startG = 250;
var fd_startB = 250;
var fd_endR = 200;
var fd_endG = 0;
var fd_endB = 0;
var tbl_max_step = 0;

function blank_enter(oEvent)
{
	if (navigator.appName == "Netscape")
	{
		if (oEvent.which == 13)
		return false;
	}
	else
	{
		if (oEvent.keyCode == 13)
		return false;
	}	
	return true;
}

function JQ_MM_preloadImages() {
	var d=document;
	if (d.images) {
		if (!d.MM_p) {
			d.MM_p=new Array();
		}
		
		var i, j = d.MM_p.length, a = JQ_MM_preloadImages.arguments;
		
		for(i=0; i<a.length; i++) {
			if (a[i].indexOf("#") != 0) { 
				d.MM_p[j] = new Image; 
				d.MM_p[j++].src = a[i];
			}
		}
	}
}

//'jq_results_panel_table' - id of the table with user results
function jq_ShowPanel_go() {
	var jq_quiz_r_c = jq_getObj('jq_quiz_result_container');
	var jq_quiz_r = jq_getObj('jq_results_panel_table');
	start_index = 0;
	end_index = jq_quiz_r.rows.length;
	if (jq_quiz_r.rows[start_index]) {
		for (var i=start_index; i<jq_quiz_r.rows.length; i++) {
			jq_quiz_r.rows[i].style.visibility = 'hidden';
			//jq_quiz_r.rows[i].style.display = 'none';
		}
	}
	var jq_quiz_c_cont = jq_getObj('jq_quiz_container');
	if (jq_quiz_c_cont) { jq_quiz_c_cont.style.visibility = 'hidden'; jq_quiz_c_cont.style.display = 'none'; }
	if (jq_quiz_r_c) { jq_quiz_r_c.style.visibility = 'visible'; jq_quiz_r_c.style.display = 'block'; }
	if (jq_quiz_r) { jq_quiz_r.style.visibility = 'visible'; }
	tbl_max_step = end_index;
	setTimeout("jq_StepShowPanel(0)", 100);
	jq_jQuery("#jq_panel_link").html("{$hide_result_panel}");
	jq_jQuery(".jq_quiz_task_container").css("visibility", "hidden");
}

function jq_StepShowPanel(row_index) {
	var jq_quiz_r_c = jq_getObj('jq_results_panel_table');
	if (jq_quiz_r_c.rows[row_index]) {
		jq_quiz_r_c.rows[row_index].style.visibility = 'visible';
	}
	
	if ((row_index + 1) < tbl_max_step) {
		setTimeout("jq_StepShowPanel("+(row_index + 1)+")", 100);
	}
}

function jq_StepHidePanel(row_index) {
	var jq_quiz_r_c = jq_getObj('jq_results_panel_table');
	if (jq_quiz_r_c.rows[row_index]) {
		jq_quiz_r_c.rows[row_index].style.visibility = 'hidden';
	}
	
	if ((row_index - 1) >= 0) {
		setTimeout("jq_StepHidePanel("+(row_index - 1)+")", 100);
	} else {
		var jq_quiz_r_c = jq_getObj('jq_quiz_result_container');
		if (jq_quiz_r_c) { jq_quiz_r_c.style.visibility = 'hidden'; jq_quiz_r_c.style.display = 'none';}
		var jq_quiz_c_cont = jq_getObj('jq_quiz_container');
		if (jq_quiz_c_cont) { jq_quiz_c_cont.style.visibility = 'visible'; jq_quiz_c_cont.style.display = 'block';}
		jq_jQuery("#jq_panel_link").html("{$show_result_panel}");
		jq_jQuery(".jq_quiz_task_container").css("visibility", "visible");
	}	
}

function jq_HidePanel_go() {
	var jq_quiz_r_c = jq_getObj('jq_quiz_result_container');
	var jq_quiz_r = jq_getObj('jq_results_panel_table');
	start_index = 0;
	end_index = jq_quiz_r.rows.length;
	if (jq_quiz_r_c) { jq_quiz_r_c.style.visibility = 'visible'; jq_quiz_r_c.style.display = 'block';}
	if (jq_quiz_r) { jq_quiz_r.style.visibility = 'visible'; //jq_quiz_r.style.display = 'table';
	}
	tbl_max_step = end_index;
	setTimeout("jq_StepHidePanel("+end_index+")", 50);
}

//list here all  template images
JQ_MM_preloadImages(
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_simple/images/hs_round.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_simple/images/drag_img.gif',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_simple/images/cont_img.gif',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_simple/images/incorrect.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_simple/images/correct.png'
	);	
//--><!]]>
</script>


<!-- Quiz header -->
<div class="componentheading"><h2 id="jq_quiz_container_title"><!-- x --></h2></div>

<div id="jq_quiz_container_tbl" class="jq_quiz_container_tbl">
	<div class="error_messagebox"><span id="error_messagebox">error messagebox<!-- x --></span></div>	
	<div class="jq_time_tick_container"><span id="jq_time_tick_container"  ><!-- x -->00:00</span></div>

	<div id="jq_quiz_container1">
		<div id="jq_quiz_container">
			<div id="jq_quiz_container_description">
				<!-- QUIZ DESCRIPTION -->
				{$descr}
			</div>
			<div id="jq_quiz_container_author"><!-- x --></div>
		</div>
		<div id="jq_quiz_result_container" class="jq_quiz_result_container"><!-- x --></div>
	</div>
	
	<div class="jq_bottom_container">
		<div id="jq_panel_link_container" class="jq_panel_link_container">
			<a id="jq_panel_link" href="javascript: void(0)" onclick="javascript: jq_ShowPanel();"><!-- x --></a>
		</div>	
		<div class="jq_flagged_question">
			<input type="checkbox" name="c_flag" id="c_flag" />
			<label for="c_flag" id="c_flag_label"><img src="components/com_joomlaquiz/assets/images/flag.png" />&nbsp;{$flag_question}</label>
		</div>
		<div class="jq_quiz_task_container"><!-- x --></div>
	</div>
</div>
EOFTMPL;

		return $jq_tmpl_html;
	}
	
	public static function JQ_QuizBody() {
		$return_str = <<<EOFTMPL
			<div class="jq_question_text_cont">{QUESTION_TEXT}</div>
			<div class="jq_question_answers_cont">{ANSWERS}</div>				
EOFTMPL;
		//remove new line characters
		$return_str = str_replace("\n", '', $return_str);
		$return_str = str_replace("\r", '', $return_str);
		return $return_str;
	}

	public static function JQ_getQuestionInfo() {
		$return_str = <<<EOFTMPL
			<div class="jq_question_info_container" id="jq_question_info_container">
				<!-- x -->
				&nbsp;<span id="jq_quest_num_container"><!-- QUESTION_X_Y --></span>
				<span id="jq_points_container"><!-- POINTS --></span>
		
			</div>
EOFTMPL;
		//remove new line characters
		$return_str = str_replace("\n", '', $return_str);
		$return_str = str_replace("\r", '', $return_str);
		return $return_str;
	}
		
	public static function JQ_getQuestionDelimeter() {
		$return_str = <<<EOFTMPL
			<hr/>
EOFTMPL;
		//remove new line characters
		$return_str = str_replace("\n", '', $return_str);
		$return_str = str_replace("\r", '', $return_str);
		return $return_str;
	}


	public static function JQ_show_results() {
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
        if(file_exists(JPATH_SITE.'/components/com_alphauserpoints/helper.php')){
			$AUP_isset = require_once(JPATH_SITE.'/components/com_alphauserpoints/helper.php');
		} else {
			$AUP_isset = false;
		}
        if($AUP_isset) {     
            $result = AlphaUserPointsHelper::checkRuleEnabled('plgup_joomlaquizpoints');
    		if ($result[0]->displaymsg == 1) {
    		  $jq_tmpl_html = '
                  <!-- MAIN RESULT PART BEGIN -->
                    <div id="system-message-container">
                        <dl id="system-message">
                            <dt class="message">Message</dt>
                            <dd class="message message">
                                <ul>
                                    <!-- SYSTEM MESSAGE CONTAINER -->
                                </ul>
                            </dd>
                        </dl>
                    </div>';
             }
        } else {
                $jq_tmpl_html = '
                    <!-- MAIN RESULT PART BEGIN -->
                ';
             }
		$jq_tmpl_html .= '
		<table class="jq_results_container" cellpadding="0" cellspacing="0" border="0"  width="100%">
			<tr>	
				<td class="sectiontableheader jq_results_header" colspan="2">'.JText::_('COM_QUIZ_HEADER_FIN_RESULTS').'</td>
			</tr>
			<tr class="sectiontableentry1">
				<td class="jq_result_key" valign="top">'.JText::_('COM_QUIZ_RES_MES_SCORE').'</td>
				<td class="jq_result_value"><!-- TOTAL USER SCORE --></td>
			</tr>
			
			<!-- SCORE BY CATEGORIES BEGIN -->
			<tr class="sectiontableentry1">
				<td class="jq_result_key" valign="top">'.JText::_('COM_QUIZ_RES_CAT_SCORE').'</td>
				<td class="jq_result_value"><!-- SCORE BY CATEGORIES --></td>
			</tr>
			<!-- SCORE BY CATEGORIES END -->
			
			<tr class="sectiontableentry2">
				<td class="jq_result_key" valign="top">'.JText::_('COM_QUIZ_RES_MES_PAS_SCORE').'</td>
				<td class="jq_result_value"><!-- PASSING SCORE --></td>
			</tr>	
				
			<tr class="sectiontableentry1">
				<td class="jq_result_key" valign="top">'.JText::_('COM_QUIZ_RES_MES_TIME').'</td>
				<td class="jq_result_value"><!-- SPENT TIME --></td>
			</tr>		
		</table>
		<br />
		<!-- MAIN RESULT PART END -->
		
		<!-- FIN MESSAGE BEGIN -->
		<table class="jq_fmessage_container" cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>	
				<td class="sectiontableheader jq_message_header">'.JText::_('COM_QUIZ_HEADER_FIN_MESSAGE').'</td>
			</tr>
			
			<tr class="sectiontableheader1">	
				<td><!-- QUIZ FINAL MESSAGE --></td>
			</tr>
		</table>	
		<br />
		<!-- FIN MESSAGE END -->

		<!-- QUIZ FOOTER BEGIN -->
		<table class="jq_footer_container" cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>	
				<td class="sectiontableheader"><!-- QUIZ FOOTER LNKS --></td>
			</tr>
		</table>	
		<br />
		<!-- QUIZ FOOTER END -->
	    <div class="certificateMessage">
		    <!-- QUIZ CERTIFICATE MESSAGE -->
		</div>

		<!-- SOCIAL BUTTONS -->

		<!-- QUIZ FINAL FEEDBACK BEGIN -->
		<table class="jq_footer_container" cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>	
				<td class="sectiontableheader jq_ffeedback_header">'.JText::_('COM_QUIZ_QUIZ_FEEDNACK').'</td>
			</tr>
			<tr>	
				<td id="jq_feed_questions"><!-- QUIZ FINAL FEEDBACK --></td>
			</tr>
		</table>	
		<!-- QUIZ FINAL FEEDBACK END -->		
';
	return $jq_tmpl_html;
	}

	public static function JQ_final_feedback() {
		$jq_tmpl_html = <<<EOF_RES
		<div class="jq_feed_questions">
				<!-- QUESTIONS -->	
				<hr/>
				<!-- PAGINATION -->	
				<div style="clear: both;"><!-- --></div>
			</div>		
EOF_RES;
		return $jq_tmpl_html;
	}
	
	public static function JQ_final_feedback_question($question_number, $question_text, $question_options) {
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		$jq_tmpl_html = '<div class="jq_feedback_question">
			<fieldset class="jq_fbd_question">	
			<legend>'.JText::_('COM_QUIZ_LEGEND_QUESTION').' '.$question_number.'</legend>'	
			.$question_text.
			'<br />'
			.$question_options.
			'</fieldset>
		</div>';
		return $jq_tmpl_html;
	}
	
	public static function JQ_getFeedbackQuestionDelimeter() {
		$return_str = <<<EOFTMPL
			<div style="clear: both;"><!-- x --></div><br/><hr/><br/>	
EOFTMPL;
		//remove new line characters
		$return_str = str_replace("\n", '', $return_str);
		$return_str = str_replace("\r", '', $return_str);
		return $return_str;
	}

	public static function JQ_show_messagebox($header, $msg) {
		$msg = html_entity_decode($msg);
		$msg = str_replace('&quot;','"',$msg);
		$jq_tmpl_html = <<<EOF_MSG
						{$msg}
EOF_MSG;
		return $jq_tmpl_html;
	} 
	
	public static function JQ_createQuestion($qdata, $data){
		
		$jq_tmpl_html = '';
		$class_suffix = JoomlaquizHelper::loadAddonsFunctions($data['quest_type'], 'JoomlaquizViewCreate', $data['quest_type'].'/tmpl/'.$data['cur_template'].'/create', true);
		if(method_exists('JoomlaquizViewCreate'.$class_suffix, 'getQuestionContent')){
			$className = 'JoomlaquizViewCreate'.$class_suffix;
			$jq_tmpl_html = $className::getQuestionContent($qdata, $data);
		}
		
		return $jq_tmpl_html;
	}
	
	public static function JQ_createFeedback($feedback_data, $data)
	{
		$jq_tmpl_html = '';
		$class_suffix = JoomlaquizHelper::loadAddonsFunctions($data['quest_type'], 'JoomlaquizViewFeedback', $data['quest_type'].'/tmpl/'.$data['cur_template'].'/feedback', true);
		if(method_exists('JoomlaquizViewFeedback'.$class_suffix, 'getFeedbackContent')){
			$className = 'JoomlaquizViewFeedback'.$class_suffix;
			$jq_tmpl_html = $className::getFeedbackContent($feedback_data, $data);
		}
		
		return $jq_tmpl_html;
	}
	
	public static function JQ_createReview($review_data, $data)
	{
		$jq_tmpl_html = '';
		$class_suffix = JoomlaquizHelper::loadAddonsFunctions($data['quest_type'], 'JoomlaquizViewReview', $data['quest_type'].'/tmpl/'.$data['cur_template'].'/review', true);
		if(method_exists('JoomlaquizViewReview'.$class_suffix, 'getReviewContent')){
			$className = 'JoomlaquizViewReview'.$class_suffix;
			$jq_tmpl_html = $className::getReviewContent($review_data, $data);
		}
		
		return $jq_tmpl_html;
	}
							
	public static function JQ_createBlank($blank_id, $blank_value='', $css_class='', $blk_id=0, $qform=0, $q_id = '') { //html template for 'Blank' questions
		if ($qform){
			$jq_tmpl_html = "<span class='jq_blank_container jq_blank jq_blank_droppable q".$q_id." ".$css_class."' id=\"blk_id_".$blk_id."\"><span class=\"jq_blank_draggable\" id=\"_blk_id_".$blk_id."\">".$blank_value."</span>&nbsp;</span><input type='hidden' name='quest_blank_".$blank_id."' onblur=\"javascript: check_Blank(".$blk_id.", this.value)\" size='32' value=\"".$blank_value."\" id=\"hid_blk_id_".$blk_id."\" /> \n";
		} else {	
			$jq_tmpl_html = "<input type='text' name='quest_blank_".$blank_id."' class='inputbox jq_blank q".$q_id." ".$css_class."' size='32' value=\"".$blank_value."\" /> \n";			
		}
		return $jq_tmpl_html;
	}

	
	public static function JQ_createBlank_review($review_val = '', $color = 'red') { //html template for review 'Blank' questions
		$jq_tmpl_html = "<font color='{$color}' class='jq_blank'>{".$review_val."}</font> \n";
		return $jq_tmpl_html;
	}

	public static function JQ_createBlank_fdb(&$correct_answers, $user_answer = '', $color = 'red', $is_correct) { //html template for final feedback 'Blank' questions
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
		
		if($is_correct){
			$html = '<font color="green">'.$user_answer.'</font>';
		}else{
			$html = '<font color="red" style="text-decoration: line-through;">'.$user_answer.'</font> <font color="green">'.array_shift($correct_answers).(($correct_answers)?' ('.implode(', ',$correct_answers).')':'').'</font>';
		}
		
		return $html;
	}

	public static function escape($var)
    {
		return call_user_func('htmlspecialchars', $var, ENT_COMPAT, 'UTF-8');
	}
	
	public static function JQ_get_questcaption($c_question)
	{
		return '<div style="clear:both;">'.$c_question.'</div>';
	}
	
	public static function JQ_panel_start(){
		$panel_str = '<table id="jq_results_panel_table" width="100%" style="padding: 0px 20px 0px 20px">';
		
		return $panel_str;
	}
	
	public static function JQ_panel_data($panel_row, $all_quests, $cquests, $stu_quiz_id, $k, $n){
		$panel_str = '<tr class="sectiontableentry'.$k.'"><td><a href="javascript:void(0)" onClick="javascript:JQ_gotoQuestionOn('.$panel_row->c_id.')">'.$panel_row->c_question.'</a></td><td width="25px" align="center"><div id="result_point_'.$panel_row->c_id.'">-</div></td><td width="25px" align="center"><div id="quest_result_'.$panel_row->c_id.'">'.($stu_quiz_id && in_array($panel_row->c_id, $all_quests)? (in_array($panel_row->c_id, $cquests)?"<img src='".JURI::root()."components/com_joomlaquiz/assets/images/tick.png' border=0>":"<img src='".JURI::root()."components/com_joomlaquiz/assets/images/publish_x.png' border=0>"):'-').'</div></td></tr>';
		
		return $panel_str;
	}
}