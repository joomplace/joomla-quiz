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
            $this->migrateParamsOnAir();
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
				if ((int)$jq_version_arr[$key] > (int)$value){
					break;
				}
				elseif ((int)$jq_version_arr[$key] < (int)$value) {
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

    public function migrateParamsOnAir()
    {
        $db      = JFactory::getDbo();
        $columns = $db->getTableColumns('#__quiz_t_quiz');
        if (array_key_exists('c_guest', $columns)) {
            /*
             * Migrate guest access to permissions
             */
            $query = $db->getQuery(true);
            $query->select($db->qn('c_id'))
                ->select($db->qn('c_title'))
                ->select($db->qn('c_guest'))
                ->from($db->qn('#__quiz_t_quiz'));
            $rows = $db->setQuery($query)->loadObjectList();
            /** @var JTableAsset $asset */
            $asset       = JTable::getInstance('Asset');
            $rule        = "core.view";
            $user        = JFactory::getUser(0);
            $user_groups = $user->getAuthorisedGroups();
            $guest_group = array_pop($user_groups);
            foreach ($rows as $row) {
                $asset_name = 'com_joomlaquiz.quiz.' . $row->c_id;
                $asset->loadByName($asset_name);
                $rules = !empty($asset->rules) ? json_decode($asset->rules) : new stdClass();
                if (isset($rules->$rule) && !is_array($rules->$rule)) {
                    if (!$rules->$rule->$guest_group) {
                        $rules->$rule->$guest_group = $row->c_guest;
                    }
                } else {
                    $rules->$rule               = new stdClass();
                    $rules->$rule->$guest_group = $row->c_guest;
                }
                $asset->rules = json_encode($rules);
                $asset->name = $asset_name;
                $asset->store();
                if (!$user->authorise('core.view', $asset_name)
                    && $user->authorise('core.view', $asset_name)
                    != $row->c_guest
                ) {
                    JFactory::getApplication()
                        ->enqueueMessage('There might be something wrong with guest access for quiz #'
                            . $row->c_id . ' ' . $row->c_title
                            . '. Please check quiz settings. (Previously "guest access" was '
                            . ($row->c_guest ? 'enabled' : 'disabled') . ')');
                }
            }
            $db->setQuery("ALTER TABLE `#__quiz_t_quiz` DROP `c_guest`;")
                ->execute();
        }
        $db->setQuery('DELETE FROM `#__quiz_t_quiz` WHERE `c_title` = "Questions Pool"')->execute();
    }
}