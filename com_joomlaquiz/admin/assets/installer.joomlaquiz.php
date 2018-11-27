<?php defined('_JEXEC') or die;
/**
* JoomlaQuiz component for Joomla
* @version $Id: installer.joomlaquiz.php 2009-11-16 17:30:15
* @package JoomlaQuiz
* @subpackage installer.joomlaquiz.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JQ_Installer {
	// name of the XML file with installation information
	var $i_installfilename	= "";
	var $i_installarchive	= "";
	var $i_installdir		= "";
	var $i_iswin			= false;
	var $i_errno			= 0;
	var $i_error			= "";
	var $i_installtype		= "";
	var $i_unpackdir		= "";
	var $i_docleanup		= true;

	/** @var string The directory where the element is to be installed */
	var $i_elementdir 		= '';
	var $i_componentadmindir = '';
	/** @var string The name of the Joomla! element */
	var $i_elementname 		= '';
	/** @var string The name of a special atttibute in a tag */
	var $i_elementspecial 	= '';
	/** @var object A DOMIT XML document */
	var $i_xmldoc			= null;
	var $i_hasinstallfile 	= null;
	var $i_installfile 		= null;

	/**
	* Constructor
	*/
	function JQ_Installer() {
		$this->i_iswin = (substr(PHP_OS, 0, 3) == 'WIN');
	}
	/**
	* Uploads and unpacks a file
	* @param string The uploaded package filename or install directory
	* @param boolean True if the file is an archive file
	* @return boolean True on success, False on error
	*/
	function upload($p_filename = null, $p_unpack = true) {
		$this->i_iswin = (substr(PHP_OS, 0, 3) == 'WIN');
		$this->installArchive( $p_filename );

		if ($p_unpack) {
			if ($this->extractArchive()) {
				return $this->findInstallFile();
			} else {
				return false;
			}
		}
	}
	
	function JQ_PathName($p_path, $p_addtrailingslash = true)
	{
		jimport('joomla.filesystem.path');
		$path = JPath::clean($p_path);
		if ($p_addtrailingslash) {
			$path = rtrim($path, '/') . '/';
		}
		return $path;
	}
	
	/**
	* Extracts the package archive file
	* @return boolean True on success, False on error
	*/
	function extractArchive() {

		$base_Dir 		= $this->JQ_PathName( JPATH_SITE . '/tmp' );
		
		$archivename 	= $base_Dir . $this->installArchive();
		$tmpdir 		= uniqid( 'install_' );

		$extractdir 	= $this->JQ_PathName( $base_Dir . $tmpdir );
		$archivename 	= $this->JQ_PathName( $archivename, false );
		
		$this->unpackDir( $extractdir );
		if (preg_match( '/.zip$/i', $archivename )) {
			// Extract functions
			require_once( JPATH_SITE . '/administrator/components/com_joomlaquiz/assets/pcl/pclzip.lib.php' );
			require_once( JPATH_SITE . '/administrator/components/com_joomlaquiz/assets/pcl/pclerror.lib.php' );
			$zipfile = new PclZip( $archivename );
			if($this->isWindows()) {
				define('OS_WINDOWS',1);
			} else {
				define('OS_WINDOWS',0);
			}

			$ret = $zipfile->extract( PCLZIP_OPT_PATH, $extractdir );
			if($ret == 0) {
				$this->setError( 1, 'Unrecoverable error "'.$zipfile->errorName(true).'"' );
				return false;
			}
		} else {
			$this->setError( 1, 'Extract Error' );
			return false;
		}

		$this->installDir( $extractdir );
		
		if (!function_exists('JQ_ReadDirectory')) {
			function JQ_ReadDirectory( $path, $filter='.', $recurse=false, $fullpath=false  )
			{
				$arr = array(null);
			
				// Get the files and folders
				jimport('joomla.filesystem.folder');
				$files		= JFolder::files($path, $filter, $recurse, $fullpath);
				$folders	= JFolder::folders($path, $filter, $recurse, $fullpath);
				// Merge files and folders into one array
				$arr = array_merge($files, $folders);
				// Sort them all
				asort($arr);
				return $arr;
			}
		}

		
		// Try to find the correct install dir. in case that the package have subdirs
		// Save the install dir for later cleanup
		$filesindir = JQ_ReadDirectory( $this->installDir(), '' );

		if (count( $filesindir ) == 1) {
			if (is_dir( $extractdir . $filesindir[0] )) {
				$this->installDir( $this->JQ_PathName( $extractdir . $filesindir[0] ) );
			}
		}
		
		return true;
	}
	/**
	* Tries to find the package XML file
	* @return boolean True on success, False on error
	*/
	function findInstallFile() {
		$found = false;
		
		if (!function_exists('JQ_ReadDirectory')) {
			function JQ_ReadDirectory( $path, $filter='.', $recurse=false, $fullpath=false  )
			{
				$arr = array(null);
			
				// Get the files and folders
				jimport('joomla.filesystem.folder');
				$files		= JFolder::files($path, $filter, $recurse, $fullpath);
				$folders	= JFolder::folders($path, $filter, $recurse, $fullpath);
				// Merge files and folders into one array
				$arr = array_merge($files, $folders);
				// Sort them all
				asort($arr);
				return $arr;
			}
		}
		
		// Search the install dir for an xml file
		$files = JQ_ReadDirectory( $this->installDir(), '.xml$', true, true );
		if (!empty( $files )) {
			foreach ($files as $file) {
			
				$packagefile = $this->isPackageFile( $file );
				
				if (!is_null( $packagefile ) && !$found ) {
					$this->xmlDoc( $packagefile );
					return true;
				}
			}
		
			$this->setError( 1, 'ERROR: Could not find a Joomla! XML setup file in the package.' );
			return false;
		} else {
		
			$this->setError( 1, 'ERROR: Could not find an XML setup file in the package.' );
			return false;
		}
	}
	/**
	* @param string A file path
	* @return object A DOMIT XML document, or null if the file failed to parse
	*/
	function isPackageFile( $p_file ) {
	
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );

		if (!$xmlDoc->loadXML( $p_file, false, true )) {
			return null;
		}
		$root = &$xmlDoc->documentElement;

		if ($root->getTagName() != 'jqinstall') {
		
			return null;
		}
		
		// Set the type
		$this->installType( $root->getAttribute( 'type' ) );
		$this->installFilename( $p_file );
		return $xmlDoc;
	}
	/**
	* Loads and parses the XML setup file
	* @return boolean True on success, False on error
	*/
	function readInstallFile() {

		if ($this->installFilename() == "") {
			$this->setError( 1, 'No filename specified' );
			return false;
		}

		$this->i_xmldoc = new DOMIT_Lite_Document();
		$this->i_xmldoc->resolveErrors( true );
		if (!$this->i_xmldoc->loadXML( $this->installFilename(), false, true )) {
			return false;
		}
		$root = &$this->i_xmldoc->documentElement;

		// Check that it's am installation file
		if ($root->getTagName() != 'jqinstall') {
			$this->setError( 1, 'File :"' . $this->installFilename() . '" is not a valid Joomla! installation file' );
			return false;
		}

		$this->installType( $root->getAttribute( 'type' ) );
		return true;
	}
	/**
	* Abstract install method
	*/
	function install() {
		die( 'Method "install" cannot be called by class ' . strtolower(get_class( $this )) );
	}
	/**
	* Abstract uninstall method
	*/
	function uninstall($id, $option) {
		die( 'Method "uninstall" cannot be called by class ' . strtolower(get_class( $this )) );
	}
	/**
	* return to method
	*/
	function returnTo( $option ) {
		return "index.php?option=$option";
	}
	/**
	* @param string Install from directory
	* @param string The install type
	* @return boolean
	*/
	function preInstallCheck( $p_fromdir, $type ) {

		if (!is_null($p_fromdir)) {
			$this->installDir($p_fromdir);
		}

		if (!$this->installfile()) {
			$this->findInstallFile();
		}

		if (!$this->readInstallFile()) {
			$this->setError( 1, 'Installation file not found:<br />' . $this->installDir() );
			return false;
		}

		if ($this->installType() != $type) {
			$this->setError( 1, 'XML setup file is not for a "'.$type.'".' );
			return false;
		}

		// In case there where an error doring reading or extracting the archive
		if ($this->errno()) {
			return false;
		}

		return true;
	}
	/**
	* @param string The tag name to parse
	* @param string An attribute to search for in a filename element
	* @param string The value of the 'special' element if found
	* @param boolean True for Administrator components
	* @return mixed Number of file or False on error
	*/
	function parseFiles( $tagName='files', $special='', $specialError='', $adminFiles=0 ) {
		jimport('joomla.filesystem.folder');

		// Find files to copy
		$xmlDoc =& $this->xmlDoc();
		$root =& $xmlDoc->documentElement;

		$files_element =& $root->getElementsByPath( $tagName, 1 );
		if (is_null( $files_element )) {
			return 0;
		}

		if (!$files_element->hasChildNodes()) {
			// no files
			return 0;
		}
		$files = $files_element->childNodes;
		$copyfiles = array();
		if (count( $files ) == 0) {
			// nothing more to do
			return 0;
		}

		if ($folder = $files_element->getAttribute( 'folder' )) {
			$temp = $this->JQ_PathName( $this->unpackDir() . $folder );
			if ($temp == $this->installDir()) {
				// this must be only an admin component
				$installFrom = $this->installDir();
			} else {
				$installFrom = $this->JQ_PathName( $this->installDir() . $folder );
			}
		} else {
			$installFrom = $this->installDir();
		}

		foreach ($files as $file) {
			if (basename( $file->getText() ) != $file->getText()) {
				$newdir = dirname( $file->getText() );

				if ($adminFiles){
					if (!JFolder::create($this->componentAdminDir().$newdir, 0755) ) {
						$this->setError( 1, 'Failed to create directory "' . ($this->componentAdminDir()) . $newdir . '"' );
						return false;
					}
				} else {
					if (!JFolder::create($this->elementDir().$newdir, 0755) ) {
						$this->setError( 1, 'Failed to create directory "' . ($this->elementDir()) . $newdir . '"' );
						return false;
					}
				}
			}
			$copyfiles[] = $file->getText();

			// check special for attribute
			if ($file->getAttribute( $special )) {
				$this->elementSpecial( $file->getAttribute( $special ) );
			}
		}

		if ($specialError) {
			if ($this->elementSpecial() == '') {
				$this->setError( 1, $specialError );
				return false;
			}
		}

		if ($tagName == 'media') {
			// media is a special tag
			$installTo = $this->JQ_PathName( JPATH_SITE . '/images/stories' );
		} else if ($adminFiles) {
			$installTo = $this->componentAdminDir();
		} else {
			$installTo = $this->elementDir();
		}
		$result = $this->copyFiles( $installFrom, $installTo, $copyfiles );

		return $result;
	}
	/**
	* @param string Source directory
	* @param string Destination directory
	* @param array array with filenames
	* @param boolean True is existing files can be replaced
	* @return boolean True on success, False on error
	*/
	function copyFiles( $p_sourcedir, $p_destdir, $p_files, $overwrite=false ) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');
				
		if (is_array( $p_files ) && !empty( $p_files )) {
			foreach($p_files as $_file) {
				$filesource	= $this->JQ_PathName( $this->JQ_PathName( $p_sourcedir ) . $_file, false );
				$filedest	= $this->JQ_PathName( $this->JQ_PathName( $p_destdir ) . $_file, false );
				
				if (!file_exists( $filesource )) {
					$this->setError( 1, "File $filesource does not exist!" );
					return false;
				} else if (file_exists( $filedest ) && !$overwrite) {
					$this->setError( 1, "There is already a file called $filedest - Are you trying to install the same CMT twice?" );
					return false;
				} else {
                    $path_info = pathinfo($_file);
                    if (!is_dir( $path_info['dirname'] )){
						JFolder::create($p_destdir. $path_info['dirname'], 0755);                                                
                    }
										
					if( !( JFile::copy($filesource,$filedest) && JPath::setPermissions($filedest) ) ) {
						$this->setError( 1, "Failed to copy file: $filesource to $filedest" );
						return false;
					}
				}
			}
		} else {
			return false;
		}
		return count( $p_files );
	}
	/**
	* Copies the XML setup file to the element Admin directory
	* Used by Components/Modules/Mambot Installer Installer
	* @return boolean True on success, False on error
	*/
	function copySetupFile( $where='admin' ) {
		if ($where == 'admin') {
			return $this->copyFiles( $this->installDir(), $this->componentAdminDir(), array(  $this->installFilename() ), true );
		} else if ($where == 'front') {
			return $this->copyFiles( $this->installDir(), $this->elementDir(), array( $this->installFilename()  ), true );
		}
	}

	/**
	* @param int The error number
	* @param string The error message
	*/
	function setError( $p_errno, $p_error ) {
		$this->errno( $p_errno );
		$this->error( $p_error );
	}
	/**
	* @param boolean True to display both number and message
	* @param string The error message
	* @return string
	*/
	function getError($p_full = false) {
		if ($p_full) {
			return $this->errno() . " " . $this->error();
		} else {
			return $this->error();
		}
	}
	/**
	* @param string The name of the property to set/get
	* @param mixed The value of the property to set
	* @return The value of the property
	*/
	function &setVar( $name, $value=null ) {
		if (!is_null( $value )) {
			$this->$name = $value;
		}
		return $this->$name;
	}

	function installFilename( $p_filename = null ) {
		if(!is_null($p_filename)) {
			if($this->isWindows()) {
				$this->i_installfilename = str_replace('/','\\',$p_filename);
			} else {
				$this->i_installfilename = str_replace('\\','/',$p_filename);
			}
		}
		return $this->i_installfilename;
	}

	function installType( $p_installtype = null ) {
		return $this->setVar( 'i_installtype', $p_installtype );
	}

	function error( $p_error = null ) {
		return $this->setVar( 'i_error', $p_error );
	}

	function &xmlDoc( $p_xmldoc = null ) {
		return $this->setVar( 'i_xmldoc', $p_xmldoc );
	}

	function installArchive( $p_filename = null ) {
		return $this->setVar( 'i_installarchive', $p_filename );
	}

	function installDir( $p_dirname = null ) {
		return $this->setVar( 'i_installdir', $p_dirname );
	}

	function unpackDir( $p_dirname = null ) {
		return $this->setVar( 'i_unpackdir', $p_dirname );
	}

	function isWindows() {
		return $this->i_iswin;
	}

	function errno( $p_errno = null ) {
		return $this->setVar( 'i_errno', $p_errno );
	}

	function hasInstallfile( $p_hasinstallfile = null ) {
		return $this->setVar( 'i_hasinstallfile', $p_hasinstallfile );
	}

	function installfile( $p_installfile = null ) {
		return $this->setVar( 'i_installfile', $p_installfile );
	}

	function elementDir( $p_dirname = null )	{
		return $this->setVar( 'i_elementdir', $p_dirname );
	}
	
	function componentAdminDir( $p_dirname = null )	{
		return $this->setVar( 'i_componentadmindir', $p_dirname );
	}
	
	function elementName( $p_name = null )	{
		return $this->setVar( 'i_elementname', $p_name );
	}
	function elementSpecial( $p_name = null )	{
		return $this->setVar( 'i_elementspecial', $p_name );
	}
}

if (!function_exists('cleanupInstall')) {
function cleanupInstall( $userfile_name, $resultdir) {
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');
	
	if (file_exists( $resultdir )) {
		JFolder::delete( $resultdir );
		JFile::delete( JPATH_SITE . '/tmp/' . $userfile_name );
	}
}
}

if (!function_exists('deldir')) {
function deldir( $dir ) {
	$current_dir = opendir( $dir );
	$old_umask = umask(0);
	while ($entryname = readdir( $current_dir )) {
		if ($entryname != '.' and $entryname != '..') {
			if (is_dir( $dir . $entryname )) {
				deldir( $this->JQ_PathName( $dir . $entryname ) );
			} else {
                @chmod($dir . $entryname, 0777);
				unlink( $dir . $entryname );
			}
		}
	}
	umask($old_umask);
	closedir( $current_dir );
	return rmdir( $dir );
} 
}

class JQ_InstallerQuestions extends JQ_Installer {

	function install( $p_fromdir = null ) {
		jimport('joomla.filesystem.folder');
		if (!$this->preInstallCheck( $p_fromdir, 'question' )) {
			return false;
		}
		
		$xmlDoc 	=& $this->xmlDoc();
		$jqinstall =& $xmlDoc->documentElement;
		$method = $jqinstall->getAttribute('method');
		
		if(!isset($method) ||  $method != 'upgrade'){
			$this->setError(1, 'Method must be upgrade!' );
			return false;
		}
		
		// Set some vars
		$e = &$jqinstall->getElementsByPath( 'name', 1 );
		$this->elementName($e->getText());
		if ($e = &$jqinstall->getElementsByPath( 'description', 1 )) {
			$this->setError( 0, $this->elementName() . '<p>' . $e->getText() . '</p>' );
		}
		
		#-----------------Administrator---------------------#
		$admin = &$jqinstall->getElementsByPath( 'administrator', 1 );
		if (!$admin->hasChildNodes()) {
			return 0;
		}
		
		$this->componentAdminDir( $this->JQ_PathName( JPATH_SITE
		. '/administrator/components/' . strtolower(str_replace(" ","_",$this->elementName())).'/')
		);
		$this->installDir($this->installDir().'admin/');
		
		$childs = $admin->childNodes;
		foreach($childs as $child){
			$files = preg_split('/\.php/', $child->getText());
			if(!empty($files)){
				foreach($files as $file){
					if($file == '') continue;
					$this->installFilename($file.'.php');
					$this->copySetupFile('admin');
				}
			}
		}
		
		#-----------------End Administrator---------------------#
		
		#-----------------Start Site---------------------#
		$site = &$jqinstall->getElementsByPath( 'files', 1 );
		if (!$site->hasChildNodes()) {
			return 0;
		}
		$this->elementDir($this->JQ_PathName( JPATH_SITE
		. '/components/' . strtolower(str_replace(" ","_",$this->elementName())).'/'));
		
		$this->installDir(str_replace("admin/", "", $this->installDir()));
		$this->installDir($this->installDir().'site/');
		$childs = $site->childNodes;
		foreach($childs as $child){
			$files = preg_split('/\.php/', $child->getText());
			if(!empty($files)){
				foreach($files as $file){
					if($file == '') continue;
					
					if (strpos($file, ".js") === false){
						$this->installFilename($file.'.php');
					} else {
						$this->installFilename($file);
					}
					
					$this->copySetupFile('front');
				}
			}
		}
		
		#-----------------End Site---------------------#
		
		#-----------------------------SQL-----------------------#
		$type_element = &$jqinstall->getElementsByPath( 'type', 1 );
		$type = $type_element->getText();
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT COUNT(c_id) FROM #__quiz_t_qtypes WHERE c_type = '".$type."'");
		if($db->loadResult()){
			return true;
		}
		
		$sql = &$jqinstall->getElementsByPath( 'sql', 1 );
		if (!$sql->hasChildNodes()) {
			return 0;
		}
		$queries = $sql->childNodes;
		foreach($queries as $query){
			$db->setQuery($query->getText());
			if(!$db->query()){
				$this->setError(1, 'Query is not correct!' );
				return false;
			}
		}
		#-----------------------------SQL-----------------------#
		return true;
	}
	
	function returnTo( $option ) {
		return "index.php?option=".$option."&view=addons";
	}
	function isPackageFile( $p_file ) {
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );

		if (!$xmlDoc->loadXML( $p_file, false, true )) {
			

			return null;
		}
		$root = &$xmlDoc->documentElement;

		if ($root->getTagName() != 'jqinstall') {

			return null;
		}
		// Set the type
		$this->installType( $root->getAttribute( 'type' ) );
		$this->installFilename( $p_file );
		return $xmlDoc;
	}
	function readInstallFile() {

		if ($this->installFilename() == "") {
			$this->setError( 1, 'No filename specified' );
			return false;
		}

		$this->i_xmldoc = new DOMIT_Lite_Document();
		$this->i_xmldoc->resolveErrors( true );
		if (!$this->i_xmldoc->loadXML( $this->installFilename(), false, true )) {
			return false;
		}
		$root = &$this->i_xmldoc->documentElement;

		// Check that it's an installation file
		if ($root->getTagName() != 'jqinstall') {
			$this->setError( 1, 'File :"' . $this->installFilename() . '" is not a valid JoomlaQuiz question installation file' );
			return false;
		}

		$this->installType( $root->getAttribute( 'type' ) );
		return true;
	}
}

class JQ_InstallerTemplate extends JQ_Installer {

	function install( $p_fromdir = null ) {
		global $database;
		jimport('joomla.filesystem.folder');

		if (!$this->preInstallCheck( $p_fromdir, 'template' )) {
			return false;
		}

		$xmlDoc 	=& $this->xmlDoc();
		$mosinstall =& $xmlDoc->documentElement;

		$client = 'admin';

		// Set some vars
		$e = &$mosinstall->getElementsByPath( 'name', 1 );
		$this->elementName($e->getText());
		$this->elementDir( $this->JQ_PathName( JPATH_SITE
		. '/media/joomlaquiz/' . strtolower(str_replace(" ","_",$this->elementName())))
		);

		if (!file_exists( $this->elementDir() ) && !JFolder::create($this->elementDir(), 0755) ) {
			$this->setError(1, 'Failed to create directory "' . $this->elementDir() . '"' );
			return false;
		}

		if ($this->parseFiles( 'files' ) === false) {
			return false;
		}
		if ($this->parseFiles( 'images' ) === false) {
			return false;
		}
		if ($e = &$mosinstall->getElementsByPath( 'description', 1 )) {
			$this->setError( 0, $this->elementName() . '<p>' . $e->getText() . '</p>' );
		}

		return $this->copySetupFile('front');
	}
	function uninstall( $id, $option ) {
		global $database;
		$tmpl_name = '';
		$database->SetQuery("SELECT template_name FROM #__quiz_templates WHERE id = '".$id."'");
		$tmpl_name = $database->LoadResult();
		// Delete directories
		$path = JPATH_SITE
		. '/media/joomlaquiz/' . $tmpl_name;

		$tmpl_name = str_replace( '..', '', $tmpl_name );
		if ($id == 1) {
			admin_JoomQuiz_html::showInstallMessage( 'You cannot remove this template', 'Uninstall -  error',
				$this->returnTo( $option ) );
			exit();
		} else {
			if (trim( $tmpl_name )) {
				if (is_dir( $path )) {
					$ret = deldir( $this->JQ_PathName( $path ) ); //function from installer.class.php joomla file
					return $ret;
				} else {
					admin_JoomQuiz_html::showInstallMessage( 'Directory does not exist, cannot remove files', 'Uninstall -  error',
						$this->returnTo( $option ) );
				}
			} else {
				admin_JoomQuiz_html::showInstallMessage( 'Template id is empty, cannot remove files', 'Uninstall -  error',
					$this->returnTo( $option ) );
				exit();
			}
		}
	}
	function returnTo( $option ) {
		return "index.php?option=".$option."&task=templates";
	}
	function isPackageFile( $p_file ) {
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );

		if (!$xmlDoc->loadXML( $p_file, false, true )) {
			

			return null;
		}
		$root = &$xmlDoc->documentElement;

		if ($root->getTagName() != 'jqinstall') {

			return null;
		}
		// Set the type
		$this->installType( $root->getAttribute( 'type' ) );
		$this->installFilename( $p_file );
		return $xmlDoc;
	}
	function readInstallFile() {

		if ($this->installFilename() == "") {
			$this->setError( 1, 'No filename specified' );
			return false;
		}

		$this->i_xmldoc = new DOMIT_Lite_Document();
		$this->i_xmldoc->resolveErrors( true );
		if (!$this->i_xmldoc->loadXML( $this->installFilename(), false, true )) {
			return false;
		}
		$root = &$this->i_xmldoc->documentElement;

		// Check that it's an installation file
		if ($root->getTagName() != 'jqinstall') {
			$this->setError( 1, 'File :"' . $this->installFilename() . '" is not a valid JoomlaQuiz template installation file' );
			return false;
		}

		$this->installType( $root->getAttribute( 'type' ) );
		return true;
	}
}
?>