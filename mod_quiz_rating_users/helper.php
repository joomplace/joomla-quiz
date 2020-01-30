<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_quiz_rating_users
 *
 * @copyright   Copyright (C) JoomPlace, www.joomplace.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;

/**
 * Helper for mod_quiz_rating_users
 *
 */
abstract class ModQuizRatingUsersHelper
{
	public static function getList(&$params)
	{
		$count = (int)$params->get('count');
        $quiz_id = (int)$params->get('quiz_id');

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('qrsq.c_student_id')
            ->select('u.name')
            ->select($db->qn('u.name'))
            ->from($db->qn('#__quiz_r_student_quiz', 'qrsq'))
            ->leftJoin($db->qn('#__users', 'u') . ' ON ' . $db->qn('u.id') . '=' . $db->qn('qrsq.c_student_id'))
            ->where($db->qn('qrsq.c_finished') .'='. $db->q(1))
            ->where($db->qn('qrsq.c_student_id') .'>'. $db->q(0));

        if($quiz_id){
            $query->where($db->qn('qrsq.c_quiz_id') .'='. $db->q($quiz_id));
        }

        $query->order(array('qrsq.c_passing_score DESC', 'qrsq.c_total_time ASC'))
            ->setLimit($count);
        $db->setQuery($query);
        $list = $db->loadObjectList();

		return $list;
	}
}
