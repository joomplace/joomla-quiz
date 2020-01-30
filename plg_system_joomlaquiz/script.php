<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Joomlaquiz
 *
 * @copyright   Copyright (C) 2005 - 2020 JoomPlace, www.joomplace.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class PlgSystemJoomlaquizInstallerScript
{
    public function install($parent)
    {
        // Enable plugin
        $db  = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__extensions');
        $query->set($db->qn('enabled') .'='. $db->q(1));
        $query->where($db->qn('name') .'='. $db->q('plg_system_joomlaquiz'));
        $query->where($db->qn('element') .'='. $db->q('joomlaquiz'));
        $query->where($db->qn('type') .'='. $db->q('plugin'));
        $query->where($db->qn('folder') .'='. $db->q('system'));
        $db->setQuery($query);
        $db->execute();
    }
}
