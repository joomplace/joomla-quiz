<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted Access');

$lpath_list = $this->lpath_data;
$document 	= JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/assets/css/joomlaquiz.css');

?>
<div class="container-fluid">

	<?php if ($lpath_list != false) { ?>
	<div class="row lp-link-row">
		<div class="span12">
			<h2><?= JFactory::getApplication()->getParams()->get('page_heading',JText::_('COM_JOOMLAQUIZ_LPS_MENU_ITEM')); ?></h2>
		</div>
	</div>
	<?php } ?>

    <?php if(!empty($lpath_list)){ ?>
        <?php foreach($lpath_list as $k=>$lpath_item) { ?>
            <div class="row lp-link-row">
                <div class="span12 lp-link">
                    <a href="<?= JRoute::_('index.php?option=com_joomlaquiz&view=lpath&lpath_id='.$lpath_item->id) ?>"><h2><?= $lpath_item->title?></h2></a>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
	
</div>
<?php
	echo JoomlaquizHelper::poweredByHTML();
?>
