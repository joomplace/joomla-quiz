<?php
/**
* JoomlaQuiz module for Joomla
* @version $Id: helper.php 2017-16-01 13:30:15
* @package JoomlaQuiz
* @subpackage helper.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class modNotcompletedlpHelper
{

	public static function getResult()
	{
        $user_id = JFactory::getUser()->id;
        $db = JFactory::getDBO();
        $result = array();

        $query = $db->getQuery(true);
        $query->select(array('ql.id','ql.title AS title', 'c.title AS category', 'qls.qid'))
            ->from($db->qn('#__quiz_lpath_stage', 'qls'))
            ->join('right', $db->qn('#__quiz_lpath', 'ql') . ' ON (' . $db->qn('qls.lpid') . ' = ' . $db->qn('ql.id') . ')')
            ->join('right', $db->qn('#__categories', 'c') . ' ON (' . $db->qn('ql.category') . ' = ' . $db->qn('c.id') . ')')
            ->where($db->qn('qls.uid') . ' = ' . $user_id . ' AND ' . $db->qn('ql.published') . ' = 1' . ' AND ' . $db->qn('qls.stage') . ' = 0');
        $db->SetQuery($query);
        $result = $db->loadObjectList();

		return $result;
	}
}
