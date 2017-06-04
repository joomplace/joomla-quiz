<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

class QuizHelperRoute
{
    public static function routeQuestionTag($contentItem, $contentCatId, $language){
        static $_script_declared = false;
        if(!$_script_declared){
            JFactory::getDocument()->addScriptDeclaration("
                function noNavigationAlert(){
                    alert('You can`t navigate to the question directly! :(');
                }
            ");
            $_script_declared = true;
        }
        return "javascript: noNavigationAlert();";
    }
}