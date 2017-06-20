<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
* Configuration model.
*
*/
class JoomlaquizModelConfiguration extends JModelAdmin
{
	protected $text_prefix = 'COM_JOOMLAQUIZ';
		
	public function getTable($type = 'configuration', $prefix = 'JoomlaquizTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getItem($pk = null)
	{
		$result = parent::getItem($pk);
		return $result;
	}
		
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	
	protected function loadFormData()
	{
		$data = JoomlaquizHelper::getSettings();
		return $data;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$app	= JFactory::getApplication();

		$form = $this->loadForm('com_joomlaquiz.configuration', 'configuration', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	function store(){
		$db = JFactory::getDBO();

		$db->setQuery("DELETE FROM #__quiz_configuration");
		$db->execute();
			
		$db->setQuery("INSERT INTO `#__quiz_configuration` (`config_var`, `config_value`) VALUES ('wysiwyg_options', '".$_POST['jform']['wysiwyg_options']."'), ('lp_content_catid', '".$_POST['jform']['lp_content_catid']."'),
			('include_articles_from_subcats', '".$_POST['jform']['include_articles_from_subcats']."')");
		$db->execute();

		return true;
	}

}