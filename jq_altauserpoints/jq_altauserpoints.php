<?php
/**
* JoomlaQuiz system plugin for Joomla
* @version $Id: jq_altauserpoints.php 2009-11-16 17:30:15
* @package JoomlaQuiz
* @subpackage jq_altauserpoints.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemJQ_altauserpoints extends JPlugin
{ 
	/*
	 * Constructor
	 */
	function plgSystemJQ_altauserpoints(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

/*
 * This method calls when user finishes quiz
 * start_id - unique start ID in JoomlaQuiz component
 * quiz_id - quiz id
 * quiz_title - Quiz title
 * user_points - user points
 * passing_points - passing points
 * total_points - max points
 * passed - passed or failed (1 or 0)
 * started - start date
 * spent_time - spent time
*/
	function onJQuizFinished($params){	
		if(!$this->params->get('use_quiz_rules')){
			return true;
		}
		if (!file_exists(JPATH_SITE . '/components/com_altauserpoints/helper.php')) 
			return;		
		require_once(JPATH_SITE . '/components/com_altauserpoints/helper.php');
		
		$user = & JFactory::getUser(); 
		
		$comment = $this->params->get('comment', '');
		$points_rule = $this->params->get('points_rule', 'always');
		$fixed_points = (int)$this->params->get('fixed_points', 0);
		$points = $fixed_points ? (int)$this->params->get('points', 0): $params['user_points'];
		$add_points_once = (int)$this->params->get('add_points_once', 0);

		
		if ($points == 0 || ($points_rule == 'onsuccess' && !$params['passed'])) {
			return;
		}
		
		$params['user_id'] = $user->get('id');
		$params['user_name'] = $user->get('name');
		$params['ended'] = JHtml::_('date', time(), 'Y-m-d H:i:s');
		foreach($params as $key=>$value){	
			$comment = str_replace('{'.$key.'}', $value, $comment);
		}
		
		AltaUserPointsHelper::newpoints('plgup_joomlaquizpoints', '', ($add_points_once ? $params['quiz_id'] : ''), $comment, $points);
	}

	/*
	 * This method calls when user submits an answer
	 * $data contains all available question data on onSaveQuestion
	*/
	function onJQuizAnswerSubmitted($data){			
		if (!file_exists(JPATH_SITE . '/components/com_altauserpoints/helper.php')) 
			return;		
		require_once(JPATH_SITE . '/components/com_altauserpoints/helper.php');
		
		$user = & JFactory::getUser(); 
		
		$comment = $this->params->get('question_comment', '');
		$points_rule = $this->params->get('question_points_rule', 'always');
		$fixed_points = (int)$this->params->get('question_fixed_points', 0);
		$points = $fixed_points ? (int)$this->params->get('question_points', 0): $data['score'];
		$add_points_once = (int)$this->params->get('question_add_points_once', 0);
	
		if ($points == 0 || ($points_rule == 'onsuccess' && !$data['is_correct'])) {
			return;
		}
		
		$data['user_id'] = $user->get('id');
		$data['user_name'] = $user->get('name');
		$data['ended'] = JHtml::_('date', time(), 'Y-m-d H:i:s');
		/*
			need to rewrite this kind of functions to work with JText::sprintf('%s')
		*/
		foreach($data as $key=>$value){	
			$comment = str_replace('{'.$key.'}', $value, $comment);
		}
		
		AltaUserPointsHelper::newpoints('plgup_joomlaquizpoints', '', ($add_points_once ? $data['quiz_id'].'.'.$data['quest_id'] : ''), $comment, $points);
		
	}
}
?>