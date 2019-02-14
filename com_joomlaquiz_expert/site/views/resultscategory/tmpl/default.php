<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die();

JHtml::_('script', 'system/core.js', true, true);

$my = JFactory::getUser();
$isAdim = $my->authorise('core.managefe','com_joomlaquiz') ? 1 : 0;
 
$tag = JFactory::getLanguage()->getTag();
$lang = JFactory::getLanguage();
$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);
$Itemid = JFactory::getApplication()->input->getInt('Itemid', 0);
$document 	= JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_standard/css/jq_template.css');		

$rows = (isset($this->results[0])) ? $this->results[0] : null;
$pagination = (isset($this->results[1])) ? $this->results[1] : null;

$category_id = JFactory::getApplication()->input->getInt('cat_id', 0);
$return_category = $category_id ? '&rc='.$category_id : '';

$database = JFactory::getDBO();
$share_id = JFactory::getApplication()->input->get('share_id', '');
$is_share = false;
if($share_id != ''){
	$database->setQuery("SELECT COUNT(id) FROM `#__quiz_r_student_share` WHERE `id` = '".$share_id."'");
	$is_share = $database->loadResult();
}

if(!$my->id && !$is_share) {
	echo JText::_('COM_RESULTS_FOR_REGISTERED');
} elseif(empty($rows)) {
	?>
	<div class="contentpane joomlaquiz">
		<h1 class="componentheading"><?php echo JText::_('COM_JOOMLAQUIZ_VIEW_RESULTSCATEGORY_TITLE') . $this->categoryname; ?></h1>
		<br/>
		<?php
			echo JText::_('COM_JOOMLAQUIZ_VIEW_RESULTSCATEGORY_NO_RESULTS');
			echo JoomlaquizHelper::poweredByHTML();
		?>
	</div>
	<?php
} else {
?>
<style>
.limit{
	position: relative !important;
}
</style>
<div class="contentpane">
	<h1 class="jq_results_title"><?php echo JText::_('COM_JOOMLAQUIZ_VIEW_RESULTSCATEGORY_TITLE') . $this->categoryname; ?></h1>
	<br />
	<div class="jq_results_descr">
		<strong><?php echo JText::_('COM_JOOMLAQUIZ_VIEW_RESULTSCATEGORY_RESULTS_DESCR'); ?></strong>
	</div>
	<br />
    <div class="jq_results_container">
        <form name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=resultscategory'.JoomlaquizHelper::JQ_GetItemId());?>" method="post">
            <table class="jq_results_container_table table-striped" cellpadding="10" cellspacing="10" border="0" width="100%">
                <tr>
                    <td class="sectiontableheader">#</td>
                    <?php if($isAdim): ?>
                    <td class="sectiontableheader"><?php echo JText::_('COM_QUIZ_MAIL_MESSAGE_USER'); ?></td>
                    <?php endif; ?>
                    <td class="sectiontableheader"><?php echo JText::_('COM_JQ_QUIZ'); ?></td>
                    <td class="sectiontableheader"><?php echo JText::_('COM_JQ_DATE_TIME'); ?></td>
                    <?php if($isAdim): ?>
                    <td class="sectiontableheader"><?php echo JText::_('COM_QUIZ_RES_MES_SCORE2'); ?></td>
                    <?php else: ?>
                    <td class="sectiontableheader"><?php echo JText::_('COM_JQ_YOUR_SCORE'); ?></td>
                    <td class="sectiontableheader"><?php echo JText::_('COM_JQ_PASS_SCORE'); ?></td>
                    <td class="sectiontableheader"><?php echo JText::_('COM_JQ_MAX_SCORE'); ?></td>
                    <?php endif; ?>
                    <td class="sectiontableheader"><?php echo JText::_('COM_JQ_PASSED'); ?></td>
                    <td class="sectiontableheader"><?php echo JText::_('COM_JQ_SPEND_TIME'); ?></td>
                    <td class="sectiontableheader"><?php echo JText::_('COM_JQ_CERTIFICATE'); ?></td>
                </tr>

                <?php
                $k = 1;
                foreach($rows as $i=>$row){
                    $link 	= JRoute::_("index.php?option=com_joomlaquiz&task=results.sturesult&id=".$row->id.$return_category.JoomlaquizHelper::JQ_GetItemId());

                    $img_passed	= $row->c_passed ? 'result_panel_true.png' : 'result_panel_false.png';
                    if(!$row->c_finished){
                        $img_passed = 'incomplete_icon_16.png';
                    }
                    $alt_passed = $row->c_passed ? JText::_('COM_JQ_RESULT_PASSED') : JText::_('COM_JQ_RESULT_FAILED');
                    ?>
                    <tr class="sectiontableentry<?php echo $k; ?>">
                        <td align="center"><?php echo ( $pagination->limitstart + $i + 1 ); ?></td>
                        <?php if($isAdim): ?>
                        <td align="left">
                            <?php if ($row->name) { ?>
                                <span title="<?php echo JFactory::getUser($row->c_student_id)->username; ?>" alt="<?php echo JFactory::getUser($row->c_student_id)->username; ?>" style="cursor: help;"><?php echo JFactory::getUser($row->c_student_id)->name; ?></span>
                            <?php } else {  ?>
                                <span title="<?php echo $row->user_surname; ?>" alt="<?php echo $row->user_surname; ?>" style="cursor: help;"><?php echo $row->user_name; ?></span>
                            <?php } ?>
                        </td>
                        <?php endif; ?>
                        <td align="left">
                            <a href="<?php echo JRoute::_($link); ?>">
                                <?php echo $row->c_title; ?>
                            </a>
                        </td>
                        <td align="left">
                            <?php echo $row->c_date_time; ?>
                        </td>
                        <td align="left">
                            <?php if ($row->c_passed == -1)	 { echo JText::_('COM_JQ_SCORE_PENDING'); } else {?>
                                <?php echo number_format($row->user_score, 2, '.', ' '); ?>
                            <?php }?>
                        </td>
                        <?php if(!$isAdim): ?>
                        <td align="left">
                            <?php
                            if ($row->c_passing_score) {
                                $passed_score = ceil(($row->c_full_score * $row->c_passing_score) / 100);
                                echo $passed_score . (strlen($row->c_passing_score)?(" (".$row->c_passing_score."%)"):'');
                            } else {
                                echo JText::_('COM_JQ_NA');
                            }
                            ?>
                        </td>
                        <td align="left">
                            <?php echo $row->c_full_score; ?>
                        </td>
                        <?php endif; ?>
                        <td align="center">
                            <?php if ($row->c_passed == -1)	 { ?><strong>?</strong><?php } else {?>
                                <img src="<?php echo JURI::root();?>components/com_joomlaquiz/assets/images/<?php echo $img_passed;?>" border="0" alt="<?php echo $alt_passed; ?>" />
                            <?php }?>
                        </td>
                        <td align="left">
                            <?php
                            $tot_min = floor($row->c_total_time / 60);
                            $tot_sec = $row->c_total_time - $tot_min*60;
                            echo str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT);
                            ?>
                        </td>
                        <td align="center">
                            <?php if($row->c_certificate && $row->c_passed):?>
                                <a onclick="window.open ('<?php echo JRoute::_("index.php?option=com_joomlaquiz&task=printcert.get_certificate&stu_quiz_id=".$row->id.".&user_unique_id=".$row->unique_id); ?>','blank');" href="javascript:void(0)"><?php echo JText::_('COM_JOOMLAQUIZ_DOWNLOAD');?></a>
                            <?php endif;?>
                        </td>
                    </tr>
                    <?php
                    $k = 3 - $k;
                }?>
                <tfoot>
                <tr>
                    <td colspan="<?php echo $isAdim ? '9' : '8' ?>"><?php echo $pagination->getListFooter(); ?></td>
                </tr>
                </tfoot>
            </table>
            <input type="hidden" name="option" value="com_joomlaquiz" />
            <input type="hidden" name="view" value="resultscategory" />
            <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
        </form>
    </div>
</div>
<?php 
} 
?>