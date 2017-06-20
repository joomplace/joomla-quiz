<?php
/**
* Joomlaquiz component for Joomla 3.0
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class JoomlaquizModelDashboard_item extends JModelAdmin
{
	protected $context = 'com_joomlaquiz';

	public function getTable($type = 'Dashboard_item', $prefix = 'JoomlaquizTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) 
	{
		$form = $this->loadForm('com_joomlaquiz.dashboard_item', 'dashboard_item', array('control' => 'jform', 'load_data' => false));
		if (empty($form)) {
			return false;
		}

        $item = $this->getItem();
		$form->bind($item);

		return $form;
	}
}
