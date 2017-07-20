<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 22.06.2017
 * Time: 23:31
 */

$cat = JTable::getInstance('Category');
$cat->load($displayData);
echo $cat->title?$cat->title:JText::_('NOCATEGORY');