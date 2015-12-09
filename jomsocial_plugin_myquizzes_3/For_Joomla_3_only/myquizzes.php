<?php
/**
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once( JPATH_ROOT . '/components/com_community/libraries/core.php');

if(!class_exists('plgCommunityMyQuizzes'))
{
	class plgCommunityMyQuizzes extends CApplications
	{
		var $_user		= null;
		var $name		= "My Quizzes";
		var $category;
        var $db;
		
	    function plgCommunityMyQuizzes(& $subject, $config)
	    {
			$this->_user	= CFactory::getRequestUser();
			$this->db 		= JFactory::getDBO();

			parent::__construct($subject, $config);
			
			$this->category = trim($this->params->get('category'), ',');
	    }
	
		/**
		 * Ajax function to save a new wall entry
		 * 	 
		 * @param message	A message that is submitted by the user
		 * @param uniqueId	The unique id for this group
		 * 
		 **/	 	 	 	 	 		
		function onProfileDisplay(){
			//Load language file.
			JPlugin::loadLanguage( 'plg_myquizzes', JPATH_ADMINISTRATOR );
			
			// Attach CSS
			$document	= JFactory::getDocument();
			$css		= JURI::base() . 'plugins/community/myquizzes/myquizzes/style.css';
			$document->addStyleSheet($css);
			if(JFactory::getApplication()->input->get('task', '') == 'app'){
				$app = 1;	
			}else{
				$app = 0;
			}
			
			$userid	= $this->_user->id;
			$def_limit = $this->params->get('count', 10);
			$limit = JFactory::getApplication()->input->get('limit', $def_limit);
			$limitstart = JFactory::getApplication()->input->get('limitstart', 0);
			
			$rows = $this->getQuiz($userid, $limitstart, $limit, $this->category);
			
			$quizzes = array();
								
			foreach($rows as $i=>$row) {
				$sql = "SELECT q_chain FROM #__quiz_q_chain WHERE s_unique_id = '".$row->unique_id."'";
				$this->db->setQuery($sql);
				$chain_question_ids = $this->db->loadResult();
				$chain_question_ids = str_replace('*', ',', $chain_question_ids);
		
				$sql = "SELECT SUM(c_point) FROM #__quiz_t_question WHERE c_id IN (".$chain_question_ids.") AND published = 1";
				$this->db->setQuery( $sql );
				$rows[$i]->max_score = $this->db->loadResult();
				
				$sql = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id IN (".$chain_question_ids.") AND c_right = 1";
				$this->db->setQuery( $sql );
				$rows[$i]->max_score += $this->db->loadResult();
				
				$sql = "SELECT COUNT(*) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 AND `c_student_id` <> '".@$row->c_student_id."' AND `c_total_score` > '".$row->c_total_score."' GROUP BY `c_student_id`";
				$this->db->setQuery($sql);
				$rows[$i]->rank  = 1 + count($this->db->loadResultArray());
				
				if (isset($quizzes[$row->c_quiz_id])) {
					$rows[$i]->total = $quizzes[$row->c_quiz_id]->total;
					$rows[$i]->total_tries = $quizzes[$row->c_quiz_id]->total_tries;
					$rows[$i]->total_passed = $quizzes[$row->c_quiz_id]->total_passed;
					$rows[$i]->total_score_avg = $quizzes[$row->c_quiz_id]->total_score_avg;
				} else {
					$quizzes[$row->c_quiz_id] = new stdClass();									
					
					$sql = "SELECT COUNT(*) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 GROUP BY `c_student_id` ";
					$this->db->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total  = count($this->db->loadResultArray());
					$rows[$i]->total = $quizzes[$row->c_quiz_id]->total;
					
					$sql = "SELECT COUNT(c_id) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' ";
					$this->db->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total_tries  = (int)$this->db->loadResult();
					$rows[$i]->total_tries = $quizzes[$row->c_quiz_id]->total_tries;
					
					$sql = "SELECT COUNT(*) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 AND c_passed = 1 GROUP BY c_student_id";
					$this->db->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total_passed  = count($this->db->loadResultArray());
					$rows[$i]->total_passed = $quizzes[$row->c_quiz_id]->total_passed;
					
					$sql = "SELECT AVG(c_total_score) FROM `#__quiz_r_student_quiz` WHERE `c_quiz_id` = '".$row->c_quiz_id."' AND c_finished = 1 ";
					$this->db->setQuery($sql);
					$quizzes[$row->c_quiz_id]->total_score_avg  = $this->db->loadResult();
					$rows[$i]->total_score_avg = $quizzes[$row->c_quiz_id]->total_score_avg;
				}
				
			}		
			
			$total = $this->countQuiz($userid, $this->category);
			$introtext = $this->params->get("introtext", 0);
			
			$mainframe = JFactory::getApplication();
			$caching = $this->params->get('cache', 1);
			if($caching)
			{
				$caching = $mainframe->getCfg('caching');
			}
			
			$cache = JFactory::getCache('plgCommunityMyQuizzes');
			$cache->setCaching($caching);
			$callback = array('plgCommunityMyQuizzes', '_getQuizHTML');
			$content = $cache->call($callback, $userid, $limit, $limitstart, $rows, $app, $total, $introtext, $this->params);
			
			return $content;
		}
		
		public static function _getQuizHTML($userid, $limit, $limitstart, $rows, $app, $total, $introtext, $params){
			
			JPluginHelper::importPlugin('content');
			$dispatcher	= JDispatcher::getInstance();
			$html = "";
			
			$html .= "<script type='text/javascript'>
						function myquizzes_initMenu() {
							jQuery('#myquizzes li table.myquizzes-table').hide();
							jQuery('#myquizzes li table.myquizzes-header').click(
								function(event) {
									var checkElement = jQuery(this).next();								
									if((checkElement.is('table.myquizzes-table')) && (checkElement.is(':visible'))) { 
										checkElement.slideUp('slow');
										return false;
									}
									if((checkElement.is('table.myquizzes-table')) && (!checkElement.is(':visible'))) {
										jQuery('#myquizzes table.myquizzes-table:visible').slideUp('slow');
										checkElement.slideDown('slow');
										event = event || window.event

                                         if (event.stopPropagation) {
                                                 event.stopPropagation()
                                         } else {
                                                 event.cancelBubble = true
                                         }
										return false;
									}
								}
							);
						}
						jQuery(document).ready(function() {myquizzes_initMenu();});</script>";
			
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
					$result = $dispatcher->trigger('onPrepareContent', array ('com_content.article', &$data, $params, 0));
												
					$date = CTimeHelper::getDate($data->c_date_time);
					
					$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="myquizzes-header" style="margin: 0 0 5px;cursor: pointer;">';
					$html .= '	<tr style="border-bottom: solid 1px #ccc;">';
					$html .= '		<td height="20"><div class="myquizzes-title"><strong>'.$data->c_title.'</strong></div></td>';
					$html .= '		<td valign="top" width="200" style="text-align: right; font-weight: 700;" class="createdate">'.$date->format(JText::_('DATE_FORMAT_LC2'), true).'</td>';
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
					$html .= '	<td valign="top" width="150" class="mq_key">'.JText::_('PLG_MYQUIZZES_TEST_CATEGORY').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->c_category.'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_TEST_SCORE').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.JText::sprintf('PLG_MYQUIZZES_USER_SCORE', $data->c_total_score, $data->max_score, round($data->c_passing_score*$data->max_score/100), $data->max_score).'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_PERCENTITLE').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.(($data->total) ? round( (1 - $data->rank/$data->total)*100 ) : 0).' ('.JText::sprintf('PLG_MYQUIZZES_BETTER_THAN', (($data->total) ? round( (1 - $data->rank/$data->total)*100 ) : 0)).')</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_RANK').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->rank.' ('.JText::sprintf('PLG_MYQUIZZES_RANKED', $data->rank, $data->total).')</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_DURATION').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.str_pad(floor($data->c_total_time / 60), 2, "0", STR_PAD_LEFT).":".str_pad($data->c_total_time % 60, 2, "0", STR_PAD_LEFT).' ('.JText::sprintf('PLG_MYQUIZZES_ALLOWED_TIME', $data->c_time_limit).')</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_PLACE').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->rank.'</td>';
					$html .= '</tr>';
					
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" align="left" colspan="2"><h4>'.JText::_('PLG_MYQUIZZES_TEST_STATISTICS').'</h4></td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_NUMBER_OF_TEST').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->total_tries.'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_NUMBER_PROVIDERS').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->total_passed.'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::sprintf('PLG_MYQUIZZES_AVERAGE_SCORE', $data->total).':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->total_score_avg.'</td>';
					$html .= '</tr>';

					
					$html .= '</table>';
					$html .= '</li>';
			
				}
				$html .= '</ul>';
								
				if($app == 1){
					jimport('joomla.html.pagination');
					
					$pagination	= new JPagination( $total , $limitstart , $limit );
					$html .= '
					<!-- Pagination -->
					<div style="text-align: center;">
						'.$pagination->getPagesLinks().'
					</div>
					<!-- End Pagination -->';			
				}else{
					$showall = CRoute::_('index.php?option=com_community&view=profile&userid='.$userid.'&task=app&app=myquizzes');
					$html .= "<div style='float:right;'><a href='".$showall."'>".JText::_('CC_SHOW_ALL')."</a></div>";
				}
			}else{
				$html .= "<div>".JText::_("PLG_MYQUIZZES_NO_QUIZZES")."</div>";
			}	
			
			$html .= "<div style='clear:both;'></div>";
			
			return $html;
		}
		
		function onAppDisplay(){
			ob_start();
			$limit=0;
			$html= $this->onProfileDisplay($limit);
			echo $html;
			
			$content	= ob_get_contents();
			ob_end_clean(); 
		
			return $content;		
		}
		
		function getQuiz($userid, $limitstart, $limit, $category)
		{
		
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
						sq.c_student_id = ".$this->db->quote($userid)." AND
						sq.c_finished = ".$this->db->quote(1)."
						".$condition."
						".$expired."
						ORDER BY
								`sq`.`c_id` DESC
						LIMIT 
								".$limitstart.",".$limit;					
								
			$this->db->setQuery($sql);
			$row  = $this->db->loadObjectList();
			if($this->db->getErrorNum()) {
				JError::raiseError( 500, $this->db->stderr());
			}
			return $row;
		}
		
		
		function countQuiz($userid, $category)
		{		
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
					FROM 
						`#__quiz_r_student_quiz` AS `a`, `#__quiz_t_quiz` AS `b`
					WHERE 
						`a`.`c_quiz_id` = `b`.`c_id` AND 
						`a`.`c_student_id` = ".$this->db->quote($userid)." AND
						`a`.`c_finished` = ".$this->db->quote(1)."
						".$condition;

			$query = $this->db->setQuery($sql);
			$count  = $this->db->loadObject();
			if($this->db->getErrorNum()) {
				JError::raiseError( 500, $this->db->stderr());
			}		
			
			return @$count->total;
		}
	}
}
