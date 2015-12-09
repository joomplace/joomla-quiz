<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
JHtml::_('bootstrap.tooltip');
$extension = 'com_joomlaquiz'; 
jimport('joomla.filesystem.path');

$css_path = JPATH_SITE . '/components/com_joomlaquiz/views/templates/tmpl/' . $this->template . '/css/jq_template.css';
?>
<style>
	textarea.inputbox:focus
	{
		background-color:#FFFFDD;
	}
	
	label
	{
		display:inline;
	}
</style>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=templates&layout=edit_css&cid='.$this->cid); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container" class="span10">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td valign="top">
					<form action="index.php" method="post" name="adminForm"  id="adminForm">
					<table cellpadding="1" cellspacing="1" border="0" width="100%">
					<tr>
						<td width="280"><table class="adminheading"><tr><th class="templates"><?php echo JText::_('COM_JOOMLAQUIZ_TEMPLATE_CSSEDITOR');?></th></tr></table></td>
						<td width="260">
							<span class="componentheading"><?php echo JText::_('COM_JOOMLAQUIZ_IP_TEMPLATE');?>
							<b><?php echo is_writable($css_path) ? '<font color="green"> Writable</font>' : '<font color="red"> Unwritable</font>' ?></b>
							</span>
						</td>
						<?php
						if (JPath::canChmod($css_path)) {
							if (is_writable($css_path)) {
						?>
						<td>
							<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
							<label for="disable_write"><?php echo JText::_('COM_JOOMLAQUIZ_MAKE_UNWRITEABLE_AFTER');?></label>
						</td>
						<?php
							} else {
						?>
						<td>
							<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
							<label for="enable_write"><?php echo JText::_('COM_JOOMLAQUIZ_OVERRIDE_WRITE_PROTECTION');?></label>
						</td>
						<?php
							}
						}
						?>
					</tr>
					</table>
					<table class="adminform">
						<tr><th><?php echo $css_path; ?></th></tr>
						<tr><td><textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $this->content; ?></textarea></td></tr>
					</table>
					<input type="hidden" name="template" value="<?php echo $this->cid; ?>" />
					</form>
				</td>
			</tr>
		</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>