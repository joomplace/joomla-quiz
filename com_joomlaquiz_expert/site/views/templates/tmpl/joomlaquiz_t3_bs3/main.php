<?php
/**
* JoomlaQuiz component for Joomla
* @version $Id: jp_template.php 2009-11-16 17:30:15
* @package JoomlaQuiz
* @subpackage jp_template.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::register('JoomlaQuizTemplateClass', JPATH_SITE.'/components/com_joomlaquiz/views/templates/tmpl/default.php');

class JoomlaQuiz_template_class extends JoomlaQuizTemplateClass {
	
	public static function JQ_getTemplateName() {
		return 'joomlaquiz_t3_bs3';
	}
	
	public static function JQ_MainScreen($descr) {
		
		$document 	= JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/components/com_joomlaquiz/views/templates/tmpl/'.static::JQ_getTemplateName().'/css/jq_template.css');
		$document->addStyleSheet(JURI::root(true).'/components/com_joomlaquiz/views/templates/tmpl/'.static::JQ_getTemplateName().'/css/choosen.css');
		$document->addScript(JURI::root(true)."/components/com_joomlaquiz/assets/js/jquery-1.9.1.min.js");
		$document->addScript(JURI::root(true)."/components/com_joomlaquiz/assets/js/jquery-ui-1.9.2.custom.min.js");
		$document->addScript(JURI::root(true).'/components/com_joomlaquiz/views/templates/tmpl/'.static::JQ_getTemplateName().'/js/choosen.js');
		$document->addScript(JURI::root(true)."/components/com_joomlaquiz/assets/js/raphael.js");
        $document->addStyleDeclaration('
        #jq_results_panel_table li > a{
            border: 1px solid #ddd;
        }
        #jq_results_panel_table li > a.correct,
        #jq_results_panel_table li > a.wrong{
            color: #fff;
        }
        #jq_results_panel_table li > a.wrong{
            background: red;
        }
        #jq_results_panel_table li > a.correct{
            background: green;
        }
        td.jq_input_pos {
            padding: 1px 6px;
        }
        .answered_row{
            background: #f3dede;
            border: 1px solid #f12727!important;
            border-radius: 4px!important;
        }
        .correct_answer_row{
            background: #6fab6f;
            border: 1px solid #008000!important;
        }
        ');
		
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

		$tmpl_folder = 'joomlaquiz_t3_bs3';
		
		$hide_result_panel = JText::_('COM_QUIZ_HIDE_RESULT_PANEL');
		$show_result_panel = JText::_('COM_QUIZ_SHOW_RESULT_PANEL');
		$flag_question = JText::_('COM_QUIZ_FLAG_QUESTION');

        $panel_script = self::getPanelScripts($show_result_panel,$hide_result_panel);
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

{$panel_script}

preload([
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/check_off.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/check_on.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/custom_select.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/radio_off.png',
	'{$live_url}components/com_joomlaquiz/views/templates/tmpl/{$tmpl_folder}/images/radio_on.png'
]);

//--><!]]>
</script>


<!-- Quiz header -->
<div class="panel panel-primary">
	<div class="panel-heading">
		<div id="jq_quiz_container_title" class="jquiz_title">
			<!-- x -->
		</div>
	</div>
	<div class="panel-body">
		<div id="jq_quiz_container_tbl" class="jq_quiz_container_tbl">
			<div role="alert" style="visibility:hidden;"><span id="error_messagebox"><!-- x --></span></div>
			<div class="jq_time_tick_container" style="display:none;">
				<span id="jq_time_tick_container" class="jq_quize_time"><!--x--></span>
			</div>
			<div id="jq_quiz_container1" class="jq_quiz_container1">
				<div id="jq_quiz_container">
					<div id="jq_quiz_container_description">
						<!-- QUIZ DESCRIPTION -->
						{$descr}
					</div>
					<div id="jq_quiz_container_author"><!-- x --></div>
				</div>
			</div>
			
			<div class="jq_quest_time">
				<div class="jq_quest_time_past">
					<!--x-->
				</div>
			</div>
			
			<div class="jq_quiz_task_container"><!-- x --></div>
			<div class="jquiz_bottom well">
				<div id="jq_panel_link_container" class="jq_panel_link_container" style="visibility:hidden;">
					<a id="jq_panel_link" class="jq_panel_link" href="javascript: void(0)" onclick="javascript: jq_ShowPanel();"><!-- x --></a>
				</div>	
				<div class="jq_flagged_question">
					<input type="checkbox" name="c_flag" id="c_flag" />
					<label for="c_flag" id="c_flag_label"><img src="components/com_joomlaquiz/assets/images/flag.png" />&nbsp;{$flag_question}</label>
				</div>
			</div>
            <div id="jq_quiz_result_container" class="jq_quiz_result_container jq_block_visible" style="display:none;"><!-- x --></div>
		</div>
	</div>
</div>
EOFTMPL;

		return $jq_tmpl_html;
	}
	
}