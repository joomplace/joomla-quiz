<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

/**
 * Statistic model.
 *
 */
class JoomlaquizModelStatistic extends JModelList
{
	public function getStatisticData(){
		
		$database = JFactory::getDBO();
		$app = JFactory::getApplication();
	
		// $quiz_id	= intval( $app->getUserStateFromRequest( "filter.quiz_id", 'filter_quiz_id', -1 ) );
        $post = \JFactory::getApplication()->input->post;
        $quiz_id = $post->getInt('filter_quiz_id', 0);
		/*if ($quiz_id < 1) {
			$query = "SELECT c_quiz_id FROM #__quiz_r_student_quiz ORDER BY c_id DESC";
			$database->setQuery( $query );
			$quiz_id = (int)$database->loadResult();
		}*/
		
		
		$query = "SELECT c_id AS value, c_title AS text FROM #__quiz_t_quiz ORDER BY c_title";
		$database->setQuery( $query );
		$quizzes = $database->loadObjectList();

		if($quizzes && is_array($quizzes)){
            array_unshift($quizzes, (object) array('value' => 0, 'text' => JText::_('JSELECT')));
        }

		$javascript = 'onchange="document.adminForm.submit();"';
		$quizzes = JHTML::_('select.genericlist', $quizzes, 'filter_quiz_id', 'class="text_area" size="1" style="max-width: 300px;" '.$javascript, 'value', 'text', $quiz_id ); 
		
		$statistic = array();
		$statistic['quizzes'] = $quizzes;
		
		$summary = array();
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1";
		$database->setQuery( $query );
		$summary['total_finished'] = $database->loadResult();
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1 AND c_passed = 1";
		$database->setQuery( $query );
		$summary['total_passed'] = $database->loadResult();
		
		$query = "SELECT MAX(c_total_score) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1";
		$database->setQuery( $query );
		$summary['user_max_score'] = (int)$database->loadResult();
		$query = "SELECT MIN(c_total_score) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1";
		$database->setQuery( $query );
		$summary['user_min_score'] = (int)$database->loadResult();
		$query = "SELECT AVG(c_total_score) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1";
		$database->setQuery( $query );
		$summary['user_avg_score'] = (int)$database->loadResult();
		
		$query = "SELECT c_full_score FROM #__quiz_t_quiz WHERE c_id = '$quiz_id' ";
		$database->setQuery( $query );
		$summary['quiz_max_score'] = $database->loadResult();
		$query = "SELECT c_passing_score FROM #__quiz_t_quiz WHERE c_id = '$quiz_id' ";
		$database->setQuery( $query );
		$summary['quiz_pass_score'] = $database->loadResult();
		
		$statistic['summary'] = $summary;
	
		$query = "SELECT * FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND published = 1 AND c_type NOT IN (9) ORDER BY ordering, c_id";
		$database->SetQuery( $query );
		$questions = $database->LoadObjectList();
				
		if (is_array($questions))
		foreach($questions as $ii => &$question){
			
			$img_urls = array();
			$pat_im = '/<img[^>]+src=([\'|\"])([^>]+)\1[^>]*>/iU';
			$pat_url = '/^(http|https|ftp):\/\//i';
			$out_arr = preg_split($pat_im, $question->c_question);
			if(preg_match_all($pat_im, $question->c_question, $quest_images, PREG_SET_ORDER))
			{
				foreach($quest_images as $img_c => $quest_image){
					$img_urls[$img_c] = @$quest_image[2];
					if(preg_match($pat_url, $img_urls[$img_c], $url_match)){
						$question->c_question[$img_c] = '';
					}
				}
			}
			
			$out_html = "";
			if(!empty($out_arr))
			{
				foreach($out_arr as $html_c => $html_peace){
					if(count($out_arr) != $html_c && isset($img_urls[$html_c])){
						if(!$img_urls[$html_c]){
						$out_html .= $html_peace.$quest_images[$html_c][0];
						} else {
						$src_arr = explode($quest_images[$html_c][2], $quest_images[$html_c][0]);
						$img_tag = implode(JURI::root().$quest_images[$html_c][2], $src_arr);
						$out_html .= $html_peace.$img_tag;
						}
					} else {
						$out_html .= $html_peace;
					}
				}
				$questions[$ii]->c_question = $out_html;
			}
			
			$data = array();
			$type		= JoomlaquizHelper::getQuestionType($question->c_type);
			$data['quest_type'] = $type;
			$data['question'] = $question;
			
			$className = 'plgJoomlaquiz'.ucfirst($type);
			$appsLib = JqAppPlugins::getInstance();
			$appsLib->loadApplications();
			
			$quest = (method_exists($className, 'onGetAdminQuestionData')) ? $appsLib->triggerEvent( 'onGetAdminQuestionData' , $data ) : array();
			$question = @$quest[0];
		}
		
		$statistic['questions'] = $questions;
		return $statistic;
	}

    public function getCurrDate()
    {
        $db = $this->_db;
        $query = $db->getQuery(true);
        $query->select('c_par_value');
        $query->from('`#__quiz_setup`');
        $query->where("c_par_name='curr_date'");


        $result = $db->setQuery($query)->loadResult();
        if (strtotime("+2 month",strtotime($result))<=strtotime(JFactory::getDate())) {
            return true;
        } else {
            return false;
        }
    }
}