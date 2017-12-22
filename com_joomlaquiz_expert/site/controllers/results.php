<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controllerform');
 
/**
 * Results Controller
 */
class JoomlaquizControllerResults extends JControllerForm
{
  	public function getModel($name = 'results', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function sturesult(){
		
		$model = $this->getModel();
		$quiz_params = $model->getQuizParams();

		/*
		 * need to generate and set meta data
		 * and og:data
		 * depending on quiz_id
		 */
        $this->addFBMetaTags($quiz_params);
		require_once(JPATH_SITE.'/components/com_joomlaquiz/views/quiz/view.html.php');
		$view = $this->getView("quiz");
		$view->display(null, $quiz_params);
		
		return true;
	}

	protected function addFBMetaTags($quiz_params){
  	    if(!isset($quiz_params)){
  	        return false;
        }
        try{
            $db = JFactory::getDbo();
            $query = "SELECT q_chain FROM #__quiz_q_chain "
                . "\n WHERE s_unique_id = '".$quiz_params->result_data->unique_id."'";
            $db->SetQuery($query);
            $qch_ids = $db->LoadResult();
            $qch_ids = str_replace('*',',',$qch_ids);
            $max_score = JoomlaquizHelper::getTotalScore($qch_ids, $quiz_params->result_data->c_quiz_id);
            $user_score = $quiz_params->result_data->c_total_score;
            $user_score_percent = ($max_score) ? ($user_score/$max_score) * 100 : 0;
            $nugno_score = ($quiz_params->result_data->c_passing_score * $max_score) / 100;
            $query = "SELECT c_total_time FROM #__quiz_r_student_quiz WHERE c_id = '".$quiz_params->result_data->c_id."'";
            $db->SetQuery( $query );
            $user_time = $db->LoadResult();
            $tot_min = floor($user_time / 60);
            $tot_sec = $user_time - $tot_min*60;
            $tot_time = str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT);
            $desc =
                JText::_('COM_QUIZ_RES_MES_SCORE') . ' ' .
                sprintf(
                    JText::_('COM_QUIZ_RES_MES_SCORE_TPL'),
                    number_format($user_score,2, '.', ' '),
                    number_format($max_score, 2, '.', ' '),
                    number_format($user_score_percent, 2, '.', ' ')
                ) . '. ' .
                JText::_('COM_QUIZ_RES_MES_PAS_SCORE').'  '.sprintf(JText::_('COM_QUIZ_RES_MES_PAS_SCORE_TPL'),$nugno_score, $quiz_params->result_data->c_passing_score) . '. ' .
                JText::_('COM_QUIZ_RES_MES_TIME').'  '.$tot_time;

            $document = JFactory::getDocument();
            $document->setMetaData( 'og:title', 'Quiz Results', 'property' );
            $document->setMetaData( 'og:description', $desc, 'property' );
        }catch (Exception $e){
            //nothing
        }
    }

}
