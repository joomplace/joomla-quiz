<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controllerform');
 
/**
 * Quizzes Controller
 */
class JoomlaquizControllerQuiz extends JControllerForm
{
	
	/**
    * Proxy for getModel.
    * @since       1.6
    */
    public function getModel($name = 'Quiz', $prefix = 'JoomlaquizModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
	}
	
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
    protected function allowEdit($data = array(), $key = 'c_id')
    {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_joomlaquiz');             
    }
	
	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.6
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Quiz', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_joomlaquiz&view=quizzes' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
	
	
	public function save($key = null, $urlVar = null){
		$task = JFactory::getApplication()->input->getCmd('task');
		if($task=='save2copy'){
			$data = JFactory::getApplication()->input->get('jform',array(),'array');
            $session = JFactory::getSession();
            $session->set('com_joomlaquiz.copy.quizzes.cids', array($data['c_id']));
			JFactory::getApplication()->input->set('categorycopy',$data['c_category_id']);
			$model = $this->getModel('Quizzes');
			$msg = $model->copyQuizzes();
			$this->setRedirect('index.php?option=com_joomlaquiz&view=quizzes');
		}else{
			parent::save();
			if($task == 'apply'){
		
			} elseif($task == 'save') {
				$this->setRedirect('index.php?option=com_joomlaquiz&view=quizzes');
			}
		}
	}
	
	public function cancel($key = null){
		$this->setRedirect('index.php?option=com_joomlaquiz&view=quizzes');
	}
}
