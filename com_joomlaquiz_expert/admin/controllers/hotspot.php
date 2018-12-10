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
 * Question Controller
 */
class JoomlaquizControllerHotspot extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	public function uploadimage() {
		$css = JFactory::getApplication()->input->get('t','khepri');
		
		$message = '';
        $userfile = JFactory::getApplication()->input->files->get('userfile', array(), 'array');
		$userfile2=(isset($userfile['tmp_name']) ? $userfile['tmp_name'] : "");
		$userfile_name=(isset($userfile['name']) ? $userfile['name'] : "");
		$directory = 'joomlaquiz';
		if (!empty($userfile)) {
			$base_Dir = JPATH_SITE."/images/joomlaquiz/images/";
			
			if (empty($userfile_name)) {
				echo "<script>alert('".JText::_('COM_JOOMLAQUIZ_PLEASE_SELECT_AN_IMAGE')."'); document.location.href='index.php?no_html=1&tmpl=component&option=com_joomlaquiz&task=uploadimage&directory=".$directory."&t=".$css."';</script>";
			}
			$filename = explode(".", $userfile_name);
		
			if (preg_match("/[^0-9a-zA-Z_\-]/", $filename[0])) {
				$message = JText::_('COM_JOOMLAQUIZ_FILE_MUST');
			}
		
			if (file_exists($base_Dir.$userfile_name)) {
				$message = JText::_('COM_JOOMLAQUIZ_IMAGE').$userfile_name.JText::_('COM_JOOMLAQUIZ_ALREADY_EXISTS');
			}
		
			if ((strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".gif")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".jpg")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".png")) && (strcasecmp(JoomlaquizHelper::jq_substr($userfile_name,-4),".bmp")) ) {
				$message = JText::_('COM_JOOMLAQUIZ_ACCEPTED_FILES');
			}
			
			jimport('joomla.filesystem.path');
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			
			if (!JFile::move($userfile['tmp_name'],$base_Dir.$userfile['name']) || !JPath::setPermissions($base_Dir.$userfile['name'])) {
				$message = JText::_('COM_JOOMLAQUIZ_UPLOAD_OF').$userfile_name.JText::_('COM_JOOMLAQUIZ_FAILED');
			} else {
				$message = '<span class="hotspotimg_upload_success" style="color: #3c763d;">'.JText::_('COM_JOOMLAQUIZ_UPLOAD_OF')
                    .$userfile_name.JText::_('COM_JOOMLAQUIZ_TO').$base_Dir.JText::_('COM_JOOMLAQUIZ_SUCCESSFUL').'</span>';
			}
		}
		
		require_once(JPATH_BASE.'/components/com_joomlaquiz/views/upload/view.html.php');
        $view = $this->getView('Upload', 'HTML', 'JoomlaquizView');
        $view->assignRef('filename', $userfile_name);
        $view->assignRef('message', $message);
        $view->display();
	}
	
	public function createhotspot() {
		require_once(JPATH_BASE.'/components/com_joomlaquiz/views/createhotspot/view.html.php');
		$view = $this->getView("createhotspot");
		$view->display();
	}
	
	public function savehotspot() {
		require_once(JPATH_BASE.'/components/com_joomlaquiz/views/createhotspot/view.html.php');
		$view = $this->getView("createhotspot");
		$view->display();
	}

	public function createexthotspot() {
		require_once(JPATH_BASE.'/components/com_joomlaquiz/views/createexthotspot/view.html.php');
		$view = $this->getView("createexthotspot");
		$view->display();
	}
	
	public function saveexthotspot() {
		require_once(JPATH_BASE.'/components/com_joomlaquiz/views/createexthotspot/view.html.php');
		$view = $this->getView("createexthotspot");
		$view->display();
	}
}
