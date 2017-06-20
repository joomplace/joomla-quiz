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
 * Product Controller
 */
class JoomlaquizControllerProduct extends JControllerForm
{
    protected function allowEdit($data = array(), $key = 'pid')
    {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_joomlaquiz');             
    }
	
	public function save(){
		parent::save();
		$task = JFactory::getApplication()->input->getCmd('task');
		$pid = JFactory::getApplication()->input->getCmd('pid');
		
		if($task == 'apply'){
			$this->setRedirect('index.php?option=com_joomlaquiz&view=product&layout=edit&pid='.$pid);
		} elseif($task == 'save') {
			$this->setRedirect('index.php?option=com_joomlaquiz&view=products');
		}
	}

}
