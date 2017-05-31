<?php
/**
* JoomlaQuiz component for Joomla
* @version $Id: jp_template.php 2009-11-16 17:30:15
* @package JoomlaQuiz
* @subpackage jp_template.phpJQ_show_results
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::register('JoomlaQuizTemplateClass', JPATH_SITE.'/components/com_joomlaquiz/views/templates/tmpl/default.php');

class JoomlaQuiz_template_class extends JoomlaQuizTemplateClass {
	
	public static function JQ_getTemplateName() {
		return 'joomlaquiz_standard';
	}
	
	public static function JQ_MainScreen($descr) {
		
		$document 	= JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/components/com_joomlaquiz/views/templates/tmpl/'.static::JQ_getTemplateName().'/css/jq_template.css');		
		$document->addScript(JURI::root(true)."/components/com_joomlaquiz/assets/js/jquery-1.9.1.min.js"); 
		$document->addScript(JURI::root(true)."/components/com_joomlaquiz/assets/js/raphael.js");

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
		
		$tmpl_folder = static::JQ_getTemplateName();
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
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/hs_round.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/drag_img.gif',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/cont_img.gif',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/apply.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/back.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/next.png',	
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/submit.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/start.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/incorrect.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/correct.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/certificate.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/email.png',	
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/lpath.png',	
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/nquiz.png',	
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/print.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/qfeedback.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/qmessage.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/qresults.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/review.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/try_again.png'	
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
	
}