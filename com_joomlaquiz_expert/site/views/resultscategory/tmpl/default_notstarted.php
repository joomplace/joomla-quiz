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

$app= JFactory::getApplication();
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

//filter
$javascript = 'onchange="document.adminForm.submit();"';
$filter_start_state = array();
$filter_start_state[] = JHTML::_('select.option', '0', JText::_('COM_JOOMLAQUIZ_VIEW_RESULTSCATEGORY_OPTION_STARTED') );
$filter_start_state[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_VIEW_RESULTSCATEGORY_OPTION_NOTSTARTED') );
$filter_start_state_output = JHTML::_('select.genericlist', $filter_start_state, 'filter_start_state', 'class="text_area" style="max-width: 300px;" size="1" '. $javascript, 'value', 'text', $this->escape($this->state->get('filter.startstate', 0)) );

if(empty($rows)) { ?>
	<div class="contentpane joomlaquiz">
		<h1 class="componentheading"><?php echo JText::_('COM_JOOMLAQUIZ_VIEW_RESULTSCATEGORY_TITLE') . $this->categoryname; ?></h1>
		<br/>
        <form name="adminForm" id="adminForm" action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post">
            <?php echo $filter_start_state_output; ?>
            <input type="hidden" name="option" value="com_joomlaquiz" />
            <input type="hidden" name="view" value="resultscategory" />
            <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
        </form>
        <?php echo JText::_('COM_JOOMLAQUIZ_VIEW_RESULTSCATEGORY_NO_RESULTS'); ?>
        <?php echo JoomlaquizHelper::poweredByHTML(); ?>
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
    <div class="jq_results_container">
        <form name="adminForm" id="adminForm" action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post">
            <?php echo $filter_start_state_output; ?>
            <table class="jq_results_container_table table-striped" cellpadding="10" cellspacing="10" border="0" width="100%">
                <tr>
                    <td class="sectiontableheader" width="10%" align="center">#</td>
                    <?php if($isAdim): ?>
                    <td class="sectiontableheader" width="30%"><?php echo JText::_('COM_QUIZ_MAIL_MESSAGE_USER'); ?></td>
                    <?php endif; ?>
                    <td class="sectiontableheader"><?php echo JText::_('COM_JQ_QUIZ'); ?></td>
                </tr>
                <?php
                $k = 1;
                foreach($rows as $i=>$row){
                    $link 	= JRoute::_("index.php?option=com_joomlaquiz&view=quiz&quiz_id=".$row->quiz_id.JoomlaquizHelper::JQ_GetItemId());
                    ?>
                    <tr class="sectiontableentry<?php echo $k; ?>">
                        <td align="center"><?php echo ( $pagination->limitstart + $i + 1 ); ?></td>
                        <?php if($isAdim): ?>
                        <td align="left"><?php echo $row->user_name; ?></td>
                        <?php endif; ?>
                        <td align="left">
                            <a href="<?php echo $link; ?>">
                                <?php echo $row->c_title; ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                    $k = 3 - $k;
                }?>
                <tfoot>
                <tr>
                    <td colspan="<?php echo $isAdim ? '3' : '2' ?>"><?php echo $pagination->getListFooter(); ?></td>
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