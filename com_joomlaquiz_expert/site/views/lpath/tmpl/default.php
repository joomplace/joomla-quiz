<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted Access');

$tag = JFactory::getLanguage()->getTag();
$lang = JFactory::getLanguage();
$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);

$lpath = $this->lpath_data[0];
$lpath_all = $this->lpath_data[1];

if(isset($lpath->error) && $lpath->error){
	echo $lpath->message;		
} else {
$document 	= JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_standard/css/jq_template.css');		
$document->addScript(JURI::root()."components/com_joomlaquiz/assets/js/jquery-1.9.1.min.js"); 
$document->addScriptDeclaration("
			function JO_initAccordion() {
				jq_jQuery('.jq_lpath_container .jq_lpath_step_descr').hide();
				
				//comment to hide first row
				jq_jQuery('.jq_lpath_container .expanded').show('150');
				
				jq_jQuery('.jq_lpath_container .jq_lpath_step_title').click(
					function() {								
						var checkElement = jq_jQuery(this).next();								
						if((checkElement.is('.jq_lpath_step_descr')) && (checkElement.is(':visible'))) { 
							checkElement.slideUp('150');
							return false;
						}
						if((checkElement.is('.jq_lpath_step_descr')) && (!checkElement.is(':visible'))) {
							jq_jQuery('.jq_lpath_container .jq_lpath_step_descr:visible').slideUp('normal');
							checkElement.slideDown('150');
							return false;
						}
					}
				);
			}
			jq_jQuery(document).ready(function() {JO_initAccordion();});
		");

?>
<div class="contentpane joomlaquiz">
	<h1 class="componentheading"><?php echo JText::_('COM_LPATH').":&nbsp;".$lpath->title;?></h1>
	<br/>
	<?php echo $lpath->descr ?>
	<br/>
	<h4><?php echo JText::_('COM_LPATH_STAGES'); ?></h4>
	<div style="float:right; padding-right:15px;">
	<a href="javascript: void(0);" onclick="javascript: jq_jQuery('.jq_lpath_container .jq_lpath_step_descr').show('150');"><?php echo JText::_('COM_QUIZ_EXPAND_ALL');?></a>&nbsp;&nbsp;
	<a href="javascript: void(0);" onclick="javascript: jq_jQuery('.jq_lpath_container .jq_lpath_step_descr').hide('150');"><?php echo JText::_('COM_QUIZ_COLLAPSE_ALL');?></a>
	</div>
	<br />
	<div class="jq_lpath_container">
	<?php foreach($lpath_all as $k=>$lpath_item) { ?>
		<div class="jq_lpath_step_title">
			<?php if ($lpath_item->show_link) {	?><strong><?php }?>
				<span style="cursor:pointer;text-transform:uppercase"><?php echo $lpath_item->title;?></span>
			<?php if ($lpath_item->show_link) {	?></strong><?php }?>
			<?php if (false && $lpath_item->short_description) { ?>
				<div class="jq_lpath_step_expand" style="float:right;">[<?php echo JText::_('COM_JQ_SHOW_DESCRIPTION');?>]</div>
			<?php } ?>
		</div>
		<div class="jq_lpath_step_descr<?php echo ($lpath_item->show_link && isset($lpath_all[$k+1])&&!$lpath_all[$k+1]->show_link?' expanded':'')?>">
			<?php 
				$short_des = explode('<hr id="system-readmore" />',$lpath_item->short_description);
				echo JHtml::_('content.prepare',$short_des[0]); 
			?>
			
			<?php 
			$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
			if ($lpath_item->show_link) {						
				$item_id_name = ($lpath_item->type == 'q' ? 'quiz_id' : 'article_id');?>
				<br />
				<div id="jq_start_link_container" style="float:right; margin-right:15px; line-height: 32px; text-transform: uppercase; width: auto !important; background-position: right center !important;">
					<a style="padding-right:40px;" href="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=quiz'.($lpath->rel_id? '&package_id='.$lpath->package_id.'&rel_id='.$lpath->rel_id: '&lid='.$lpath->id).'&'.$item_id_name.'='.$lpath_item->all_id.JoomlaquizHelper::JQ_GetItemId());?>"><?php echo JText::_('COM_QUIZ_START');?></a>
				</div><br /><br />
				<hr>
			<?php }?>
		</div>
	<?php } ?>
	</div>
</div>

<?php } 
	echo JoomlaquizHelper::poweredByHTML();
?>
