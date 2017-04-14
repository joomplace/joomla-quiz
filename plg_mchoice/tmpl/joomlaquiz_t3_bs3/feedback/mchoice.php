<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JPluginHelper::importPlugin('joomlaquiz','mchoice');

class JoomlaquizViewFeedbackMchoice extends plgJoomlaquizMchoice
{
	public static function getFeedbackContent($feedback_data, $data){
        return JLayoutHelper::render('question.results.display', array('question' => $data['q_data'], 'quiz_result_id' => $data['stu_quiz_id']), JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
	}
}