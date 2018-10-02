<?php

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\Router\RouterBase;

class JoomlaquizRouter extends RouterBase
{
    public function build(&$query)
    {
        $segments = array();
        $app = JFactory::getApplication();
        $menu = $app->getMenu();

        if (empty($query['Itemid']))
        {
            $menuItem = $menu->getActive();
        }
        else
        {
            $menuItem = $menu->getItem($query['Itemid']);
        }

//        isset($query['view']) ?: $query['view'] = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];

//        if ( isset( $query['option'] ) ) {
//            $segments[] = $query['option'];
//            unset( $query['option'] );
//        }
        isset($query['view']) ? $query['task'] : isset($query['task']) ? $query['view'] = explode('.', $query['task'])[0]:'';
        if ( isset( $query['view'] )) {
//            if (empty($query['Itemid']) || empty($menuItem) || $menuItem->component != 'com_joomlaquiz') {
				$segments[] = $query['view'];
//			}
            switch ($query['view']) {
                case 'quiz' :
                    if ( isset($query['quiz_id'])){
                        $segments[] = $query['quiz_id'];
                        unset( $query['quiz_id'] );
                    }
                    if ( isset($query['lid'])){
                        $segments[] = $query['lid'];
                        unset( $query['lid'] );
                    }
                    break;
                case 'qcategory' :
                    break;
                case 'lpath' :
                    if ( isset($query['lpath_id'])){
                        $segments[] = $query['lpath_id'];
                        unset( $query['lpath_id'] );
                    }
                    break;
                case 'lpaths' :
                    if ( isset($query['lpath_id'])){
                        $segments[] = $query['lpath_id'];
                        unset( $query['lpath_id'] );
                    }
                    break;
                case 'packages' :
                    break;
                case 'results' :
                    if ( isset($query['task'])){
                        $segments[] = $query['task'];
                        unset( $query['task'] );
                    }
                    if ( isset($query['id'])){
                        $segments[] = $query['id'];
                        unset( $query['id'] );
                    }
                    break;
                case 'statistics' :
                    break;
                case 'printcert' :
                    if ( isset($query['task'])){
                        $segments[] = $query['task'];
                        unset( $query['task'] );
                    }
                    if ( isset($query['stu_quiz_id'])){
                        $segments[] = $query['stu_quiz_id'];
                        unset( $query['stu_quiz_id'] );
                    }
                    if ( isset($query['user_unique_id'])){
                        $segments[] = $query['user_unique_id'];
                        unset( $query['user_unique_id'] );
                    }
                    break;
                case 'printresult':
                    if ( isset($query['task'])){
                        $segments[] = $query['task'];
                        unset( $query['task'] );
                    }
                    if ( isset($query['lang'])){
                        $segments[] = $query['lang'];
                        unset( $query['lang'] );
                    }
                    if ( isset($query['stu_quiz_id'])){
                        $segments[] = $query['stu_quiz_id'];
                        unset( $query['stu_quiz_id'] );
                    }
                    if ( isset($query['user_unique_id'])){
                        $segments[] = $query['user_unique_id'];
                        unset( $query['user_unique_id'] );
                    }
                    break;
                default:
                    if ( isset($query['quiz_id'])){
                        $segments[] = $query['quiz_id'];
                        unset( $query['quiz_id'] );
                    }
                    break;
            }
            unset( $query['view'] );
        }

        return $segments;
    }

    public function parse(&$segments)
    {
        $vars = array();
        $count = count( $segments );
        if ( $count ) {
            $count--;
            $segment = array_shift($segments);
            $vars['view'] = $segment;
        }
        switch ($vars['view']){
            case 'quiz' :
                if ( $count ) {
                    $count--;
                    $segment = array_shift($segments);
                    $vars['quiz_id'] = $segment;
                }
                if ( $count ) {
                    $count--;
                    $segment = array_shift($segments);
                    $vars['lid'] = $segment;
                }
                break;
            case 'qcategory' :
                break;
            case 'lpath' :
                if ( $count ) {
                    $count--;
                    $vars['lpath_id'] = array_shift($segments);
                }
                break;
            case 'lpaths':
                break;
            case 'packages':
                break;
            case 'results':
                if ( $count ) {
                    $count--;
                    $vars['task'] = array_shift($segments);
                }
                if ( $count ) {
                    $count--;
                    $vars['id'] = array_shift($segments);
                }
                break;
            case 'statistics':
                break;
            case 'printcert' :
                if ( $count ) {
                    $count--;
                    $vars['task'] = array_shift($segments);
                }
                if ( $count ) {
                    $count--;
                    $vars['stu_quiz_id'] = array_shift($segments);
                }
                if ( $count ) {
                    $count--;
                    $vars['user_unique_id'] = array_shift($segments);
                }
                break;
            case 'printresult' :
                if ( $count ) {
                    $count--;
                    $vars['task'] = array_shift($segments);
                }
                if ( $count ) {
                    $count--;
                    $vars['lang'] = array_shift($segments);
                }
                if ( $count ) {
                    $count--;
                    $vars['stu_quiz_id'] = array_shift($segments);
                }
                if ( $count ) {
                    $count--;
                    $vars['user_unique_id'] = array_shift($segments);
                }
                break;
            default:
                break;
        }

        $vars['option'] = "com_joomlaquiz";
        return $vars;


    }
}