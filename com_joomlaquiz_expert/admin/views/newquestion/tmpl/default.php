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
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$option = 'com_joomlaquiz';
$app = JFactory::getApplication();
$new_qtype_id = intval( $app->getUserStateFromRequest( "new_qtype_id{$option}", 'new_qtype_id', 0 ) );
if (class_exists('JToolBar')) {
	$bar = JToolBar::getInstance('toolbar');
	// Add a cancel button
	$bar->appendButton( 'Standard', 'next', JText::_('COM_JOOMLAQUIZ_NEXT_BUTTON'), 'question.add', false, true );
	$bar->appendButton( 'Standard', 'cancel', JText::_('COM_JOOMLAQUIZ_CANCEL_BUTTON'), 'cancel', false, false ); 
}

$i = 0;
$countOfQuest = sizeof($this->questions);
?>
<script language="javascript" type="text/javascript" src="<?php echo JURI::root();?>administrator/components/com_joomlaquiz/assets/js/thickbox/thickbox.js" ></script>
<style type="text/css" >
	label { cursor:pointer;width: auto !important;}
	.btn-toolbar {float:right;}
</style>
<script>
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;
        var elem = document.getElementsByName('new_qtype_id');

        var flag = false;
        for(var i=0;i < elem.length;i++){
            if(elem[i].checked == true){
                flag = true;
            }
        }

        if(!flag && pressbutton != 'cancel'){
            alert('<?php echo $this->escape(JText::_('COM_JOOMLAQUIZ_CHOOSE_TYPE'));?>');
            return false;
        } else {

            if (pressbutton == 'cancel') {
                parent.tb_remove();
                return;
            }

            form.submit();
        }

    }

</script>
<form onsubmit="return false" action="index.php?option=com_joomlaquiz&view=question&layout=edit" method="post" name="adminForm" target="_parent" enctype="multipart/form-data">
	<fieldset class="adminform">
	<legend><?php echo JText::_('COM_JOOMLAQUIZ_SELECT_NEW_QUESTION');?></legend>
	<?php if (class_exists('JToolBar')) { echo $bar->render(); } ?>
		<table width="100%" cellpadding="2" cellspacing="2" class="admintable">
		<?php while($i < count($this->questions)):?>
			<tr>
				<td width="50%">
					<label for="new_qtype_id_<?php echo $this->questions[$i]->c_id?>"><input type="radio" onclick="isChecked(this.checked);" name="new_qtype_id" id="new_qtype_id_<?php echo $this->questions[$i]->c_id?>" value="<?php echo $this->questions[$i]->c_id?>" <?php echo ($new_qtype_id == $this->questions[$i]->c_id? ' checked="checked" ': '')?> />
						<?php echo JText::_('COM_JOOMLAQUIZ_'.preg_replace("/[^a-zA-Z0-9]/", "_", $this->questions[$i]->c_qtype));?>
					</label>
				</td>
				<td width="50%">
					<?php if(isset($this->questions[$i+1])):?>
					<label for="new_qtype_id_<?php echo $this->questions[$i+1]->c_id?>"><input type="radio" onclick="isChecked(this.checked);" name="new_qtype_id" id="new_qtype_id_<?php echo $this->questions[$i+1]->c_id?>" value="<?php echo $this->questions[$i+1]->c_id?>" <?php echo ($new_qtype_id == $this->questions[$i+1]->c_id? ' checked="checked" ': '')?> />
						<?php echo JText::_('COM_JOOMLAQUIZ_'.preg_replace("/[^a-zA-Z0-9]/", "_", $this->questions[$i+1]->c_qtype));?>
					</label>
					<?php endif;?>
				</td>
			</tr>
		<?php $i = $i + 2;?>
		<?php endwhile;?>
		</table>
	</fieldset>
			
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_joomlaquiz" />
	<input type="hidden" name="c_id" value="0" />
	<input type="hidden" name="task" value="" />
</form>