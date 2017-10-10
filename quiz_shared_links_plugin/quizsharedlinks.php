<?php
/**
 * JoomlaQuiz plugin for Joomla
 * @version $Id: quizsharedlinks.php 2011-03-03 17:30:15
 * @package JoomlaQuiz
 * @subpackage quizsharedlinks.php
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgContentQuizSharedLinks extends JPlugin {

    function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    function onContentPrepare($context, &$article, &$params, $limitstart)
    {
        // simple performance check to determine whether bot should process further
        if (strpos($article->text, 'quiz_results') === false) {
            return true;
        }

        $regex = '/task%3Dresults.sturesult%26id%3D(\d+)%26share_id%3D(\d+)/';

        $shareTitle = '<p class="lead">' . JText::_('PLG_CONTENT_QUIZSHAREDLINKS_SHARE_TITLE') . '</p>';

        $position = '<div id="jq_share">';

        $article->text = str_replace($position, $position.$shareTitle, $article->text);

        /**
         * $matches[1] contains quiz id
         * $matches[2] contains share_id
         */
        preg_match_all($regex, $article->text, $matches);

        $share_link = 'view%3Dquiz%26';

        $article->text = str_replace($matches[0], $share_link, $article->text);

        return true;
    }
}

?>