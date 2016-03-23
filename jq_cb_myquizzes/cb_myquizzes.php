<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;

/** ensure this file is being included by a parent file */
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) {	die( 'Direct Access to this location is not allowed.' ); }

jimport('joomla.plugin.plugin');
if(!class_exists('getCommunityMyQuizzes')){
class getCommunityMyQuizzes extends cbTabHandler {
	
	function getCommunityMyQuizzes() {
		$this->cbTabHandler();
		if (!defined('_UE_MYQUIZZES_TEST_CATEGORY')) DEFINE('_UE_MYQUIZZES_TEST_CATEGORY',JText::_('_UE_MYQUIZZES_TEST_CATEGORY'));
		if (!defined('_UE_MYQUIZZES_TEST_SCORE')) DEFINE('_UE_MYQUIZZES_TEST_SCORE',JText::_('_UE_MYQUIZZES_TEST_SCORE'));
		if (!defined('_UE_MYQUIZZES_PERCENTITLE')) DEFINE('_UE_MYQUIZZES_PERCENTITLE',JText::_('_UE_MYQUIZZES_PERCENTITLE'));
		if (!defined('_UE_MYQUIZZES_RANK')) DEFINE('_UE_MYQUIZZES_RANK',JText::_('_UE_MYQUIZZES_RANK'));
		if (!defined('_UE_MYQUIZZES_DURATION')) DEFINE('_UE_MYQUIZZES_DURATION',JText::_('_UE_MYQUIZZES_DURATION'));
		if (!defined('_UE_MYQUIZZES_PLACE')) DEFINE('_UE_MYQUIZZES_PLACE',JText::_('_UE_MYQUIZZES_PLACE'));
		if (!defined('_UE_MYQUIZZES_TEST_STATISTICS')) DEFINE('_UE_MYQUIZZES_TEST_STATISTICS',JText::_('_UE_MYQUIZZES_TEST_STATISTICS'));
		if (!defined('_UE_MYQUIZZES_NUMBER_OF_TEST')) DEFINE('_UE_MYQUIZZES_NUMBER_OF_TEST',JText::_('_UE_MYQUIZZES_NUMBER_OF_TEST'));
		if (!defined('_UE_MYQUIZZES_NUMBER_PROVIDERS')) DEFINE('_UE_MYQUIZZES_NUMBER_PROVIDERS',JText::_('_UE_MYQUIZZES_NUMBER_PROVIDERS'));
		if (!defined('_UE_MYQUIZZES_AVERAGE_SCORE')) DEFINE('_UE_MYQUIZZES_AVERAGE_SCORE',JText::_('_UE_MYQUIZZES_AVERAGE_SCORE'));
		if (!defined('_UE_MYQUIZZES_USER_SCORE')) DEFINE('_UE_MYQUIZZES_USER_SCORE',JText::_('_UE_MYQUIZZES_USER_SCORE'));
		if (!defined('_UE_MYQUIZZES_BETTER_THAN')) DEFINE('_UE_MYQUIZZES_BETTER_THAN',JText::_('_UE_MYQUIZZES_BETTER_THAN'));
		if (!defined('_UE_MYQUIZZES_RANKED')) DEFINE('_UE_MYQUIZZES_RANKED',JText::_('_UE_MYQUIZZES_RANKED'));
		if (!defined('_UE_MYQUIZZES_ALLOWED_TIME')) DEFINE('_UE_MYQUIZZES_ALLOWED_TIME',JText::_('_UE_MYQUIZZES_ALLOWED_TIME'));
		if (!defined('_UE_MYQUIZZES_NO_QUIZZES')) DEFINE('_UE_MYQUIZZES_NO_QUIZZES',JText::_('_UE_MYQUIZZES_NO_QUIZZES'));
		if (!defined('_UE_JOOMLAQUIZNOTINSTALLED')) DEFINE('_UE_JOOMLAQUIZNOTINSTALLED',JText::_('_UE_JOOMLAQUIZNOTINSTALLED'));
		$lang = JFactory::getLanguage();
		$extension = 'com_joomlaquiz';
		$base_dir = JPATH_SITE;
		$language_tag = $lang->getTag();
		$reload = true;
		$lang->load($extension, $base_dir, $language_tag, $reload);
	}
		
	function getDisplayTab($tab,$user,$ui) {
		global $_CB_database, $_CB_framework, $mainframe;
		if(!file_exists( $_CB_framework->getCfg('absolute_path') . '/components/com_joomlaquiz/joomlaquiz.php' )){
			$return = _UE_JOOMLAQUIZNOTINSTALLED;
		} else {
		
			// Attach CSS and JQuery
			$document	=& JFactory::getDocument();
			$css		= JURI::base() . 'components/com_comprofiler/plugin/user/plug_cbjoomlaquiztab/style.css';
			$document->addStyleSheet($css);
			$jquery = JURI::root() . 'components/com_comprofiler/plugin/user/plug_cbjoomlaquiztab/jquery-1.8.0.min.js';
			$document->addScript($jquery);
			
			$id = JRequest::getVar('user', '');
			$userid = ($id) ? JFactory::getUser($id)->id : JFactory::getUser()->id;
			$return = "";
	
			$return .= $this->_writeTabDescription( $tab, $user );

			$params = $this->params;
			$category = trim($params->get('category'), ',');
			$def_limit = $params->get('count', 10);
			
			$limit = JRequest::getVar('limit', $def_limit, 'REQUEST');
			$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');
			
			$rows = $this->getQuiz($userid, $limitstart, $limit, $category);
			
			$quizzes = array();
								
			foreach($rows as $i=>$row) {
				$sql = "SELECT q_chain FROM #__quiz_q_chain WHERE s_unique_id = '".$row->unique_id."'";
				$_CB_database->setQuery($sql);
				$chain_question_ids = $_CB_database->loadResult();
				$chain_question_ids = str_replace('*', ',', $chain_question_ids);
		
				$sql = "SELECT SUM(c_point) FROM #__quiz_t_question WHERE c_id IN (".$chain_question_ids.") AND published = 1";
				$_CB_database->setQuery( $sql );
				$rows[$i]->max_score = $_CB_database->loadResult();
				
				$sql = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id IN (".$chain_question_ids.") AND c_right = 1";
				$_CB_database->setQuery( $sql );
				$rows[$i]->max_score += $_CB_database->loadResult();
				
				$sql = "SELECT COUNT(*) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".@$row->c_quiz_id."' AND c_finished = 1 AND `c_student_id` <> '".@$row->c_student_id."' AND `c_total_score` > '".@$row->c_total_score."' GROUP BY `c_student_id`";
				$_CB_database->setQuery($sql);
				$rows[$i]->rank  = 1 + count($_CB_database->loadResultArray());
				
				if (isset($quizzes[$row->c_quiz_id])) {
					$rows[$i]->total = $quizzes[$row->c_quiz_id]->total;
					$rows[$i]->total_tries = $quizzes[$row->c_quiz_id]->total_tries;
					$rows[$i]->total_passed = $quizzes[$row->c_quiz_id]->total_passed;
					$rows[$i]->total_score_avg = $quizzes[$row->c_quiz_id]->total_score_avg;
				} else {
					$quizzes[$row->c_quiz_id] = new stdClass();									
					
					$sql = "SELECT COUNT(*) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 GROUP BY `c_student_id` ";
					$_CB_database->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total  = count($_CB_database->loadResultArray());
					$rows[$i]->total = $quizzes[$row->c_quiz_id]->total;
					
					$sql = "SELECT COUNT(c_id) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' ";
					$_CB_database->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total_tries  = (int)$_CB_database->loadResult();
					$rows[$i]->total_tries = $quizzes[$row->c_quiz_id]->total_tries;
					
					$sql = "SELECT COUNT(*) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 AND c_passed = 1 GROUP BY c_student_id";
					$_CB_database->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total_passed  = count($_CB_database->loadResultArray());
					$rows[$i]->total_passed = $quizzes[$row->c_quiz_id]->total_passed;
					
					$sql = "SELECT AVG(c_total_score) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 ";
					$_CB_database->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total_score_avg  = $_CB_database->loadResult();
					$rows[$i]->total_score_avg = $quizzes[$row->c_quiz_id]->total_score_avg;
				}
				
			}
			
			$total = $this->countQuiz($userid, $category);
			$introtext = $params->get("introtext", 0);
			$return .= $this->_getQuizHTML($userid, $limit, $limitstart, $rows, $total, $introtext, $params);
			
			return $return;
			
		}
	}
	
	function _getQuizHTML($userid, $limit, $limitstart, $rows, $total, $introtext, $params){
			
			JPluginHelper::importPlugin('content');
			$dispatcher	=& JDispatcher::getInstance();
			$html = "";
			
			if(!empty($rows))
			{	
				$html .= '<ul id="myquizzes">';
				foreach($rows as $data)
				{			
					$html .= '<li>';	
					$text_limit = $params->get('limit', 50);				
					if(JString::strlen($data->introtext) > $text_limit)
					{
						$content = strip_tags(JString::substr($data->introtext, 0, $text_limit));
						$content .= " .....";
					}
					else
					{
						$content = $data->introtext;
					}		
							
					$data->text =& $content;
					$result = $dispatcher->trigger('onPrepareContent', array (& $data, $params, 0));
												
					$date = JFactory::getDate($data->c_date_time);
					
					$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="myquizzes-header" style="margin: 0 0 5px;cursor: pointer;">';
					$html .= '	<tr style="border-bottom: solid 1px #ccc;">';
					$html .= '		<td height="20"><div class="myquizzes-title"><strong>'.$data->c_title.'</strong></div></td>';
					$html .= '		<td valign="top" width="200" style="text-align: right; font-weight: 700;" class="createdate">'.$data->c_date_time.'</td>';
					$html .= '	</tr>';
					
					$html .= '	<tr>';
					$html .= '		<td colspan="2">';
					if ( $introtext == 1 ) {
						$html .= '<div class="myquizzes-content">'.$content.'</div>';
					}
					$html .= '			<hr style="color: #ccc;" />';
					$html .= '		</td>';
					$html .= '	</tr>';
					$html .= '</table>';
					
					$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 0 0 5px;" class="myquizzes-table">';
					
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" width="150" class="mq_key">'._UE_MYQUIZZES_TEST_CATEGORY.':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->c_category.'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'._UE_MYQUIZZES_TEST_SCORE.':</td>';
					$html .= '	<td valign="top" class="mq_value">'.sprintf(_UE_MYQUIZZES_USER_SCORE, $data->c_total_score, $data->max_score, round($data->c_passing_score*$data->max_score/100), $data->max_score).'</td>';
										
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'._UE_MYQUIZZES_PERCENTITLE.':</td>';
					$html .= '	<td valign="top" class="mq_value">'.round( (1 - $data->rank/$data->total)*100 ).' ('.sprintf(_UE_MYQUIZZES_BETTER_THAN, round((1 - $data->rank/$data->total)*100)).')</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'._UE_MYQUIZZES_RANK.':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->rank.' ('.sprintf(_UE_MYQUIZZES_RANKED, $data->rank, $data->total).')</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'._UE_MYQUIZZES_DURATION.':</td>';
					$html .= '	<td valign="top" class="mq_value">'.str_pad(floor($data->c_total_time / 60), 2, "0", STR_PAD_LEFT).":".str_pad($data->c_total_time % 60, 2, "0", STR_PAD_LEFT).' ('.sprintf(_UE_MYQUIZZES_ALLOWED_TIME, $data->c_time_limit).')</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'._UE_MYQUIZZES_PLACE.':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->rank.'</td>';
					$html .= '</tr>';
					
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" align="left" colspan="2"><h4>'._UE_MYQUIZZES_TEST_STATISTICS.'</h4></td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'._UE_MYQUIZZES_NUMBER_OF_TEST.':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->total_tries.'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'._UE_MYQUIZZES_NUMBER_PROVIDERS.':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->total_passed.'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.sprintf(_UE_MYQUIZZES_AVERAGE_SCORE, $data->total).':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->total_score_avg.'</td>';
					$html .= '</tr>';

					
					$html .= '</table>';
					$html .= '</li>';
			
				}
				$html .= '</ul>';
				
				$document	=& JFactory::getDocument();
				$document->addScriptDeclaration("
					function myquizzes_initMenu() {
						jQuery('#myquizzes li table.myquizzes-table').hide();
						//uncomment to expand first row
						//jQuery('#myquizzes li:first table.myquizzes-table').show();
						jQuery('#myquizzes li table.myquizzes-header').click(
							function() {								
								var checkElement = jQuery(this).next();								
								if((checkElement.is('table.myquizzes-table')) && (checkElement.is(':visible'))) { 
									checkElement.slideUp('normal');
									return false;
								}
								if((checkElement.is('table.myquizzes-table')) && (!checkElement.is(':visible'))) {
									jQuery('#myquizzes table.myquizzes-table:visible').slideUp('normal');
									checkElement.slideDown('normal');
									return false;
								}
							}
						);
					}
					jQuery(document).ready(function() {myquizzes_initMenu();});
				");


				jimport('joomla.html.pagination');
					
				$pagination	= new JPagination( $total , $limitstart , $limit );
				$html .= '
				<!-- Pagination -->
				<div style="text-align: center;">
					'.$pagination->getPagesLinks().'
				</div>
				<!-- End Pagination -->';			
			}else{
				$html .= "<div>"._UE_MYQUIZZES_NO_QUIZZES."</div>";
			}	
			
			$html .= "<div style='clear:both;'></div>";
			
			return $html;
		}
		
		function getQuiz($userid, $limitstart, $limit, $category)
		{
			global $_CB_database;
			if(!empty($category))
			{
				$condition = " AND q.c_category_id IN (".$category.") ";
			}
			else
			{
				$condition = "";
			}
			
			if($this->params->get('display_notactive', 1))
			{
				$expired = "";
			}
			else
			{
				$expired = " AND q.published = 1 ";
			}

					
			$sql = "SELECT sq.c_quiz_id, sq.unique_id, q.c_title, q.c_short_description AS `introtext`, c.c_category, sq.c_date_time, sq.c_total_score, sq.c_total_time, q.c_time_limit, q.c_passing_score, sq.c_passed  
					FROM (`#__quiz_r_student_quiz` AS sq, `#__quiz_t_quiz` AS q) LEFT JOIN `#__quiz_t_category` AS c ON c.c_id = q.c_category_id
					WHERE sq.c_quiz_id = q.c_id AND						
						sq.c_student_id = '".$userid."' AND
						sq.c_finished = '1'
						".$condition."
						".$expired."
						ORDER BY
								`sq`.`c_id` DESC
						LIMIT 
								".$limitstart.",".$limit;					
								
			$_CB_database->setQuery($sql);
			$row  = $_CB_database->loadObjectList();
			if($_CB_database->getErrorNum()) {
				JError::raiseError( 500, $_CB_database->stderr());
			}
			
			return $row;
		}
		
		
		function countQuiz($userid, $category)
		{
			global $_CB_database;
			if(!empty($category))
			{
				$condition = " AND `b`.`c_category_id` IN (".$category.")";
			}
			else
			{
				$condition = "";
			}
			
			$sql = "SELECT 
						COUNT(`a`.`c_id`) 
					FROM `#__quiz_r_student_quiz` AS `a`, `#__quiz_t_quiz` AS `b`
					WHERE 
						`a`.`c_quiz_id` = `b`.`c_id` AND 
						`a`.`c_student_id` = '".$userid."' AND
						`a`.`c_finished` = '1'
						".$condition;

			$query = $_CB_database->setQuery($sql);
			$_CB_database->loadObject($count);
			if($_CB_database->getErrorNum()) {
				JError::raiseError( 500, $_CB_database->stderr());
			}		
			
			return @$count->total;
		}
	}
}