<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
 
JHtml::_('behavior.tooltip');
?>
<style>
	select, input[type="file"] {
		height: 20px;
	}
</style>
<?php echo $this->loadTemplate('menu');?>
<div id="j-main-container" class="span10">
					<table width="100%" style="background-color: #F7F8F9; border: solid 1px #d5d5d5; width: 100%; padding: 10px; border-collapse: collapse;">
						<tr>
							<td style="text-align:left; font-size:14px; font-weight:400; line-height:18px " colspan="2"><strong><?php echo JText::_('COM_JOOMLAQUIZ')?></strong> <?php echo JText::_('COM_JOOMLAQUIZ_COMPONENT_FOR_JOOMLA');?><?php echo JText::_('COM_JOOMLAQUIZ')?>  <a href="http://www.JoomPlace.com"></strong> <?php echo JText::_('COM_JOOMLAQUIZ_JOOMOLACE');?></a>.</td>
						</tr>
						<tr>
							<td width="120" bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;"><?php echo JText::_('COM_JOOMLAQUIZ_INSTALLED_VERSION');?></td>

							<td bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;"> &nbsp;<b><?php echo $this->version;?></b></td>
						 </tr>
						 <tr>
							<td bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;"><?php echo JText::_('COM_JOOMLAQUIZ_LATEST_VERSION');?></td>
							<td style="border: solid 1px #d5d5d5;" id="jq_LatestVersion">
								<a href="javascript:void(0);" onclick="return jq_CheckVersion();" class="update_link">
									Check now
								</a>
							</td>
						 </tr>
						 <tr>
							<td valign="top" bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;"><?php echo JText::_('COM_JOOMLAQUIZ_ABOUT_ABOUT');?></td>

							<td bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;">
							This component manages Quizzes and Quiz Clients.
							</td>
						</tr>						
						<tr>
							<td bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;">Support forum:</td>
							<td bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;">
							<a target="_blank" href="http://www.joomplace.com/support">http://www.JoomPlace.com/support</a>
							</td>
						</tr>
						<tr>
							<td bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;">Disclaimer/License:</td>
							<td bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;">
							<a target="_blank" href="http://www.joomplace.com/disclaimer.html">http://www.JoomPlace.com/disclaimer.html</a>
							</td>
						</tr>
					</table> 
					<br/>
					
					<table border="1" cellpadding="5" width="100%" style="background-color: #F7F8F9; border: solid 1px #d5d5d5; width: 100%; padding: 10px; border-collapse: collapse;">						
						<tr>
							<td colspan="2" style="background-color: #e7e8e9;text-align:left; font-size:16px; font-weight:400; line-height:18px "><strong><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/tick.png">Say your "Thank you" to Joomla community for WonderFull Joomla CMS</strong></td>
						</tr>
						<tr>
							<td colspan="2" style="padding-left:20px">			
							<div style="float:left; width:720px;">
							<p style="font-size:12px; font-weight:800;">Say your "Thank you" to Joomla community for WonderFull Joomla CMS and <span style="font-size:14pt;font-weight:bold">help it</span> by sharing your experience with this component. It will only take 1 min for registration on <a href="http://extensions.joomla.org/extensions/vertical-markets/education-a-culture/quiz/11302" target="_blank">http://extensions.joomla.org/</a> and 3 minutes to write useful review! A lot of people will thank you!</p>
							</div>
							<div style="float:left;margin:5px">
							<a href="http://extensions.joomla.org/extensions/vertical-markets/education-a-culture/quiz/11302" target="_blank"><img src="<?php echo JURI::root();?>administrator/components/com_joomlaquiz/assets/images/rate_us.png" title="Rate Us!" alt="Rate us at Extensions Joomla.org"  style="padding-top:5px;"/></a>	
							</div>
							
							<div style="clear:both; margin:5px; padding-top:5px;"><hr style="color:#CCCCCC;"/></div>
							<div style="float:left; width:680px;">
								<p style="font-size:12px; font-weight:800;">Alternatively here  is a quick way to rate US on largest Script website - just make your choice and Vote:</p>
							</div>
							<div style="float:left;display: block; width: 234px; height: 75px; background: transparent url(http://cdn.hotscripts.com/img/widgets/rt_234x60-1.gif) 0 0 no-repeat; font: normal 11px/12px Arial, Helvetica, sans-serif; color: #fff; text-align: left;"><form action="http://www.hotscripts.com/rate/73018/?RID=N578805" method="post" style="display: block; position: relative; left: 79px; margin: 0; padding: 8px 0 0; width: 153px; overflow: hidden; text-align: left;" target="_blank"><strong>Like our script?</strong> Rate it at <a target="_blank" href="http://www.hotscripts.com/listing/joomlaquiz-make-a-quiz-in-a-minutes/?RID=N578805" style="color: #fff; text-decoration: none;" >PHP</a> > <a target="_blank" href="http://www.hotscripts.com/?RID=N578805" style="color: #fff; text-decoration: none;">Hot Scripts</a><br /><select name="rate" style="width: 98px; overflow: hidden; font: normal 11px/12px Arial, Helvetica, sans-serif; color: #000; float: left; margin: 5px 4px 0 0; padding: 0; clear: none;"><option value="5">Excellent</option><option value="4">Very Good</option><option value="3">Good</option><option value="2">Fair</option><option value="1">Poor</option></select><input type="image" src="http://cdn.hotscripts.com/img/widgets/btn_vote-3.gif" style="width: 49px; height: 22px; overflow: hidden; float: left; margin: 4px 0 0; clear: none; padding: 0; border: 0;" /></form></div>
							<div style="clear:both">
								<!--x-->
							</div>
							</td>
						</tr>	
						<tr>
							<td colspan="2" style="background-color: #e7e8e9;text-align:left; font-size:14px; font-weight:400; line-height:18px "><strong><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/tick.png">Joomplace news/campaigns</strong></td>
						</tr>
						<tr>
							<td colspan="2" style="padding-left:20px" align="justify"><div id="jq_LatestNews">
								<script type="text/javascript" language="javascript">
										<!--//--><![CDATA[//><!-- 
										jq_CheckNews(); 
										//--><!]]>
								</script>
							</div></td>
						</tr>					
					</table>
</div>
