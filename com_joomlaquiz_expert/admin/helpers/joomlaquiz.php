<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

require_once( JPATH_ROOT .'/components/com_joomlaquiz/libraries/apps.php' );

/**
 * Joomlaquiz Deluxe component helper.
 */
class JoomlaquizHelper
{
		public static function JQ_Delete_Items($cids, $path, $event){
			
			jimport('joomla.filesystem.folder');
			$folders = JFolder::folders(JPATH_SITE.'/plugins/joomlaquiz/', '.');
			if(!empty($folders)){
				foreach($folders as $folder){
					if(file_exists(JPATH_SITE.'/plugins/joomlaquiz/'.$folder.'/admin/'.$path.$folder.'.php')){
						require_once(JPATH_SITE.'/plugins/joomlaquiz/'.$folder.'/admin/'.$path.$folder.'.php');
						$functionName = $event.ucfirst($folder);
						call_user_func($functionName, $cids);
					}
				}
			}			
		}
				
		public static function JQ_Calculate_Quiz_totalScore($qid){
			
			$total_score = 0;
			$database = JFactory::getDBO();
            $query = "SELECT SUM(q.c_point) FROM #__quiz_t_question as q LEFT JOIN `#__quiz_t_qtypes` as `b` ON b.c_id = q.c_type LEFT JOIN `#__extensions` as `e` ON (CONVERT (e.element USING utf8) COLLATE utf8_unicode_ci) = b.c_type WHERE q.c_quiz_id = '".$qid."' AND q.published = 1 AND q.c_type != 11 AND e.folder = 'joomlaquiz' AND e.type = 'plugin' AND e.enabled = 1";
            $database->SetQuery( $query );
			$total_score = $database->LoadResult();
			
			jimport('joomla.filesystem.folder');
			$folders = JFolder::folders(JPATH_SITE.'/plugins/joomlaquiz', '.');
			
			if(!empty($folders)){
				foreach($folders as $folder){
					if(file_exists(JPATH_SITE.'/plugins/joomlaquiz/'.$folder.'/admin/total/'.$folder.'.php')){
						$database->setQuery("SELECT `enabled` FROM `#__extensions` WHERE folder = 'joomlaquiz' AND type = 'plugin' AND element = '".$folder."'");
						$enabled = $database->loadResult();
						
						if($enabled){
							require_once(JPATH_SITE.'/plugins/joomlaquiz/'.$folder.'/admin/total/'.$folder.'.php');
							$functionName = 'getTotalScore'.ucfirst($folder);
							$total_score += call_user_func($functionName, $qid);
						}
					}
				}
			}
			
			$query = "UPDATE #__quiz_t_quiz SET c_full_score = '".$total_score."' WHERE c_id = '".$qid."'";
			$database->SetQuery( $query );
			$database->execute();
			
			return true;
		}
		
		public static function jq_substr($str, $start, $length = false) {
			
			return JString::substr($str, $start, $length);
			
		/*	if (function_exists('mb_substr')) {
				if ($length!==null)
					return mb_substr($str, $start, $length);
				else
					return mb_substr($str, $start);
			} else {
				if ($length!==null)
					return substr($str, $start, $length);
				else
					return substr($str, $start);
			}*/
		}
		
		public static function getQuestionType($new_qtype_id){
			
			$db = JFactory::getDBO();
			$reg_types = array(); $type = '';
			
			$db->setQuery("SELECT `c_id`, `c_type` FROM #__quiz_t_qtypes");
			$reg_types = $db->loadObjectList();
			
			if(!empty($reg_types)){
				foreach($reg_types as $reg_type){
					$reg_type = (array) $reg_type;
					if($reg_type['c_id'] == $new_qtype_id){
						$type = $reg_type['c_type'];
						break;
					}
				}
			}
			
			return $type;
		}
		
        public static function getVersion() 
        {
			$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/joomlaquiz.xml');
			return (string)$xml->version;
        }
                
        public static function showTitle($submenu)  
        {       
         	$document = JFactory::getDocument();
			$title = JText::_('COM_JOOMLAQUIZ_ADMINISTRATION_'.strtoupper($submenu));
            $document->setTitle($title);
            JToolBarHelper::title($title, $submenu);                	               	              
        }
		
		public static function addSubmenu($vName)
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_CATEGORY'),
				'index.php?option=com_categories&extension=com_joomlaquiz',
				$vName == 'quizcategories'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_QUESTIONS_CATEGORY'),
				'index.php?option=com_categories&extension=com_joomlaquiz.questions',
				$vName == 'questcategories'
			);
			JHtmlSidebar::addEntry(
                JText::_('COM_JOOMLAQUIZ_SUBMENU_CATEGORIES_LPATH'),
                'index.php?option=com_categories&extension=com_joomlaquiz.lpath',
                $vName == 'lpathscategories'
            );
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_QUIZ'),
				'index.php?option=com_joomlaquiz&view=quizzes',
				$vName == 'quizzes'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_QUEST'),
				'index.php?option=com_joomlaquiz&view=questions',
				$vName == 'questions'
			);
			JHtmlSidebar::addEntry(
                JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_LPATH'),
                'index.php?option=com_joomlaquiz&view=lpaths',
                $vName == 'lpaths'
            );
		}

		public static function addQuizzesSubmenu($vName)
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_CATEGORY'),
				'index.php?option=com_categories&extension=com_joomlaquiz',
				$vName == 'quizcategories'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_QUIZ'),
				'index.php?option=com_joomlaquiz&view=quizzes',
				$vName == 'quizzes');
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_IMPORT_QUIZZES'),
				'index.php?option=com_joomlaquiz&view=quizzes&layout=import_quizzes',
				$vName == 'import_quizzes'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_LPATH'),
				'index.php?option=com_joomlaquiz&view=lpaths',
				$vName == 'lpaths'
			);
            JHtmlSidebar::addEntry(
                JText::_('COM_JOOMLAQUIZ_SUBMENU_CATEGORIES_LPATH'),
                'index.php?option=com_categories&extension=com_joomlaquiz.lpath',
                $vName == 'lpathscategories'
            );
		}
		
		public static function addQuestionsSubmenu($vName)
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_QUESTIONS_CATEGORY'),
				'index.php?option=com_categories&extension=com_joomlaquiz.questions',
				$vName == 'questcategories'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_QUEST'),
				'index.php?option=com_joomlaquiz&view=questions',
				$vName == 'questions');
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_QUESTIONS_POOL'),
				'index.php?option=com_joomlaquiz&view=questions&quiz_id=0',
				$vName == 'questions_pool'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_UPLOAD_QUEST'),
				'index.php?option=com_joomlaquiz&view=questions&layout=uploadquestions',
				$vName == 'uploadquestions'
			);
		}
		
		public static function addPaymentsSubmenu($vName)
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_MANUAL_PAYMENTS'),
				'index.php?option=com_joomlaquiz&view=payments',
				$vName == 'payments'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_REACTIVATE_ACCESS'),
				'index.php?option=com_joomlaquiz&view=reactivates',
				$vName == 'reactivates');
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_QUIZ_PRODUCTS'),
				'index.php?option=com_joomlaquiz&view=products',
				$vName == 'products'
			);
		}
		
		public static function addSettingsSubmenu($vName)
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_TEMPLATES'),
				'index.php?option=com_joomlaquiz&view=templates',
				$vName == 'templates');
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_CERTIFICATES'),
				'index.php?option=com_joomlaquiz&view=certificates',
				$vName == 'certificates'
			);
		}
		
		public static function addReportsSubmenu($vName)
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_VIEW_REPORTS'),
				'index.php?option=com_joomlaquiz&view=results',
				$vName == 'results'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_VIEW_STATISTIC'),
				'index.php?option=com_joomlaquiz&view=statistic',
				$vName == 'statistic');
			JHtmlSidebar::addEntry(
				JText::_('COM_JOOMLAQUIZ_SUBMENU_VIEW_DYNAMIC'),
				'index.php?option=com_joomlaquiz&view=dynamic',
				$vName == 'dynamic'
			);
		}

		public static function getVirtuemartCategories() {
			VmConfig::loadConfig();
			VmConfig::loadJLang('com_virtuemart');

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			$categoriesVm = array();


			$query->select($db->qn(array('vm_pc.virtuemart_category_id', 'vm_c.category_name')));
			$query->from($db->qn('#__virtuemart_categories', 'vm_pc'));
			$query->join('LEFT', $db->qn('#__virtuemart_categories_' . VmConfig::$vmlang, 'vm_c') . ' ON (' . $db->quoteName('vm_c.virtuemart_category_id') . ' = ' . $db->quoteName('vm_pc.virtuemart_category_id') . ')');

			$db->setQuery( $query );
			$categories = $db->loadObjectList();

			for($i = 0; $i < count($categories); $i++) {
				$categoriesVm[$categories[$i]->virtuemart_category_id] = $categories[$i]->category_name;
			}

			return $categoriesVm;
		}

		public static function getQuizzesForSelect(){
			$db = JFactory::getDBO();

			$query = "SELECT c_id AS value, c_title AS text"
				. "\n FROM #__quiz_t_quiz"
				. "\n ORDER BY c_title"
			;
			$db->setQuery( $query );
			$quizzes = $db->loadObjectList();

			//Add question pool
			$qpoll = new stdClass();
			$qpoll->value = '0';
			$qpoll->text = JTEXT::_('COM_JOOMLAQUIZ_SUBMENU_QUESTIONS_POOL');

			array_unshift($quizzes, $qpoll);

			return $quizzes;
		}
}