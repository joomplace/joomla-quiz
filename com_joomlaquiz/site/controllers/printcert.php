<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controllerform');
 
/**
 * Print Certificate Controller
 */
class JoomlaquizControllerPrintcert extends JControllerForm
{
  	public function getModel($name = 'printcert', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function get_certificate(){
		
		$model = $this->getModel();
		$model->JQ_printCertificate();
		
		return;		
	}
		
	
}
