<?php
/**
* JoomlaQuiz module for Joomla
* @version $Id: default.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage default.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access

defined('_JEXEC') or die;
?>
<div class="moduletable joomlaquiz_container qmodule">
<table width="95%" border="0" cellspacing="0" cellpadding="1" align="center">
<tr><td><b><?php echo JText::_('MOD_JOOMLAQUIZ_MOD_UNAME');?></b></td><td width="30%"><b><?php echo JText::_('MOD_JOOMLAQUIZ_MOD_QPOINTS');?></b></td></tr>
<?php
$sec_tbl = 1;
foreach ($result as $one_res) {
	if($user_profile && is_file(JPATH_SITE."/components/com_comprofiler/comprofiler.php")){
		$usr_d = '<a href="'.JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$one_res->id).'">'.($m_user_display?$one_res->name:$one_res->username).'</a>';
	}else{
		$usr_d = ($m_user_display?$one_res->name:$one_res->username);		
	}
	echo "<tr><td class='sectiontableentry".$sec_tbl.$moduleclass_sfx."'>".$usr_d."</td><td class='sectiontableentry".$sec_tbl.$moduleclass_sfx."'>".$one_res->c_total_score."</td></tr>";
	if ($sec_tbl == 1) $sec_tbl = 2;
	else $sec_tbl = 1;
}
if (!empty($result) && (count($result) == $v_content_count)) {
	echo "<tr><td class='sectiontableentry".$sec_tbl.$moduleclass_sfx."'>" . JText::_('MOD_JOOMLAQUIZ_MOD_SOON') . "</td><td class='sectiontableentry".$sec_tbl.$moduleclass_sfx."'>&nbsp;</td></tr>";
}
echo "</table></div>";
?>