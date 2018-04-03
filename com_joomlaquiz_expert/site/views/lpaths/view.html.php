<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
 defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomlaquiz Deluxe Component
 */
class JoomlaquizViewLpaths extends JViewLegacy
{
    public $lpath_data;

    public function display($tpl = null)
    {
        $this->lpath_data = $this->get('LearningPaths');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        if($this->lpath_data != false){
            $this->user = JFactory::getUser();
            foreach ($this->lpath_data as $k=> $lpath) {
                $viewAccessGranted = $this->user->authorise('core.access', 'com_joomlaquiz.lp.'.$lpath->id);
                if (!$viewAccessGranted) {
                    unset($this->$lpath[$k]);
                }
            }
        }

        if (empty($this->lpath_data)) { ?>
            <div>
                <p><strong style="color:red;"><?= JText::_('COM_JOOMLAQUIZ_LPS_NOT_AVAILABLE') . '.'; ?></strong></p>
            </div>
            <?php

        }
        parent::display($tpl);
    }
}
?>
