<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');


/**
 * Joomlaquiz Deluxe Table class
 */
class JoomlaquizTableQuestion extends JTable
{
    public static $_ucm = false;
    public $type = 'com_joomlaquiz.question';

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db)
    {
        parent::__construct('#__quiz_t_question', 'c_id', $db);
        JTableObserverTags::createObserver($this, array('typeAlias' => $this->type));
        $this->checkUCM();

    }

    protected function checkUCM(){
        if(!self::$_ucm){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->qn('type_id'))
                ->from($db->qn('#__content_types'))
                ->where($db->qn('type_alias').'='.$db->q($this->type));
            if(!$db->setQuery($query,0,1)->loadResult()){
                $content_type = new stdClass();
                $content_type->type_title = 'Quiz Questions';
                $content_type->type_alias = $this->type;
                $content_type->router = 'QuizHelperRoute::routeQuestionTag';
                $content_type->table = '{"special":{"dbtable":"#__quiz_t_question","key":"c_id","type":"Question","prefix":"JoomlaquizTable"}}';
                $content_type->field_mappings = '{"common": {"core_content_item_id": "c_id","core_title": "c_id","core_state": "published","core_params": "params","core_ordering": "ordering","core_catid": "c_quest_cat"}}';
//            {"common": {"core_content_item_id": "c_id","core_title": "c_id","core_state": "published","core_alias": "null","core_created_time": "null","core_modified_time": "null","core_body": "null","core_hits": "null","core_publish_up": "null","core_publish_down": "null","core_access": "null","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "null","core_ordering": "null","core_metakey": "null","core_metadesc": "null","core_catid": "c_quest_cat","core_xreference": "null","asset_id": "null"}}
                $db->insertObject('#__content_types',$content_type);
            }
            self::$_ucm = true;

        }
    }
				
		function store($updateNulls = false){

            $data = JFactory::getApplication()->input->get('jform',array(),'ARRAY');

            JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/categories.php');

            // Cast catid to integer for comparison
            $catid = (int) $data['c_ques_cat'];
            $cat_extension = 'com_joomlaquiz.questions';

            // Check if New Category exists
            if ($catid > 0)
            {
                $catid = CategoriesHelper::validateCategoryId($data['c_ques_cat'], $cat_extension);
            }

            // Save New Categoryg
            if ($catid == 0 && JFactory::getUser()->authorise('core.create', 'com_joomlaquiz'))
            {
                $table = array();
                $table['title'] = $data['c_ques_cat'];
                $table['parent_id'] = 1;
                $table['extension'] = $cat_extension;
                $table['language'] = $data['language']?$data['language']:'*';
                $table['published'] = 1;

                // Create new category and get catid back
                $data['c_ques_cat'] = CategoriesHelper::createCategory($table);
            }

            /*
             * Somehow this is not binded!!!
             */
            $this->bind($data);

            require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

			$input = JFactory::getApplication()->input;
            $jform = $input->get('jform', array(), 'ARRAY');
			$appsLib = JqAppPlugins::getInstance();
			$appsLib->loadApplications();
			
			$app = JFactory::getApplication();			
			$database = JFactory::getDBO();
			$this->c_question = stripslashes($this->c_question);
			$this->c_right_message = stripslashes($this->c_right_message);
			$this->c_wrong_message = stripslashes($this->c_wrong_message);

			if(empty($jform['c_id']) && !$this->c_type){
				$query = "SELECT MAX(`ordering`) FROM #__quiz_t_question
				WHERE `c_quiz_id`='" . $this->c_quiz_id . "'
				";
				$database->setQuery($query);
				$this->ordering = $database->loadResult() + 1;
				
				$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
				$this->c_type = $new_qtype_id;
			}

            if(!empty($jform['c_id'])){
				$this->c_id = $jform['c_id'];
                if(!empty($jform['c_type'])) {   //we are not in the quiz (copy quiz)
                    $this->c_type = $jform['c_type'];
                }
			}
			
			if ($input->get('task') == 'save2copy')
			{
				$this->c_id = '';
			}

            if(!empty($this->params)) {
                if (!is_string($this->params)) {
                    $this->params = new Joomla\Registry\Registry($this->params);
                    $this->params = $this->params->toString();
                }
            }

            unset($this->type);
            $res = parent::store($updateNulls);
			
			$data = array();
			$type = JoomlaquizHelper::getQuestionType($this->c_type);
			$data['quest_type'] = $type;
			$data['qid'] = $this->c_id;
			
			$appsLib->triggerEvent( 'onAdminSaveOptions' , $data );
            /* new approach */
            $reg = new Joomla\Registry\Registry($this->getProperties());
            $reg->merge(new Joomla\Registry\Registry($data));
            $appsLib->triggerEvent( 'onStoreQuestion', $reg );

            JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($this->c_quiz_id);
			
			return $res;
		}
		
		function storeCopy($updateNulls = false){
			
			require_once(JPATH_SITE."/components/com_joomlaquiz/libraries/apps.php");

			$appsLib = JqAppPlugins::getInstance();
			$appsLib->loadApplications();
			
			$app = JFactory::getApplication();
            $input = $app->input;
            $jform = $input->get('jform', array(), 'ARRAY');

			$database = JFactory::getDBO();
			$this->c_question = stripslashes($this->c_question);
			$this->c_right_message = stripslashes($this->c_right_message);
			$this->c_wrong_message = stripslashes($this->c_wrong_message);
						
			if(empty($jform['c_id']) && !$this->c_type){
				$query = "SELECT MAX(`ordering`) FROM #__quiz_t_question
				WHERE `c_quiz_id`='" . $this->c_quiz_id . "'
				";
				$database->setQuery($query);
				$this->ordering = $database->loadResult() + 1;
				
				$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
				$this->c_type = $new_qtype_id;
			}

			if(!empty($jform['c_id'])){
				$this->c_id = $jform['c_id'];
                if(!empty($jform['c_type'])) {   //we are not in the quiz (copy quiz)
                    $this->c_type = $jform['c_type'];
                }
			}
			
			$res = parent::store($updateNulls);			
			JoomlaquizHelper::JQ_Calculate_Quiz_totalScore($this->c_quiz_id);
			
			return $res;
		}
}