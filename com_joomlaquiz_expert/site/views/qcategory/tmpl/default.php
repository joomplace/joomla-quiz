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

	$document 	= JFactory::getDocument();
	$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_standard/css/jq_template.css');		
	$document->addScript(JURI::root()."components/com_joomlaquiz/assets/js/jquery-1.9.1.min.js"); 
	$document->addScriptDeclaration("
				function JO_initAccordion() {
					jq_jQuery('.jq_quiz_container .jq_cat_quiz_descr').hide();
											
					//comment to hide first row
					jq_jQuery('.jq_quiz_container .jq_cat_quiz_descr:first').show('150');
					jq_jQuery('.jq_quiz_container .jq_cat_quiz_expand:first').html('[".JText::_('COM_QUIZ_HIDE_DESCRIPTION')."]');
					
					jq_jQuery('.jq_quiz_container .jq_cat_quiz_expand').click(
						function() {
							jq_jQuery('.jq_quiz_container .jq_cat_quiz_title .jq_cat_quiz_expand').html('[".JText::_('COM_JQ_SHOW_DESCRIPTION')."]');
							var checkElement = jq_jQuery(this).parent().next();								
							if((checkElement.is('.jq_cat_quiz_descr')) && (checkElement.is(':visible'))) { 
								checkElement.slideUp('150');
								jq_jQuery(this).html('[".JText::_('COM_JQ_SHOW_DESCRIPTION')."]');
								return false;
							}
							if((checkElement.is('.jq_cat_quiz_descr')) && (!checkElement.is(':visible'))) {
								jq_jQuery('.jq_quiz_container .jq_cat_quiz_descr:visible').slideUp('normal');
								checkElement.slideDown('150');
								jq_jQuery(this).html('[".JText::_('COM_QUIZ_HIDE_DESCRIPTION')."]');
								return false;
							}
						}
					);
				}
				jq_jQuery(document).ready(function() {JO_initAccordion();});
			");
	$document->addScriptDeclaration("
				function JO_initAccordion2() {
					jq_jQuery('.jq_pquiz_container .jq_cat_pquiz_descr').hide();
					
					//comment to hide first row
					jq_jQuery('.jq_pquiz_container .jq_cat_pquiz_descr:first').show('150');
					
					jq_jQuery('.jq_pquiz_container .jq_cat_pquiz_expand').click(
						function() {								
							var checkElement = jq_jQuery(this).parent().next();								
							if((checkElement.is('.jq_cat_pquiz_descr')) && (checkElement.is(':visible'))) { 
								checkElement.slideUp('150');
								return false;
							}
							if((checkElement.is('.jq_cat_pquiz_descr')) && (!checkElement.is(':visible'))) {
								jq_jQuery('.jq_pquiz_container .jq_cat_pquiz_descr:visible').slideUp('normal');
								checkElement.slideDown('150');
								return false;
							}
						}
					);
				}
				jq_jQuery(document).ready(function() {JO_initAccordion2();});
			");
	$document->addScriptDeclaration("
			function JO_initAccordion3() {
				jq_jQuery('.jq_lpath_container .jq_cat_lpath_descr').hide();
				
				//comment to hide first row
				jq_jQuery('.jq_lpath_container .jq_cat_lpath_descr:first').show('150');
				
				jq_jQuery('.jq_lpath_container .jq_cat_lpath_expand').click(
					function() {								
						var checkElement = jq_jQuery(this).parent().next();								
						if((checkElement.is('.jq_cat_lpath_descr')) && (checkElement.is(':visible'))) { 
							checkElement.slideUp('150');
							return false;
						}
						if((checkElement.is('.jq_cat_lpath_descr')) && (!checkElement.is(':visible'))) {
							jq_jQuery('.jq_lpath_container .jq_cat_lpath_descr:visible').slideUp('normal');
							checkElement.slideDown('150');
							return false;
						}
					}
				);
			}
			jq_jQuery(document).ready(function() {JO_initAccordion3();});
		");

foreach($this->categories as $categ){
	$cat = (isset($categ->cat)) ? $categ->cat : array();
	$rows = (isset($categ->rows)) ? $categ->rows : array();
	$lpaths = (isset($categ->lpaths)) ? $categ->lpaths : array();
	$bought_quizzes = (isset($categ->bought_quizzes)) ? $categ->bought_quizzes : array();
	?>
	<div class="contentpane <?php echo ($cat->parent_id != 'root')?'child-cat':'parent-cat'; echo ' rel-level'.$cat->level; ?> joomlaquiz">
		<?php if(JFactory::getApplication()->getParams()->get('show_page_heading',0)){ ?>
			<h1 class="componentheading"><?php echo JFactory::getApplication()->getParams()->get('page_heading',JText::_('COM_QUIZ_CAREGORY').": ".$cat->title);?></h1>
		<?php } ?>
		<br />	
		<?php echo $cat->description ;?>	
		<br />
		<div class="jq_quiz_container">
		<?php if(!empty($rows)) { ?>
			<h4><?php echo JText::_('COM_QUIZ_QUIZZES');?></h4>
			<?php foreach($rows as $row) { ?>
				<div class="jq_cat_quiz_title">
					<strong style="text-transform:uppercase">
						<a href="<?php echo JRoute::_("index.php?view=quiz&option=com_joomlaquiz&quiz_id=".$row->c_id);?>"><?php echo $row->c_title;?></a>
					</strong>
					<?php echo $row->payment; ?>
					<?php if ($row->c_short_description) { ?>
						<div class="jq_cat_quiz_expand" style="float:right;">[<?php echo JText::_('COM_JQ_SHOW_DESCRIPTION');?>]</div>
					<?php } ?>
				</div>
				<div class="jq_cat_quiz_descr">
					<?php echo $row->c_short_description;?>
				</div>
			<?php } ?>
		<?php } ?>
		</div>
		
		<?php if(empty($bought_quizzes) && empty($lpaths)) {
			echo '</div>';
		}else{ ?>
		
		<br />
		<div class="jq_pquiz_container">
		<?php if(!empty($bought_quizzes)) { ?>
			<h4><?php echo JText::_('COM_JQ_PURCH_QUIZZES'); ?></h4>
		
			<?php
			foreach($bought_quizzes as $b_quizz) {
				?>
				<div class="jq_cat_pquiz_title">
					<strong style="text-transform:uppercase">
					<a href="<?php echo JRoute::_("index.php?view=quiz&option=com_joomlaquiz&package_id=".$b_quizz->pid."&rel_id=".$b_quizz->id.JoomlaquizHelper::JQ_GetItemId());?>"><?php echo $b_quizz->row->c_title;?></a>
					</strong>
					<br />
					<small><?php echo $b_quizz->suffix; ?></small>
					<?php if ($b_quizz->row->c_short_description) { ?>
						<div class="jq_cat_pquiz_expand" style="float:right;">[<?php echo JText::_('COM_JQ_SHOW_DESCRIPTION');?>]</div>
					<?php } ?>
				</div>
				<div class="jq_cat_pquiz_descr">
					<?php echo $b_quizz->row->c_short_description;?>
				</div>
				<?php
			}
		} ?>
		</div>
		<br />
		
		<div class="jq_lpath_container">
		<?php if(!empty($lpaths)) { ?>
			<h4><?php echo JText::_('COM_QUIZ_PURCH_LPATHS');	?></h4>
			
			<?php
			foreach($lpaths as $lpath) {
				?>
				<div class="jq_cat_lpath_title">
					<strong style="text-transform:uppercase">
						<a href="<?php echo JRoute::_("index.php?view=lpath&option=com_joomlaquiz&package_id=".$lpath->pid."&rel_id=".$lpath->id.JoomlaquizHelper::JQ_GetItemId());?>"><?php echo $lpath->title;?></a>
					</strong>
					<br />
					<small><?php echo $lpath->suffix; ?></small>
					<?php if ($lpath->short_descr) { ?>
						<div class="jq_cat_lpath_expand" style="float:right;">[<?php echo JText::_('COM_JQ_SHOW_DESCRIPTION');?>]</div>
					<?php } ?>
				</div>
				<div class="jq_cat_lpath_descr">
					<?php echo $lpath->short_descr;?>
				</div>
				<?php
			}
		}
		?>
		</div>
	</div>
<?php 
	}
}