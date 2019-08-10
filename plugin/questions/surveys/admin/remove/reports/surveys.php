<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

function removeReportsSurveys($cids){
	
	$database = JFactory::getDBO();
	$query = "DELETE FROM #__quiz_r_student_survey"
	. "\n WHERE c_sq_id IN ( $cids )";
	$database->setQuery( $query );
	if (!$database->execute()) {
		echo 
		"<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	
	return true;
}

?>
    
		 
		 