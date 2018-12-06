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
	public function getLearningPaths()
	{
		$db = JFactory::getDBO();
        $query = 'SELECT lp.* FROM #__quiz_lpath AS lp WHERE lp.published = 1 AND lp.paid_check = 0';
        $db->setQuery($query);
        $lpaths = $db->loadObjectList();
        return !empty($lpaths) ? $lpaths : false;
	}
}