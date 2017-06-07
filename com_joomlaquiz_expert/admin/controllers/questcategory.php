<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controllerform');
 
/**
 * Quizzes Controller
 */
class JoomlaquizControllerQuestcategory extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
    protected function allowEdit($data = array(), $key = 'qc_id')
    {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_joomlaquiz');             
    }
	
		
	public function save(){
		parent::save();
		$task = JFactory::getApplication()->input->getCmd('task');
				
		if($task == 'apply'){
	
		} elseif($task == 'save') {
			$this->setRedirect('index.php?option=com_joomlaquiz&view=questcategories');
		}
	}
	
	public function cancel(){
		$this->setRedirect('index.php?option=com_joomlaquiz&view=questcategories');
	}
}
