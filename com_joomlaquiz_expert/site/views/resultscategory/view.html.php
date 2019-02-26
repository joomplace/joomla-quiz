<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
 defined('_JEXEC') or die();

class JoomlaquizViewResultscategory extends JViewLegacy
{
    protected $state;

    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->categoryname = $this->get('Categoryname');

        if($this->state->get('filter.startstate') == -1){
            $this->results = $this->get('NotStarted');
            $tpl = 'notstarted';
        } else {
            $this->results = $this->get('Results');
        }

		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

		if($this->results == 'no_access'){
            ?>
            <div>
                <span style="color:red;"><?php echo JText::_('COM_JOOMLAQUIZ_FE_NO_RIGHTS_VIEW_RESULTS') . '.'; ?></span>
                <br/><br/>
            </div>
            <?php
        }else{
            parent::display($tpl);
        }
    }
}
?>
