<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$user = JFactory::getUser();
$userId = $user->get('id');
$extension = 'com_joomlaquiz';

$sortFields = $this->getSortFields();
if (extension_loaded ('mbstring')) {
    $enable_mod_mb_string = 'load';
}
?>
<script type="text/javascript">
    Joomla.orderTable = function () {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>
<?php echo $this->loadTemplate('menu'); ?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=quizzes'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <div id="filter-bar" class="btn-toolbar">
                <div class="filter-search btn-group pull-left">
                    <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_JOOMLAQUIZ_FILTER_SEARCH_DESC'); ?></label>
                    <input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_JOOMLAQUIZ_FILTER_SEARCH_DESC'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_JOOMLAQUIZ_FILTER_SEARCH_DESC'); ?>" />
                </div>
                <div class="btn-group pull-left hidden-phone">
                    <button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value = '';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
                </div>
                <div class="btn-group pull-right hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>
                <div class="btn-group pull-right hidden-phone">
                    <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
                    <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
                        <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                        <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
                    </select>
                </div>
                <div class="btn-group pull-right">
                    <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
                    <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
                        <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
                    </select>
                </div>

            </div>
            <div class="clearfix"> </div>
            <table class="table table-striped" id="quizzesList">
                <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone">
                            #
                        </th>
                        <th width="1%" class="nowrap center hidden-phone">
                            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                        </th>
                        <th width="25%">
                            <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_NAME', 'c_title', $listDirn, $listOrder); ?>
                        </th>
                        <th width="5%">
                            <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'published', $listDirn, $listOrder); ?>
                        </th>
                        <th width="20%">
                            <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_CATEGORY', 'c_category', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%">
                            <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_AUTHOR', 'c_author', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%">
                            <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_TOTAL_SCORE', 'c_full_score', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%">
                            <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_PASSING_SCORE', 'c_passing_score', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%">
                            <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_TIME_LIMIT', 'c_time_limit', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%">
                            <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_CREATED_ON', 'c_created_time', $listDirn, $listOrder); ?>
                        </th>
                        <th width="5%">
                            <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_ID', 'c_id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="11">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    if (sizeof($this->items)) {
                        foreach ($this->items as $i => $item) :
                            $ordering = ($listOrder == 'ordering');
                            $canEdit = $user->authorise('core.edit', $extension . '.quizzes.' . $item->c_id);
                            $canCheckin = $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                            $canChange = $user->authorise('core.edit.state', $extension . '.quizzes.' . $item->c_id) && $canCheckin;

                            ?>
                            <tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
                                <td class="order nowrap center">
                                    <span class="sortable-handler inactive" >
                                        <i class="icon-menu"><?php echo ($i + 1); ?></i>
                                    </span>
                                    <input type="text" style="display:none" name="c_id" size="5"
                                           value="<?php echo $item->c_id; ?>" class="width-20 text-area-order " />
                                </td>
                                <td class="center">
                                    <?php echo JHtml::_('grid.id', $i, $item->c_id); ?>
                                </td>
                                <td class="has-context">
                                    <div>
                                        <?php
                                        $quiz = strip_tags($item->c_title);
                                        $length = 40;
                                        $dots = "...";
                                        $spacebar = ' ';
                                        $length_quiz = (!empty($enable_mod_mb_string)) ? mb_strlen($quiz) : strlen($quiz);
                                        if ($length_quiz > $length) {
                                            $part = (!empty($enable_mod_mb_string)) ? mb_substr($quiz, 0, $length) : substr($quiz, 0, $length);
                                            $pos = strrpos($part, $spacebar);
                                            if ($pos === false) {
                                                $part = (!empty($enable_mod_mb_string)) ? mb_substr($part, 0, $length) : substr($part, 0, $length);
                                            } else {
                                                $part = (!empty($enable_mod_mb_string)) ? mb_substr($part, 0, $pos) : substr($part, 0, $pos);
                                            }
                                            $quiz = $part . $dots;
                                        }

                                        if ($canEdit) :

                                            ?>
                                            <a title="<?php echo $this->escape($item->c_title); ?>" href="<?php echo JRoute::_('index.php?option=com_joomlaquiz&task=quiz.edit&c_id=' . $item->c_id); ?>"><?php echo $this->escape($quiz); ?></a>
                                            <a href="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=questions&quiz_id=' . $item->c_id); ?>"><?php echo JText::_('COM_JOOMLAQUIZ_VIEW_QUESTIONS'); ?></a>
                                        <?php else : ?>
                                            <a title="<?php echo $this->escape($item->c_title); ?>"><?php echo $this->escape($quiz); ?>
                                            <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'quizzes.', $canChange); ?>
                                </td>
                                <td class="has-context">
                                    <div>
                                        <?php echo $item->c_category; ?>
                                    </div>
                                </td>
                                <td class="has-context">
                                    <div>
                                        <?php echo $item->c_author; ?>
                                    </div>
                                </td>
                                <td class="has-context">
                                    <div>
                                        <?php echo $item->c_full_score; ?>
                                    </div>
                                </td>
                                <td class="has-context">
                                    <div>
                                        <?php echo $item->c_passing_score; ?>
                                    </div>
                                </td>
                                <td class="has-context">
                                    <div>
                                        <?php echo $item->c_time_limit; ?>
                                    </div>
                                </td>
                                <td class="has-context">
                                    <div>
                                        <?php echo $item->c_created_time; ?>
                                    </div>
                                </td>
                                <td class="has-context">
                                    <?php echo $item->c_id; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="11" align="center" >
                                <?php echo JText::sprintf('COM_JOOMLAQUIZ_NOQUIZZESCREATED'); ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_joomlaquiz&task=quiz.add'); ?>" >
                                    <?php echo JText::_('COM_JOOMLAQUIZ_CREATEANEWONE'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
            <?php echo JHtml::_('form.token'); ?>

        </div>
</form>
<?php if ($this->messageTrigger) { ?>
    <div id="notification" class="jqd-survey-wrap clearfix" style="clear: both">
        <div class="jqd-survey">
            <span><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES1"); ?><a onclick="jq_dateAjaxRef()" style="cursor: pointer" rel="nofollow" target="_blank"><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES2"); ?></a><?php echo JText::_("COM_JOOMLAQUIZ_NOTIFICMES3"); ?><i id="close-icon" class="icon-remove" onclick="jq_dateAjaxIcon()"></i></span>
        </div>
    </div>
<?php } ?>