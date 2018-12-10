<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

/**
 * Result HTML View class for the Joomlaquiz Deluxe Component
 */
 
class JoomlaquizViewResult extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
    function display($tpl = null) 
	{
		$submenu = 'result';
		JoomlaquizHelper::showTitle($submenu);
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		
		$this->addToolBar();
		$id = JFactory::getApplication()->input->get('cid');
		
		$report_html = '';
		$lists		= $this->get('QuestionReport');
		$type		= JoomlaquizHelper::getQuestionType($lists['c_type']);
		
		$data = array();
		$data['quest_type'] = $type;
		$data['id'] = $id;
		$data['q_id'] = $lists['q_id'];
		
		$className = 'plgJoomlaquiz'.ucfirst($type);
		$appsLib = JqAppPlugins::getInstance();
		$appsLib->loadApplications();
		
		$add_lists = (method_exists($className, 'onGetAdminAddLists')) ? $appsLib->triggerEvent( 'onGetAdminAddLists' , $data ) : array();
		if(!empty($add_lists)){
			$data['lists'] = array_merge($lists, $add_lists[0]);
		} else {
			$data['lists'] = $lists;
		}
		
		$report_html = (method_exists($className, 'onGetAdminReportsHTML')) ? $appsLib->triggerEvent( 'onGetAdminReportsHTML' , $data ) : array('');
				
		if (!empty($errors = $this->get('Errors')))
		{
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
		
		$this->lists	= $data['lists'];
		$this->report_html = $report_html[0];
		$this->cid = $id;
		
        parent::display($tpl);
    }
 
    /**
    * Setting the toolbar
    */
	protected function addToolBar() 
    {
        JToolBarHelper::custom('result.view_report', 'previous.png', 'previous_f2.png', 'COM_JOOMLAQUIZ_BACK', false);    
    }
}
