<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
function removeQuestionsDalliclick($cids){
	
	$database = JFactory::getDBO();
	$query = "DELETE FROM #__quiz_t_dalliclick WHERE c_question_id IN ( $cids )";
	$database->setQuery( $query );
	
	return true;
}

?>
    
		 
		 