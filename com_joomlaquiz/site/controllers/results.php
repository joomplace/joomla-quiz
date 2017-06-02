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
 * Results Controller
 */
class JoomlaquizControllerResults extends JControllerForm
{
    public function getModel($name = 'results', $prefix = '', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function sturesult()
    {

        $model = $this->getModel();
        $quiz_params = $model->getQuizParams();

        require_once(JPATH_SITE . '/components/com_joomlaquiz/views/quiz/view.html.php');
        $view = $this->getView("quiz");
        $view->display(null, $quiz_params);

        return true;
    }
}
