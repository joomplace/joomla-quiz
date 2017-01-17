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

class modAvailablelpHelper
{

	public static function getResult()
	{
        $user_id = JFactory::getUser()->id;
        $db = JFactory::getDBO();
        $result = array();

        $query = $db->getQuery(true);
        $query = 'SELECT ql.id,ql.title AS title, c.title AS category
                  FROM l9o6i_quiz_lpath ql 
                  RIGHT JOIN l9o6i_categories c ON (c.id = ql.category) 
                  WHERE ql.id NOT IN ( 
                      SELECT qls.lpid 
                      FROM l9o6i_quiz_lpath_stage qls 
                      WHERE qls.uid = ' . $user_id . ')';
        $db->SetQuery($query);
        $result = $db->LoadObjectList();

        return $result;
	}
}
