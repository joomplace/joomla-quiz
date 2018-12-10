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
class JoomlaquizViewNewquestion extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
    public function display($tpl = null) 
    {		
		$this->questions	= $this->getAllquestions();
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
				
		parent::display($tpl);
    }
	
	public function getAllquestions(){
		$db = JFactory::getDBO();
        $db->setQuery("SELECT b.* FROM `#__quiz_t_qtypes` as `b` LEFT JOIN `#__extensions` as `e` ON (CONVERT (e.element USING utf8) COLLATE utf8_unicode_ci) = b.c_type WHERE e.folder = 'joomlaquiz' AND e.type = 'plugin' AND e.enabled = 1");
		$questions = $db->loadObjectList();
		return $questions;
	 }
}
?>
