<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controlleradmin');
 
/**
 * Quiz categories Controller
 */
class JoomlaquizControllerQuizcategories extends JControllerAdmin
{
        /**
         * Proxy for getModel.
         * @since       1.6
         */
        public function getModel($name = 'Quizcategories', $prefix = 'JoomlaquizModel', $config = array('ignore_request' => true))
        {
                $model = parent::getModel($name, $prefix, $config);
                return $model;
        }

        public function datedb()
        {
            $db = JFactory::getDbo();
            $curr_date = date("Y-m-d");
            $querySelect="SELECT `c_par_value` FROM `#__quiz_setup` WHERE `c_par_name` = 'curr_date'";
            $db->setQuery($querySelect);
            $result = $db->loadResult();
            if(empty($result)){
                $query = "INSERT INTO `#__quiz_setup` (c_par_name, c_par_value) VALUES ('curr_date', '".$curr_date."')";
            }
            else $query="UPDATE `#__quiz_setup` SET `c_par_value`='".$curr_date."' WHERE `c_par_name`='curr_date'";
            $db->setQuery($query);
            $db->execute();
        }
}
