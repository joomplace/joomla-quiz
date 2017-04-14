<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 14:32
 */
$data = $displayData;
echo JHtml::_('bootstrap.addTab', $data->get('TabSet'), 'question', JText::_('COM_JOOMLAQUIZ_ADMIN_TAB_QUESTION'));
?>
    <p>Content of the first tab.</p>
<?php
echo JHtml::_('bootstrap.endTab');