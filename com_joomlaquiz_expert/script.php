<?php
/**
* JoomlaQuiz component for Joomla
* @version $Id: install.joomlaquiz.php 2009-11-16 17:30:15
* @package JoomlaQuiz
* @subpackage install.joomlaquiz.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// Don't allow access
defined( '_JEXEC' ) or die( 'Restricted access' );

class com_joomlaquizInstallerScript
{
	function install() {
	
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );
		
		$adminDir = JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_joomlaquiz';
		
		/* is this needed? */
		if (!JFolder::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'images'. DIRECTORY_SEPARATOR . 'joomlaquiz') ) {
			JFolder::create( JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'joomlaquiz');
		}
					
		if (!JFolder::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'images'. DIRECTORY_SEPARATOR . 'joomlaquiz' . DIRECTORY_SEPARATOR . 'images') ) {
			JFolder::create( JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'joomlaquiz' . DIRECTORY_SEPARATOR . 'images');
		}
		
		/* need to be refactored // delete duplication */
		if (!JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'joomlaquiz' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'certificate_green.jpg')) {
			JFile::copy($adminDir . DIRECTORY_SEPARATOR . 'assets'. DIRECTORY_SEPARATOR . 'images' .DIRECTORY_SEPARATOR. 'certificate_green.jpg', JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'joomlaquiz' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'certificate_green.jpg');
		}
		
		if (!JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'joomlaquiz' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'certificate_blue.jpg')) {
			JFile::copy($adminDir . DIRECTORY_SEPARATOR . 'assets'. DIRECTORY_SEPARATOR . 'images' .DIRECTORY_SEPARATOR. 'certificate_blue.jpg', JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'joomlaquiz' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'certificate_blue.jpg');
		}
		
		if (!JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'joomlaquiz' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'certificate_beige.jpg')) {
			JFile::copy($adminDir . DIRECTORY_SEPARATOR . 'assets'. DIRECTORY_SEPARATOR . 'images' .DIRECTORY_SEPARATOR. 'certificate_beige.jpg', JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'joomlaquiz' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'certificate_beige.jpg');
		}
		
		/* copy font used for certificate //  need to be refactored  */
		JFile::copy($adminDir . DIRECTORY_SEPARATOR . 'assets' .DIRECTORY_SEPARATOR. 'fonts' .DIRECTORY_SEPARATOR. 'arial.ttf', JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'arial.ttf');
		
	}
	
	function uninstall($parent)
    {
	    echo '<p>' . JText::_('COM_JOOMLAQUIZ_UNINSTALL_TEXT') . '</p>';
    }

    function update($parent)
    {
        // Delete sql update file 0.0.0.0001.sql
        if(JFile::exists(JPATH_ADMINISTRATOR .'/components/com_joomlaquiz/sql/updates/mysql/0.0.0.001.sql')){
            JFile::delete(JPATH_ADMINISTRATOR .'/components/com_joomlaquiz/sql/updates/mysql/0.0.0.001.sql');
        }

		if(file_exists(JPATH_ADMINISTRATOR .'/components/com_joomlaquiz/joomlaquiz.xml')){
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
		}

		$newColumns = array(
			't_qtypes' => array(
				'c_type' => "VARCHAR( 50 ) NOT NULL"
			),
			't_question' => array(
				'c_detailed_feedback' => "TEXT NOT NULL",
                'sq_delayed' => "INT(5) NOT NULL DEFAULT '1'",
                'c_width' => "INT(10) NOT NULL DEFAULT '150'",
                'c_timer' => "INT(10) NOT NULL",
                'c_height' => "INT(10) NOT NULL DEFAULT '150'",
                'c_column' => "INT(11) NOT NULL DEFAULT '1'",
                'c_img_cover' => "TEXT NOT NULL",
                'c_sec_penalty' => "VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'tnnophoto.jpg'"
			),
			'r_student_question' => array(
				'c_flag_question' => "TINYINT( 2 ) NOT NULL"
			),
			't_quiz' => array(
				'c_flag' => 'TINYINT( 3 ) NOT NULL',
				'c_hide_feedback' => 'TINYINT( 3 ) NOT NULL',
				'c_share_buttons' => 'TINYINT( 3 ) NOT NULL',
				'c_auto_breaks' => 'TINYINT( 5 ) NOT NULL',
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
				'lp_access_message' => 'TEXT NOT NULL',
                'category' => 'INT(11) NULL'
			),
			'certificates' => array(
				'text_font' => 'VARCHAR(255) NOT NULL'
			),
            'cert_fields' => array(
                'text_x_center' =>"TINYINT(1) NOT NULL DEFAULT '0'"
            )
		);

		$db = JFactory::getDbo();
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
						
						
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'puzzle' WHERE `c_id` =11");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'imgmatch' WHERE `c_id` =12");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'memory' WHERE `c_id` =13");
						$db->execute();
						$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'dalliclick' WHERE `c_id` =14");
						$db->execute();
					}
				}
			}
		}
		
		$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` =11");
		if(!$db->loadResult()){
			$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'puzzle' WHERE `c_id` =11");
			$db->execute();
		}
		$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` =12");
		if(!$db->loadResult()){
			$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'imgmatch' WHERE `c_id` =12");
			$db->execute();
		}
		$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` =13");
		if(!$db->loadResult()){
			$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'memory' WHERE `c_id` =13");
			$db->execute();
		}
		$db->setQuery("SELECT `c_type` FROM `#__quiz_t_qtypes` WHERE `c_id` =14");
		if(!$db->loadResult()){
			$db->setQuery("UPDATE `#__quiz_t_qtypes` SET `c_type` = 'dalliclick' WHERE `c_id` =14");
			$db->execute();
		}

		$db->setQuery("ALTER TABLE `#__quiz_r_student_question` CHANGE `c_score` `c_score` FLOAT( 11 ) NULL DEFAULT '0';");
		$db->execute();
		$db->setQuery("ALTER TABLE `#__quiz_r_student_quiz` CHANGE `c_total_score` `c_total_score` FLOAT NOT NULL DEFAULT '0';");
		$db->execute();
		
		$oldColumns = $db->getTableColumns('#__quiz_t_question');
		if(empty($oldColumns['c_time_limit'])){
			$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD  `c_time_limit` INT UNSIGNED NOT NULL DEFAULT  '0'");
			$db->execute();
		}
		if(empty($oldColumns['c_show_timer'])){
			$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD  `c_show_timer` TINYINT NOT NULL DEFAULT  '0'");
			$db->execute();
		}
		
		$db->setQuery("ALTER TABLE `#__quiz_t_question` CHANGE `c_point` `c_point` FLOAT NOT NULL DEFAULT '0';");
		$db->execute();
		
		$oldColumns = $db->getTableColumns('#__quiz_t_quiz');
		if(empty($oldColumns['c_time_limit'])){
			$db->setQuery("ALTER TABLE `#__quiz_t_question` ADD  `c_time_limit` TINYINT NOT NULL DEFAULT  '0'");
			$db->execute();
		}
		
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
			$db->execute();
			$db->setQuery("UPDATE `#__quiz_t_quiz` SET `c_id` = 0 , `c_skin` = 1 WHERE `c_title` = 'Questions Pool'");
			$db->execute();
		}
		
		$db->setQuery("SELECT COUNT(id) FROM #__quiz_dashboard_items");
		if(!$db->loadResult()){
			$db->setQuery("INSERT INTO `#__quiz_dashboard_items` (`id`, `title`, `url`, `icon`, `published`) VALUES
			(1, 'Manage Quizzes', 'index.php?option=com_joomlaquiz&view=quizzes', '/administrator/components/com_joomlaquiz/assets/images/quizzes48.png', 1),
			(2, 'Manage Questions', 'index.php?option=com_joomlaquiz&view=questions', '/administrator/components/com_joomlaquiz/assets/images/questions48.png', 1),
			(3, 'Help', 'https://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe-3.0/index.html', '/administrator/components/com_joomlaquiz/assets/images/help48.png', 1);");
			$db->execute();
		} else {
            $query = $db->getQuery(true);
            $query->update('`#__quiz_dashboard_items`')
                ->where('`title` = \'Help\'')
                ->where('`url` != \'https://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe-3.0/index.html\'')
                ->set('`url` = \'https://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe-3.0/index.html\'');
            $db->setQuery($query);
            $db->execute();
        }

		/*
		$db->setQuery("INSERT INTO `#__quiz_cert_fields` (`c_id`, `cert_id`, `f_text`, `text_x`, `text_y`, `text_h`, `shadow`, `font`) VALUES ('', 2, 'For the successful completion of quiz:', 170, 520, 20, 0, 'arial.ttf'), ('', 2, '#reg_answer#', 170, 680, 20, 0, 'arial.ttf'), ('', 2, 'dated from #date(d F Y)#', 170, 630, 20, 0, 'arial.ttf'), ('', 2, '#course#', 170, 570, 20, 1, 'arial.ttf'), ('', 2, '#name#', 350, 450, 20, 1, 'arial.ttf');");
		$db->execute();
		*/
		
    }


	function migrateCategories(){
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

        //quiz categories
        $error = false;

        $query->select('*')
            ->from('#__quiz_t_category');
        $quiz_categories = $db->setQuery($query)->loadObjectList('c_id');

        if($quiz_categories) {
            $handled_quizzez = array();

            foreach ($quiz_categories as $key => $qzc) {
                $extension = 'com_joomlaquiz';
                $title = $qzc->c_category;
                $desc = $qzc->c_instruction;
                $parent_id = 1;
                $quiz_categories[$key] = $this->createCategory($extension, $title, $desc, $parent_id, $qzc->c_id);

                if (!$quiz_categories[$key]->id) {
                    $error = true;
                } else {
                    $query->clear();
                    $query->select($db->qn('c_id'))
                        ->from($db->qn('#__quiz_t_quiz'))
                        ->where($db->qn('c_category_id') .'='. $db->q((int)$quiz_categories[$key]->note));
                    $quiz_ids = $db->setQuery($query)->loadObjectList();
                    if ($quiz_ids) {
                        foreach ($quiz_ids as $quiz_id) {
                            if (in_array((int)$quiz_id->c_id, $handled_quizzez)) {
                                continue;
                            }
                            $query->clear();
                            $query->update($db->qn('#__quiz_t_quiz'))
                                ->set($db->qn('c_category_id') .'='. $db->q((int)$quiz_categories[$key]->id))
                                ->where($db->qn('c_id') .'='. $db->q((int)$quiz_id->c_id));
                            if ($db->setQuery($query)->execute()) {
                                $handled_quizzez[] = (int)$quiz_id->c_id;
                            }
                        }
                    }
                }
            }
            if (!$error) {
                $query->clear();
                $query->delete('#__quiz_t_category');
                $db->setQuery($query)->execute();
            }

            unset($handled_quizzez);
        }
        unset($quiz_categories);

        //questions categories
        $error = false;
        // create pseudo-tree
        $query->clear();
		$query->select('DISTINCT(qc_tag) AS value, qc_tag')
			->from('#__quiz_q_cat')
			->where('TRIM(qc_tag) <> \'\'');
		$head_categories = $db->setQuery($query)->loadObjectList('qc_tag');

		if($head_categories){
            foreach ($head_categories as $key => $hqc) {
                $extension = 'com_joomlaquiz.questions';
                $title = $hqc->qc_tag;
                $desc = '';
                $parent_id = 1;
                $head_categories[$key] = $this->createCategory($extension, $title, $desc, $parent_id);
            }
        }
		// pseudo-tree done

        $query->clear();
		$query->select('*')
			->from('#__quiz_q_cat');
		$quest_categories = $db->setQuery($query)->loadObjectList('qc_id');

		if($quest_categories) {
		    $handled_questions      = array();
            $handled_questions_pool = array();

            foreach ($quest_categories as $key => $quc) {
                $extension = 'com_joomlaquiz.questions';
                $title = $quc->qc_category;
                $desc = $quc->qc_instruction;
                $parent_id = $head_categories[$quc->qc_tag]->id ? (int)$head_categories[$quc->qc_tag]->id : 1;
                $quest_categories[$key] = $this->createCategory($extension, $title, $desc, $parent_id, $quc->qc_id);

                if (!$quest_categories[$key]->id) {
                    $error = true;
                } else {

                    $query->clear();
                    $query->select($db->qn('c_id'))
                        ->from($db->qn('#__quiz_t_question'))
                        ->where($db->qn('c_ques_cat') .'='. $db->q((int)$quest_categories[$key]->note));
                    $qids = $db->setQuery($query)->loadObjectList();
                    if ($qids) {
                        foreach ($qids as $qid) {
                            if (in_array((int)$qid->c_id, $handled_questions)) {
                                continue;
                            }
                            $query->clear();
                            $query->update($db->qn('#__quiz_t_question'))
                                ->set($db->qn('c_ques_cat') .'='. $db->q((int)$quest_categories[$key]->id))
                                ->where($db->qn('c_id') .'='. $db->q((int)$qid->c_id));
                            if ($db->setQuery($query)->execute()) {
                                $handled_questions[] = (int)$qid->c_id;
                            }
                        }
                    }

                    $query->clear();
                    $query->select($db->qn('q_id'))
                        ->from($db->qn('#__quiz_pool'))
                        ->where($db->qn('q_cat') .'='. $db->q((int)$quest_categories[$key]->note))
                        ->group('q_id');
                    $qids_pool = $db->setQuery($query)->loadObjectList();
                    if ($qids_pool) {
                        foreach ($qids_pool as $qid_pool) {
                            if (in_array((int)$qid_pool->q_id, $handled_questions_pool)) {
                                continue;
                            }
                            $query->clear();
                            $query->update($db->qn('#__quiz_pool'))
                                ->set($db->qn('q_cat') .'='. $db->q((int)$quest_categories[$key]->id))
                                ->where($db->qn('q_id') .'='. $db->q((int)$qid_pool->q_id))
                                ->where($db->qn('q_cat') .'='. $db->q((int)$quest_categories[$key]->note));
                            if ($db->setQuery($query)->execute()) {
                                $handled_questions_pool[] = (int)$qid_pool->q_id;
                            }
                        }
                    }
                }
            }
            if (!$error) {
                $query->clear();
                $query->delete('#__quiz_q_cat');
                $db->setQuery($query)->execute();
            }

            unset($quest_categories);
            unset($handled_questions);
            unset($handled_questions_pool);
            if($head_categories){
                unset($head_categories);
            }
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
            JFactory::getApplication()->enqueueMessage($category->getError(), 'error');
		    return false;
		}
		if (!$category->store())
		{
            JFactory::getApplication()->enqueueMessage($category->getError(), 'error');
            return false;
		}
		
		// Rebuild the path for the category:
        if (!$category->rebuildPath($category->id))
        {
            JFactory::getApplication()->enqueueMessage($category->getError(), 'error');
            return false;
        }
        // Rebuild the paths of the category's children:
        if (!$category->rebuild())
        {
            JFactory::getApplication()->enqueueMessage($category->getError(), 'error');
            return false;
        }
		
		return $category;
	}
	
	function postflight($type, $parent)
    {
		/* need to be refacrored */
		
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
				(3, 'Help', 'https://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe-3.0/index.html', '".JURI::root(true)."/media/com_joomlaquiz/images/help48.png', 1);
			");
			$db->execute();
		}
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__quiz_t_ext_hotspot` (`c_id` int(12) unsigned NOT NULL AUTO_INCREMENT, `c_question_id` int(12) NOT NULL, `c_paths` text NOT NULL, PRIMARY KEY (`c_id`))");
		$db->execute();

		/* add Uncategorialised category for learning path */
		$db->setQuery("SELECT `extension` FROM `#__categories` WHERE `extension` = 'com_joomlaquiz.lpath' ");
		if (!$db->loadColumn()) {
			$db->setQuery("INSERT INTO `#__categories` (`path`, `extension`, `title`, `alias`, `description`, `parent_id`, `published`, `params`, `metadata`) VALUES ('uncategorised', 'com_joomlaquiz.lpath', 'Uncategorised', 'uncategorised', 'A default category for the joomlaquiz questions.', 1, 1, '{\"target\":\"\",\"image\":\"\"}', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}')");
			$db->execute();
		}

        if ( $type == 'update' ) {
            $db->setQuery("DROP TABLE IF EXISTS `#__quiz_configuration`");
            $db->execute();
        }

        $this->migrateCategories();
		$this->defaultCategoryCheck();
	}
}
?>