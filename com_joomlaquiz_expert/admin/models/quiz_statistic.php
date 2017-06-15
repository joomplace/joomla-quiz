<?php
/**
 * Joomlaquiz component for Joomla 3.0
 * @package Joomlaquiz
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class JoomlaquizModelQuiz_statistic extends JModelList {

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'progress',
                'total_time',
                'u.username',
                'title',
                'total_score',
                'id',
                'respond_at',
                'start_at',
            );
        }

        parent::__construct($config);
    }


    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */

    protected function getListQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $subquery = $db->getQuery(true);
        $subquery2 = $db->getQuery(true);
        //$layout = JFactory::getApplication()->input->get('layout');

        $subquery->select(array('COUNT(qsq.c_stu_quiz_id) as passed', 'SUM(qsq.c_score) as total_score', 'qsq.c_stu_quiz_id', 'MAX(qsq.respond_at) AS respond_at'));
        $subquery->from($db->qn('#__quiz_r_student_question', 'qsq'));
        $subquery->group($db->qn('qsq.c_stu_quiz_id'));

        $subquery2->select(array('COUNT(qtq.c_quiz_id) AS total', 'qtq.c_quiz_id'))
			->from($db->qn('#__quiz_t_question', 'qtq'))
			->where($db->qn('qtq.published').' = "1"')
			->group($db->qn('qtq.c_quiz_id'));
		
        $query->select(array(
            'q.c_title AS title',
            'sq.c_id AS id',
            'sq.c_student_id AS user_id',
            'qsq.passed AS passed',
            'qsq.total_score AS total_score',
            'qsq.respond_at',
            'qtq.total AS total',
            'qsq.passed/qtq.total AS progress',
            'sq.c_date_time AS start_at',
            'unix_timestamp(qsq.respond_at) AS respond_at'
        ));
        $query->from($db->qn("#__quiz_r_student_quiz", "sq"));
        $query->where($db->qn('sq.c_passed') . ' = '. $db->quote('0'));
        $query->join('LEFT','(' . $subquery . ') AS `qsq` ON ' . $db->qn('qsq.c_stu_quiz_id') . ' = ' . $db->qn('sq.c_id') );
        $query->join('LEFT','(' . $subquery2 . ') AS `qtq` ON ' . $db->qn('qtq.c_quiz_id') . ' = ' . $db->qn('sq.c_quiz_id') );
        $query->join('LEFT',$db->qn('#__quiz_t_quiz').' AS `q` ON ' . $db->qn('q.c_id') . ' = ' . $db->qn('sq.c_quiz_id') );

		$query->where(' (NOW() - INTERVAL '.JComponentHelper::getParams('com_joomlaquiz')->get('lttrack',15).' MINUTE) < qsq.respond_at');
		$query->where(' !`sq`.`c_total_time` ');

        // Filter: like / search
        $search = $this->getState('filter.search');
        $quiz_id = $this->getState('filter.quiz_id');

        if (!empty($search))
        {
            $like = $db->quote('%' . $search . '%');
			$where_likes = array();
			$where_likes[] = 'sq.c_student_id LIKE '.$like;
			$where_likes[] = 'q.c_title LIKE '.$like;
			
            $query->where('('.implode(' OR ',$where_likes).')');
        }
        if ($quiz_id)
        {
            $quiz_id = $db->quote($quiz_id);
            $query->where('q.c_id = ' . $quiz_id);
        }

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'total_score');
        $orderDirn 	= $this->state->get('list.direction', 'asc');
		if($orderCol=='respond_at'){
			$orderDirn = $orderDirn=='asc'?'desc':'asc';
		}
		
		$this->setState('list.limit',1000000);
		
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
    
}