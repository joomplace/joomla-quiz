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

            $sshare_message = (JText::_('COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE_'.$quiz_params->result_data->c_quiz_id) && JText::_('COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE_'.$quiz_params->result_data->c_quiz_id)!='COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE_'.$quiz_params->result_data->c_quiz_id)?(JText::_('COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE_'.$quiz_params->result_data->c_quiz_id)):(JText::_('COM_QUIZ_SOCIAL_SCORE_SHARING_MESSAGE'));
            $user_score_replaced = sprintf($sshare_message, number_format($user_score, 2, '.', ' '), number_format($max_score, 2, '.', ' ')).$quiz_params->c_title;

            $document = JFactory::getDocument();
            $document->setMetaData( 'og:title', $user_score_replaced, 'property' );
            if(isset($quiz_params->c_image) && file_exists($quiz_params->c_image)){
                $document->setMetaData( 'og:image', $quiz_params->c_image, 'property' );
            }
        }catch (Exception $e){
            //nothing
        }
    }

}
