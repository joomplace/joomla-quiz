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
 * Results HTML View class for the Joomlaquiz Deluxe Component
 */
 
class JoomlaquizViewResults extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
    public $messageTrigger = false;

    function display($tpl = null) 
	{
        $document = JFactory::getDocument();
        $document->addScript('components/com_joomlaquiz/assets/js/js.js');

		$layout = $this->getLayout();
        $this->messageTrigger = $this->get('CurrDate');
		$this->addTemplatePath(JPATH_BASE.'/components/com_joomlaquiz/helpers/html');
		if($layout == 'stu_report'){
			$cid = JFactory::getApplication()->input->get('cid');
			$this->cid = $cid;

			$submenu = 'stu_report';
			JoomlaquizHelper::showTitle($submenu);			
			$this->addStureportToolBar();

			$this->items = $this->get('Items');
			$model = $this->getModel();
			foreach($this->items as &$row){
			    $row->c_point += $model->getItemSum($row);
			}

			$pagination = new JPagination($this->get('Total'), $this->get('Start'), $model->getState('list.limit'),'stu_');

			//$pagination = $this->get('Pagination');
			//$this->items = $model->getReportItems($cid, $pagination);
			
			if (!empty($errors = $this->get('Errors')))
			{
                JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
				return false;
			}	
			
			$this->pagination = $pagination;
			
		}else{
			$submenu = 'results';
			JoomlaquizHelper::showTitle($submenu);
			
			JoomlaquizHelper::addReportsSubmenu('results');
			$this->sidebar = JHtmlSidebar::render();
			$this->addToolBar();
							 
			$items 		= $this->get('Items');
			$pagination = $this->get('Pagination');
			$state		= $this->get('State');
			$lists		= $this->get('Lists');
			
			if (!empty($errors = $this->get('Errors')))
			{
                JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
				return false;
			}
			
			$this->items = $items;
			$this->pagination = $pagination;
			$this->state = $state;
			$this->lists = $lists;
		}
        parent::display($tpl);
    }
 
    /**
    * Setting the toolbar
    */
	protected function addToolBar() 
    {
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JToolBarHelper::custom('results.csv_summary', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_SUMMARY', false);
		JToolBarHelper::custom('results.csv_report', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_CSV', true);
		JToolBarHelper::custom('results.stu_report', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_VIEW', true);
		JToolBarHelper::custom('results.csv_flag_questions', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_FLAG_QUESTIONS', true);
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'results.delete');
            JToolBarHelper::custom('results.del_flags', 'delete.png', 'delete_f2.png', 'COM_JOOMLAQUIZ_DELETE_FLAGS', true);
        }
    }
	
	protected function addStureportToolBar()
	{
        $canDo = JHelperContent::getActions('com_joomlaquiz', 'component');
        JToolBarHelper::custom('result.quest_report', 'featured.png', 'featured_f2.png', 'COM_JOOMLAQUIZ_VIEW', true);
        if ($canDo->get('core.delete')) {
            JToolBarHelper::custom('result.del_stu_report', 'delete.png', 'delete_f2.png', 'COM_JOOMLAQUIZ_DELETE', true);
        }
		JToolBarHelper::custom('result.view_reports', 'previous.png', 'previous_f2.png', 'COM_JOOMLAQUIZ_BACK', false);
	}
	
	protected function getSortFields()
	{
		return array(
			'c_date_time' => JText::_('COM_JOOMLAQUIZ_DATA_TIME'),
			'c_title' => JText::_('COM_JOOMLAQUIZ_QUIZ2'),
			'c_total_score' => JText::_('COM_JOOMLAQUIZ_USER_SCORE'),
			'c_full_score' => JText::_('COM_JOOMLAQUIZ_TOTAL_SCORE'),
			'c_passing_score' => JText::_('COM_JOOMLAQUIZ_PASSING_SCORE'),
			'c_passed' => JText::_('COM_JOOMLAQUIZ_PASSED2'),
			'c_total_time' => JText::_('COM_JOOMLAQUIZ_SPEND_TIME'),
		);
	}

	protected function getConvertedURL($url) {
		$newUrl = $url;

		$router = new JRouterSite(array('mode'=>JROUTER_MODE_SEF));
		$newUrl = $router->build($newUrl)->toString(array('path', 'query', 'fragment'));

		$newUrl = str_replace('/administrator/', '/', $newUrl);
		$newUrl = str_replace('component/content/article/', '', $newUrl);

		return $newUrl;
	}
}
