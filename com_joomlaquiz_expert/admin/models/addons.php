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
 * Addons model.
 *
 */
class JoomlaquizModelAddons extends JModelAdmin
{
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		return;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		return;
	}
	
	public function install(){
		jimport('joomla.filesystem.files');
		
		$option = 'com_joomlaquiz';
		// XML library
		require_once(JPATH_BASE . '/components/com_joomlaquiz/assets/domit/xml_domit_lite_include.php' ); 
		require_once(JPATH_BASE . "/components/com_joomlaquiz/assets/installer.joomlaquiz.php");

		$installer = new JQ_InstallerQuestions();
		// Check if file uploads are enabled
		if (!(bool)ini_get('file_uploads')) {
			$this->showInstallMessage( JText::_('COM_JOOMLAQUIZ_INSTALLER_CANT'), JText::_('COM_JOOMLAQUIZ_INSTALLER_ERROR'), $installer->returnTo( $option ) );
			exit();
		}

		// Check that the zlib is available
		if(!extension_loaded('zlib')) {
			$this->showInstallMessage( JText::_('COM_JOOMLAQUIZ_INSTALLER_ERROR_ZLIB'), JText::_('COM_JOOMLAQUIZ_INSTALLER_ERROR'), $installer->returnTo( $option ) );
			exit();
		}

        $userfile = JFactory::getApplication()->input->files->get('userfile', array(), 'array');
		
		if (!$userfile) {
			$this->showInstallMessage( JText::_('COM_JOOMLAQUIZ_NO_FILE_SELECTED'), JText::_('COM_JOOMLAQUIZ_UPLOAD_NEW_TEMPLATE'), $installer->returnTo( $option ));
			exit();
		}

		$userfile_name = $userfile['name'];
		
		$baseDir = JPATH_SITE . '/tmp/';
		$msg = '';
		$resultdir = JFile::move( $userfile['tmp_name'], $baseDir.$userfile['name'] );
		
		if ($resultdir !== false) {
			if (!$installer->upload( $userfile['name'] )) {
				$this->showInstallMessage( $installer->getError(), JText::_('COM_JOOMLAQUIZ_UPLOAD_TEMPLATE'),
				$installer->returnTo( $option ) );
			}
			$ret = $installer->install();
			cleanupInstall( $userfile['name'], $installer->unpackDir() );
			
			return JText::_('COM_JOOMLAQUIZ_NEW_QUESTION_SUCCESSFULLY_INSTALLED');
		} else {
			$this->showInstallMessage( $msg, JText::_('COM_JOOMLAQUIZ_UPLOAD_TEMPLATE'), $installer->returnTo( $option ) );
		}
	}
	
	function showInstallMessage( $message, $title, $url ) {
		?>
		<table class="adminheading">
		<tr>
			<th class="install">
			<?php echo $title; ?>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<td align="left">
			<strong><?php echo $message; ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
			[&nbsp;<a href="<?php echo $url;?>" style="font-size: 16px; font-weight: bold"><?php echo JText::_('COM_JOOMLAQUIZ_CONTINUE');?></a>&nbsp;]
			</td>
		</tr>
		</table>
		<?php
	}

    public function getCurrDate()
    {
        $db = $this->_db;
        $query = $db->getQuery(true);
        $query->select('c_par_value');
        $query->from('`#__quiz_setup`');
        $query->where("c_par_name='curr_date'");


        $result = $db->setQuery($query)->loadResult();
        if (strtotime("+2 month",strtotime($result))<=strtotime(JFactory::getDate())) {
            return true;
        } else {
            return false;
        }
    }

}