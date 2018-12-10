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
 * Form Field class for the Testimonials.
 *
 */
class JFormFieldQuiz extends JFormFieldList
{
	/**
	 * The form field type.
	 */
	protected $type = 'Quiz';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();

		$db		= JFactory::getDbo();
		
		$query	= "(SELECT '- Select quiz -' AS `text`, '- Select quiz -' AS `quiz_id`, '0' AS `value` FROM `#__users` LIMIT 0,1) UNION (SELECT `c_title` AS `text`, `c_title` AS `quiz_id`, `c_id` AS `value` FROM `#__quiz_t_quiz` WHERE `c_id` > 0)";
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
            JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
		}
		
		return $options; 
	} 
}