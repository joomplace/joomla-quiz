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
 * Addons Controller
 */
class JoomlaquizControllerAddons extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	public function install(){
		$model = $this->getModel();
		$msg = $model->install();
		$this->setRedirect('index.php?option=com_joomlaquiz&view=addons', $msg);
	}
}
