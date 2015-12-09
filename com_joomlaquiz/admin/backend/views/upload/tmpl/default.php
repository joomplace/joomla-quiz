<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
 
$css = JFactory::getApplication()->input->get('t','khepri');
$directory = JFactory::getApplication()->input->get('directory');
$imgPath = JURI::root().'images/joomlaquiz/images/'.$this->filename;
?>
<script>
    function load(){
        var newOption = "<?php echo $this->filename?>";
        if(newOption!=""){
            var path = "<?php echo $imgPath?>";
            jQuery('#c_image',window.parent.document).append("<option value="+newOption+" selected='selected'>"+newOption+"</option>").hide().show();
            jQuery('#img_hotspot', window.parent.document).attr("src",path).hide().show();
            return true;
        }
    else return false;
    }
</script>
<body onload="load()">
<form method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&task=hotspot.uploadimage');?>" enctype="multipart/form-data" name="filename"  id="filename">
	<div><strong style="color:red;"><?php echo $this->message;?></strong></div>
	<table class="adminform">
		<tr>
			<th class="title" style="text-align: left">
				<?php echo JText::_('COM_JOOMLAQUIZ_FILE_UPLOAD');?> 
			</th>
		</tr>
		<tr>
			<td align="center">
				<input class="inputbox" name="userfile" type="file"/>
			</td>
		</tr>
		<tr>
			<td>
				<input class="button" type="submit" value="Upload" name="fileupload"/>
				<?php echo JText::_('COM_JOOMLAQUIZ_MAX_SIZE');?> <?php echo ini_get( 'post_max_size' );?>
			</td>
		</tr>
</table>
		
<input type="hidden" name="directory" value="<?php echo $directory;?>" />
<input type="hidden" name="t" value="<?php echo $css?>">
<input type="hidden" name="task" value="hotspot.uploadimage">
<input type="hidden" name="option" value="com_joomlaquiz">
<input type="hidden" name="tmpl" value="component">
</form>
</body>