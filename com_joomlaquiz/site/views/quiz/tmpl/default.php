<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted Access');

$quiz = $this->quiz_params;

$is_preview = $this->is_preview;
$preview_quest = $this->preview_quest;
$preview_id = $this->preview_id;

if(isset($quiz->error) && $quiz->error){
	echo $quiz->message;
	echo JoomlaquizHelper::poweredByHTML();
} else {

$tag = JFactory::getLanguage()->getTag();
$lang = JFactory::getLanguage();
$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);

$mainframe = JFactory::getApplication();
$component_params = JComponentHelper::getParams('com_joomlaquiz');
$padding_top = $component_params->get('margin_top');

if (!isset($quiz->c_show_quest_points)) {
	$quiz->c_show_quest_points = 1;
}
if (!isset($quiz->c_show_quest_pos)) {
	$quiz->c_show_quest_pos = 1;
}

if ($quiz->template_name) {
	JoomlaquizHelper::JQ_load_template($quiz->template_name);
}
$document = JFactory::getDocument();
$document->addScript(JURI::root(true)."/components/com_joomlaquiz/assets/js/jquery-1.9.1.min.js");
$document->addScript(JURI::root(true)."/components/com_joomlaquiz/assets/js/jquery-ui-1.9.2.custom.min.js");
$document->addStyleSheet(JURI::root(true).'/components/com_joomlaquiz/assets/css/joomlaquiz.css');
	
?>

<noscript>
<?php echo JText::_('COM_JQ_NOSCRIPT');?>
</noscript>

<script language="JavaScript" src="<?php echo JURI::root(true);?>/components/com_joomlaquiz/assets/js/bits_mycommon.js" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo JURI::root(true);?>/components/com_joomlaquiz/assets/js/bits_message.js" type="text/javascript"></script>
<?php
	include_once(JPATH_SITE.'/components/com_joomlaquiz/views/quiz/tmpl/js/joomlaquiz.js.php');
?>
<div class="<?php if(preg_match("/pretty_green/", $quiz->template_name) || preg_match("/pretty_blue/", $quiz->template_name) ) { echo "jq_quiz_container_tbl_content"; } else { echo 'moduletable joomlaquiz_container';}?>
<?php if ($padding_top) echo 'style="margin-top: '.$padding_top.'px"'?>
">
<?php if ($quiz->template_name) {
		if ($is_preview) {
			echo JoomlaQuiz_template_class::JQ_MainScreen('');
		?>
		<script language="JavaScript" type="text/javascript">
		<!--//--><![CDATA[//><!--
			jq_jQuery(document).ready(function() {is_preview_mode();});
			function is_preview_mode(){
				var jq_quiz_c_t = jq_getObj('jq_quiz_container_title');
				if (jq_quiz_c_t) jq_quiz_c_t.innerHTML = "<?php echo addslashes($quiz->c_title)?>";
				var jq_quiz_c_d = jq_getObj('jq_quiz_container_description');
				if (jq_quiz_c_d) jq_quiz_c_d.innerHTML = "<?php echo addslashes(JText::_('COM_QUIZ_CLICK_HERE'));?>";
			}
		//--><!]]>
		</script>
		</div>
		<?php
		} else if(@$quiz->rel_id && $quiz->rel_data && $quiz->rel_data->c_passed && $quiz->rel_data->c_finished && !$quiz->force) {
			echo JoomlaQuiz_template_class::JQ_MainScreen('');
			$_SESSION['jq_result_mode'] = array(@$quiz->rel_id, @$quiz->rel_data->c_quiz_id, @$quiz->package_id);
		?>
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
			jq_jQuery(document).ready(function() {result_mode();});

			function result_mode(){
				var jq_quiz_c_t = jq_getObj('jq_quiz_container_title');
				if (jq_quiz_c_t) jq_quiz_c_t.innerHTML = "<?php echo addslashes($quiz->c_title)?>";

				user_unique_id = '<?php echo $quiz->rel_data->unique_id; ?>';
				stu_quiz_id = <?php echo $quiz->rel_data->c_id; ?>;
				jq_MakeRequest('&ajax_task=finish_stop&quiz=<?php echo $quiz->rel_data->c_quiz_id; ?>'+'&stu_quiz_id='+stu_quiz_id, 1);
			}
		//--><!]]>
		</script>
		</div>
		<?php
		} else if(@$quiz->lid && isset($quiz->lid_data) && $quiz->lid_data->c_passed && $quiz->lid_data->c_finished  && !$quiz->force) {

			echo JoomlaQuiz_template_class::JQ_MainScreen('');
			$_SESSION['jq_result_mode_lid'] = array(@$quiz->lid, @$quiz->lid_data->c_quiz_id);
		?>
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
			jq_jQuery(document).ready(function() {result_mode();});

			function result_mode(){
				var jq_quiz_c_t = jq_getObj('jq_quiz_container_title');
				if (jq_quiz_c_t) jq_quiz_c_t.innerHTML = "<?php echo addslashes($quiz->c_title)?>";

				user_unique_id = '<?php echo $quiz->lid_data->unique_id; ?>';
				stu_quiz_id = <?php echo $quiz->lid_data->c_id; ?>;
				jq_MakeRequest('&ajax_task=finish_stop&lid=<?php echo $quiz->lid;?>&quiz=<?php echo $quiz->lid_data->c_quiz_id; ?>'+'&stu_quiz_id='+stu_quiz_id, 1);
			}
		//--><!]]>
		</script>
		</div>
		<?php
		} else if(isset($quiz->result_data)) {

			echo JoomlaQuiz_template_class::JQ_MainScreen('');
			$_SESSION['jq_result_mode_5'] = array($quiz->result_data, $quiz->result_data->c_quiz_id);
		?>
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
			jq_jQuery(document).ready(function() {result_mode();});

			function result_mode(){


				var jq_quiz_c_t = jq_getObj('jq_quiz_container_title');
				if (jq_quiz_c_t) jq_quiz_c_t.innerHTML = "<?php echo addslashes($quiz->c_title)?>";

				user_unique_id = '<?php echo $quiz->result_data->unique_id; ?>';
				stu_quiz_id = <?php echo $quiz->result_data->c_id; ?>;
				jq_MakeRequest('&ajax_task=finish_stop&quiz=<?php echo $quiz->result_data->c_quiz_id; ?>'+'&stu_quiz_id='+stu_quiz_id, 1);

			}
		//--><!]]>
		</script>
		</div>
		<?php
		} elseif ($quiz->c_autostart) {
			echo JoomlaQuiz_template_class::JQ_MainScreen('');
		?>
		<script language="JavaScript" type="text/javascript">
		<!--//--><![CDATA[//><!--

			jq_jQuery(document).ready(function() {not_preview_mode();});

			function not_preview_mode(){

				var jq_quiz_c_t = jq_getObj('jq_quiz_container_title');
				if (jq_quiz_c_t) jq_quiz_c_t.innerHTML = "<?php echo addslashes($quiz->c_title)?>";
				var jq_quiz_c_d = jq_getObj('jq_quiz_container_description');
				var jq_quiz_c_pl = jq_getObj('jq_panel_link');
				
				if (jq_quiz_c_pl)
				<?php if(!preg_match("/pretty_green/", $quiz->template_name) && !preg_match("/pretty_blue/", $quiz->template_name)){?>
				jq_quiz_c_pl.innerHTML = "<?php echo addslashes(JText::_('COM_QUIZ_SHOW_RESULT_PANEL'))?>";
				<?php } else {?>
				jq_quiz_c_pl.innerHTML = "<i class='jq_quize_arrow_little'><!--x--></i><?php echo addslashes(JText::_('COM_QUIZ_SHOW_RESULT_PANEL'))?>";
				<?php }?>
				jq_StartQuizOn();
			}
		//--><!]]>
		</script>
		</div>
		<?php
		} else {
		echo JoomlaQuiz_template_class::JQ_MainScreen($quiz->c_description);
		?>
		<script language="JavaScript" type="text/javascript">
		<!--//--><![CDATA[//><!--

			jq_jQuery(document).ready(function() {not_preview_mode();});

			function not_preview_mode(){
				jq_UpdateTaskDiv('start');
				var jq_quiz_c_t = jq_getObj('jq_quiz_container_title');
				if (jq_quiz_c_t) jq_quiz_c_t.innerHTML = "<?php echo addslashes($quiz->c_title)?>";
				var jq_quiz_c_d = jq_getObj('jq_quiz_container_description');
				var jq_quiz_c_a = jq_getObj('jq_quiz_container_author');
				<?php if ($quiz->c_show_author): ?>
				if (jq_quiz_c_a) jq_quiz_c_a.innerHTML = "<?php echo addslashes(JText::_('COM_QUIZ_AUTHOR_PREFIX'))?> <?php echo addslashes($quiz->c_author)?>";
				<?php endif; ?>
				var jq_quiz_c_pl = jq_getObj('jq_panel_link');
				if (jq_quiz_c_pl)
				<?php if(!preg_match("/pretty_green/", $quiz->template_name) && !preg_match("/pretty_blue/", $quiz->template_name)){?>
				jq_quiz_c_pl.innerHTML = "<?php echo addslashes(JText::_('COM_QUIZ_SHOW_RESULT_PANEL'))?>";
				<?php } else {?>
				jq_quiz_c_pl.innerHTML = "<i class='jq_quize_arrow_little'><!--x--></i><?php echo addslashes(JText::_('COM_QUIZ_SHOW_RESULT_PANEL'))?>";
				<?php }?>
			}
		//--><!]]>
		</script>
		</div>
<?php	}
	}

	if (defined('_JEXEC')) {
		$document	= JFactory::getDocument();
		if ($quiz->c_ismetadescr && $quiz->c_metadescr) {
			$document->setDescription( $quiz->c_metadescr );
		}
		if ($quiz->c_iskeywords && $quiz->c_keywords) {
			$document->setMetadata('keywords', $quiz->c_keywords);
		}
		if ($quiz->c_ismetatitle && $quiz->c_metatitle) {
			$document->setTitle($quiz->c_metatitle);
		}
	}
	
	echo JoomlaquizHelper::poweredByHTML();
}

?>

