<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaquiz Deluxe Component Controller
 */
class JoomlaquizController extends JControllerLegacy
{
        /**
         * display task
         *
         * @return void
         */
        function display($cachable = false, $urlparams = array())
        {
        	$view = JFactory::getApplication()->input->getCmd('view', 'dashboard');
            JFactory::getApplication()->input->set('view', $view);
            parent::display($cachable);
        }

        function latestVersion()
        {
        	require_once(JPATH_BASE.'/components/com_joomlaquiz/assets/Snoopy.class.php' );
			$jq_version = JoomlaquizHelper::getVersion();
			$s = new Snoopy();
			$s->read_timeout = 90;
			$s->referer = JURI::root();
			@$s->fetch('http://www.joomplace.com/version_check/componentVersionCheck.php?component=quiz_deluxe&current_version='.urlencode($jq_version));
			$version_info = $s->results;
			$version_info_pos = strpos($version_info, ":");
			if ($version_info_pos === false) {
				$version = $version_info;
				$info = null;
			} else {
				$version = substr( $version_info, 0, $version_info_pos );
				$info = substr( $version_info, $version_info_pos + 1 );
			}

			$version_arr = explode('.',$version);
			array_pop($version_arr);

			$jq_version_arr = explode('.',$jq_version);
			array_pop($jq_version_arr);

			$actual_version = true;

			foreach ($version_arr as $key => $value) {
				if ((int)$value > (int)$jq_version_arr[$key]){
					break;
				}
				elseif ((int)$value < (int)$jq_version_arr[$key]) {
					$actual_version = false;
					break;
				}
			}

			if($s->error || $s->status != 200){
		    	echo '<font color="red">Connection to update server failed: ERROR: ' . $s->error . ($s->status == -100 ? 'Timeout' : $s->status).'</font>';
			} else if($actual_version){
		    	echo '<font color="green">' . $version . '</font>' . $info;
		    } else {
		    	echo '<font color="red">' . $version . '</font>&nbsp;<a href="http://www.joomplace.com/members-area.html" target="_blank">(Upgrade to the latest version)</a>' ;
		    }
		    exit();
        }

        public function latestNews()
        {
        	require_once(JPATH_BASE.'/components/com_joomlaquiz/assets/Snoopy.class.php' );

			$s = new Snoopy();
			$s->read_timeout = 10;
			$s->referer = JURI::root();
			@$s->fetch('http://www.joomplace.com/news_check/componentNewsCheck.php?component=quiz_deluxe');
			$news_info = $s->results;

			if($s->error || $s->status != 200){
		    	echo '<font color="red">Connection to update server failed: ERROR: ' . $s->error . ($s->status == -100 ? 'Timeout' : $s->status).'</font>';
		    } else {
			echo $news_info;
		    }
		    exit();
        }
		
		public function fix_database(){
			JPluginHelper::importPlugin( 'joomplace_lab' );
			$dispatcher = JEventDispatcher::getInstance();
			$results = $dispatcher->trigger('fixTableStructure');
			JFactory::getApplication()->enqueueMessage(JText::_('JDONE'));
			$this->diplay();
		}
}