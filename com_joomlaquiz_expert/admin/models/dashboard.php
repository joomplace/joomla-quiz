<?php
/**
* Joomlaquiz component for Joomla 3.0
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class JoomlaquizModelDashboard extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) { $config['filter_fields'] = array(); }
		parent::__construct($config);


	}
	
	public function getDatabaseState(){

	    $folders = array();
		$folders[] = JPATH_ADMINISTRATOR . '/components/com_joomlaquiz/sql/updates/';
		$folders[] = JPATH_ADMINISTRATOR . '/components/com_joomlaquiz/sql/initial_sync/';

		try{
            $changeSets = array();
		    foreach ($folders as $folder){
                $changeSets[] = new JSchemaChangeset($this->getDbo(), $folder);
            }
		}catch (RuntimeException $e){
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');

			return false;
		}
		
		return $changeSets;
		
	}
	
	/**
	 * Fixes database problems.
	 *
	 * @return  void
	 */
	public function fix(){
		if (!$changeSets = $this->getDatabaseState()){
			return false;
		}

		foreach ($changeSets as $changeSet){
            $changeSet->fix();
        }
	}

	public function  fixEncode(){

		$folder = JPATH_ADMINISTRATOR . '/components/com_joomlaquiz/sql/other/';

		try{
			$changeSet = JSchemaChangeset::getInstance($this->getDbo(), $folder);
		}catch (RuntimeException $e){
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');

			return false;
		}

		$db = $this->getDbo();
		$result = $changeSet->getStatus();
		$result = $result['unchecked'];
		//$mb4 = $db->hasUTF8mb4Support();
		$mb4 = false;
		foreach($result as $item){
			if((strpos($item->file, 'mb4')!== false && $mb4) || (strpos($item->file, 'mb4')=== false && !$mb4)){
				if(!$item->checkQueryExpected || ($item->checkQueryExpected && $db->setQuery($item->checkQuery)->loadObject())){
					if($db->setQuery($item->updateQuery)->execute()){
						echo $item->updateQuery."<br/>";
					}
				}
			}
		}
	}

	protected function getListQuery() 
	{
		$db = $this->_db;
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('`#__quiz_dashboard_items` AS `di`');
		$query->where('`di`.published=1');

		return $query;
	}

    public function getCurrDate()
    {
        $db = $this->_db;
        $query = $db->getQuery(true);
        $query->select('c_par_value');
        $query->from('`#__quiz_setup`');
        $query->where("c_par_name='curr_date'");


        $result = $db->setQuery($query)->loadResult();
        if (strtotime("+2 month",strtotime($result))<=strtotime(JFactory::getDate())) {
            return true;
        } else {
            return false;
        }
    }
}
