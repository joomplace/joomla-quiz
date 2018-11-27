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
* Certificate model.
*
*/
class JoomlaquizModelCertificate extends JModelAdmin
{
	protected $text_prefix = 'COM_JOOMLAQUIZ';
		
	public function getTable($type = 'certificate', $prefix = 'JoomlaquizTable', $config = array())
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
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomlaquiz.edit.certificate.data', array());

		if (empty($data)) {
			$data = $this->getItem();
			// Prime some default values.
			if ($this->getState('certificate.id') == 0) {
				$app = JFactory::getApplication();
				$id = $app->getUserState('com_joomlaquiz.edit.certificate.id');
				if ($id) $data->set('id', JFactory::getApplication()->input->getInt('id', $id));
			}
		}
		return $data;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$app	= JFactory::getApplication();

		$form = $this->loadForm('com_joomlaquiz.certificate', 'certificate', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	public function getLists($id)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `cert_file` FROM #__quiz_certificates WHERE `id` = '".$id."'");
		$cert_file = $db->loadResult();
		
		$lists = array();
		$directory = '/images/joomlaquiz/images/';
		$javascript = "onchange=\"javascript:if (document.getElementById('jformcert_file').options[selectedIndex].value!='') {"
		. " document.imagelib.src='..$directory/' + document.getElementById('jformcert_file').options[selectedIndex].value; } else {"
		. " document.imagelib.src='".JURI::root()."administrator/components/com_joomlaquiz/assets/images/blank.png'}\""; 
		$lists['images'] = JHTML::_('list.images', 'jform[cert_file]', $cert_file, $javascript, $directory);
		
		$fonts = $this->jrReadDirectory(JPATH_SITE.'/media','.ttf');
		
		$lists['fonts'] = array();
		if(is_array($fonts) && !empty($fonts)) {
			foreach($fonts as $i=>$font) {
				$lists['fonts'][$i] = new stdClass();
				$lists['fonts'][$i]->value = $font;
				$lists['fonts'][$i]->text = $font;
			}
		}
		
		$query = "SELECT * FROM #__quiz_cert_fields WHERE cert_id = '{$id}' ORDER BY c_id";
		$db->setQuery($query);
		$fields = $db->loadObjectList();
		$lists['fields'] = $fields;
		
		return $lists;
	}
	
	public function jrReadDirectory( $path, $filter='.', $recurse=false, $fullpath=false  ) {
		$arr = array();
		if (!@is_dir( $path )) {
			return $arr;
		}
		$handle = opendir( $path );

		while ($file = readdir($handle)) {
			$dir = $this->jrPathName( $path.'/'.$file, false );
			$isDir = is_dir( $dir );
			if (($file != ".") && ($file != "..")) {
				if (preg_match( "/$filter/", $file ) && $file[0] != '_') {
					if ($fullpath) {
						$arr[] = trim( $this->jrPathName( $path.'/'.$file, false ) );
					} else {
						$arr[] = trim( $file );
					}
				}
				if ($recurse && $isDir) {
					$arr2 = $this->jrReadDirectory( $dir, $filter, $recurse, $fullpath );
					$arr = array_merge( $arr, $arr2 );
				}
			}
		}
		closedir($handle);
		asort($arr);
		return $arr;
	}
	
	public function jrPathName($p_path,$p_addtrailingslash = true) {
		$retval = "";
		$isWin = (JoomlaquizHelper::jq_substr(PHP_OS, 0, 3) == 'WIN');

		if ($isWin)	{
			$retval = str_replace( '/', '\\', $p_path );
			if ($p_addtrailingslash) {
				if (JoomlaquizHelper::jq_substr( $retval, -1 ) != '\\') {
					$retval .= '\\';
				}
			}
			// Remove double \\
			$retval = str_replace( '\\\\', '\\', $retval );
		} else {
			$retval = str_replace( '\\', '/', $p_path );
			if ($p_addtrailingslash) {
				if (JoomlaquizHelper::jq_substr( $retval, -1 ) != '/') {
					$retval .= '/';
				}
			}
			// Remove double //
			$retval = str_replace('//','/',$retval);
		}

		return $retval;
	}
}