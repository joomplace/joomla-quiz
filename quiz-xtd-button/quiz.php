<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5 Flipping Book plugin
* @package HTML5 Flipping Book
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

jimport('joomla.plugin.plugin');

class plgButtonQuiz extends JPlugin
{
	protected $autoloadLanguage = true;

	//----------------------------------------------------------------------------------------------------
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}
	//----------------------------------------------------------------------------------------------------
	function onDisplay($name, $asset, $author)
	{
		$js = "
			function onQuizInsertClick(quiz_id)
			{
				var tagContent = '';
				
				if(quiz_id>0){
					tagContent = '{quiz id=' + quiz_id + '}';
				}
				
				jInsertEditorText(tagContent, '".$name."');
				SqueezeBox.close();
			}";
		
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		
		$button = new JObject();
		$button->modal = true;
		$button->link = 'index.php?option=com_joomlaquiz&view=quizzes&layout=modal&tmpl=component';
		$button->class = 'btn';
		$button->text = JText::_('PLG_QUIZ_BUTTON_TEXT');
		$button->name = 'puzzle';
		$button->options =  "{handler: 'iframe', size: {x: 700, y: 300}}";

		return $button;
	}
}