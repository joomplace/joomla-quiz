<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controllerform');
 
/**
 * Print Certificate Controller
 */
class JoomlaquizControllerMigrate extends JControllerForm
{
    public function getModel($name = 'quiz', $prefix = '', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function reinitRules($rules, $id = null){
        $get = JFactory::getApplication()->input->get;
        if($get->get('secure','') == 'jp_allow'){
            $model = $this->getModel();
            $table = $model->getTable();
            if($id){
                $table->load($id);
                $table->setRules($rules);
                $table->store(null);
            }else{
                $key = $table->getKeyName();
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select($key);
                $query->from($table->getTableName());
                $db->setQuery($query);
                $instance = $db->loadObjectList();
                $query->clear();
                foreach($instance as $inst){
                    if($inst->$key!=0){
                        $table->load($inst->$key);
                        $table->setRules($rules);
                        $table->store(null);
                    }
                }
            }
            echo 'Done.';
            return true;
        }
        else {
            JFactory::getApplication()->enqueueMessage(JText::_('Access denied!'), 'error');
            return false;
        }
    }

  	public function regfullaccess(){
	    return $this->reinitRules('{"core.view":{"2":1},"core.result":{"2":1},"core.certificate":{"2":1},"core.review":{"2":1}}');
	}
		
}
