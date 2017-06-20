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
 * Configuration Controller
 */
class JoomlaquizControllerConfiguration extends JControllerForm
{
    protected function allowEdit($data = array(), $key = 'c_par_name')
    {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_joomlaquiz');             
    }
	
	public function save($key = NULL, $urlVar = NULL)
    {
		
		$model = $this->getModel();
		$model->store();
		$popup = JFactory::getApplication()->input->get('popup');

		if($popup == 'true'){
			$this->setRedirect('index.php?option=com_joomlaquiz&view=configuration&tmpl=component', JText::_('COM_JOOMLAQUIZ_CONFIG_DETAILS'));
		} elseif($popup == 'false') {
			$this->setRedirect('index.php?option=com_joomlaquiz&view=configuration', JText::_('COM_JOOMLAQUIZ_CONFIG_DETAILS'));
		}	
	}
}
