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
 * Print PDF Controller
 */
class JoomlaquizControllerPrintresult extends JControllerForm
{
  	public function getModel($name = 'printresult', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function get_pdf(){
		
		$model = $this->getModel();
		$model->JQ_PrintResult();
		
		return;		
	}
		
	
}
