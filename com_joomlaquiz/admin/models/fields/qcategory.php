<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 *
 */
class JFormFieldQcategory extends JFormFieldList
{
	/**
	 * @var		string	The form field type.
	 */
	public $type = 'Qcategory';
	
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$this->element['extension'] = 'com_joomlaquiz';
				
		$db		= JFactory::getDbo();
		
		$query = "(SELECT '- Select category -' AS `text`, '- Select category -' AS `cat_id`, '0' AS `value` FROM `#__users` LIMIT 0,1) UNION (SELECT `c_category` AS `text`, `c_category` AS `cat_id`, `c_id` AS `value` FROM `#__quiz_t_category`)";
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		
		return $options; 
	} 
}