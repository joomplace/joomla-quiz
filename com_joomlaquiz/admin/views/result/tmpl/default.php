<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 *
 * @package   Joomlaquiz Deluxe
 * @author    JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$extension = 'com_joomlaquiz';

$db    = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select($db->qn('type.c_type', 'type'))
    ->select($db->qn('quest') . '.*')
    ->select($db->qn('answer.c_id', 'answer'))
    ->select($db->qn('answer.c_stu_quiz_id'))
    ->from($db->qn('#__quiz_r_student_question', 'answer'))
    ->where($db->qn('answer.c_id') . ' = ' . $db->q($this->cid))
    ->leftJoin($db->qn('#__quiz_t_question', 'quest') . ' ON '
        . $db->qn('quest.c_id') . ' = ' . $db->qn('answer.c_question_id'))
    ->leftJoin($db->qn('#__quiz_t_qtypes', 'type') . ' ON '
        . $db->qn('quest.c_type') . ' = ' . $db->qn('type.c_id'));
$quest_data = $db->setQuery($query)->loadObject();

?>
<?php echo $this->loadTemplate('menu'); ?>
<form action="<?php echo JRoute::_('index.php?option=com_joomlaquiz&view=result&cid='
    . $this->cid); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container" class="span12">
        <?php
        if ($html = JLayoutHelper::render('question.report.display',
            $quest_data,
            JPATH_SITE . '/plugins/joomlaquiz/' . $quest_data->type . '/')
        ) {
            echo $html;
        } else {
            echo $this->report_html;
            ?>
            <div>
                <table class="table table-striped">
                    <tr>
                        <td><?php echo $this->lists['question'] ?></td>
                    </tr>
                </table>
            </div>
            <?php
        }
        ?>
        <div>
            <table class="table table-striped">
                <tr>
                    <td align="left"><?php echo JText::_('COM_JOOMLAQUIZ_USERNAME'); ?></td>
                    <td><?php echo $this->lists['user']->username ?></td>
                </tr>
                <tr>
                    <td align="left"><?php echo JText::_('COM_JOOMLAQUIZ_NAME_NAME'); ?></td>
                    <td><?php echo $this->lists['user']->name ?></td>
                </tr>
                <tr>
                    <td align="left"><?php echo JText::_('COM_JOOMLAQUIZ_EMAIL'); ?></td>
                    <td><?php echo $this->lists['user']->email ?></td>
                </tr>
            </table>
        </div>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>