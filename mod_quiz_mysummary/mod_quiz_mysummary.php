<?php
/**
 * JoomlaQuiz module for Joomla
 * @package JoomlaQuiz
 * @subpackage mod_quiz_myresult.php
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('models.qcategory', JPATH_SITE.'/components/com_joomlaquiz/');

$db = JFactory::getDbo();
$query = $db->getQuery(true);
$sub_query = $db->getQuery(true);
$sub_query->select('MAX('.$db->qn('c_total_score').')')
    ->from($db->qn('#__quiz_r_student_quiz'))
    ->where($db->qn('c_student_id').' = '.$db->q(JFactory::getUser()->id))
    ->where($db->qn('c_quiz_id').' = '.$db->qn('q.c_id'));
$query->select('('.$sub_query.') AS '.$db->qn('score'));
$sub_query->clear('select')
    ->select('IF(MAX('.$db->qn('c_finished').'),MAX('.$db->qn('c_passed').')+1,MAX('.$db->qn('c_finished').'))');
$query->select('('.$sub_query.') AS '.$db->qn('status'));
$query->select($db->qn(array('c_id','c_title')))
    ->from($db->qn('#__quiz_t_quiz','q'))
    ->where($db->qn('q.c_id').' IN ('.implode(',',array_merge(array(-1),array_map(function($q){return $q->c_id;},JoomlaquizModelQcategory::getAvaliableQuizzes()))).')');
if($params->get('quiz_ids', array())){
    $query->where($db->qn('q.c_id').' IN ('.implode(',',$params->get('quiz_ids', array())).')');
}

$results = $db->setQuery($query)->loadObjectList();

require JModuleHelper::getLayoutPath('mod_quiz_mysummary', $params->get('layout', 'default'));