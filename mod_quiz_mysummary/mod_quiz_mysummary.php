<?php
/**
 * JoomlaQuiz module for Joomla
 * @version $Id: mod_quiz_myresult.php 2011-03-03 17:30:15
 * @package JoomlaQuiz
 * @subpackage mod_quiz_myresult.php
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('models.qcategory', JPATH_SITE.'/components/com_joomlaquiz/');

$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select($db->qn('q.c_id'))
    ->select($db->qn('q.c_title'))
    ->select('IF('.$db->qn('pr.c_id').',1,IF('.$db->qn('fr.c_finished').',0,IF('.$db->qn('fr.c_id').',2,-1))) AS '.$db->qn('status'))
    ->select('IF('.$db->qn('pr.c_id').','.$db->qn('pr.c_total_score').','.$db->qn('fr.c_total_score').') AS '.$db->qn('score'))
    ->from($db->qn('#__quiz_t_quiz','q'));
$sub_query = $db->getQuery(true);
$sub_query->select('*')
    ->from($db->qn('#__quiz_r_student_quiz'))
    ->where($db->qn('c_passed').'='.$db->q(1))
    ->order($db->qn('c_date_time').' ASC');
$query->leftJoin('('.$sub_query.' LIMIT 0,1) AS '.$db->qn('pr').' ON '.$db->qn('q.c_id').' = '.$db->qn('pr.c_quiz_id'));
$sub_query->clear('where')
    ->where($db->qn('c_passed').'='.$db->q(0));
$query->leftJoin('('.$sub_query.' LIMIT 0,1) AS '.$db->qn('fr').' ON '.$db->qn('q.c_id').' = '.$db->qn('fr.c_quiz_id'));
$query->where($db->qn('q.c_id').' IN (0,'.implode(',',array_map(function($q){return $q->c_id;},JoomlaquizModelQcategory::getAvaliableQuizzes())).')');

$results = $db->setQuery($query)->loadObjectList();

require JModuleHelper::getLayoutPath('mod_quiz_myresult', $params->get('layout', 'default'));