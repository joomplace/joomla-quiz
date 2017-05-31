<?php
/**
* JoomlaQuiz plugin for Joomla
* @version $Id: quizcont.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage quizcont.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* <b>Usage:</b>
* <code>{quiz id=6}</code>
*/

class plgContentQuizcont extends JPlugin {

	function onContentPrepare( $context, &$article, &$params, $page = 0 ) {
	
		JFactory::getLanguage()->load('com_joomlaquiz', JPATH_SITE, 'en-GB', true);
		
		// simple performance check to determine whether bot should process further
		if ( strpos( $article->text, 'quiz' ) === false ) {
			return true;
		}
	
		// define the regular expression for the bot
		$regex = '/{quiz\s*.*?}/i';	

		// perform the replacement
		$article->text = preg_replace_callback( $regex, array(&$this, 'quizCode_replacer'), $article->text, 1 );
	
		$article->text = preg_replace( $regex, '', $article->text );
		return true;
	}
	
	/**
	* Replaces the matched tags an image
	* @param array An array of matches (see preg_match_all)
	* @return string
	*/
	
	protected function quizCode_replacer( &$matches ) {
		
		$db = JFactory::getDBO();
		$text = $matches[0];	
		$rres[1] = $matches[0];
		$rres[1] = str_replace('{quiz','', $rres[1]);
		$rres[1] = str_replace('}','', $rres[1]);
		$quiz_id = (int)str_replace('id=','', $rres[1]);

		if(intval($quiz_id)) { 
			
			JLoader::register('JoomlaquizHelper', JPATH_SITE . '/components/com_joomlaquiz/helpers/joomlaquiz.php');
			JoomlaquizHelper::isJoomfish();
			
			require_once JPATH_SITE.'/components/com_joomlaquiz/models/quiz.php';
			$model = JModelLegacy::getInstance('Quiz', 'JoomlaquizModel');
			$quiz_params = $model->getQuizParams($quiz_id);
			
			$db->setQuery("SELECT `template_name` FROM #__quiz_templates WHERE `id` = '".$quiz_params->c_skin."'");
			$template_name = $db->loadResult();
			
			require_once JPATH_SITE.'/components/com_joomlaquiz/views/templates/view.html.php';
			$tmpl = new JoomlaquizViewTemplates($template_name);
			
			$this->quiz_params = $quiz_params;
			$this->is_preview = false;
			$this->preview_quest = 0;
			$this->preview_id = '';
			
			// Checking access and displaying.
			$user = JFactory::getUser();
			$viewAccessGranted = $user->authorise('core.view', 'com_joomlaquiz.quiz.'.$this->quiz_params->c_id);

			if ($viewAccessGranted OR $this->quiz_params->c_guest)
			{
				@ob_start();
				require_once JPATH_SITE.'/components/com_joomlaquiz/views/quiz/tmpl/default.php';
				$text = @ob_get_contents();			
				@ob_end_clean();

			} else {

	            $db->setQuery('SELECT c_quiz_access_message FROM `#__quiz_t_quiz` WHERE c_id='.$this->quiz_params->c_id);
	            $msgDbText = $db->loadResult();

	            if(!empty($msgDbText)){
	                $text = $msgDbText;
	            }else{
	                $text = '<div><span style="color:red;">'.JText::_('COM_JOOMLAQUIZ_FE_NO_RIGHTS_VIEW_QUIZ').'</span><br/><br/></div>';
	            }
			}

		}
		
		return $text;
	}
}
?>
