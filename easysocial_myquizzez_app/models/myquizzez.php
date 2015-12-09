<?php

defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import the model file from the core
Foundry::import( 'admin:/includes/model' );


class MyQuizzezModel extends EasySocialModel
{
	//protected $db;
	
	function __construct(){
		$this->db = Foundry::db();
        $this->params = JFactory::getApplication()->input;
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
					COUNT(`a`.`c_id`) as count
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
		return $count->count;
	}

	
	
	
	


}