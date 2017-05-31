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
 * Result Controller
 */
class JoomlaquizControllerResult extends JControllerAdmin
{
        /**
         * Proxy for getModel.
         * @since       1.6
         */
        public function getModel($name = 'Result', $prefix = 'JoomlaquizModel', $config = array('ignore_request' => true))
        {
                $model = parent::getModel($name, $prefix, $config);
                return $model;
        }
		
		public function del_stu_report(){
			$cid = JFactory::getApplication()->input->get('cid', array(), '');
			$model = $this->getModel();
			$model->del_stu_report($cid);
			
			$c_id = JFactory::getApplication()->input->get('c_id');
			$this->setRedirect('index.php?option=com_joomlaquiz&view=results&layout=stu_report&cid='.$c_id);
		}
		
		public function view_report(){
			$db = JFactory::getDbo();
			$qid = JFactory::getApplication()->input->get('cid');
			if($qid){
				$rid = $db->setQuery("SELECT `c_stu_quiz_id` FROM `#__quiz_r_student_question` WHERE `c_id` = \"".$qid."\"")->loadResult();
				$this->setRedirect('index.php?option=com_joomlaquiz&view=results&layout=stu_report&cid='.$rid);
			}else{
				$this->setRedirect('index.php?option=com_joomlaquiz&view=results');
			}
		}
		
		public function view_reports(){
			$this->setRedirect('index.php?option=com_joomlaquiz&view=results');
		}
		
		public function quest_report(){
			$cid = JFactory::getApplication()->input->get('cid', array(), '');
			$cid = $cid[0];
			
			$this->setRedirect('index.php?option=com_joomlaquiz&view=result&cid='.$cid);
		}
}
