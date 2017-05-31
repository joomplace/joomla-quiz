<?php
/**
 * JLMSCoordinators component for Joomla
 * @version $Id: install.joomlaquiz.php 2016-05-16 17:30:15
 * @package JLMSCoordinators
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->filter_order);
$listDirn      = $this->escape($this->filter_order_Dir);

function secondsToWords($seconds)
{
    /*** return value ***/
    $ret = "";

    /*** get the hours ***/
    $hours = intval(intval($seconds) / 3600);
    if($hours > 0)
    {
        $ret .= "$hours ".JText::_('JHOURS').' ';
    }
    /*** get the minutes ***/
    $minutes = bcmod((intval($seconds) / 60),60);
    if($minutes > 0)
    {
        $ret .= "$minutes ".JText::_('JMINUTES').' ';
    }
  
    /*** get the seconds ***/
    $seconds = bcmod(intval($seconds),60);
    if($seconds > 0)
    {
		$ret .= "$seconds ".JText::_('JSECONDS');
    }

    return $ret;
}

?>
<form action="index.php?option=com_joomlaquiz&view=quiz_statistic" method="post" id="adminForm" name="adminForm">
    
    <div class="row-fluid">
        <div class="span12">
            <?php
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)
            );
            ?>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th width="">
                <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_START_ID', 'id', $listDirn, $listOrder); ?>
            </th>
            <th width="8%">
                <?php  echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_QUIZ_TITLE', 'title', $listDirn, $listOrder); ?>
            </th>
            <th width="8%">
                <?php  echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_USER', 'u.username', $listDirn, $listOrder); ?>
            </th>
            <th width="7%">
                <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_TOTAL_SCORE', 'total_score', $listDirn, $listOrder); ?>
            </th>
            <th style="text-align: center;" width="40%">
                <?php  echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_PROGRESS', 'progress', $listDirn, $listOrder); ?>
            </th>
            <th style="text-align: center;" width="">
                <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_PROGRESS', 'progress', $listDirn, $listOrder); ?>
            </th>
            <th width="">
                <?php echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_RESPOND_AGO', 'respond_at', $listDirn, $listOrder); ?>
            </th>
            <th style="text-align: center;" width="7%">
                <?php  echo JHtml::_('grid.sort', 'COM_JOOMLAQUIZ_STARTED', 'start_at', $listDirn, $listOrder); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as $i => $row) : ?>
                <tr>
                    <td>
                        <?php echo $row->id; ?>
                    </td>
                    <td align="center">
                        <?php echo $row->title; ?>
                    </td>
                    <td>
                        <?php echo ($row->user_id)?(JFactory::getUser($row->user_id)->name.' ('.JFactory::getUser($row->user_id)->username.')'):'Anonymous'; ?>
                    </td>
                    <td>
                        <?php echo ($row->total_score ? number_format($row->total_score,2) : '0'); ?>
                    </td>
                    <td align="center">
						<div class="progress progress-striped active">
						  <div class="bar" style="width: <?php echo number_format($row->progress*100,0) ?>%;"></div>
						</div>
                    </td>
                    <td style="text-align: center;">
                        <?php echo ($row->passed ? $row->passed : '0' ).'/'.($row->total ? $row->total : '0'); ?>
                    </td>
                    <td>
                        <?php echo secondsToWords(time() - $row->respond_at); ?>
                    </td>
                    <td style="text-align: center;">
						<?php echo JHtml::_('date.relative',$row->start_at,null, JFactory::getDate()); ?>(<?php echo JHtml::_('date',$row->start_at,'H:i:s'); ?>)
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
<script>
jQuery(document).ready(function($){
	function refreshTable(){
		$.post($('#adminForm').attr('action'), $('#adminForm').serialize()).always(function(data) {
			console.log($(data).find('#adminForm table tbody').html());
			$('#adminForm table tbody').html($(data).find('#adminForm table tbody').html());
		});
	}
	setInterval(refreshTable,<?php echo JComponentHelper::getParams('com_joomlaquiz')->get('lttrack_up',1)?JComponentHelper::getParams('com_joomlaquiz')->get('lttrack_up',1):1; ?>000);
});
</script>