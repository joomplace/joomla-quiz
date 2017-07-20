<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 22.06.2017
 * Time: 15:11
 */
defined('_JEXEC') or die;

$view = $this;

JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
$input = JFactory::getApplication()->input;
JToolbarHelper::title(JText::_(strtoupper($input->get('option')).'_BRAND').JText::_(strtoupper($input->get('option')).'_'.strtoupper(JText::_($input->get('view'))).'_'.($input->get('id')?'EDIT':'CREATE').'_HEADER'), 'article');
JToolbarHelper::apply();
JToolbarHelper::save();
$key = $view->item->getKeyName();
if($view->item->$key){
    JToolbarHelper::save2copy();
}
JToolbarHelper::save2new();
JToolbarHelper::cancel();

$form = $view->item->getForm();
?>
<form id="adminForm" name="adminForm" class="adminForm" method="POST">
    <?php
    $set = 'editForm';
    echo JHtml::_('bootstrap.startTabSet', $set, array('active' => array_keys($form->getFieldsets())[0]));
    array_map(function($fieldset)use($form,$set){
        if($fieldset->name){
            $fieldset->fields = $form->getFieldset($fieldset->name);
            $fieldset->set = $set;
            $layout = new JLayoutFile('form.fieldset');
            $iPs = $layout->getIncludePaths();
            $JPXPath = JLoader::getNamespaces('psr4')['JoomPlaceX'][0];
            $iPs[] = $JPXPath.DIRECTORY_SEPARATOR.'layouts';
            $layout->setIncludePaths($iPs);
            echo $layout->render($fieldset);
        }
    },$form->getFieldsets());
    echo JHtml::_('bootstrap.startTabSet', $set);
    ?>
    <input type="hidden" name="option" value="<?= $input->get('option') ?>">
    <input type="hidden" name="view" value="<?= $input->get('view') ?>">
    <input type="hidden" name="task" value="">
</form>