<?php
/**
 * Joomlaquiz component for Joomla 3.0
 * @package Joomlaquiz
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class JoomlaquizModelCleaning extends JModelAdmin
{
    public function getForm($data = array(), $loadData = true)
    {
        $app	= JFactory::getApplication();
        $form = $this->loadForm('com_joomlaquiz.cleaning', 'cleaning', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }
}
