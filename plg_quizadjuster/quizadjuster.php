<?php
/**
* JoomlaQuiz plugin for Joomla
* @version $Id: quizcont.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage quizcont.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );


class plgContentQuizadjuster extends JPlugin {

	function onContentPrepare( $context, &$article, &$params, $page = 0 ) {
	
		// define the regular expression for the bot
		$regex = '/<div[^<]*?class=("|\')[^"\']*?jq_nquiz("|\').*?\/div>/m';
		$article->text = preg_replace( $regex, '', $article->text );
		return true;
	}
}
