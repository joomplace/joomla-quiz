<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');

?>

<?php if($this->result['is_available']):?>

<?php endif; ?>

<?php if(!$this->result['is_available']):?>
	
	<?php if($this->result['error_code'] == '0001'):?>
		<p align="left"><?php echo JText::_('COM_QUIZ_NOT_AVAILABLE')?><br/>(Error code: 0001 - Template for quiz not found.)</p>
		<?php echo JoomlaquizHelper::poweredByHTML();?>
	<?php endif; ?>
	
	<?php if($this->result['error_code'] == '0002'):?>
		<p align="left"><?php echo JText::_('COM_QUIZ_NOT_AVAILABLE')?><br/>(Error code: 0002 - Quiz not found.)</p>
		<?php echo JoomlaquizHelper::poweredByHTML();?>
	<?php endif; ?>
	
	<?php if($this->result['error_code'] == '0003'):?>
		<p align="left"><?php echo JText::_('COM_QUIZ_NOT_AVAILABLE')?><br/>(Error code: 0003 - You have no permissions to preview this question.)</p>
		<?php echo JoomlaquizHelper::poweredByHTML();?>
	<?php endif; ?>
	
<?php endif; ?>