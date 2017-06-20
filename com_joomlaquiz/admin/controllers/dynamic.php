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
 * Dynamic Controller
 */
class JoomlaquizControllerDynamic extends JControllerForm
{
	public function get_dynamic_csv(){
		$model = $this->getModel();
		$model->get_dynamic_csv(1);
		
		return;
	}
	
	public function get_dynamic(){
		$model = $this->getModel();
		$lists = $model->get_dynamic_csv(0);
		
		print_r($lists);
		die;
		
		return;
	}	
}
