<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
$my = JFactory::getUser();
 
$tag = JFactory::getLanguage()->getTag();
$lang = JFactory::getLanguage();
$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);

$document 	= JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_standard/css/jq_template.css');		

$rows = (isset($this->results[0])) ? $this->results[0] : null;
$pagination = (isset($this->results[1])) ? $this->results[1] : null;

$database = JFactory::getDBO();
$share_id = JFactory::getApplication()->input->get('share_id', '');
$is_share = false;
if($share_id != ''){
	$database->setQuery("SELECT COUNT(id) FROM `#__quiz_r_student_share` WHERE `id` = '".$share_id."'");
	$is_share = $database->loadResult();
}

//filter
$javascript = 'onchange="document.adminForm.submit();"';
$filter_start_state = array();
$filter_start_state[] = JHTML::_('select.option', '0', JText::_('COM_JOOMLAQUIZ_VIEW_RESULTS_OPTION_STARTED') );
$filter_start_state[] = JHTML::_('select.option', '-1', JText::_('COM_JOOMLAQUIZ_VIEW_RESULTS_OPTION_NOTSTARTED') );
$filter_start_state_output = JHTML::_('select.genericlist', $filter_start_state, 'filter_quizstart_state', 'class="text_area" style="max-width: 300px;" size="1" '. $javascript, 'value', 'text', $this->escape($this->state->get('filter.quizstartstate', 0)) );

if(!$my->id && !$is_share) {
	echo JText::_('COM_RESULTS_FOR_REGISTERED');
} elseif(empty($rows)) {
	?>
	<div class="contentpane joomlaquiz">
		<h1 class="componentheading"><?php echo JText::_('COM_SHOW_RESULTS_TITLE'); ?></h1>
		<br/>
        <form name="adminForm" id="adminForm" action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post">
            <?php echo $filter_start_state_output; ?>
            <input type="hidden" name="option" value="com_joomlaquiz" />
            <input type="hidden" name="view" value="results" />
            <input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->getInt('Itemid', 0); ?>" />
        </form>
        <?php echo JText::_('COM_NO_RESULTS'); ?>
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
	<h1 class="jq_results_title"><?php echo JText::_('COM_SHOW_RESULTS_TITLE'); ?></h1>
	<br />
	<div class="jq_results_descr">
		<strong><?php echo JText::_('COM_SHOW_RESULTS_DESCR'); ?></strong>
	</div>
	<br />
	<?php

	$layout = new JLayoutFile('joomlaquiz.results');
	$layout->setComponent('com_joomlaquiz');
	$html = $layout->sublayout($my->authorise('core.managefe','com_joomlaquiz')?'manager':'user',(object)array('items'=>$rows,'pagination'=>$pagination,'filter'=>$filter_start_state_output));
	if(!$html) $html = $layout->render((object)array('items'=>$rows,'pagination'=>$pagination,'filter'=>$filter_start_state_output));
	echo $html;
	
	?>
</div>
<?php 
} 
?>