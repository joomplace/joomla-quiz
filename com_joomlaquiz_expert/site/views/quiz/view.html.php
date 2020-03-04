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
class JoomlaquizViewQuiz extends JViewLegacy
{
    public function display($tpl = null, $quiz_params = null, $is_preview = false, $preview_quest = 0, $preview_id = '')
    {
		$this->quiz_params = ($quiz_params !== null) ? $quiz_params : $this->get('QuizParams');
		
		$this->is_preview = $is_preview;
		$this->preview_quest = $preview_quest;
		$this->preview_id = $preview_id;

		$component_params = JComponentHelper::getParams('com_joomlaquiz');
		$this->margin_top = $component_params->get('margin_top', 0);
		
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

        if(!$this->is_preview)
        {
            $session = JFactory::getSession();
            if(@$this->quiz_params->rel_id
                && !empty($this->quiz_params->rel_data) && !empty($this->quiz_params->rel_data->c_passed) && !empty($this->quiz_params->rel_data->c_finished)
                && !$this->quiz_params->force)
            {
                $session->set('jq_result_mode', array(@$this->quiz_params->rel_id, @$this->quiz_params->rel_data->c_quiz_id, @$this->quiz_params->package_id));
            }
            else if(@$this->quiz_params->lid && isset($this->quiz_params->lid_data) && $this->quiz_params->lid_data->c_passed
                    && $this->quiz_params->lid_data->c_finished  && !$this->quiz_params->force)
            {
                $session->set('jq_result_mode_lid', array(@$this->quiz_params->lid, @$this->quiz_params->lid_data->c_quiz_id));
            }
            else if(isset($quiz->result_data))
            {
                $session->set('jq_result_mode_5', array($this->quiz_params->result_data, $this->quiz_params->result_data->c_quiz_id));
            }
        }

		if(!$this->quiz_params->error){
			// Checking access and displaying.
			$this->user = JFactory::getUser();
			$viewAccessGranted = $this->user->authorise('core.view', 'com_joomlaquiz.quiz.'.$this->quiz_params->c_id);

            if (!empty($viewAccessGranted) || !empty($this->quiz_params->c_guest)
                || ( !empty($this->quiz_params->package_id) && !empty($this->quiz_params->product_data->id) && !empty($this->quiz_params->is_attempts) ) //quiz in product
            ) {
				parent::display($tpl);

			} else {

	            $db = JFactory::getDBO();
	            $db->setQuery('SELECT c_quiz_access_message FROM #__quiz_t_quiz WHERE c_id='.$this->quiz_params->c_id);
	            $msgDbText = $db->loadResult();
	            if(!empty($msgDbText)){
	                echo $msgDbText;
	            }else{
	                ?>
	                    <div>
	                        <span style="color:red;"><?php echo JText::_('COM_JOOMLAQUIZ_FE_NO_RIGHTS_VIEW_QUIZ') . '.'; ?></span>
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
