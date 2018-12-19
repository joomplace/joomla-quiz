<?php
/**
 * Joomlaquiz Component for Joomla 3
 * @package Joomlaquiz
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterBase;

class JoomlaquizRouter extends RouterBase
{
    public function build(&$query)
    {
        $segments = array();

        if(empty($query['view'])){
            if(!empty($query['task'])){
                $query['view'] = explode('.', $query['task'])[0];
            }else {
                $query['view'] = '';
            }
        }

        if(!empty($query['view'])) {
            $segments[] = $query['view'];

            switch ($query['view']) {
                case 'quiz':
                    if ( isset($query['quiz_id']) && !isset($query['lid'])){
                        $segments[] = $query['quiz_id'];
                        unset( $query['quiz_id'] );
                    }
                    if ( isset($query['lid'])){              //inside lpath
                        $segments[] = 'lp'.$query['lid'];
                        unset( $query['lid'] );
                        if ( isset($query['quiz_id'])){
                            $segments[] = $query['quiz_id'];
                            unset( $query['quiz_id'] );
                        }
                        else if ( isset($query['article_id'])){
                            $segments[] = 'a'.$query['article_id'];
                            unset( $query['article_id'] );
                        }
                    }
                    break;
                case 'lpath':
                    if ( isset($query['lpath_id'])){
                        $segments[] = $query['lpath_id'];
                        unset( $query['lpath_id'] );
                    }
                    break;
                case 'results':
                    if ( isset($query['task'])){
                        $segments[] = $query['task'];
                        unset( $query['task'] );
                    }
                    if ( isset($query['id'])){
                        $segments[] = $query['id'];
                        unset( $query['id'] );
                    }
                    break;
                case 'printcert':
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
                case 'qcategory':
                case 'lpaths':
                case 'packages':
                case 'statistics':
                    break;
                default:
                    if ( isset($query['quiz_id'])){
                        $segments[] = $query['quiz_id'];
                        unset( $query['quiz_id'] );
                    }
                    break;
            }
        }
        unset( $query['view'] );

        return $segments;
    }

    public function parse(&$segments)
    {
        $vars = array();
        $count = count($segments);
        if($count){
            $count--;
            $segment = array_shift($segments);
            $vars['view'] = $segment;
        }
        switch ($vars['view']){
            case 'quiz':
                if($count){
                    $count--;
                    $segment = array_shift($segments);
                    if(stristr($segment, 'lp') === false) {
                        $vars['quiz_id'] = $segment;
                    } else {
                        $vars['lid'] = str_replace('lp', '', $segment);

                        if($count){
                            $count--;
                            $segment = array_shift($segments);
                            if(stristr($segment, 'a') === false) {
                                $vars['quiz_id'] = $segment;
                            } else {
                                $vars['article_id'] = str_replace('a', '', $segment);
                            }
                        }
                    }
                }
                break;
            case 'lpath':
                if($count){
                    $count--;
                    $vars['lpath_id'] = array_shift($segments);
                }
                break;
            case 'results':
                if($count){
                    $count--;
                    $vars['task'] = array_shift($segments);
                }
                if($count){
                    $count--;
                    $vars['id'] = array_shift($segments);
                }
                break;
            case 'printcert':
                if($count){
                    $count--;
                    $vars['task'] = array_shift($segments);
                }
                if($count){
                    $count--;
                    $vars['stu_quiz_id'] = array_shift($segments);
                }
                if($count){
                    $count--;
                    $vars['user_unique_id'] = array_shift($segments);
                }
                break;
            case 'printresult':
                if($count){
                    $count--;
                    $vars['task'] = array_shift($segments);
                }
                if($count){
                    $count--;
                    $vars['lang'] = array_shift($segments);
                }
                if($count){
                    $count--;
                    $vars['stu_quiz_id'] = array_shift($segments);
                }
                if($count){
                    $count--;
                    $vars['user_unique_id'] = array_shift($segments);
                }
                break;
            case 'qcategory':
            case 'lpaths':
            case 'packages':
            case 'statistics':
            default:
                break;
        }

        $vars['option'] = 'com_joomlaquiz';
        return $vars;
    }
}