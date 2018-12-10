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
	protected $state;
	protected $item;
	protected $form;
	
    public function display($tpl = null) 
    {
        $submenu = 'quizzes';
        JoomlaquizHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		
 		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
			
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
		
		$jq_cats = $this->get("Categories");
		$this->jq_cats	= JHTML::_('select.genericlist', $jq_cats, 'jform[c_category_id]', 'class="" size="1"', 'c_id', 'c_category', $this->item->c_category_id );
		
		$jq_temps = $this->get("Templates");
		$this->jq_templates = JHTML::_('select.genericlist', $jq_temps, 'jform[c_skin]', 'class="" size="1"', 'id', 'template_name', $this->item->c_skin );
		
		$jq_cert = $this->get("Certificates");
		
		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('COM_JOOMLAQUIZ_NO_CERTIFICATE'));
		$jq_cert = array_merge($options, $jq_cert);
		$this->c_certificates = JHTML::_('select.genericlist', $jq_cert, 'jform[c_certificate]', 'class="" size="1"', 'value', 'text', $this->item->c_certificate );
		
		$model = $this->getModel("Quiz");
		$quiz_data = $model->getQuizData($this->item->c_id);
		
		if($this->item->c_id)
		{
			$this->feed_opres = $quiz_data['feed_opres'];
			$this->if_pool = $quiz_data['if_pool'];
		}
			
		$qc_tag = $quiz_data['head_cat_arr'];
		$this->head_cat = JHTML::_('select.genericlist', $qc_tag, 'head_cat', 'class="" size="1" onchange="javascript:showHideCategories(this)"', 'value', 'text' );
		$this->jq_pool_cat = $quiz_data['jq_pool_cat'];
		$this->q_count = $quiz_data['q_count'];
		
		$this->addToolbar();
		parent::display($tpl);
    }
        
    protected function addToolbar()
	{
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JToolbarHelper::title(JText::_('COM_JOOMLAQUIZ_COMPONENT').': '.JText::_('COM_JOOMLAQUIZ_QUIZ_NEW_EDIT'));
		JFactory::getApplication()->input->set('hidemainmenu', true);
        if ($canDo->get('core.edit')) {
            JToolBarHelper::apply('quiz.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('quiz.save', 'JTOOLBAR_SAVE');
        }
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('quiz.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            JToolBarHelper::custom('quiz.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY',
                false);
        }
		JToolBarHelper::cancel('quiz.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
	}
}
?>
