<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
function removeResultsMchoice($stu_cids){
	
	$database = JFactory::getDBO();
	$query = "DELETE FROM #__quiz_r_choice"
	. "\n WHERE c_sq_id IN ( $stu_cids )";
	$database->setQuery( $query );
	if (!$database->execute()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	
	return true;
}

?>
    
		 
		 