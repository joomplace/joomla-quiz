<?php
/**
* JoomlaQuiz component for Joomla
* @version $Id: install.joomlaquiz.php 2009-11-16 17:30:15
* @package JoomlaQuiz
* @subpackage install.joomlaquiz.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// Don't allow access
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', '/');

class com_joomlaquizInstallerScript
{
	function install() {
	
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );
		
		$adminDir = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_joomlaquiz';
		
		/* is this needed? */
		if (!JFolder::exists(JPATH_ROOT . DS . 'images'. DS . 'joomlaquiz') ) {
			JFolder::create( JPATH_ROOT . DS . 'images' . DS . 'joomlaquiz');
		}
					
		if (!JFolder::exists(JPATH_ROOT . DS . 'images'. DS . 'joomlaquiz' . DS . 'images') ) {
			JFolder::create( JPATH_ROOT . DS . 'images' . DS . 'joomlaquiz' . DS . 'images');
		}
		
		/* need to be refactored // delete duplication */
		if (!JFile::exists(JPATH_SITE . DS . 'images' . DS . 'joomlaquiz' . DS . 'images' . DS . 'certificate_green.jpg')) {
			JFile::copy($adminDir . DS . 'assets'. DS . 'images' .DS. 'certificate_green.jpg', JPATH_SITE . DS . 'images' . DS . 'joomlaquiz' . DS . 'images' . DS . 'certificate_green.jpg');
		}
		
		if (!JFile::exists(JPATH_SITE . DS . 'images' . DS . 'joomlaquiz' . DS . 'images' . DS . 'certificate_blue.jpg')) {
			JFile::copy($adminDir . DS . 'assets'. DS . 'images' .DS. 'certificate_blue.jpg', JPATH_SITE . DS . 'images' . DS . 'joomlaquiz' . DS . 'images' . DS . 'certificate_blue.jpg');
		}
		
		if (!JFile::exists(JPATH_SITE . DS . 'images' . DS . 'joomlaquiz' . DS . 'images' . DS . 'certificate_beige.jpg')) {
			JFile::copy($adminDir . DS . 'assets'. DS . 'images' .DS. 'certificate_beige.jpg', JPATH_SITE . DS . 'images' . DS . 'joomlaquiz' . DS . 'images' . DS . 'certificate_beige.jpg');
		}
		
		/* is this needed? //  need to be refactored  */
		JFile::copy($adminDir . DS . 'assets' .DS. 'fonts' .DS. 'arial.ttf', JPATH_SITE . DS . 'media' . DS . 'arial.ttf');
		
	}
	
	function uninstall($parent)
    {
	    echo '<p>' . JText::_('COM_JOOMLAQUIZ_UNINSTALL_TEXT') . '</p>';
    }

    function update($parent)
    {
			
		$xml = JFactory::getXML(JPATH_ADMINISTRATOR .'/components/com_joomlaquiz/joomlaquiz.xml');
		$this->version_from = $version = preg_split( '/(\s|\.)/', $xml->version );
		if($version[1]<=4 && $version[2]<1){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')
				->from('`#__menu`')
				->where('`link` LIKE "%com_joomlaquiz%"');
			$db->setQuery($query);
			$menu_items = $db->loadObjectList();
			
			$query->clear();
			$query->select('*')
				->from('`#__categories`')
				->where('`extension` LIKE "%com_joomlaquiz%"');
			$db->setQuery($query);
			$categories = $db->loadObjectList('note');
		
			foreach($menu_items as $mi){
				$mi->params = json_decode($mi->params);
				$mi->params->cat_id = $categories[$mi->params->cat_id]->id;
				$mi->params = json_encode($mi->params);
				$query->clear();
				$query->update('`#__menu`')
					->where('`id` = '.$mi->id)
					->set('`params` = '.$db->q($mi->params).'');
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		

		$newColumns = array(
			't_qtypes' => array(
				'c_type' => "VARCHAR( 50 ) NOT NULL"
			),
			't_question' => array(
				'c_detailed_feedback' => "TEXT NOT NULL"
			),
			'r_student_question' => array(
				'c_flag_question' => "TINYINT( 2 ) NOT NULL"
			),
			'r_student_blank' => array(
				'is_correct' => "TINYINT( 4 ) NOT NULL"
			),
			't_quiz' => array(
				'c_flag' => 'TINYINT( 3 ) NOT NULL',
				'c_hide_feedback' => 'TINYINT( 3 ) NOT NULL',
				'c_auto_breaks' => 'TINYINT( 5 ) NOT NULL',
				'c_share_buttons' => 'TINYINT( 3 ) NOT NULL',
				'asset_id' => 'INT( 18 ) NOT NULL',
                'c_quiz_access_message' => 'TEXT NOT NULL',
                'c_quiz_certificate_access_message' => 'TEXT NOT NULL'
			),
			'r_student_quiz' => array(
				'user_name' => 'VARCHAR(50) NOT NULL',
				'user_surname' => 'VARCHAR(100) NOT NULL',
                'params' => "VARCHAR( 1024 ) NOT NULL DEFAULT '{}'"
			),
			'lpath' => array(
				'asset_id' => 'INT(18) NOT NULL',
				'lp_access_message' => 'TEXT NOT NULL'
			),
			'certificates' => array(
				'text_font' => 'VARCHAR(255) NOT NULL'
			)
		);

		foreach ($newColumns as $table => $fields)
		{
			$oldColumns = $db->getTableColumns('#__quiz_'.$table);

			foreach ( $fields as $key => $value)
			{
				if ( empty($oldColumns[$key]) )
				{
					$db->setQuery('ALTER TABLE `#__quiz_'.$table.'` ADD `'.$key.'` '.$value);
					$db->execute();
					if($key=='c_type' && $table == 't_qtypes'){
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'choice' WHERE `c_id` =1");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'mresponse' WHERE `c_id` =2");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'truefalse' WHERE `c_id` =3");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'dragdrop' WHERE `c_id` =4");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'dropdown' WHERE `c_id` =5");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'blank' WHERE `c_id` =6");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'hotspot' WHERE `c_id` =7");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'surveys' WHERE `c_id` =8");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'boilerplate' WHERE `c_id` =9");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'mquestion' WHERE `c_id` =10");
						$db->execute();
					}
				}
			}
		}
		
		$db->setQuery("ALTER TABLE `#__quiz_r_student_question` CHANGE `c_score` `c_score` FLOAT( 11 ) NULL DEFAULT '0';");
		$db->execute();
		$db->setQuery("ALTER TABLE `#__quiz_r_student_quiz` CHANGE `c_total_score` `c_total_score` FLOAT NOT NULL DEFAULT '0';");
		$db->execute();

		$db->setQuery("ALTER TABLE `#__quiz_t_question` CHANGE `c_point` `c_point` FLOAT NOT NULL DEFAULT '0';");
		$db->execute();
  
		$db->setQuery("ALTER TABLE `#__quiz_t_quiz` CHANGE `c_full_score` `c_full_score` FLOAT( 10 ) NOT NULL DEFAULT '0';");
		$db->execute();
		$db->setQuery("ALTER TABLE `#__quiz_t_quiz` CHANGE `c_passing_score` `c_passing_score` FLOAT NOT NULL DEFAULT '0';");
		$db->execute();
		
		$db->setQuery("SELECT COUNT(*) FROM #__quiz_certificates WHERE `id` = 1");
		if(!$db->loadResult()){
			$db->SetQuery("INSERT INTO `#__quiz_certificates` (id, cert_name, cert_file) VALUES (1, 'Certificate Green', 'certificate_green.jpg')");
			$db->execute();
		}
		
		$db->setQuery("SELECT COUNT(*) FROM #__quiz_certificates WHERE `id` = 2");
		if(!$db->loadResult()){
			$db->SetQuery("INSERT INTO `#__quiz_certificates` (id, cert_name, cert_file) VALUES (2, 'Certificate Blue', 'certificate_blue.jpg')");
			$db->execute();
		}
		
		$db->setQuery("SELECT COUNT(*) FROM #__quiz_certificates WHERE `id` = 3");
		if(!$db->loadResult()){
			$db->SetQuery("INSERT INTO `#__quiz_certificates` (id, cert_name, cert_file) VALUES (3, 'Certificate Beige', 'certificate_beige.jpg')");
			$db->execute();
		}
		
		$db->setQuery("SELECT id FROM `#__quiz_templates` WHERE template_name='joomlaquiz_delux'");
		$joomlaquiz_delux_id = $db->loadResult();
		if ($joomlaquiz_delux_id) {
			$db->setQuery("UPDATE #__quiz_t_quiz SET c_skin = 1 WHERE c_skin = '{$joomlaquiz_delux_id}'");
			$db->execute();
			
			$db->setQuery("DELETE #__quiz_t_quiz FROM `#__quiz_templates` WHERE template_name='joomlaquiz_delux''");
			$db->execute();
		}
				
		$db->SetQuery("SELECT id FROM `#__quiz_templates` WHERE template_name='joomlaquiz_standard'");
		if(!$db->loadResult()) {
			$db->SetQuery("INSERT INTO `#__quiz_templates` (id, template_name) VALUES ('', 'joomlaquiz_standard');");
			$db->execute();
		}

		$db->SetQuery("SELECT id FROM `#__quiz_templates` WHERE template_name='joomlaquiz_t3_bs3'");
		if(!$db->loadResult()) {
			$db->SetQuery("INSERT INTO `#__quiz_templates` (id, template_name) VALUES ('', 'joomlaquiz_t3_bs3');");
			$db->execute();
		}

		$db->SetQuery("SELECT id FROM `#__quiz_templates` WHERE template_name='joomlaquiz_blue'");
		if(!$db->loadResult()) {
			$db->SetQuery("INSERT INTO `#__quiz_templates` (id, template_name) VALUES ('', 'joomlaquiz_blue');");
			$db->execute();
		}
		
		$db->SetQuery("SELECT id FROM `#__quiz_templates` WHERE template_name='joomlaquiz_simple'");
		if(!$db->loadResult()) {
			$db->SetQuery("INSERT INTO `#__quiz_templates` (id, template_name) VALUES ('', 'joomlaquiz_simple');");
			$db->execute();
		}

		$db->SetQuery("SELECT id FROM `#__quiz_templates` WHERE template_name='joomlaquiz_pretty_green'");
		if(!$db->loadResult()) {
			$db->SetQuery("INSERT INTO `#__quiz_templates` (id, template_name) VALUES ('', 'joomlaquiz_pretty_green');");
			$db->execute();
		}
		
		$db->SetQuery("SELECT id FROM `#__quiz_templates` WHERE template_name='joomlaquiz_pretty_blue'");
		if(!$db->loadResult()) {
			$db->SetQuery("INSERT INTO `#__quiz_templates` (id, template_name) VALUES ('', 'joomlaquiz_pretty_blue');");
			$db->execute();
		}
		
		$db->setQuery("SELECT COUNT(*) FROM `#__quiz_configuration` WHERE `config_var` = 'wysiwyg_options'");
		if (!$db->loadResult()) {
				$db->setQuery("INSERT INTO `#__quiz_configuration` ( `config_var` , `config_value` ) VALUES ('wysiwyg_options', '0');");
				$db->execute();
		}
		
		//add quiz pool
		$db->SetQuery("SELECT count(*) FROM `#__quiz_t_quiz` WHERE `c_title` = 'Questions Pool'");
		if(!$db->LoadResult()){
			$db->SetQuery("INSERT INTO `#__quiz_t_quiz` SET 
									`c_id` = 0, 
									`c_user_id` = 62, 
									`c_full_score` = 0, 
									`c_title` = 'Questions Pool', 
									`c_description` = '', 
									`c_right_message` = '', 
									`c_wrong_message` = '', 
									`c_pass_message` = '', 
									`c_unpass_message` = '', 
									`c_short_description` = ''");	
		}
		$db->execute();
		$db->setQuery("UPDATE `#__quiz_t_quiz` SET `c_id` = 0 , `c_skin` = 1 WHERE `c_title` = 'Questions Pool'");
		$db->execute();
		
		$db->setQuery("SELECT COUNT(id) FROM #__quiz_dashboard_items");
		if(!$db->loadResult()){
			$db->setQuery("INSERT INTO `#__quiz_dashboard_items` (`id`, `title`, `url`, `icon`, `published`) VALUES
			(1, 'Manage Quizzes', 'index.php?option=com_joomlaquiz&view=quizzes', '".JURI::root()."/media/com_joomlaquiz/images/quizzes48.png', 1),
			(2, 'Manage Questions', 'index.php?option=com_joomlaquiz&view=questions', '".JURI::root()."/media/com_joomlaquiz/images/questions48.png', 1),
			(3, 'Help', 'http://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe/index.html', '".JURI::root()."/media/com_joomlaquiz/images/help48.png', 1);");
			$db->execute();
		}

		$db->setQuery("INSERT INTO `#__quiz_cert_fields` (`c_id`, `cert_id`, `f_text`, `text_x`, `text_y`, `text_h`, `shadow`, `font`) VALUES ('', 2, 'For the successful completion of quiz:', 170, 520, 20, 0, 'arial.ttf'), ('', 2, '#reg_answer#', 170, 680, 20, 0, 'arial.ttf'), ('', 2, 'dated from #date(d F Y)#', 170, 630, 20, 0, 'arial.ttf'), ('', 2, '#course#', 170, 570, 20, 1, 'arial.ttf'), ('', 2, '#name#', 350, 450, 20, 1, 'arial.ttf');");
		$db->execute();
		
		
		$this->migrateCategories();
    }

	function migrateCategories(){
	
		$this->defaultCategoryCheck();
		
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__quiz_t_category');
		$quiz_categories = $db->setQuery($query)->loadObjectList('c_id');
		
		$error = false;
		foreach($quiz_categories as $key => $qzc){
			$extension = 'com_joomlaquiz';
			$title     = $qzc->c_category;
			$desc      = $qzc->c_instruction;
			$parent_id = 1;
			$quiz_categories[$key] = $this->createCategory($extension, $title, $desc, $parent_id, $qzc->c_id);
			if(!$quiz_categories[$key]->id){
				$error = true;
			}else{
				$query->clear();
				$query->update('#__quiz_t_quiz')
					->set('`c_category_id` = "'.$quiz_categories[$key]->id.'"')
					->where('`c_category_id` = "'.$quiz_categories[$key]->note.'"');
				$db->setQuery($query)->execute();
			}
		}
		if(!$error){
			$query->clear();
			$query->delete('#__quiz_t_category');
			$db->setQuery($query)->execute();
		}
		
		/* create pseudo-tree */
		$query = $db->getQuery(true);
		$query->select('DISTINCT(qc_tag) AS value, qc_tag')
			->from('#__quiz_q_cat')
			->where('TRIM(qc_tag) <> \'\'');
		$head_categories = $db->setQuery($query)->loadObjectList('qc_tag');
		
		foreach($head_categories as $key => $hqc){
			$extension = 'com_joomlaquiz.questions';
			$title     = $hqc->qc_tag;
			$desc      = '';
			$parent_id = 1;
			$head_categories[$key] = $this->createCategory($extension, $title, $desc, $parent_id, $hqc->c_id);
		}
		/* pseudo-tree done */
		
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__quiz_q_cat');
		$quest_categories = $db->setQuery($query)->loadObjectList('qc_id');
		
		$error = false;
		foreach($quest_categories as $key => $quc){
			$extension = 'com_joomlaquiz.questions';
			$title     = $quc->qc_category;
			$desc      = $quc->instruction;
			$parent_id = $head_categories[$quc->qc_tag]->id;
			$quest_categories[$key] = $this->createCategory($extension, $title, $desc, $parent_id, $quc->qc_id);
			if(!$quest_categories[$key]->id){
				$error = true;
			}else{
				$query->clear();
				$query->update('#__quiz_t_question')
					->set('`c_ques_cat` = "'.$quest_categories[$key]->id.'"')
					->where('`c_ques_cat` = "'.$quest_categories[$key]->note.'"');
				$db->setQuery($query)->execute();
				$query->clear();
				$query->update('#__quiz_pool')
					->set('`q_cat` = "'.$quest_categories[$key]->id.'"')
					->where('`q_cat` = "'.$quest_categories[$key]->note.'"');
				$db->setQuery($query)->execute();
			}
		}
		if(!$error){
			$query->clear();
			$query->delete('#__quiz_q_cat');
			$db->setQuery($query)->execute();
		}
		
	}
	
	function defaultCategoryCheck()
	{
		/* checking default category quizzes */
		$extension = 'com_joomlaquiz';
		$title     = 'Uncategorised';
		$desc      = 'A default category for the joomlaquiz quizzes.';
		$parent_id = 1;
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__categories')
			->where('`extension`="'.$extension.'"')
			->where('`parent_id`="'.$parent_id.'"');
		$exists = count($db->setQuery($query)->loadObjectList());
		
		if(!$exists)
			$this->createCategory($extension, $title, $desc, $parent_id);
		
		/* checking default category questions */
		$extension = 'com_joomlaquiz.questions';
		$title     = 'Uncategorised';
		$desc      = 'A default category for the joomlaquiz questions.';
		$parent_id = 1;
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__categories')
			->where('`extension`="'.$extension.'"')
			->where('`parent_id`="'.$parent_id.'"');
		$exists = count($db->setQuery($query)->loadObjectList());
		
		if(!$exists)
			$this->createCategory($extension, $title, $desc, $parent_id);
	}
	
	function createCategory($extension, $title, $desc, $parent_id=1, $note='', $published=1, $access = 1, $params = '{"target":"","image":""}', $metadata = '{"page_title":"","author":"","robots":""}', $language = '*'){	
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
		   JTable::addIncludePath(JPATH_PLATFORM . 'joomla/database/table');
		}

		// Initialize a new category
		$category = JTable::getInstance('Category');
		$category->extension = $extension;
		$category->title = $title;
		$category->description = $desc;
		$category->note = $note;
		$category->published = $published;
		$category->access = $access;
		$category->params = $params;
		$category->metadata = $metadata;
		$category->language = $language;

		$category->setLocation($parent_id, 'last-child');
		if (!$category->check())
		{
		   JError::raiseNotice(500, $category->getError());
		   return false;
		}
		if (!$category->store(true))
		{
		   JError::raiseNotice(500, $category->getError());
		   return false;
		}
		
		$category->rebuildPath($category->id);
		
		return $category;
	}
	
	function preflight($type, $parent) 
	{
		$this->defaultCategoryCheck();
	}
	
	function postflight($type, $parent)
    {
		$app = JFactory::getApplication();
		$db	= JFactory::getDBO();	
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_r_student_share` (`id` int(12) unsigned NOT NULL AUTO_INCREMENT, `c_quiz_id` int(12) unsigned NOT NULL, `c_stu_quiz_id` int(12) unsigned NOT NULL, `c_user_id` int(12) unsigned NOT NULL, `c_share_id` varchar(64) NOT NULL, PRIMARY KEY (`id`))");
		$db->execute();
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_dashboard_items` ( `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(255) NOT NULL, `url` varchar(255) NOT NULL, `icon` varchar(255) NOT NULL, `published` tinyint(1) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;");
		$db->execute();
		
		$db->setQuery("SELECT * FROM `#__quiz_dashboard_items`");
		$dashs = $db->loadObjectList();
		if(empty($dashs)){
			$db->setQuery("
				INSERT INTO `#__quiz_dashboard_items` (`id`, `title`, `url`, `icon`, `published`) VALUES
				(1, 'Manage Quizzes', 'index.php?option=com_joomlaquiz&view=quizzes', '".JURI::root(true)."/media/com_joomlaquiz/images/quizzes48.png', 1),
				(2, 'Manage Questions', 'index.php?option=com_joomlaquiz&view=questions', '".JURI::root(true)."/media/com_joomlaquiz/images/questions48.png', 1),
				(3, 'Help', 'http://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe/index.html', '".JURI::root(true)."/media/com_joomlaquiz/images/help48.png', 1);
			");
			$db->execute();
		}



		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_t_ext_hotspot` (`c_id` int(12) unsigned NOT NULL AUTO_INCREMENT, `c_question_id` int(12) NOT NULL, `c_paths` text NOT NULL, PRIMARY KEY (`c_id`))");
		$db->execute();
	}
	
}
