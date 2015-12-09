<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 *
 */
class JFormFieldLpath extends JFormFieldList
{
	/**
	 * @var		string	The form field type.
	 */
	public $type = 'Lpath';

	protected function getOptions()
	{
		// Initialise variables.
		$options = array();

		$db		= JFactory::getDbo();
		
		$query = "(SELECT '- Select learnin path -' AS `text`, '- Select learnin path -' AS `lpath_id`, '0' AS `value` FROM `#__users` LIMIT 0,1) UNION (SELECT `title` AS `text`, `title` AS `lpath_id`, `id` AS `value` FROM `#__quiz_lpath` WHERE `id` > 0)";
		$db->setQuery($query);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		
		return $options; 
	} 
}