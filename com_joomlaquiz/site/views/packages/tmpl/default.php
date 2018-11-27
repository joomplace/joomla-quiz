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

if(isset($this->packages->error) && $this->packages->error){
	echo $this->packages->message;
} else {

$document 	= JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_standard/css/jq_template.css');		
$document->addScript(JURI::root()."components/com_joomlaquiz/assets/js/jquery-1.9.1.min.js"); 
$document->addScriptDeclaration("
			function JO_initAccordion() {
				jq_jQuery('.jq_packages_container .jq_package_body_container').hide();
				
				//comment to hide first row
				jq_jQuery('.jq_packages_container .jq_package_body_container:first').show('150');
				
				jq_jQuery('.jq_packages_container .jq_package_title_container').click(
					function() {								
						var checkElement = jq_jQuery(this).next();								
						if((checkElement.is('.jq_package_body_container')) && (checkElement.is(':visible'))) { 
							checkElement.slideUp('150');
							return false;
						}
						if((checkElement.is('.jq_package_body_container')) && (!checkElement.is(':visible'))) {
							jq_jQuery('.jq_packages_container .jq_package_body_container:visible').slideUp('normal');
							checkElement.slideDown('150');
							return false;
						}
					}
				);
			}
			jq_jQuery(document).ready(function() {JO_initAccordion();});
		");
if(empty($this->packages)) {
	?>
	<div class="contentpane joomlaquiz">
		<h1 class="componentheading"><?php echo JText::_('COM_SHOW_PACKAGES_TITLE'); ?></h1>
		<br/>
		<?php echo JText::_('COM_NO_PACKAGES');?>
	</div>
	<?php
	echo JoomlaquizHelper::poweredByHTML();
	return;			
}
?>
<div class="contentpane">
	<h1 class="jq_packages_title"><?php echo JText::_('COM_SHOW_PACKAGES_TITLE'); ?></h1>
	<br />
	<div class="jq_packages_descr">
		<strong><?php echo JText::_('COM_SHOW_PACKAGES_DESCR'); ?></strong>
	</div>
	<br />
	<div class="jq_packages_container">
	<?php foreach($this->packages as $package) {
		if ($package->package_number > 1000000000)
			$package_number = sprintf("%06dm", $package->package_number - 1000000000);
		else 
			$package_number = sprintf("%06d", $package->package_number)
	?>
		<div class="jq_package_title_container">
			<h4 class="jq_package_title">
				<?php echo JText::_('COM_PACKAGE'). ": " . implode('; ', $package->products); ?>
			</h4>
			<small class="jq_package_info">
				<?php
				echo JText::_('COM_PACKAGES_NUMBER') . $package_number;
				echo ' (';
				echo JText::_('COM_PACKAGE_STATUS') . ((JText::_('COM_JOOMLAQUIZ_PACKAGE_'.strtoupper($package->order_status_name)) == 'COM_JOOMLAQUIZ_PACKAGE_'.strtoupper($package->order_status_name))?JText::_($package->order_status_name):JText::_('COM_JOOMLAQUIZ_PACKAGE_'.strtoupper($package->order_status_name))) . '; ';
				echo JText::_('COM_PACKAGE_STATUS_FROM') . $package->order_status_date;
				echo ')';
				?>
			</small>
		</div>
		<div class="jq_package_body_container">
			<?php
			if(strtoupper($package->order_status_code) != 'C' && strtoupper($package->order_status_code) != 'U') {
				echo "</div>";
				continue;
			}
			
			if($package->expired) { ?>
				<h3><?php echo JText::_('COM_PACKAGE_EXPIRED'); ?></h3>						
				<?php echo JText::_('COM_PACKAGE_RENEW_DESCR'); ?>	 
				</div> <?php
				continue;
			}
			
			if(!empty($package->bought_quizzes)) { ?>
				<br />
				<strong style="text-transform:uppercase"><?php echo JText::_('COM_JQ_QUIZZES') ?></strong>
				<br />
				<br />
				<div class="jq_package_quizzes">
				<?php
				foreach($package->bought_quizzes as $b_quizz) {
					?>
					<strong>
						<?php if ($b_quizz->expired) { 
							echo $b_quizz->quiz->c_title; 
						} else {?>
							<a href="<?php echo JRoute::_("index.php?option=com_joomlaquiz&view=quiz&vm=".$package->vm."&package_id=".$package->package_number."&rel_id=".$b_quizz->rel_id.JoomlaquizHelper::JQ_GetItemId());?>"><?php echo $b_quizz->quiz->c_title;?></a>
						<?php } ?>
					</strong><br />
														
					<small><?php echo $b_quizz->suffix; ?></small>
					<br />
					<?php echo $b_quizz->quiz->c_short_description;?><br />							
				<?php
				}
				?>
				</div>
			<?php
			}
			?>
	
			<?php
			if(!empty($package->lpaths)) { ?>
				<br />
				<strong style="text-transform:uppercase"><?php echo JText::_('COM_QUIZ_LPATHS'); ?></strong>
				<br />
				<br />					
				<div class="jq_package_lpaths">
					<?php
					foreach($package->lpaths as $lpath) {
						?>
						<strong>
							<?php if ($lpath->expired) { 
								echo $lpath->title;
							} else { ?>
								<a href="<?php echo JRoute::_("index.php?option=com_joomlaquiz&view=lpath&vm=".$package->vm."&package_id=".$package->package_number."&rel_id=".$lpath->id.JoomlaquizHelper::JQ_GetItemId());?>"><?php echo $lpath->title;?></a>
							<?php } ?>
						</strong><br />

						<small><?php echo $lpath->suffix; ?></small>
						<br />
						<?php echo $lpath->short_descr;?><br />
						<?php
					}
					?>
				</div>
			<?php
			}
		?></div><?php
	}
	?>
	
	</div>

</div>
<?php } 
	echo JoomlaquizHelper::poweredByHTML();
?>