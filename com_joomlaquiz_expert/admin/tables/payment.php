<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');
 
/**
 * Joomlaquiz Deluxe Table class
 */
class JoomlaquizTablePayment extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db) 
        {
                parent::__construct('#__quiz_payments', 'id', $db);
        }
		
		function store($updateNulls = false){
			
			$this->checked_out_time = date('Y-m-d H:i:s');
			$this->date = $this->date && $this->date != '0000-00-00 00:00:00'? $this->date: $this->checked_out_time;
	
			if ($this->status == 'Confirmed')
				$this->confirmed_time = date('Y-m-d H:i:s');

			$res = parent::store($updateNulls);
			
			return $res;
		}

		function load($keys = null, $reset = true)
        {
            if($keys > 1000000000)
                $keys = $keys-1000000000;
            return parent::load($keys, $reset);
        }
}