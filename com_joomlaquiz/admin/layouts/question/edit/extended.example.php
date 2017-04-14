<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 14:47
 */

/** @var \Joomla\Registry\Registry $data */
$data = $displayData;
echo JHtml::_('bootstrap.addTab', $data->get('TabSet'), 'question-extended', JText::_('COM_JOOMLAQUIZ_QUESTION_EXT'));


$db = JFactory::getDBO();
$c_id = JFactory::getApplication()->input->get('c_id');

$db->setQuery("SELECT `c_random`, `c_partial`, `c_qform`, `c_title_true`, `c_title_false` FROM #__quiz_t_question WHERE `c_id` = '".$c_id."'");
$row = $db->loadObject();

$lists = array();
$c_qform = array();
$c_qform[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_RADIO_BUTTONS'));
$c_qform[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_DROP_DOWN'));
$c_qform = JHTML::_('select.genericlist', $c_qform, 'jform[c_qform]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_qform) ? intval( $row->c_qform ) : 0));
$lists['c_qform']['input'] = $c_qform;
$lists['c_qform']['label'] = JText::_('COM_JOOMLAQUIZ_DISPLAY_STYLE');

$c_partial = array();
$c_partial[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
$c_partial[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
$c_partial = JHTML::_('select.genericlist', $c_partial, 'jform[c_partial]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_partial) ? intval( $row->c_partial ) : 0));
$lists['c_partial']['input'] = $c_partial;
$lists['c_partial']['label'] = JText::_('COM_JOOMLAQUIZ_PARTIAL_SCORE');

$c_random = array();
$c_random[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
$c_random[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
$c_random = JHTML::_('select.genericlist', $c_random, 'jform[c_random]', 'class="text_area" size="1" ', 'value', 'text',  (isset($row->c_random) ? intval( $row->c_random ) : 0));
$lists['c_random']['input'] = $c_random;
$lists['c_random']['label'] = JText::_('COM_JOOMLAQUIZ_RANDOMIZE_ANSWERS');

$c_title_true = (isset($row->c_title_true)) ? $row->c_title_true : '';
$lists['c_title_true']['input'] = "<input type='text' size='30' name='c_title_true' value='".$c_title_true."' />";
$lists['c_title_true']['label'] = JText::_('COM_JOOMLAQUIZ_TITLE_FOR_TRUE');

$c_title_false = (isset($row->c_title_false)) ? $row->c_title_false : '';
$lists['c_title_false']['input'] = "<input type='text' size='30' name='c_title_false' value='".$c_title_false."' />";
$lists['c_title_false']['label'] = JText::_('COM_JOOMLAQUIZ_TITLE_FOR_FALSE');

if(count($lists)){
    foreach($lists as $for => $item){?>
      <div class="control-group">
          <?php if ($for=='c_qform')
              echo $item['label'];
          else {?>
            <label class=" control-label" for="<?php echo $for;?>" id="<?php echo $for;?>-lbl" style="width:156px;"><?php echo $item['label']?></label>
          <?php }?>
        <div class="controls">
            <?php echo $item['input']?>
        </div>
      </div>
    <?php }
}
?>
<?php
echo JHtml::_('bootstrap.endTab');