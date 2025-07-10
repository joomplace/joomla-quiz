<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Joomlaquiz Component Controller
 */
class JoomlaquizController extends BaseController
{
	public function display($cachable = false, $urlparams = array())
	{
		
               $user = Factory::getUser();
               $view = Factory::getApplication()->input->getCmd('view', 'quiz');
               $task = Factory::getApplication()->input->getCmd('task');
               $rel_id = Factory::getApplication()->input->getInt('rel_id', 0);
               $package_id = Factory::getApplication()->input->getInt('package_id', 0);
		
        parent::display();
	}	
}