<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
 defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomlaquiz Deluxe Component
 */
class JoomlaquizViewTemplates extends JViewLegacy
{
	public function __construct($template_name)
	{
		if ( file_exists(JPATH_SITE . '/components/com_joomlaquiz/views/templates/tmpl/'.$template_name.'/main.php') ) {
			require_once(JPATH_SITE . '/components/com_joomlaquiz/views/templates/tmpl/'.$template_name.'/main.php');
		} else {
			require_once(JPATH_SITE . '/components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_standard/main.php');
		}
		
		return true;
	}
    public function display($tpl = null) 
    {
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
				
		parent::display($tpl);
    }
}
?>
