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
 * Certificates Controller
 */
class JoomlaquizControllerCertificates extends JControllerAdmin
{
        /**
         * Proxy for getModel.
         * @since       1.6
         */
        public function getModel($name = 'Certificate', $prefix = 'JoomlaquizModel', $config = array('ignore_request' => true))
        {
                $model = parent::getModel($name, $prefix, $config);
                return $model;
        }
		
		public function prev_cert(){
			$id = JFactory::getApplication()->input->get('id');
			$model = $this->getModel("Certificates");
			$model->previewCertificate($id);
			
			return true;
		}

		public function sample_certs() {
			$model = $this->getModel("Certificates");
			$redirect = JRoute::_('index.php?option=com_joomlaquiz&view=certificates', false);
			$message_succes = JText::_('COM_JOOMLAQUIZ_CERT_ADD_SAMPLE_MESSAGE');
			$message_error = JText::_('COM_JOOMLAQUIZ_CERT_ADD_SAMPLE_MESSAGE_ERROR');
			if ($model->sample_certs()) $this->setRedirect($redirect, $message_succes);
			else $this->setRedirect($redirect, $message_error, 'error');
		}
}
