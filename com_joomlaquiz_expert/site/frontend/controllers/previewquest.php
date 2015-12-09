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
 * Preview Question Controller
 */
class JoomlaquizControllerPreviewquest extends JControllerForm
{
  	public function getModel($name = 'previewquest', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function preview(){
		
		$model = $this->getModel();
		$return = $model->JQ_previewQuestion();
		
		require_once(JPATH_SITE.'/components/com_joomlaquiz/views/previewquest/view.html.php');
		$view = $this->getView("previewquest");
		$view->display(null, $return);
		
		return;		
	}
		
	
}
