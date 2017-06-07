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
 * Templates Controller
 */
class JoomlaquizControllerTemplates extends JControllerAdmin
{
        /**
         * Proxy for getModel.
         * @since       1.6
         */
        public function getModel($name = 'Templates', $prefix = 'JoomlaquizModel', $config = array('ignore_request' => true))
        {
            $model = parent::getModel($name, $prefix, $config);
            return $model;
        }
		
		public function edit_css()
		{
			$app = JFactory::getApplication();
			$cid = $app->input->get('cid', array(), '');
			
			$this->setRedirect('index.php?option=com_joomlaquiz&view=templates&layout=edit_css&cid='.$cid[0]);
		}
		
		public function cancel()
		{
			$this->setRedirect('index.php?option=com_joomlaquiz&view=templates');
		}
		
		public function save_css(){
		
			$database = JFactory::getDBO();
			$app = JFactory::getApplication();
			
			$template_id	= intval( $app->input->get( 'template', 0 ) );
			$query = "SELECT template_name FROM #__quiz_templates WHERE id = '".$template_id."'";
			$database->SetQuery( $query );
			$template = $database->LoadResult();
			$filecontent 	= $app->input->get( 'filecontent', '','RAW' );

			if ( !$template ) {
				$this->setRedirect( 'index.php?option=com_joomlaquiz&view=templates', JText::_('COM_JOOMLAQUIZ_OPERATION_FAILED2') );
			}

			if ( !$filecontent ) {
				$this->setRedirect( 'index.php?option=com_joomlaquiz&view=templates', JText::_('COM_JOOMLAQUIZ_OPERATION_FAILED3') );
			}

			$file = JPATH_SITE . '/components/com_joomlaquiz/views/templates/tmpl/' . $template . '/css/jq_template.css';

			$enable_write 	= $app->input->get('enable_write',0);
			$oldperms 		= fileperms($file);
			
			if ($enable_write) {
				@chmod($file, $oldperms | 0222);
			}

			clearstatcache();
			if ( is_writable( $file ) == false ) {
				$this->setRedirect( 'index.php?option=com_joomlaquiz&view=templates', JText::_('COM_JOOMLAQUIZ_OPERATION_FAILED4') );
			}

			if ($fp = fopen ($file, 'w')) {
				fputs( $fp, stripslashes( $filecontent ) );
				fclose( $fp );
				if ($enable_write) {
					@chmod($file, $oldperms);
				} else {
					if ($app->input->get('disable_write',0))
						@chmod($file, $oldperms & 0777555);
				}
				$this->setRedirect("index.php?option=com_joomlaquiz&view=templates");
			} else {
				if ($enable_write) @chmod($file, $oldperms);
				$this->setRedirect( 'index.php?option=com_joomlaquiz&view=templates', JText::_('COM_JOOMLAQUIZ_OPERATION_FAILED5') );
			}
		}
}
