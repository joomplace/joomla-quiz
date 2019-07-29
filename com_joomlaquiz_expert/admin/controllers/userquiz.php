<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die();

class JoomlaquizControllerUserquiz extends JControllerForm
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function getModel($name = 'Userquiz', $prefix = 'JoomlaquizModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
	}

	public function assign()
    {
        $this->checkToken();
        $app = JFactory::getApplication();

        $userquiz_quiz_id = $this->input->post->getInt('userquiz_quiz_id', 0);
        $userquiz_users_ids  = $this->input->post->get('userquiz_users_ids', array(), 'array');

        if(!JFactory::getUser()->authorise('core.create', 'com_joomlaquiz'))
        {
            $this->setError(\JText::_('COM_JOOMLAQUIZ_USERQUIZ_ERROR_NOT_PERMITTED'));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(\JRoute::_('index.php?option=com_joomlaquiz&view=userquiz' . $this->getRedirectToListAppend(), false));
		    return false;
        }

        if(!$userquiz_quiz_id || empty($userquiz_users_ids))
        {
            $this->setError(\JText::_('COM_JOOMLAQUIZ_USERQUIZ_ERROR_NOT_DATA'));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(\JRoute::_('index.php?option=com_joomlaquiz&view=userquiz' . $this->getRedirectToListAppend(), false));
            return false;
        }

        $data = array(
            'userquiz_quiz_id' => $userquiz_quiz_id,
            'userquiz_users_ids'   => $userquiz_users_ids
        );

        $model = $this->getModel();

		if(!$model->assign($data)){
            $this->setError(\JText::_('COM_JOOMLAQUIZ_USERQUIZ_ERROR_NO_ASSIGN'));
            $this->setMessage($this->getError(), 'error');
            $app->setUserState('userquiz.userquiz_quiz_id', $userquiz_quiz_id);
            $app->setUserState('userquiz.userquiz_users_ids', $userquiz_users_ids);
            $this->setRedirect(\JRoute::_('index.php?option=com_joomlaquiz&view=userquiz' . $this->getRedirectToListAppend(), false));
            return false;
        }

        $this->setMessage(\JText::_('COM_JOOMLAQUIZ_USERQUIZ_SUCCESS_ASSIGN'), 'message');
        $this->setRedirect(\JRoute::_('index.php?option=com_joomlaquiz&view=userquiz' . $this->getRedirectToListAppend(), false));
		return true;
	}

    public function unassign($data=array())
    {
        $this->checkToken();
        $app = JFactory::getApplication();

        $userquiz_quiz_id = $this->input->post->getInt('userquiz_quiz_id', 0);
        $userquiz_users_ids  = $this->input->get('cid', array(), 'array');

        if(!JFactory::getUser()->authorise('core.create', 'com_joomlaquiz'))
        {
            $this->setError(\JText::_('COM_JOOMLAQUIZ_USERQUIZ_ERROR_NOT_PERMITTED'));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(\JRoute::_('index.php?option=com_joomlaquiz&view=userquiz' . $this->getRedirectToListAppend(), false));
            return false;
        }

        if(!$userquiz_quiz_id || empty($userquiz_users_ids))
        {
            $this->setError(\JText::_('COM_JOOMLAQUIZ_USERQUIZ_ERROR_NOT_DATA'));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(\JRoute::_('index.php?option=com_joomlaquiz&view=userquiz' . $this->getRedirectToListAppend(), false));
            return false;
        }

        $data = array(
            'userquiz_quiz_id' => $userquiz_quiz_id,
            'userquiz_users_ids'   => $userquiz_users_ids
        );

        $model = $this->getModel();

        if(!$model->unassign($data)){
            $this->setError(\JText::_('COM_JOOMLAQUIZ_USERQUIZ_ERROR_NO_UNASSIGN'));
            $this->setMessage($this->getError(), 'error');
            $app->setUserState('userquiz.userquiz_quiz_id', $userquiz_quiz_id);
            $app->setUserState('userquiz.cid', $userquiz_users_ids);
            $this->setRedirect(\JRoute::_('index.php?option=com_joomlaquiz&view=userquiz' . $this->getRedirectToListAppend(), false));
            return false;
        }

        $this->setMessage(\JText::_('COM_JOOMLAQUIZ_USERQUIZ_SUCCESS_UNASSIGN'), 'message');
        $this->setRedirect(\JRoute::_('index.php?option=com_joomlaquiz&view=userquiz&limitstart=0' . $this->getRedirectToListAppend(), false));
        return true;
    }

    public function notify($data=array())
    {
        //ToDo
        $this->setRedirect(\JRoute::_('index.php?option=com_joomlaquiz&view=userquiz' . $this->getRedirectToListAppend(), false));
        return true;
    }
}