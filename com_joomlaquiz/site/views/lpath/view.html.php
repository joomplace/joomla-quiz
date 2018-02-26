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
class JoomlaquizViewLpath extends JViewLegacy
{

    public function display($tpl = null)
    {
        $this->lpath_data = $this->get('LearningPaths');
        $this->allow_certificate = $this->_models[$this->_defaultModel]->getAllowCertificate(!empty($this->lpath_data[0]->id) ? $this->lpath_data[0]->id : false);

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        if (!isset($this->lpath_data[0]->error)) {

            $this->user = JFactory::getUser();
            $viewAccessGranted = $this->user->authorise('core.access', 'com_joomlaquiz.lp.' . $this->lpath_data[0]->id);

            if ($viewAccessGranted) {
                parent::display($tpl);
            } else {

                $db = JFactory::getDBO();
                $db->setQuery('SELECT lp_access_message FROM #__quiz_lpath WHERE id=' . $this->lpath_data[0]->id);
                $msgDbText = $db->loadResult();
                if (!empty($msgDbText)) {
                    echo $msgDbText;
                } else {

                    ?>
                    <div>
                        <span style="color:red;"><?php echo JText::_('COM_JOOMLAQUIZ_LP_ACCESS_MSG_FE') . '.'; ?></span>
                        <br/><br/>
                    </div>
                    <?php
                }
            }
        } else {

            parent::display($tpl);
        }
    }
}

?>
