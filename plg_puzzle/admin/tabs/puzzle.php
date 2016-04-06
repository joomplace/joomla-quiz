 <div class="tab-pane" id="question-image">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_IMAGE')?></legend>
			<table class="admintable" style="margin-top:2px; margin-bottom:2px; ">
				<tr><td><span style="padding-left:20px ">
					<?php echo JText::_('COM_JOOMLAQUIZ_THE_IMAGE_WIDTH_CANNOT_BE');?>
					</span><br />
					<span style="padding-left:20px ">
					<?php echo JText::_('COM_JOOMLAQUIZ_TAFTER_SELECTING_AN_IMAGE');?>
					</span><br />
					<span style="padding-left:20px ">
					<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_NOTE_THAT_INSOME_JOOMLA');?>
					</span>
				</td></tr>
			</table>
			<table width="100%" class="adminform" style="margin-top:15px;">
				<tr>
					<td><?php echo JText::_('COM_JOOMLAQUIZ_IMAGE');?></td>
					<td>
					<?php $directory = 'joomlaquiz';
					$mainframe = JFactory::getApplication();
					$cur_template = $mainframe->getTemplate();
					if(isset($row->c_image) && $row->c_image != '' && file_exists(JPATH_SITE.'/images/joomlaquiz/images/'.$row->c_image)){
						list($c_image_width, $c_image_height) = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$row->c_image);
					}
					?>
						<table cellpadding="0" cellspacing="0" border="0"><tr><td>
						<?php echo $lists['images']?></td><td><a href="#" onclick="window.open('index.php?no_html=1&option=com_joomlaquiz&amp;task=hotspot.uploadimage&amp;directory=<?php echo $directory; ?>&amp;t=<?php echo $cur_template; ?>&tmpl=component','win1','width=450,height=200');" style="margin-left:15px;"><?php echo JText::_('COM_JOOMLAQUIZ_UPLOAD_NEWIMAGE');?></a>
						</td></tr></table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php $imgPath = (isset($row->c_image)) ? JURI::root().'images/joomlaquiz/images/'.$row->c_image : JURI::base().'components/com_joomlaquiz/assets/images/blank.png'; ?>
						<img id="img_puzzle" src="<?php echo $imgPath;?>" name="imagelib">
					</td>
				</tr>
			</table>
	</fieldset>
</div>