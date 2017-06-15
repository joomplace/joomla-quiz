<?php
/**
* JoomlaQuiz applications for Joomla
* @version $Id: apps.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage apps.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JqAppPlugins extends JObject
{
	var $name;
	var $triggerCount = 0;

	public function __construct($name){
		$this->name = $name;
		parent::__construct($name);
	}

	private function JqAppPlugins($name){
		$this->__construct($name);
	}

	public static function &getInstance(){
		static $instance;
		if(!$instance){
			$instance = new JqAppPlugins('JqAppPlugins');
		}
		return $instance;
	}

	public function loadApplications()
	{
		static $loaded = false;
		// Although JPluginHelper will load it only once, we need to track it to
		// enable a trigger to our plugins
		if( !$loaded ){
			$plugs = JPluginHelper::importPlugin('joomlaquiz');
		}
		return $plugs;
	}

	/**
	 * Used to trigger applications
	 * @param	string	eventName
	 * @param	array	params to pass to the function
	 * @param	bool	do we need to use custom user ordering ?
	 *
	 * returns	Array	An array of object that the caller can then manipulate later.
	 **/
	public function triggerEvent( $event , &$arrayParams = null , $needOrdering = false )
	{
		$content	= array();
		
		// Avoid problem with php 5.3
		if(is_null($arrayParams)){
			$arrayParams = array();
		}
		
		$className = 'plgJoomlaquiz'.ucfirst($arrayParams['quest_type']);
		if(class_exists($className)){
			$plgObj = new $className();
			if(method_exists($plgObj, $event)){
				$content[] = call_user_func_array(array($plgObj, $event), array(&$arrayParams));
				$this->triggerCount++;
			}
			return $content;
		} else {
			$arrayParams['error'] = 1;
		}
		
		return false;
	}
}