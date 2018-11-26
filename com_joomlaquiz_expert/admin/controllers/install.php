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
 * Install Controller
 */
class JoomlaquizControllerInstall extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	* Install plugins
	*/
	public function plugins(){
		$allowContinue = true;
		include_once(JPATH_ROOT . '/administrator/components/com_joomlaquiz/installer/plugins.html');
		exit;
	}
	
	public function install_plugins(){
		
		ignore_user_abort(false); // STOP script if User press 'STOP' button
		@set_time_limit(0);
		@ob_end_clean();
		@ob_start();
		echo "<script>function getObj_frame(name) {"
		. " if (parent.document.getElementById) { return parent.document.getElementById(name); }"
		. "	else if (parent.document.all) { return parent.document.all[name]; }"
		. "	else if (parent.document.layers) { return parent.document.layers[name]; }}"
		. "parent.jQuery('#jq_install_btn').css('opacity', '0.5');"
		. "</script>";
		
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.archive' );
		
		$plugins		= array();
		$plg_names 		= array();
		$source			= JPATH_ROOT . '/components/com_joomlaquiz/jq_plugins.zip';
		$destination	= JPATH_ROOT . '/components/com_joomlaquiz/jq_plugins/';

		if (!JFolder::exists($destination))
		{
			JFolder::create($destination);
		}

		if(JArchive::extract($source, $destination))
		{
			if(!empty($_REQUEST['jform'])){
				foreach($_REQUEST['jform'] as $plg_name => $enable){
					if($enable){
						$plugins[]     = JPATH_ROOT . '/components/com_joomlaquiz/jq_plugins/plg_'.$plg_name.'.zip';
						$plg_names[] = $plg_name;
					}
				}
			}
		}

		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');
		
		$plugins_count = count($plugins);
		if(empty($plugins)){
			echo "<script>"
			. "var div_log = getObj_frame('div_log');"
			. " if (div_log) {"
			. "div_log.style.width = '100%';"
			. "}"
			. "parent.jQuery('#div_progress').removeClass('progress-striped');"
			. "parent.jQuery('#div_progress').addClass('progress-success');"
			. "parent.jQuery('#jq_install_btn').css('opacity', '1');"
			. "parent.jQuery('#jq_install_btn').text('Next »');"
			. "parent.jQuery('#jq_install_btn').attr('onclick', 'location.href=\'index.php?option=com_joomlaquiz&task=install.modules\'');"
			. "</script>";
			@flush();
			@ob_end_flush();
			
			//remove temp folder
			JFolder::delete($destination);
			JFile::delete(JPATH_ROOT . '/components/com_joomlaquiz/jq_plugins.zip');
			die;
		}
		$app = JFactory::getApplication();
		foreach($plugins as $ii => $plugin)
		{
			$package   = JInstallerHelper::unpack($plugin);
			$installer = JInstaller::getInstance();
						
			if ( ! $installer->install($package['dir']))
			{
				// There was an error installing the package
			}
			
			// Cleanup the install files
			if ( ! is_file($package['packagefile']))
			{
				$package['packagefile'] = $app->getCfg('tmp_path').'/'.$package['packagefile'];
			}
			
			JInstallerHelper::cleanupInstall('', $package['extractdir']);
			
			$this->_installDatabase($plg_names[$ii]);
			
			echo "<script>var div_log = getObj_frame('div_log');"
			. " if (div_log) {"
			. "div_log.style.width = '".intval(($ii+1)*600/$plugins_count)."px';"
			. "}"
			. "</script>";
			@flush();
			@ob_flush();
			sleep(1);
		}
	
		if(!empty($_REQUEST['jform'])){
			foreach($_REQUEST['jform'] as $plg_name => $enable){
				if($enable){
					$this->_enablePlugin($plg_name);
				}
			}
		}
		
		//remove temp folder
		JFolder::delete($destination);
		//remove temp zip archive
		JFile::delete(JPATH_ROOT . '/components/com_joomlaquiz/jq_plugins.zip');
		
		echo "<script>"
		. "parent.jQuery('#div_progress').removeClass('progress-striped');"
		. "parent.jQuery('#div_progress').addClass('progress-success');"
		. "parent.jQuery('#jq_install_btn').css('opacity', '1');"
		. "parent.jQuery('#jq_install_btn').text('Next >>');"
		. "parent.jQuery('#jq_install_btn').attr('onclick', 'location.href=\'index.php?option=com_joomlaquiz&task=install.modules\'');"
		. "</script>";
		@flush();
		@ob_end_flush();
		
		die;
	}
	
	function _enablePlugin($plugin)
	{
		$db         = JFactory::getDBO();
		$version    = new JVersion();
		$joomla_ver = $version->getHelpVersion();

		$query	= 'UPDATE '.$db->quoteName('#__extensions').' SET '.$db->quoteName('enabled').' = '.$db->quote(1)
					.' WHERE '.$db->quoteName('element').' = '.$db->quote($plugin);

		$db->setQuery($query);

		if ( ! $db->query())
		{
			return $db->getErrorNum().':'.$db->getErrorMsg();
		}
		else
		{
			return null;
		}
	}
	
	/**
	* Install modules
	*/
	public function modules(){
		$allowContinue = true;
		include_once(JPATH_ROOT . '/administrator/components/com_joomlaquiz/installer/modules.html');
		exit;
	}
	
	function install_modules(){
		
		ignore_user_abort(false); // STOP script if User press 'STOP' button
		@set_time_limit(0);
		@ob_end_clean();
		@ob_start();
		echo "<script>function getObj_frame(name) {"
		. " if (parent.document.getElementById) { return parent.document.getElementById(name); }"
		. "	else if (parent.document.all) { return parent.document.all[name]; }"
		. "	else if (parent.document.layers) { return parent.document.layers[name]; }}"
		. "parent.jQuery('#jq_install_btn').css('opacity', '0.5');"
		. "</script>";
		
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.archive' );
		
		$modules		= array();
		$source			= JPATH_ROOT . '/components/com_joomlaquiz/jq_modules.zip';
		$destination	= JPATH_ROOT . '/components/com_joomlaquiz/jq_modules/';

		if (!JFolder::exists($destination))
		{
			JFolder::create($destination);
		}

		if(JArchive::extract($source, $destination))
		{
			if(!empty($_REQUEST['jform'])){
				foreach($_REQUEST['jform'] as $mod_name => $enable){
					if($enable){
						$modules[]     = JPATH_ROOT . '/components/com_joomlaquiz/jq_modules/'.$mod_name.'.zip';
					}
				}
			}
		}

		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');
		
		$modules_count = count($modules);
		if(empty($modules)){
			echo "<script>"
			. "var div_log = getObj_frame('div_log');"
			. " if (div_log) {"
			. "div_log.style.width = '100%';"
			. "}"
			. "parent.jQuery('#div_progress').removeClass('progress-striped');"
			. "parent.jQuery('#div_progress').addClass('progress-success');"
			. "parent.jQuery('#jq_install_btn').css('opacity', '1');"
			. "parent.jQuery('#jq_install_btn').text('Next >>');"
			. "parent.jQuery('#jq_install_btn').attr('onclick', 'location.href=\'index.php?option=com_joomlaquiz&task=install.content_plugin\'');"
			. "</script>";
			@flush();
			@ob_end_flush();
			
			//remove temp folder
			JFolder::delete($destination);
			JFile::delete(JPATH_ROOT . '/components/com_joomlaquiz/jq_modules.zip');
			die;
		}
		$app = JFactory::getApplication();
		foreach($modules as $ii => $module)
		{
			$package   = JInstallerHelper::unpack($module);
			$installer = JInstaller::getInstance();
						
			if ( ! $installer->install($package['dir']))
			{
				// There was an error installing the package
			}
			
			// Cleanup the install files
			if ( ! is_file($package['packagefile']))
			{
				$package['packagefile'] = $app->getCfg('tmp_path').'/'.$package['packagefile'];
			}
			
			JInstallerHelper::cleanupInstall('', $package['extractdir']);
			echo "<script>var div_log = getObj_frame('div_log');"
			. " if (div_log) {"
			. "div_log.style.width = '".intval(($ii+1)*600/$modules_count)."px';"
			. "}"
			. "</script>";
			@flush();
			@ob_flush();
			sleep(1);
		}
		
		//remove temp folder
		JFolder::delete($destination);
		//remove temp zip archive
		JFile::delete(JPATH_ROOT . '/components/com_joomlaquiz/jq_modules.zip');
		
		echo "<script>"
		. "parent.jQuery('#div_progress').removeClass('progress-striped');"
		. "parent.jQuery('#div_progress').addClass('progress-success');"
		. "parent.jQuery('#jq_install_btn').css('opacity', '1');"
		. "parent.jQuery('#jq_install_btn').text('Next >>');"
		. "parent.jQuery('#jq_install_btn').attr('onclick', 'location.href=\'index.php?option=com_joomlaquiz&task=install.content_plugin\'');"
		. "</script>";
		@flush();
		@ob_end_flush();
		die;
	}
	
	/**
	* Install modules
	*/
	public function content_plugin(){
		jimport( 'joomla.filesystem.file' );
		
		if(file_exists(JPATH_ROOT . '/components/com_joomlaquiz/jq_modules.zip')){
			//remove temp zip archive
			JFile::delete(JPATH_ROOT . '/components/com_joomlaquiz/jq_modules.zip');
		}
		
		$allowContinue = true;
		include_once(JPATH_ROOT . '/administrator/components/com_joomlaquiz/installer/content_plugin.html');
		exit;
	}
	
	function install_content_plugin()
	{
		ignore_user_abort(false); // STOP script if User press 'STOP' button
		@set_time_limit(0);
		@ob_end_clean();
		@ob_start();
		echo "<script>function getObj_frame(name) {"
		. " if (parent.document.getElementById) { return parent.document.getElementById(name); }"
		. "	else if (parent.document.all) { return parent.document.all[name]; }"
		. "	else if (parent.document.layers) { return parent.document.layers[name]; }}"
		. "parent.jQuery('#jq_install_btn').css('opacity', '0.5');"
		. "</script>";
		
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		
		$plugin = false;
		if(!empty($_REQUEST['jform'])){
			foreach($_REQUEST['jform'] as $plg_name => $enable){
				if($enable){
						$plugin     = JPATH_ROOT . '/components/com_joomlaquiz/quiz_content_plugin.zip';
				}
			}
		}
		
		if(!$plugin){
			echo "<script>"
			. "var div_log = getObj_frame('div_log');"
			. " if (div_log) {"
			. "div_log.style.width = '100%';"
			. "}"
			. "parent.jQuery('#div_progress').removeClass('progress-striped');"
			. "parent.jQuery('#div_progress').addClass('progress-success');"
			. "parent.jQuery('#jq_install_btn').css('opacity', '1');"
			. "parent.jQuery('#jq_install_btn').text('Done >>');"
			. "parent.jQuery('#jq_install_btn').attr('onclick', 'location.href=\'index.php?option=com_joomlaquiz&task=install.done\'');"
			. "</script>";
			@flush();
			@ob_end_flush();
			
			die;
		}
				
		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');

		$app = JFactory::getApplication();
		$package   = JInstallerHelper::unpack($plugin);
		$installer = JInstaller::getInstance();
					
		if ( ! $installer->install($package['dir']))
		{
			// There was an error installing the package
		}
		
		// Cleanup the install files
		if ( ! is_file($package['packagefile']))
		{
			$package['packagefile'] = $app->getCfg('tmp_path').'/'.$package['packagefile'];
		}
		
		JInstallerHelper::cleanupInstall('', $package['extractdir']);
		$this->_enablePlugin('quizcont');
		
		//remove temp zip archive
		JFile::delete(JPATH_ROOT . '/components/com_joomlaquiz/quiz_content_plugin.zip');
		
		echo "<script>"
		. "var div_log = getObj_frame('div_log');"
		. " if (div_log) {"
		. "div_log.style.width = '100%';"
		. "}"
		. "parent.jQuery('#div_progress').removeClass('progress-striped');"
		. "parent.jQuery('#div_progress').addClass('progress-success');"
		. "parent.jQuery('#jq_install_btn').css('opacity', '1');"
		. "parent.jQuery('#jq_install_btn').text('Done >>');"
		. "parent.jQuery('#jq_install_btn').attr('onclick', 'location.href=\'index.php?option=com_joomlaquiz&task=install.done\'');"
		. "</script>";
		@flush();
		@ob_end_flush();
		die;
	}
	
	public function done(){
		jimport( 'joomla.filesystem.file' );
		
		if(file_exists(JPATH_ROOT . '/components/com_joomlaquiz/quiz_content_plugin.zip')){
			//remove temp zip archive
			JFile::delete(JPATH_ROOT . '/components/com_joomlaquiz/quiz_content_plugin.zip');
		}
		
		$allowContinue = true;
		include_once(JPATH_ROOT . '/administrator/components/com_joomlaquiz/installer/done.html');
		exit;
	}
	
	function _installDatabase($plugin)
	{
		$db	= JFactory::getDBO();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');
		jimport('joomla.base.adapter');
		
		$sqlfile = JPATH_SITE.'/plugins/joomlaquiz/'.$plugin.'/sql/install.mysql.utf8.sql';
		$buffer = file_get_contents($sqlfile);
		
		// Graceful exit and rollback if read not successful
		if ($buffer === false)
		{
			JLog::add(JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), JLog::WARNING, 'jerror');

			return false;
		}

		// Create an array of queries from the sql file
		$queries = JDatabaseDriver::splitSql($buffer);

		if (empty($queries))
		{
			// No queries to process
			return 0;
		}
		
		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query)
		{
			$query = trim($query);

			if ($query != '' && $query{0} != '#')
			{
				$db->setQuery($query);

				if (!$db->execute())
				{
					JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), JLog::WARNING, 'jerror');

					return false;
				}
			}
		}
		
		$newColumns = array();
		
		switch($plugin){
			case 'blank':
				$newColumns = array(
					'r_student_blank' => array(
						'is_correct' => "TINYINT( 3 ) NOT NULL"
					)
				);
				break;
			default:
				break;
		}
		
		if($newColumns){
			foreach ($newColumns as $table => $fields)
			{
				$oldColumns = $db->getTableColumns('#__quiz_'.$table);
				foreach ( $fields as $key => $value)
				{
					if ( empty($oldColumns[$key]) )
					{
						$db->setQuery('ALTER TABLE `#__quiz_'.$table.'` ADD `'.$key.'` '.$value);
						$db->execute();
						if($key=='c_type' && $table == 't_qtypes'){
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'choice' WHERE `c_id` =1");
							$db->execute();
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'mresponse' WHERE `c_id` =2");
							$db->execute();
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'truefalse' WHERE `c_id` =3");
							$db->execute();
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'dragdrop' WHERE `c_id` =4");
							$db->execute();
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'dropdown' WHERE `c_id` =5");
							$db->execute();
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'blank' WHERE `c_id` =6");
							$db->execute();
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'hotspot' WHERE `c_id` =7");
							$db->execute();
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'surveys' WHERE `c_id` =8");
							$db->execute();
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'boilerplate' WHERE `c_id` =9");
							$db->execute();
							$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'mquestion' WHERE `c_id` =10");
							$db->execute();
						}
					}
				}
			}
		}
		
	}
}
