<?php
/**
* Joomlaquiz component for Joomla 3.0
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
$imgpath = JURI::root().'/administrator/components/com_joomlaquiz/assets/images/';
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

function subfolding($r_url){
	$r_url = '/'.trim(str_replace(JUri::root(),'',$r_url),'/');
	$subfold = JUri::root(true);
	if($subfold){
        if(strpos('/' . $r_url, $subfold) === false){
			return $subfold.$r_url;
		}else{
			return $r_url;
		}
	}else{
		return $r_url;
	}
}
?>
<?php echo $this->loadTemplate('menu');?>
<div id="pgm_dashboard">
    <?php
    foreach($this->dashboardItems as $ditem) { ?>
        <div onclick="window.location ='<?php echo $ditem->url; ?>'" class="pgm-dashboard_button">
            <?php if ($ditem->icon) { ?>
                <img src="<?php echo subfolding($ditem->icon); ?>" class="pmg-dashboard_item_icon"/>
            <?php } ?>
            <?php echo '<div class="pgm-dashboard_button_text">'.$ditem->title.'</div>'?>
        </div>
   <?php } ?>
<div id="dashboard_items" ><a href="index.php?option=com_joomlaquiz&view=dashboard_items"><?php echo JText::_('COM_JOOMLAQUIZ_MANAGE_DASHBOARD_ITEMS');?></a></div>
</div>
<div id="pgm_collapse">
<div class="accordion" id="accordion2">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a style="text-decoration: underline !important;" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                About Joomla Quiz Deluxe
            </a>
        </div>
        <div id="collapseOne" class="accordion-body collapse in">
            <div class="accordion-inner">
                <table border="1" width="100%" class="about_table" >
                    <tr>
                        <th colspan="2" class="a_comptitle">
                            <strong><?php echo JText::_('COM_JOOMLAQUIZ'); ?></strong> component for Joomla! 3.0 Developed by
                            <a href="http://www.JoomPlace.com">JoomPlace</a>.
                        </th>
                    </tr>
                    <tr>
                        <td width="13%"  align="left">Installed version:</td>
                        <td align="left">&nbsp;<b><?php echo JoomlaquizHelper::getVersion();?></b>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="left">About:</td>
                        <td align="left"><?php echo JText::_('COM_JOOMLAQUIZ_ABOUT_TEXT'); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Support Helpdesk:</td>
                        <td align="left"><a target="_blank" href="http://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create">http://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create</a></td>
                    </tr>
                    </table>
            </div>
        </div>
    </div>
	<?php if($this->errors){ ?>
    <div class="accordion-group">
        <div class="accordion-heading" style="background: #DC0000;border-radius: 4px;">
            <a style="color: #FFF;font-weight: bold;text-decoration: none!important;" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseErrors">
                Databse Errors (<?php echo count($this->errors); ?>)
            </a>
        </div>
        <div id="collapseErrors" class="accordion-body collapse">
            <div class="accordion-inner">
				<div style="padding: 15px;">
				<?php foreach ($this->errors as $line => $error) : ?>
					<?php $key = 'COM_INSTALLER_MSG_DATABASE_' . $error->queryType;
					$msgs = $error->msgElements;
					$file = basename($error->file);
					$msg0 = (isset($msgs[0])) ? $msgs[0] : ' ';
					$msg1 = (isset($msgs[1])) ? $msgs[1] : ' ';
					$msg2 = (isset($msgs[2])) ? $msgs[2] : ' ';
					$message = JText::sprintf($key, $file, $msg0, $msg1, $msg2); ?>
					<p><?php echo $message; ?></p>
				<?php endforeach; ?>
				</div>
            </div>
        </div>
    </div>
	<?php } ?>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a style="text-decoration: underline !important" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                <?php echo JText::_("COM_JOOMLAQUIZ_ABOUT_SAYTHANKSTITLE"); ?>
            </a>
        </div>
        <div id="collapseTwo" class="accordion-body collapse">
            <div class="accordion-inner">
                <div class="thank_fdiv" style="font-size:12px;margin-left: 4px;">
                    <?php echo JText::_("COM_JOOMLAQUIZ_ABOUT_SAYTHANKS1"); ?>
                    <a href="http://extensions.joomla.org/extensions/vertical-markets/education-a-culture/quiz/11302" target="_blank">http://extensions.joomla.org/</a>
                    <?php echo JText::_("COM_JOOMLAQUIZ_ABOUT_SAYTHANKS2"); ?>
                </div>
                <div style="float:right; margin:3px 5px 5px 5px;">
                    <a href="http://extensions.joomla.org/extensions/vertical-markets/education-a-culture/quiz/11302" target="_blank">
                        <img src="http://www.joomplace.com/components/com_jparea/assets/images/rate-2.png" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=dashboard');?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php if ($this->messageTrigger) { ?>
<div id="notification" class="jqd-survey-wrap clearfix" style="clear: both">
    <div class="jqd-survey">
        <span><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES1"); ?><a onclick="jq_dateAjaxRef()" style="cursor: pointer" rel="nofollow" target="_blank"><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES2"); ?></a><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES3"); ?><i id="close-icon" class="icon-remove" onclick="jq_dateAjaxIcon()"></i></span>
    </div>
</div>
<?php } ?>