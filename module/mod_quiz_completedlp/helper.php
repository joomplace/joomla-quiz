<?php
/**
 * JoomlaQuiz module for Joomla
 * @version $Id: helper.php 2017-16-01 13:30:15
 * @package JoomlaQuiz
 * @subpackage helper.php
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class modCompletedlpHelper
{
    public static function getResult()
    {
        $user_id = JFactory::getUser()->id;
        $db = JFactory::getDBO();
        $result = array();

        $query = $db->getQuery(true);
        $query = 'SELECT COUNT(`r`.`lid`) AS `dif`, `r`.`lid`, `r`.`stage`, `r`.`uid`
                    FROM
                    (
                        SELECT COUNT(`l`.`lid`), `l`.*, `s`.`uid`, IF(`s`.`stage` IS NULL OR `s`.`stage` = 0,0,1) AS `stage`
                        FROM `#__quiz_lpath_quiz` AS `l`
                        LEFT JOIN 
                        (
                            SELECT *
                            FROM `#__quiz_lpath_stage`
                            WHERE `uid` = ' . $user_id . '
                        ) AS `s` ON `s`.`qid` = `l`.`qid` AND `s`.`type` = `l`.`type` AND `s`.`lpid` = `l`.`lid`
                        GROUP BY `l`.`lid`,`s`.`stage`
                    ) AS `r` 
                    GROUP BY `r`.`lid`';
        $db->SetQuery($query);
        $result = $db->loadAssocList();

        $lid_array =  array();

        foreach ($result as $item) {
            if ($item['dif'] == 1 && $item['stage'] == 1) {
                $lid_array[] = $item['lid'];
            }
        }

        $final_result = array();
        if ($lid_array) {
            $query = $db->getQuery(true);
            $query->select(array('ql.id', 'ql.title AS title', 'c.title AS category'))
                ->from($db->qn('#__quiz_lpath', 'ql'))
                ->join('right', $db->qn('#__categories', 'c') . ' ON (' . $db->qn('ql.category') . ' = ' . $db->qn('c.id') . ')')
                ->where($db->qn('ql.published') . ' = 1')
                ->where($db->qn('ql.id') . ' IN (' . implode(",", $lid_array) . ')');
            $db->SetQuery($query);
            $final_result = $db->loadObjectList();
        }

        return $final_result;
    }
}
