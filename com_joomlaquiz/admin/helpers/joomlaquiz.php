<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 20.07.2017
 * Time: 16:22
 */

class JoomlaQuizHelper{

    public static function addSubmenu($vName)
    {
//        JHtmlSidebar::addEntry(
//            JText::_('COM_JOOMLAQUIZ_SUBMENU_DASHBOARD'),
//            'index.php?option=com_joomlaquiz',
//            $vName == 'dashboard'
//        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMLAQUIZ_SUBMENU_QUIZ'),
            'index.php?option=com_joomlaquiz&view=quiz',
            $vName == 'quiz'
        );
//        JHtmlSidebar::addEntry(
//            JText::_('COM_JOOMLAQUIZ_SUBMENU_CATEGORY'),
//            'index.php?option=com_categories&extension=com_joomlaquiz',
//            $vName == 'categories'
//        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMLAQUIZ_SUBMENU_QUESTION'),
            'index.php?option=com_joomlaquiz&view=question',
            $vName == 'question'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_JOOMLAQUIZ_SUBMENU_CATEGORY_QUESTION'),
            'index.php?option=com_categories&extension=com_joomlaquiz.question',
            $vName == 'categories.question'
        );
    }
}