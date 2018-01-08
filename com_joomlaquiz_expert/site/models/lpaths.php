<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
/**
 * Learning Path Model.
 *
 */
class JoomlaquizModelLpaths extends JModelList
{	
	public function getLearningPaths(){
		
		$db = JFactory::getDBO();
//		$mainframe = JFactory::getApplication();
//		$my = JFactory::getUser();

//        $query = $db->getQuery(true);
//        $query->select('lp.*, c.title as c_title, c.id as c_id')
//            ->from($db->qn('#__quiz_lpath', 'lp'))
//            ->join('LEFT', $db->qn('#__categories', 'c') . ' ON (' . $db->qn('lp.category') . ' = ' . $db->qn('c.id') . ')')
//            ->where($db->qn('lp.published') . ' = ' . $db->qn(1)
//                . ' AND ' .$db->qn('c.published') . ' = '. $db->qn(1)
//                . ' AND ' . $db->qn('lp.paid_check') . ' = ' . $db->qn(0));

        $query='SELECT lp.*, c.title as c_title, c.id as c_id FROM #__quiz_lpath AS lp '
            . 'LEFT JOIN #__categories AS c ON lp.category = c.id '
            . 'WHERE lp.published = 1 AND c.published = 1 AND lp.paid_check = 0';

//        echo $query;
//        die();

        $db->setQuery($query);
        $lpaths = $db->loadObjectList();

        return !empty($lpaths)?$lpaths:false;
	}
}
